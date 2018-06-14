<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiErrorException;
use App\Models\Autonum;
use App\Models\Exhibit;
use App\Models\Exhibition;
use App\Models\SvgMapTable as Map;
use Illuminate\Support\Facades\Auth;
use App\Models\Deviceno;
use App\Models\Positions;
use App\Models\Trajectory;
use Illuminate\Support\Facades\DB;

/**
 * 设备号相关控制器
 *
 * @author yyj
 * @package App\Http\Controllers\Api
 */
class DevicenoController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 机器号请求接口
	 *
	 * @author yyj 20171110
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /request_deviceno 01.机器号请求接口(导览机忽略)
	 * @apiGroup Deviceno
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓（app存本地一直用到卸载）
	 * @apiSuccess {string} data 机器号
	 * @apiSuccessExample {json} 返回值
	 * {
	 * "status": 1,
	 * "data": "AND1000000002",
	 * "msg": ""
	 * }
	 */
	public function request_deviceno()
	{
		$this->validate([
			'p' => 'required|in:i,a',
		]);
		$app_kind = request('p');
		//判断注册设备类型
		switch ($app_kind) {
			case 'a':
				$deviceno = 'AND';
				break;
			case 'i':
				$deviceno = 'IOS';
				break;
			case 'w':
				$deviceno = 'WEB';
				break;
		}
		$info = Deviceno::create([
				'deviceno' => time(),
				'app_kind' => $app_kind
			]);
		$deviceno = $deviceno . ($info['id'] + 1000000000);
		Deviceno::where('id', $info['id'])->update(['deviceno' => $deviceno]);
		return response_json(1, $deviceno);
	}



	/**
	 * 定位上传接口
	 *
	 * @author yyj 20170809
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /positions 02.定位上传接口
	 * @apiGroup Deviceno
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓
	 * @apiParam {string} deviceno 机器号
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {int} auto_num 蓝牙编号
	 * @apiParam {string} [api_token] token(登录后上传)
	 * @apiSuccess {int} statues 状态码
	 * @apiSuccess {string} data 操作结果
	 * @apiSuccess {string} msg 文字信息
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":[]}
	 */
	public function positions()
	{
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
		}
		else{
			$uid=0;
		}
		$this->validate([
			'p' => 'required|in:i,a',
			'deviceno' => 'required',
			'auto_num' => 'required|integer'
		]);
		$deviceno = request('deviceno');
		$auto_num = request('auto_num');
		$app_kind = request('p');
		//判断点位是否存在
		$is_autonum=Autonum::where('autonum', $auto_num)->first();
		$is_set = Exhibit::where('auto_num', $auto_num)->first();
		if (empty($is_autonum)&&empty($is_set)) {
			throw new ApiErrorException('auto_num不存在');
		}
		if(!empty($is_autonum)){
			$is_set=$is_autonum;
		}
		//判断数据更新方法
		$is_set2 = Positions::where([
			[
				'deviceno',
				$deviceno
			],
			[
				'app_kind',
				$app_kind
			]
		])->first();
		$data['deviceno'] = $deviceno;
		$data['auto_num'] = $auto_num;
		$data['app_kind'] = $app_kind;
		$data['map_id'] = $is_set->map_id;
		$data['x'] = $is_set->x;
		$data['y'] = $is_set->y;
		$data['uid'] = $uid;
		if (empty($is_set2)) {
			Positions::create($data);
		} else {
			Positions::where('id', $is_set2->id)->update($data);
		}
		$add = false;
		/*if ($app_kind == 'd') {
			//判断是否租赁
			$deviceno_info = DB::table(DB::raw('table_rent'))->where('RENT_DEVICENO', $deviceno)->first();
			if (!empty($deviceno_info)) {
				$data['look_date'] = date('Y-m-d', time());
				$data['card_id'] = $deviceno_info->RENT_CARDNO;
				//隔一复收判断
				$last_positions = Trajectory::where([
					[
						'look_date',
						date('Y-m-d', time())
					],
					[
						'card_id',
						$deviceno_info->RENT_CARDNO
					]
				])->orderBy('id', 'desc')->first();
				if (!empty($last_positions)) {
					if ($last_positions->auto_num != $auto_num) {
						$add = true;
					}
				} else {
					$add = true;
				}
			}
		} else {
			//判断是否登录
			if ($uid) {
				$data['look_date'] = date('Y-m-d', time());
				$data['uid'] = $uid;
				//隔一复收判断
				$last_positions = Trajectory::where([
					[
						'look_date',
						date('Y-m-d', time())
					],
					[
						'uid',
						$uid
					]
				])->orderBy('id', 'desc')->first();
				if (!empty($last_positions)) {
					if ($last_positions->auto_num != $auto_num) {
						$add = true;
					}
				} else {
					$add = true;
				}
			}
		}*/

		$data['look_date'] = date('Y-m-d', time());
		$data['uid'] = $uid;
		//隔一复收判断
		$last_positions = Trajectory::where([
			[
				'look_date',
				date('Y-m-d', time())
			],
			[
				'deviceno',
				$deviceno
			],
		])->orderBy('updated_at', 'desc')->first();
		if (!empty($last_positions)) {
			if ($last_positions->auto_num != $auto_num) {
				$add = true;
			}
		} else {
			$add = true;
		}


		if ($add) {
			Trajectory::create($data);
		}else{
			Trajectory::where('id', $last_positions['id'])->update([
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}

		$language=request('language',1);
		return response_json(1, 1);
	}



}