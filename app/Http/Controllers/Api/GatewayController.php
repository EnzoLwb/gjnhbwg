<?php

namespace App\Http\Controllers\Api;

use App\Models\Group;
use App\Models\GroupMember;
use GatewayWorker\Lib\Gateway AS GatewayLib;
use Illuminate\Support\Facades\Auth;

class GatewayController extends Controller
{
	public function __construct()
	{
		$registerIP = env('WM_REGISTER_IP', '127.0.0.1');
		$registerPort = env('WM_REGISTER_PORT', '1238');
		GatewayLib::$registerAddress = "$registerIP:$registerPort";
	}
	/**
	 * 创建群组
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /create_group 01.创建群组
	 * @apiGroup Chat
	 * @apiIgnore
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} api_token 签名
	 * @apiParam {int} group_name 群组名称
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function create_group(){
		$uid=Auth::id();
		$group=Group::create([
			'holder' => $uid,
			'name' => request('group_name'),
			'create_time' => time(),
			'group_id' => rand(10000,99999),
		]);
		$group_id=$group->id;
		//将群主加入该群 并且绑定client_id
		$group_member=new GroupMember();
		$group_member->member_id=$uid;
		$group_member->add_time=time();
		$group_member->group_id=$group_id;
		$group_member->save();
		$client_id = GatewayLib::getClientIdByUid($uid);
		//对他说
		/*$new_message=['content'=>"你已成功创建并加入群组"];
		GatewayLib::sendToClient($client_id, json_encode($new_message));
		GatewayLib::sendToUid($uid, json_encode($new_message));*/
		GatewayLib::joinGroup($client_id, $group_id);
		return response_json(1,1,'你已成功创建并加入群组');
	}
}
