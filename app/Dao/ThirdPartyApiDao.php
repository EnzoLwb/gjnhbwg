<?php

namespace App\Dao;

use App\Exceptions\ApiErrorException;

/**
 * 第三方接口调用模型
 *
 * @author yyj 20171201
 */
class ThirdPartyApiDao
{

	/**
	 * xml转数组
	 *
	 * @author yyj 20171201
	 * @param string $xml_info xml字符串
	 * @return array
	 * @throws ApiErrorException
	 */
	public static function xml_to_array($xml_info){
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$arr = json_decode(json_encode(simplexml_load_string($xml_info, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $arr;
	}

	/**
	 * 海岩短信平台发送短信
	 *
	 * 注意：必须找客服设置强制签名才能正常发送短信。
	 *
	 * @author yyj 20171201
	 * @param string $phone 手机号
	 * @param string $msg 发送内容
	 * @param int $countnumber 本次提交的号码总数量
	 * @param int $mobilenumber 本次提交的手机号码数量
	 * @param string $sendTime 定时发送时间 为空表示立即发送，定时发送格式2010-10-24 09:08:10
	 * @param string $taskName 本次任务 描述100个字以内 可以为空
	 * @return array ['returnstatus'=>'返回状态值：成功返回Success 失败返回：Faild','message'=>'返回信息','remainpoint'=>'返回余额','taskID'=>'返回本次任务的序列ID','successCounts'=>'成功短信数：当成功后返回提交成功短信数',]
	 * @throws ApiErrorException
	 */
	public static function haiyan_duanxin($phone, $msg,$countnumber=1,$mobilenumber=1,$sendTime='',$taskName='')
	{
		$post_data=[
			'userid' => '',//企业ID
			'account' => '',//用户帐号，由系统管理员
			'password' => '',//用户账号对应的密码
			'mobile' => $phone,//发信发送的目的号码.多个号码之间用半角逗号隔开
			'content' => $msg,//短信的内容
			'sendTime' => $sendTime,//定时发送时间 为空表示立即发送，定时发送格式2010-10-24 09:08:10
			'action'=>'send',//发送任务命令	设置为固定的:send
			'checkcontent'=>1,//是否检查内容包含非法关键字 当设置为1时表示需要检查，默认0为不检查
			'taskName'=>$taskName,//本次任务 描述100个字以内 可以为空
			'countnumber'=>$countnumber,//本次提交的号码总数量
			'mobilenumber'=>$mobilenumber,//本次提交的手机号码数量
		];
		$url = 'http://www.duanxin10086.com/sms.aspx';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (is_array($post_data)) {
			$sets = array();
			foreach ($post_data AS $key => $val) {
				$sets[] = $key . '=' . urlencode($val);
			}
			$fields = implode('&', $sets);
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		/*curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($fields))
		);*/
		$output = curl_exec($ch);
		curl_close($ch);
		LogDao::save_log('haiyan_duanxin',['output'=>$output,'post_data'=>$post_data]);
		$reslut=self::xml_to_array($output);
		return $reslut;
	}

}
