<?php

namespace App\Http\Controllers\Api;

use App\Dao\UploadedFileDao;
use App\Exceptions\ApiErrorException;
use App\Models\ChatMessage;
use App\Models\Dlj;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Trajectory;
use App\Models\Users;
use GatewayWorker\Lib\Gateway AS GatewayLib;
use Illuminate\Support\Facades\Auth;

class GatewayController extends Controller
{

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
	 * @api {GET} tcp://192.168.10.158:9501 00.监听地址
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiSuccess {string} type 信息类型
	 * (1.bind:机器号绑定;2.heart:心跳响应;3.chat:信息推送),
	 * type等于bind 机器号/uid绑定,执行uid/设备绑定接口,
	 * type等于heart 心跳响应,使用tcp链接向服务器发送string数据'heart_response';
	 * type等于send_msg 并且send_type等于1表示普通发送消息 2 表示语音消息 'error_msg' 表示 断开连接或者其它问题 3代表有人加群或者退出群组
	 * @apiSuccess {string} client_id 连接上tcp后获得的client_id
	 * @apiSuccess {string} send_type 信息类型,标题 error_msg 表示错误
	 * @apiSuccess {string} send_content 信息内容  如果是语音  会用#拼接上语音时长  例如 http://192.168.10.158:8309/uploadfiles/mp3/20180613/201806131033452100.mp3#00:44
	 */

