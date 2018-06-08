<?php

namespace App\Http\Controllers\Api;

use App\Models\Dlj;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Users;
use GatewayWorker\Lib\Gateway AS GatewayLib;
use Illuminate\Support\Facades\Auth;

class GatewayController extends Controller
{
	//导览机前缀
	const BIND_APP='app_';
	const BIND_DLJ='dlj_';

	public function __construct()
	{
		parent::_init();
		GatewayLib::$registerAddress = env('WM_REGISTER_IP', '127.0.0.1').':'.env('WM_REGISTER_PORT', '1238');
	}
	/**
	 * tcp监听协议
	 *
	 * @author lwb 20180608
	 *
	 * @api {GET} tcp://192.168.10.158:9501 01.监听地址
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiSuccess {string} type 信息类型
	 * (1.bind:机器号绑定;2.heart:心跳响应;3.chat:信息推送),
	 * type等于bind 机器号/uid绑定,执行uid/设备绑定接口,
	 * type等于heart 心跳响应,使用tcp链接向服务器发送string数据'pong';
	 * type等于chat 并且send_type等于1表示普通发送消息 2 表示语音消息
	 * @apiSuccess {string} client_id 连接上tcp后获得的client_id
	 * @apiSuccess {string} send_type 信息类型,标题
	 * @apiSuccess {string} send_content 信息内容
	 * @apiSuccess {string} group_id 群组ID
	 */

	/**
	 * 绑定导览机信息
	 * 设备开始租赁时告诉我 现在这个设备的绑定人的身份证号，用户名。我在我自己的表中添加或者修改这个设备的信息。
	 * @author lwb 20180608
	 *
	 * @api {Get} /gateway/device_info 02.租赁时调取接口
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p   d：导览机
	 * @apiParam {string} device_no  导览机唯一设备号
	 * @apiParam {string} certificate_number 租赁者的身份证号
	 * @apiParam {string} [name] 租赁者的姓名
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function device_info(){
		$this->validate([
			'device_no' => 'required|string|max:20',
			'certificate_number' => 'required|string|max:20',
		]);
		$device_no=request('device_no');
		$certificate_number=request('certificate_number');
		$equipment = Dlj::findOrNew($device_no);
		$equipment->device_no=$device_no;
		$equipment->certificate_number=$certificate_number;
		$equipment->name=request('name');
		$equipment->save();
		return response_json(1, [], '添加成功');
	}
	/**
	 * uid/设备绑定接口
	 *
	 * @author lwb 20180608
	 *
	 * @api {Get} /gateway/bind 03.uid或设备号绑定接口(绑定时自然会断开之前的链接)
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，d：导览机
	 * @apiParam {string} user_number app传uid 导览机传唯一设备号
	 * @apiParam {string} client_id 链接上tcp后获得的client_id
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function bind()
	{
		$this->validate([
			'client_id' => 'required|string|max:20',
			'user_number' => 'required',
		]);
		$prefix=request('p')=='d' ? self::BIND_DLJ :self::BIND_APP;
		$user_number = $prefix.request('user_number');
		$client_id = request('client_id');
		if (GatewayLib::isOnline($client_id)) {
			//判断当前机器号是否绑定过client_id
			$is_bind_arr = GatewayLib::getClientIdByUid($user_number);
			if (!empty($is_bind_arr)) {
				foreach ($is_bind_arr as $g) {
					//断开之前绑定的client_id
					GatewayLib::closeClient($g);
				}
			}
			//绑定机器号
			GatewayLib::bindUid($client_id, $user_number);
		} else {
			return response_json(0, [], 'client_id无效');
		}
		return response_json(1, [], '绑定成功');
	}


	/**
	 * 创建群组
	 *
	 * @author lwb 20180607
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /gateway/create_group 04.创建群组
	 * @apiGroup GateWay
	 * @apiIgnore
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {int} user_number app传uid 导览机传唯一设备号
	 * @apiParam {string} group_name 群组名称
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function create_group(){
		$this->validate([
			'uid' => 'required',
			'group_name' => 'required'
		]);
		$uid=request('uid');
		$client_id = request('client_id');
		$group=Group::create([
			'holder' => $uid,
			'name' => request('group_name'),
			'create_time' => time(),
			'group_number' => rand(10000,99999),
		]);
		$group_id=$group->id;
		//将群主加入该群 并且绑定client_id
		$group_member=new GroupMember();
		$group_member->member_id=$uid;
		$group_member->add_time=time();
		$group_member->group_id=$group_id;
		$group_member->save();
		//显示群组信息(我的头像 我的昵称 群组名称 群成员的头像和名称)
		$users_list=GroupMember::join('users', 'users.uid', '=', 'group_member.member_id')
			->where('group_id',$group_id)->select('users.avatar','users.nickname','users.uid')->get()->toArray();
		dump($users_list);

		$data['users_list']=$users_list;
		$data['group_id']=$group_id;
		$data['group_name']=request('group_name');
		GatewayLib::joinGroup($client_id, $group_id);

		return response_json(1,$data,'你已成功创建并加入群组');
	}


	/**
	 * 加入群组
	 *
	 * @author lwb 20180607
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /gateway/join_group 05.加入群组
	 * @apiGroup GateWay
	 * @apiIgnore
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} api_token 签名
	 * @apiParam {int} group_number 对外显示的群组id号
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function join_group(){
		$uid=Auth::id();

	}
}
