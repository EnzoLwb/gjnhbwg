<?php

namespace App\Channels;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;

/**
 * 湖南省博短信平台短信通知
 *
 * @author lxp 20170811
 * @package App\Channels
 */
class AlidySmsChannel
{
	/**
	 * 发送给定通知。
	 *
	 * @param  mixed $notifiable
	 * @param  \Illuminate\Notifications\Notification $notification
	 * @return void
	 */
	public function send($notifiable, Notification $notification)
	{
		// 取得短信内容
		$message = $notification->toSms($notifiable);
		// 取得手机号
		$mobile = $notifiable->mobileForHnsbSms();

		if (is_mobile($mobile)) {
			self::sendSms($message, $mobile);
		}
	}

	/**
	 * 发送短信
	 *
	 * @author lxp 20170811
	 * @param string $content 短信内容
	 * @param string $mobile 手机号
	 * @return boolean
	 */
	public static function sendSms($content, $mobile)
	{
		if (!is_mobile($mobile)) {
			return false;
		}

		$params = array ();

		$accessKeyId = env('SMS_ALIDY_KEYID', 'bzaxjlJryRFwpikN');
		$accessKeySecret = env('SMS_ALIDY_KEYSECRET', 'UdunXBcsmEvb3AnFbyRtnVy00DJjPz');
		$params["PhoneNumbers"] = $mobile;
		$params["SignName"] = env('SMS_ALIDY_SIGNNAME', '阿里云短信测试专用');

		$params["TemplateCode"] = "SMS_137790159";

		$params['TemplateParam'] = Array (
			"code" => $content,
		);

		if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
			$params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
		}

		// 初始化SignatureHelper实例用于设置参数，签名以及发送请求
		$helper = app('signaturehelper');

		// 此处可能会抛出异常，注意catch
		$result = $helper->request(
			$accessKeyId,
			$accessKeySecret,
			"dysmsapi.aliyuncs.com",
			array_merge($params, array(
				"RegionId" => "cn-hangzhou",
				"Action" => "SendSms",
				"Version" => "2017-05-25",
			))
		);

		return true;



	}

}