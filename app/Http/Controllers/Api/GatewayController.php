<?php

namespace App\Http\Controllers\Api;

use App\Dao\UploadedFileDao;
use App\Exceptions\ApiErrorException;
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
	 *
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
	 * @author lwb 20180609
	 *
	 * @api {GET} /gateway/join_group 05.加入群组
	 * @apiGroup GateWay
	 * @apiIgnore
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} user_number app传uid   导览机传唯一设备号
	 * @apiParam {int} group_number 对外显示的群组id号
	 * @apiSuccess {object} data 操作结果1成功0失败
	 * @apiSuccess {object} group_info  群组信息  包括group_id:对内的群组id  group_number：对外的群组id  name:群组名称
	 * @apiSuccess {object} my_info    我的信息 如果是手机则返回avatar:头像  nickname：昵称  如果是导览机的用户返回空 使用自己的设备号和默认头像即可
	 * @apiSuccess {object} user_list    返回avatar:头像(导览机用户为空)  nickname：昵称(导览机为设备号) user_number：uid (导览机为设备号)
	 */
	public function join_group(){
		$this->validate([
			'user_number' => 'required',
			'group_number' => 'required'
		]);
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
		GroupMember::create([
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
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function send_msg()
	{
		$this->validate([
			'to_user_number' => 'required',
			'content' => 'required'
		]);
		$type=1;
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
	/**
	 * 退出群组
	 *
	 * @author lwb 20180609
	 *
	 * @api {Get} /gateway/exit_group 07.退出群组
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，d：导览机
	 * @apiParam {string} user_number  我的id   之前列表返回了user_number
	 * @apiParam {string} group_number 对外的群组ID
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function exit_group()
	{
		$this->validate([
			'user_number' => 'required',
			'group_number' => 'required'
		]);
		$user_number=request('user_number');
		$group_number=request('group_number');
		$group_id=Group::where('group_number',$group_number)->value('id');
		$arr=['member_id'=>$user_number,'group_id'=>$group_id];
		GroupMember::where($arr)->delete();
		GatewayLib::leaveGroup(current(GatewayLib::getClientIdByUid($user_number)), $group_id);
		return response_json(1, [], '退出成功');
	}
	/**
	 * 聊天发送语音文件
	 *
	 * @author lwb 20180609
	 *
	 * @api {Post} /gateway/upload_audio 08.聊天发送语音文件
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，d：导览机
	 * @apiParam {string} from_user_number  我的id   之前列表返回了user_number
	 * @apiParam {string} to_user_number  对方的id   之前列表返回了user_number
	 * @apiParam {file} audio 语音文件 最大不超过20m MP3格式
	 * @apiParam {string} length 语音时间长度 单位秒
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function upload_audio()
	{
		$this->validate([
			'from_user_number' => 'required',
			'to_user_number' => 'required',
			'chat_audio' => 'required|file',
			'length' => 'required'
		]);
		$scheme = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
		$url = $scheme.$_SERVER['HTTP_HOST'];
		$from_user_number = request('from_user_number');
		$to_user_number = request('to_user_number');
		// 保存图片
		$file = UploadedFileDao::saveFile('chat_audio', 'FT_CHAT_AUDIO');
		if (!$file['status']) {
			throw new ApiErrorException($file['data']);
		}
		$path=$url.$file['data']->file_path . '/' . $file['data']->file_name;
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
				'send_msg'=>$path,
				'from_user_number'=>$from_user_number,
				'to_user_number'=>$to_user_number,
				'to_client_id'=>$to_client_id,
				'send_type'=>2,
				'device_type'=>$device_type,
				'audio_duration'=>request('length'),
			]);
			$arr['type'] = 'sent_msg';
			$arr['send_type'] = '2';//1表示文本信息 2表示语音信息
			$arr['send_content'] = $path;
			$arr['audio_duration'] = request('length');
		}
		GatewayLib::sendToClient( $to_client_id,json_encode($arr));
		return response_json(1, $arr,'发送成功');
	}
	/**
	 * 获取聊天记录
	 *
	 * @author lwb 20180609
	 *
	 * @api {get} /gateway/chat_message 09.获取聊天记录
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，d：导览机
	 * @apiParam {string} from_user_number  我的id   之前列表返回了user_number
	 * @apiParam {string} to_user_number  对方的id   之前列表返回了user_number
	 * @apiParam {string} skip  页码 默认为0
	 * @apiParam {string} take  每页聊天记录数量 默认为10
	 * @apiSuccess {string} is_self  1为自己发送的 2为别人发给我的
	 * @apiSuccess {string} send_type  1为文本 2为语音
	 * @apiSuccess {string} send_msg  聊天内容
	 * @apiSuccess {string} audio_duration  语音长度
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function chat_list()
	{
		$this->validate([
			'from_user_number' => 'required',
			'to_user_number' => 'required'
		]);
		$skip=request('skip',0);
		$take=request('take',10);
		$from_user_number=request('from_user_number');
		$to_user_number=request('to_user_number');
		$list=ChatMessage::whereIn('from_user_number',[$from_user_number,$to_user_number])
			->whereIn('to_user_number',[$from_user_number,$to_user_number])
			->select('from_user_number','send_type','audio_duration','send_msg')
			->orderBy('created_at', 'desc')->skip($skip)->take($take)->get()->toArray();
		foreach ($list as &$v){
			$v['is_self']=$v['from_user_number']==$from_user_number ? '1':'2';//1为自己发送的 2为别人发给我的
			unset($v['from_user_number']);
		}
		return response_json(1, $list);
	}
}
