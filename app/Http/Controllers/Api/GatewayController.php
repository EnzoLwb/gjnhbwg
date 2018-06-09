<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatMessage;
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
	 * type等于send_msg 并且send_type等于1表示普通发送消息 2 表示语音消息 'error_msg' 表示 断开连接或者其它问题
	 * @apiSuccess {string} client_id 连接上tcp后获得的client_id
	 * @apiSuccess {string} send_type 信息类型,标题 error_msg 表示错误
	 * @apiSuccess {string} send_content 信息内容
	 * @apiSuccess {string} group_id 群组ID
	 */
	/**
	 * 返回到主页面(断开连接)/退还导览机
	 * 设备退出租赁时告诉我 将他断开tcp。
	 * @author lwb 20180608
	 *
	 * @api {Get} /gateway/close_client 02.返回到主页面(断开连接)/退还导览机
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，d：导览机
	 * @apiParam {string} user_number app传uid 导览机传唯一设备号
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function close_client(){
		$this->validate([
			'user_number' => 'required|max:20',
		]);
		$user_number=request('user_number');
		if (!GatewayLib::isUidOnline($user_number)) {
			//判断当前机器号是否绑定过client_id
			$is_bind_arr = GatewayLib::getClientIdByUid($user_number);
			if (!empty($is_bind_arr)) {
				foreach ($is_bind_arr as $g) {
					//断开之前绑定的client_id
					GatewayLib::closeClient($g);
				}
			}
		}
		return response_json(1, [], '已经断开连接');

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
	 * @apiSuccess {string} uid 私聊等地方用到的uid
	 */
	public function bind()
	{
		$this->validate([
			'client_id' => 'required|string|max:20',
			'user_number' => 'required',
		]);
		$user_number = request('user_number');
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
	 * @apiParam {int} user_number  app传uid   导览机传唯一设备号
	 * @apiParam {string} group_name 群组名称
	 * @apiSuccess {object} data 操作结果1成功0失败
	 * @apiSuccess {object} my_info 个人信息(导览机就返回空对象)(手机端返回 头像:avatar 昵称:nickname)
	 * @apiSuccess {int} group_id 群组ID号
	 * @apiSuccess {int} group_name 群组名称
	 */
	public function create_group(){
		$this->validate([
			'user_number' => 'required',
			'group_name' => 'required'
		]);
		$uid=request('user_number');
		$plat=request('p')!='d' ? '1':'2';
		//显示我的信息(我的头像 我的昵称 )
		if ($plat==1){
			//app
			$my_info=Users::where('uid',$uid)->select('avatar','nickname')->first();
		}else{
			//dlj
			$my_info=[];
		}
		$client_id = current(GatewayLib::getClientIdByUid($uid));
		$group=Group::create([
			'holder' => $uid,
			'group_name' => request('group_name'),
			'group_number' => rand(10000,99999),
		]);
		$group_id=$group->id;
		//将群主加入该群 并且绑定client_id
		$group_member=new GroupMember();
		$group_member->member_id=$uid;
		$group_member->add_time=time();
		$group_member->group_id=$group_id;
		$group_member->device_type=$plat;
		$group_member->save();

		$data['my_info']=$my_info;
		$data['group_id']=$group->group_number;
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
	 * @apiParam {string} user_number app传uid   导览机传唯一设备号
	 * @apiParam {int} group_number 对外显示的群组id号
	 * @apiSuccess {object} data 操作结果1成功0失败
	 */
	public function join_group(){
		$user_number=request('user_number');
		$group_number=request('group_number');
		$plat=request('p')!='d'?'1':'2';
		$group_info=Group::where('group_number',$group_number)->first();
		if (is_null($group_info)){
			return response_json(0,[],'不存在的群组号，加入失败！');
		}
		$group=GatewayLib::getAllGroupIdList($group_info['id']);
		if (!in_array($group_info['id'],$group)) return response_json(0,[],'不存在的群组ID，加入失败！');
		//显示我的信息(我的头像 我的昵称 )
		if ($plat==1){
			//app
			$my_info=Users::where('uid',$user_number)->select('avatar','nickname')->first();
		}else{
			//dlj
			$my_info=[];
		}
		$data['group_info']=['group_id'=>$group_info['id'],'group_number'=>$group_number,'name'=>$group_info['group_name']];
		$data['my_info']=$my_info;

		$where=array('group_id'=>$group_info['id']);
		$data['user_list']=GroupMember::leftjoin('users','users.uid','=','group_member.member_id')
			->where($where)->where('member_id','!=',$user_number)->select('users.avatar','users.nickname','users.uid','member_id')->get()->toArray();
		foreach ($data['user_list'] as &$v){
			if (!$v['uid']){
				$v['avatar']='';
				$v['nickname']=$v['member_id'];
			}
			$v['user_number']=$v['member_id'];
			unset($v['member_id'],$v['uid']);
		}
		//加入群组
		$group=GroupMember::create([
			'member_id' => $user_number,
			'group_id' => $group_info['id'],
			'add_time' =>time(),
			'device_type' =>$plat,
		]);
		GatewayLib::joinGroup(current(GatewayLib::getClientIdByUid($user_number)), $group_info['id']);
		return response_json(1, $data, '加入成功');
	}
	/**
	 * 私聊发送消息
	 *
	 * @author lwb 20180608
	 *
	 * @api {Get} /gateway/send_msg 06.私聊发送消息
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，d：导览机
	 * @apiParam {string} to_user_number 发送给的对象的user_number    之前列表返回了user_number
	 * @apiParam {string} from_user_number 我的user_number    之前返回了user_number
	 * @apiParam {string} content 发送内容
	 * @apiParam {int} type 内容类型 1为文本 2为语音
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function send_msg()
	{
		$this->validate([
			'to_user_number' => 'required',
			'content' => 'required'
		]);
		$type=request('type',1);
		$to_user_number=request('to_user_number');
		$from_user_number=request('from_user_number');
		$content=request('content');
		$device_type= request('p') !='d' ? 1:2;
		$to_client_id= current(GatewayLib::getClientIdByUid($to_user_number));
		if (empty($to_client_id)) {
			//断开连接或者uid输入错误
			//					$arr['client_id'] = $client_id;
			$arr['type'] = 'sent_msg';
			$arr['send_type'] = 'error_msg';
			$arr['send_content'] = ['error_msg' => '断开连接或者to_uid输入错误'];
		}else{
			//保存数据库
			ChatMessage::create([
				'send_msg'=>$content,
				'from_user_number'=>$from_user_number,
				'to_user_number'=>$to_user_number,
				'to_client_id'=>$to_client_id,
				'send_type'=>$type,
				'device_type'=>$device_type,
			]);
			$arr['type'] = 'sent_msg';
			$arr['send_type'] = '1';//1表示文本信息 2表示语音信息
			$arr['send_content'] = $content;
		}
		GatewayLib::sendToClient( $to_client_id,json_encode($arr));
		return response_json(1,[],'发送成功');
	}
}
