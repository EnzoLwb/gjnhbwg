<?php

namespace App\Http\Controllers\Api;

use App\Models\Group;
use App\Models\GroupMember;
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
	 * 创建群组
	 *
	 * @author lwb 20180607
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /chat/create_group 01.创建群组
	 * @apiGroup Chat
	 * @apiIgnore
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {int} uid
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
	 * @api {GET} /chat/join_group 02.加入群组
	 * @apiGroup Chat
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
