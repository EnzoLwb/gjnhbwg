<?php

namespace App\Http\Controllers\Api;

class ReserveController extends Controller
{
	public function __construct()
	{
		parent::_init();
	}
	/**
	 * 我的预约
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /my_reserve_list 01.我的预约
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} api_token 用户token
	 * @apiSuccess {array} data 数据详情
	 * @apiSuccess {int} reserve_id 预约id
	 * @apiSuccess {string} yzm 验证码(审核中和审核失败没有验证码,其它有验证码)都返回但是没有验证码的时候返回'' 空字符
	 * @apiSuccess {string} reserve_date  预约的时间 例如:2017.09.01 08:00-10:00
	 * @apiSuccess {string} language 讲解语种
	 * @apiSuccess {string} reserve_status 审核中:0   预约失败:2   审核成功(预约成功):1    已取消:3     进行中:4    已完成:5
	 */
	public function my_reserve_list()
	{

	}
	/**
	 * 预约信息详情
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /reserve_detail 02.预约信息详情
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} api_token 用户token
	 * @apiParam {string} reserve_id 预约id
	 * @apiSuccess {object} data 数据详情
	 * @apiSuccess {int} type 预约类型(1表示个人2表示团体 两个的对象返回字段不同)
	 * @apiSuccess {string} yzm 验证码(审核中和审核失败没有验证码,其它有验证码)(两种都有)都返回但是没有验证码的时候返回'' 空字符
	 * @apiSuccess {string} guide 讲解员(只有讲解评价情况下才显示)(两种都有)
	 * @apiSuccess {string} reserve_date  预约日期(两种都有) 例如:2017-09-01
	 * @apiSuccess {string} reserve_time  预约时间(两种都有) 例如:08:00-10:00
	 * @apiSuccess {string} language 讲解语种(两种都有)
	 * @apiSuccess {string} phone 联系电话(两种都有)
	 * @apiSuccess {string} contacts 联系人(两种都有)
	 * @apiSuccess {string} certificate_type 证件类型(个人预约)
	 * @apiSuccess {string} certificate_number 证件号码(个人预约)
	 * @apiSuccess {string} reserve_unit 预约单位(团队预约)
	 * @apiParam {string} manning 人员组成(团队预约)
	 * @apiParam {string} reserve_cou 预约人数(团队预约)
	 * @apiSuccess {string} button_text  取消预约   预约失败   已取消   讲解中   等待讲解  讲解评价 查看评价
	 */
	public function reserve_detail()
	{

	}
	/**
	 * 个人讲解预约提交
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /personal_reserve 03.个人讲解预约提交
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} contacts 联系人姓名
	 * @apiParam {int} certificate_type 证件类型   certificate_type_list 接口返回的id
	 * @apiParam {string} certificate_number 证件号码
	 * @apiParam {string} phone 联系电话
	 * @apiParam {int} language 语种1为中文 0为英语
	 * @apiParam {string} visit_date 参观日期 格式为:Y-m-d 例如:2017-08-01
	 * @apiParam {int} visit_time 参观时间 visit_time_list 接口返回的id
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function personal_reserve()
	{

	}
	/**
	 * 团队讲解预约提交
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /team_reserve 04.团队讲解预约提交
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} contacts 联系人姓名
	 * @apiParam {string} phone 联系电话
	 * @apiParam {string} audit_opinion 审核意见
	 * @apiParam {string} reserve_unit 预约单位
	 * @apiParam {int} manning 人员组成  manning_list 接口返回的id
	 * @apiParam {string} reserve_cou 预约人数
	 * @apiParam {string} language 语种1为中文 0为英语
	 * @apiParam {string} visit_date 参观日期 格式为:Y-m-d 例如:2017-08-01
	 * @apiParam {int} visit_time 参观时间 visit_time_list 接口返回的id
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function team_reserve()
	{

	}
	/**
	 * 取消预约
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /cancel_reserve 05.取消预约
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} api_token 用户token
	 * @apiParam {string} reserve_id 预约id
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function cancel_reserve()
	{

	}
	/**
	 * 讲解评价提交
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /explain_evaluate 06.讲解评价提交
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} api_token 用户token
	 * @apiParam {string} reserve_id 预约id
	 * @apiParam {string} content 评价内容
	 * @apiParam {int} star 星数
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function explain_evaluate()
	{

	}
	/**
	 * 证件类型下拉框
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /certificate_type_list 07.证件类型下拉框
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiSuccess {array} data
	 * @apiSuccess {int} type_id  类型id
	 * @apiSuccess {string} type  类型文字
	 */
	public function certificate_type_list()
	{

	}
	/**
	 * 参观时间下拉框
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /visit_time_list 08.参观时间下拉框
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiSuccess {array} data
	 * @apiSuccess {int} time_id  时间段id
	 * @apiSuccess {string} visit_time  时间段
	 */
	public function visit_time_list()
	{

	}
	/**
	 * 人员构成下拉框
	 *
	 * @author lwb 20180605
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /manning_list 09.人员构成下拉框
	 * @apiGroup Reserve
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiSuccess {array} data
	 * @apiSuccess {int} manning_id  id
	 * @apiSuccess {string} manning  内容
	 */
	public function manning_list()
	{

	}
}