	/**
	 * uid/设备绑定接口
	 *
	 * @author lwb 20180608
	 *
	 * @api {Get} /gateway/bind 01.uid或设备号绑定接口(搭建tcp连接后，需先请求此接口进行绑定)
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
			//加入之前加入的群组
			$group_id=GroupMember::where('member_id',request('user_number'))->value('group_id');
			if ($group_id) GatewayLib::joinGroup($client_id, $group_id);
		} else {
			return response_json(0, '', 'client_id无效');
		}
		return response_json(1, '', '绑定成功');
	}

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
		return response_json(1, '', '已经断开连接');

	}
	/**
	 * 是否之前加入群组
	 *
	 * @author lwb 201806011
	 *
	 * @api {GET} /gateway/getGroupList 03.是否之前加入群组
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {int} user_number  app传uid   导览机传唯一设备号
	 * @apiSuccess {object} data
	 * @apiSuccess {int} group_number 群组对外的ID号 空字符串表示当前未加入到群组中
	 */
	public function getGroupList(){
		$this->validate([
			'user_number' => 'required',
		]);
		$group_id=GroupMember::where('member_id',request('user_number'))->value('group_id');

		$group_number=Group::where('id',$group_id)->value('group_number');
		return response_json(1, ['group_number'=>$group_number]);
	}
	/**
	 * 创建群组
	 *
	 * @author lwb 20180607
	 *
	 * @api {GET} /gateway/create_group 04.创建群组
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {int} user_number  app传uid   导览机传唯一设备号
	 * @apiParam {string} group_name 群组名称
	 * @apiSuccess {object} data 操作结果1成功0失败
	 * @apiSuccess {object} data.my_info 个人信息(导览机就返回空对象)(手机端返回 头像:avatar 昵称:nickname)
	 * @apiSuccess {object} data.group_info  group_id 群组ID号  group_number 群组对外ID号  name 群组名称
	 */
	public function create_group(){
		$this->validate([
			'user_number' => 'required',
			'group_name' => 'required'
		]);
		$uid=request('user_number');
		$plat=request('p')!='d' ? '1':'2';
		//判断是否有重名的
		$if_repeat=Group::where('group_name',request('group_name'))->first();
		if (!is_null($if_repeat)) return response_json(0,[],'已经存在的群组名');
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
			'group_number' => rand(1000,9999).substr(time(),8),
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
		$group_info['group_id']=$group->id;
		$group_info['group_number']=$group->group_number;
		$group_info['name']=request('group_name');
		$data['group_info']=$group_info;
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
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} user_number app传uid   导览机传唯一设备号
	 * @apiParam {int} group_number 对外显示的群组id号
	 * @apiSuccess {object} data 操作结果1成功0失败
	 * @apiSuccess {object} data.group_info  群组信息  包括group_id:对内的群组id  group_number：对外的群组id  name:群组名称
	 * @apiSuccess {object} data.my_info    我的信息 如果是手机则返回avatar:头像  nickname：昵称  如果是导览机的用户返回空 使用自己的设备号和默认头像即可
	 * @apiSuccess {array} data.user_list    返回avatar:头像(导览机用户为空)  nickname：昵称(导览机为设备号) user_number：uid (导览机为设备号)
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
		//通知群组里的人
		$arr['type'] = 'sent_msg';
		$arr['send_type'] = '3';//1表示文本信息 2表示语音信息 3代表有人退群或者加群
		$arr['send_content'] = '加入群组';
		GatewayLib::sendToGroup($group_info['id'],json_encode($arr));
		return response_json(1, $data, '加入成功');
	}
	/**
	 * 私聊发送消息
	 *
	 * @author lwb 20180608
	 *
	 * @api {Post} /gateway/send_msg 06.私聊发送消息
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
		/*if (empty($to_client_id)) {
			//断开连接或者uid输入错误
			//					$arr['client_id'] = $client_id;
			$arr['type'] = 'sent_msg';
			$arr['send_type'] = 'error_msg';
			$arr['send_content'] = ['error_msg' => '断开连接或者to_uid输入错误'];
		}*/
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
		if (!empty($to_client_id)){
			GatewayLib::sendToClient( $to_client_id,json_encode($arr));
		}
		return response_json(1,'','发送成功');
	}
	//获得所有uid  测试用
	public function getAllUid(){
		$list=GatewayLib::getAllUidList();
		dd($list);
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
		//通知群组里的人
		$arr2['type'] = 'sent_msg';
		$arr2['send_type'] = '3';//1表示文本信息 2表示语音信息 3代表有人退群或者加群
		$arr2['send_content'] = '退出群组';
		GatewayLib::sendToGroup($group_id,json_encode($arr2));
		return response_json(1, '', '退出成功');
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
	 * @apiParam {file} chat_audio 语音文件 最大不超过20m MP3格式
	 * @apiSuccess {object} data 操作结果1成功0失败
	 * @apiSuccess {string} data.send_content 语音文件地址
	 * @apiSuccess {string} data.audio_duration 语音文件时长 例如 "00:44"
	 */
	public function upload_audio()
	{
		$this->validate([
			'from_user_number' => 'required',
			'to_user_number' => 'required',
			'chat_audio' => 'required|file',
		]);
		////获取语音文件长度
		$vtime = @exec("ffmpeg -i " . request('chat_audio') . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
		//$ctime = date("Y-m-d H:i:s", filectime($file));//创建时间
		$vtime = date('i:s', strtotime($vtime));
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
				'audio_duration'=>$vtime,
			]);
			$arr['type'] = 'sent_msg';
			$arr['send_type'] = '2';//1表示文本信息 2表示语音信息
			$arr['send_content'] = $path.'#'.$vtime;
//			$arr['audio_duration'] = $vtime;
		}
		GatewayLib::sendToClient( $to_client_id,json_encode($arr));
		$arr['send_content'] = $path;
		$arr['audio_duration'] = $vtime;
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
	 * @apiParam {int} skip  页码 从1开始 默认为第一页
	 * @apiParam {int} take  每页聊天记录数量 默认为10  传0代表返回全部聊天记录
	 * @apiSuccess {object} data 操作结果1成功0失败
	 * @apiSuccess {array} chat_record 操作结果1成功0失败
	 * @apiSuccess {object} avatar 双方头像
	 * @apiSuccess {string} chat_record.is_self  1为自己发送的 2为别人发给我的
	 * @apiSuccess {int} chat_record.send_type  1为文本 2为语音
	 * @apiSuccess {string} chat_record.send_msg  聊天内容
	 * @apiSuccess {int} chat_record.audio_duration  语音长度
	 * @apiSuccess {string} avatar.from_avatar  我的头像
	 * @apiSuccess {string} avatar.to_avatar  对方的头像
	 */
	public function chat_message()
	{
		$this->validate([
			'from_user_number' => 'required',
			'to_user_number' => 'required'
		]);
		$take=request('take',10);
		$skip=(request('skip',1)-1) * $take;

		$from_user_number=request('from_user_number');
		$to_user_number=request('to_user_number');
		$query=ChatMessage::whereIn('from_user_number',[$from_user_number,$to_user_number])
			->whereIn('to_user_number',[$from_user_number,$to_user_number])
			->select('from_user_number','send_type','audio_duration','send_msg')
			->orderBy('created_at', 'desc');
		if ($take!=0){
			$query=$query->skip($skip)->take($take);
		}
		$list=$query->get()->toArray();
		foreach ($list as &$v){
			$v['is_self']=$v['from_user_number']==$from_user_number ? '1':'2';//1为自己发送的 2为别人发给我的
			unset($v['from_user_number']);
		}
		//双方头像
		$avatar['to_user_number']=Users::where('uid',$to_user_number)->value('avatar');
		$avatar['from_user_number']=Users::where('uid',$from_user_number)->value('avatar');

		$data['chat_record']=$list;
		$data['avatar']=$avatar;
		return response_json(1, $data);
	}
	/**
	 * 获取群组用户列表
	 *
	 * @author lwb 20180611
	 *
	 * @api {get} /gateway/users_list 10.获取群组用户列表
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，d：导览机
	 * @apiParam {string} user_number app传uid   导览机传唯一设备号
	 * @apiParam {string} group_number 对外的群组ID
	 * @apiSuccess {object} data 操作结果1成功0失败
	 * @apiSuccess {object} data.group_info  群组信息  包括group_id:对内的群组id  group_number：对外的群组id  name:群组名称
	 * @apiSuccess {object} data.my_info    我的信息 如果是手机则返回avatar:头像  nickname：昵称  如果是导览机的用户返回空 使用自己的设备号和默认头像即可
	 * @apiSuccess {array} data.user_list    返回avatar:头像(导览机用户为空)  nickname：昵称(导览机为设备号) user_number：uid (导览机为设备号)
	 */
	public function users_list()
	{
		$this->validate([
			'user_number' => 'required',
			'group_number' => 'required'
		]);
		$user_number=request('user_number');
		$group_number=request('group_number');
		$plat=request('p')!='d'?'1':'2';
		$group_info=Group::where('group_number',$group_number)->first();
		if (is_null($group_info)){
			return response_json(0,[],'不存在的群组号');
		}
		$group=GatewayLib::getAllGroupIdList($group_info['id']);
		if (!in_array($group_info['id'],$group)) return response_json(0,[],'不存在的群组ID');
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

		$group_id=Group::where('group_number',$group_number)->value('id');
		$where=array('group_id'=>$group_id);
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
		return response_json(1, $data);
	}
	/**
	 * 获取位置坐标
	 *
	 * @author lwb 20180613
	 *
	 * @api {get} /gateway/get_gps 11.获取当前位置
	 * @apiGroup GateWay
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，d：导览机(一定要正确传 不然数据会有错误)
	 * @apiParam {string} user_number app传uid   导览机传唯一设备号
	 * @apiSuccess {object} data 操作结果1成功0失败
	 * @apiSuccess {int} data.x  x坐标
	 * @apiSuccess {int} data.y  y坐标
	 * @apiSuccess {int} data.map_id 地图编号
	 */
	public function get_gps()
	{
		$this->validate([
			'user_number' => 'required',
		]);
		$user_number=request('user_number');
		$type=request('p')!='d' ? 'uid':'deviceno';
		$res=Trajectory::where($type,$user_number)->orderby('updated_at','desc')->select('x','y','map_id')->first();
		return response_json(1,$res);
	}
}
