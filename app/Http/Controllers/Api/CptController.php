<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiErrorException;
use App\Models\SmsVerify;
use App\Models\Users;
use App\Utilities\Captcha\Securimage;
use Illuminate\Support\Facades\Cache;
use Mail;

/**
 * 验证码控制器
 *
 * @package App\Http\Controllers
 */
class CptController extends Controller
{
	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 发送短信验证码
	 *
	 * @author lxp 20170811
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /send_vcode 3. 发送短信(邮箱)验证码
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 请求平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} phoneOremail 手机号/邮箱
	 * @apiParam {int} type 用户  1注册  2找回密码
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":{},"msg":""}
	 */
	public function send_vcode()
	{
		$phoneOremail = trim(request('phoneOremail'));
		$type = trim(request('type'));
		if (empty($phoneOremail)) {
			throw new ApiErrorException('phoneOremail不能为空');
		}

		$preg_email = '/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
		$preg_phone = '/^1[34578]\d{9}$/ims';

		if (preg_match($preg_phone, $phoneOremail)) {
			$vtype = 1;
		} elseif (preg_match($preg_email, $phoneOremail)) {
			$vtype = 2;
		} else {
			throw new ApiErrorException('请填写正确的手机号或者邮箱');
		}


		$userinfo = Users::where('username',$phoneOremail)->first();
		if ($type == 1) {
			if($userinfo){
				$error_msg = $vtype==1?'该手机号已注册':'该邮箱已注册';
				return response_json(-1, -1, $error_msg);
			}

		}elseif ($type == 2) {

			if(empty($userinfo)){
				$error_msg = $vtype==1?'该手机号未注册':'该邮箱未注册';
				return response_json(-1, -1, $error_msg);
			}

		}else{
			throw new ApiErrorException('type error');
		}



		if ($vtype == 1) {
			//短信验证码
			$phone = $phoneOremail;


			// 可添加针对手机号或IP的验证
			$lastVerify = SmsVerify::where('mobile', $phone)->where('status', 1)->orderBy('created_at', 'DESC')->first();
			if ($lastVerify && (date('U') - strtotime($lastVerify->created_at)) < 60) {
				throw new ApiErrorException('操作过于频繁，请稍候再试');
			}

			// 将该手机号之前的验证码都置为失效
			SmsVerify::where('mobile', $phone)->where('status', 1)->update(['status' => 3]);

			// 生成手机验证码并保存
			$smsObj = new SmsVerify();
			$smsObj->mobile = $phone;
			$smsObj->smscode = rand(100000, 999999);
			$smsObj->ip = request()->ip();
			$smsObj->plat = request('p');
			$smsObj->save();
			// 发送短信
			$smsObj->sendSmsNotification();

			return response_json(1, 1, '发送成功');



		} elseif ($vtype == 2) {
			//邮箱验证码
			$email = $phoneOremail;

			// 邮箱频繁验证
			$lastVerify = SmsVerify::where('email', $email)->where('status', 1)->orderBy('created_at', 'DESC')->first();
			if ($lastVerify && (date('U') - strtotime($lastVerify->created_at)) < 60) {
				throw new ApiErrorException('操作过于频繁，请稍候再试');
			}

			// 将该邮箱之前的验证码都置为失效
			SmsVerify::where('email', $email)->where('status', 1)->update(['status' => 3]);

			// 生成手机验证码并保存
			$smsObj = new SmsVerify();
			$smsObj->email = $email;
			$smsObj->smscode = rand(100000, 999999);
			$smsObj->ip = request()->ip();
			$smsObj->plat = request('p');
			$smsObj->save();

			// 发送邮件
			$wenan = $smsObj->smscode . '为您的验证码，请于15分钟内填写。如非本人操作，请忽略。';
			$flag = Mail::raw($wenan, function ($message) {
				$message->to(trim(request('phoneOremail')))->subject('您的验证码');
			});

			return response_json(1, 1, '发送成功');
			//return response_json(1, $flag, '发送成功');

		} else {
			throw new ApiErrorException('vtype error');
		}

		/*$this->validate([
			//			'device_uuid' => 'required',
			'phone' => 'required|mobile',
			//			'code' => 'required'
		]);

		// 图片验证码验证
		//		$captcha = Cache::get(request('device_uuid'));
		//		if ($captcha == request('code')) {
		//			Cache::forget(request('device_uuid'));
		//		} else {
		//			throw new ApiErrorException('验证码错误');
		//		}

		// 可添加针对手机号或IP的验证
		$lastVerify = SmsVerify::where('mobile', request('phone'))->where('status', 1)->orderBy('created_at', 'DESC')->first();
		if ($lastVerify && (date('U') - strtotime($lastVerify->created_at)) < 60) {
			throw new ApiErrorException('操作过于频繁，请稍候再试');
		}

		// 将该手机号之前的验证码都置为失效
		SmsVerify::where('mobile', request('phone'))->where('status', 1)->update(['status' => 3]);

		// 生成手机验证码并保存
		$smsObj = new SmsVerify();
		$smsObj->mobile = request('phone');
		$smsObj->smscode = rand(100000, 999999);
		$smsObj->ip = request()->ip();
		$smsObj->plat = request('p');
		$smsObj->save();
		// 发送短信
		$smsObj->sendSmsNotification();

		return response_json();*/
	}

	/**
	 * 显示验证码，暂时不用
	 *
	 * @author lxp 20170811
	 * @return \Illuminate\Http\JsonResponse|void
	 */
	public function show()
	{
		$this->validate(['device_uuid' => 'required']);

		$securimage = new Securimage(config('captcha'));
		$securimage->perturbation = 0.1;
		if (intval(request('width'))) {
			$securimage->image_width = intval(request('width'));
		}
		if (intval(request('height'))) {
			$securimage->image_height = intval(request('height'));
		}
		// 阻止立刻输出图片
		$securimage->output_now = false;
		$securimage->show();

		// 绑定设备唯一id和验证码，缓存15分钟
		Cache::put(request('device_uuid'), $securimage->code, 15);

		// 输出验证码图片
		return $securimage->output();
	}

	/**
	 * 图片验证码验证，暂时不用
	 *
	 * @author lxp 20180111
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 */
	public function check()
	{
		$captcha = Cache::get(request('device_uuid'));
		if ($captcha == request('code')) {
			return response_json(1);
		} else {
			throw new ApiErrorException('验证码错误');
		}
	}
}
