<?php

namespace App\Http\Controllers\Api;

use App\Dao\SmsVerifyDao;
use App\Dao\UploadedFileDao;
use App\Dao\UsersDao;
use App\Exceptions\ApiErrorException;
use App\Models\Users;
use App\Models\UsersBind;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersController extends Controller
{
	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 用户登录
	 *
	 * @author lxp 20170113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /users/login 1. 用户登录
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} username 用户名（手机号/邮箱）
	 * @apiParam {string} password 密码
	 * @apiParam {string} [deviceno] 设备号
	 * @apiSuccess {int} uid 用户ID
	 * @apiSuccess {string} api_token 用户签名
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":{"uid":2,"api_token":"a40c76e4bc07a77f7f322530987d818e"},"msg":""}
	 */
	public function login()
	{
		$this->validate([
			'username' => 'required',
			'password' => 'required'
		]);

		// 取出用户并验证密码
		$user = Users::where('username', request('username'))->first();
		if (is_null($user)) {
			throw new ApiErrorException('用户不存在');
		}
		if (get_password(request('password'), $user->salt) != $user->password) {
			throw new ApiErrorException('用户名或密码错误');
		}

		// 登录成功，生成api token
		$user->api_token = get_api_token($user->uid);
		if (request('deviceno')) {
			$user->deviceno = request('deviceno');
		}
		$user->save();

		return response_json(1, [
			'uid' => $user->uid,
			'api_token' => $user->api_token
		]);
	}

	/**
	 * 用户详情
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /users/info 5. 用户详情
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiSuccess {object} data 用户数据
	 * @apiSuccess {int} data.uid 用户ID
	 * @apiSuccess {int} data.user_type_id 会员类型id
	 * @apiSuccess {string} data.phone 手机号
	 * @apiSuccess {string} data.email 邮箱
	 * @apiSuccess {string} data.nickname 昵称
	 * @apiSuccess {string} data.avatar 头像
	 * @apiSuccess {string} data.sex 性别  1男  2女
	 * @apiSuccess {string} data.province 省份
	 * @apiSuccess {string} data.birthday 出生年月
	 * @apiSuccess {int} data.r_type 1手机 2邮箱  3第三方
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":{"uid":1,"phone":13812341234,"nickname":"U13812341234","avatar":"\/uploadfiles\/avatar\/20170905\/201709051344549521.jpg"},"msg":""}
	 */
	public function info()
	{
		$uid = Auth::user()->uid;
		$uinfo = Users::where('uid', $uid)->first();
		$uinfo_bind = UsersBind::where('uid', $uid)->first();

		if ($uinfo_bind) {
			$r_type = 3;
		} else {

			if (strpos($uinfo['username'], '@') !== false) {
				$r_type = 2;
			} else {
				$r_type = 1;
			}
		}

		return response_json(1, [
			'uid' => $uinfo->uid,
			'phone' => $uinfo->phone,
			'email' => $uinfo->email,
			'nickname' => $uinfo->nickname,
			'avatar' => $uinfo->avatar,
			'sex' => $uinfo->sex,
			'province' => $uinfo->province,
			'birthday' => $uinfo->birthday,
			'r_type' => $r_type
		]);
	}

	/**
	 * 修改头像
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /users/avatar 修改头像
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {file} avatar 头像
	 * @apiSuccess {string} data 新头像地址
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":"\u65b0\u6635\u79f0","msg":""}
	 */
	public function avatar()
	{
		$this->validate([
			'avatar' => 'required|file'
		]);
		$uid = Auth::user()->uid;

		// 保存图片
		$file = UploadedFileDao::saveFile('avatar', 'FT_AVATAR', $uid);
		if (!$file['status']) {
			throw new ApiErrorException($file['data']);
		}

		$users = Users::findOrFail($uid);
		$users->timestamps = false;
		$users->avatar = $file['data']->file_path . '/' . $file['data']->file_name;
		$users->save();

		return response_json(1, $users->avatar);
	}

	/**
	 * 修改昵称
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /users/nickname 修改昵称
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {string} nickname 昵称
	 * @apiSuccess {string} data 新昵称
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":"\u65b0\u6635\u79f0","msg":""}
	 */
	public function nickname()
	{
		$uid = Auth::user()->uid;
		$this->validate([
			'nickname' => 'required|unique:users,nickname,' . $uid . ',uid'
		]);
		$users = Users::findOrFail($uid);
		$users->timestamps = false;
		$users->nickname = request('nickname');
		$users->save();

		return response_json(1, $users->nickname);
	}

	/**
	 * 修改性别
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /users/sex 修改性别
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {int} sex 性别  1男 2女
	 * @apiSuccess {string} data 新性别
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":"1","msg":""}
	 */
	public function sex()
	{
		$uid = Auth::user()->uid;
		$this->validate([
			'sex' => 'required|in:1,2'
		]);
		$users = Users::findOrFail($uid);
		$users->timestamps = false;
		$users->sex = request('sex');
		$users->save();

		return response_json(1, $users->sex);
	}

	/**
	 * 修改省份
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /users/province 修改省份
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {string} province 性别  1男 2女
	 * @apiSuccess {string} data 新省份
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":"1","msg":""}
	 */
	public function province()
	{
		$uid = Auth::user()->uid;
		$this->validate([
			'province' => 'required'
		]);
		$users = Users::findOrFail($uid);
		$users->timestamps = false;
		$users->province = request('province');
		$users->save();

		return response_json(1, $users->province);
	}

	/**
	 * 修改出生年月
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /users/birthday 修改出生年月
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {string} birthday 出生年月   1990-01-01
	 * @apiSuccess {string} data 新省份
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":"1","msg":""}
	 */
	public function birthday()
	{
		$uid = Auth::user()->uid;
		$this->validate([
			'birthday' => 'required'
		]);

		$today = date('Y-m-d');
		if(strtotime(request('birthday'))>strtotime($today)){
			return response_json(-1, [],'选择的出生日期无效');
		}

		$users = Users::findOrFail($uid);
		$users->timestamps = false;
		$users->birthday = request('birthday');
		$users->save();

		return response_json(1, $users->birthday);
	}

	/**
	 * 修改用户联系手机
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /users/phone 修改用户联系手机
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {string} phone 手机号
	 * @apiSuccess {string} data 新省份
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":"1","msg":""}
	 */
	public function phone()
	{
		$uid = Auth::user()->uid;
		$this->validate([
			'phone' => 'required|mobile'
		]);
		$users = Users::findOrFail($uid);
		$users->timestamps = false;
		$users->phone = request('phone');
		$users->save();

		return response_json(1, $users->phone);
	}

	/**
	 * 修改用户联系邮箱
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /users/email 修改用户联系邮箱
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {string} email 邮箱
	 * @apiSuccess {string} data 新省份
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":"1","msg":""}
	 */
	public function email()
	{
		$uid = Auth::user()->uid;
		$this->validate([
			'email' => 'required|email'
		]);
		$users = Users::findOrFail($uid);
		$users->timestamps = false;
		$users->email = request('email');
		$users->save();

		return response_json(1, $users->email);
	}

	/**
	 * 用户注册
	 *
	 * @author lxp 20170810
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /users/register 2. 用户注册
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} username 用户名（手机号/邮箱）
	 * @apiParam {string} smscode验证码
	 * @apiParam {string} password 密码
	 * @apiSuccess {int} uid 用户ID
	 * @apiSuccess {string} phone 手机号
	 * @apiSuccess {string} api_token 用户签名
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":{"uid":2,"username":"13112341234","phone":"13112341234","api_token":"a40c76e4bc07a77f7f322530987d818e"},"msg":""}
	 */
	public function register()
	{

		$phoneOremail = trim(request('username'));
		if (empty($phoneOremail)) {
			throw new ApiErrorException('username不能为空');
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

		if ($vtype == 1) {
			$this->validate([
				'username' => 'required|mobile|unique:users',
				'smscode' => 'required',
				'password' => 'required|min:8',
			]);

		} elseif ($vtype == 2) {
			$this->validate([
				'username' => 'required|email|unique:users',
				'smscode' => 'required',
				'password' => 'required|min:8',
			]);
		}

		SmsVerifyDao::code_check(request('username'), request('smscode'), $vtype);

		$user = DB::transaction(function () {
			// 生成密码盐
			$salt = Str::random(6);

			$user = new Users();
			$user->username = request('username');
			$user->password = get_password(request('password'), $salt);
			$user->nickname = UsersDao::get_nickname();
			$user->salt = $salt;
			$user->lastloginip = app('request')->ip();
			$user->plat = request('p');
			$user->save();
			if (!$user->uid) {
				throw new ApiErrorException('注册失败，请稍后重试');
			}
			// 生成API验证token
			$user->api_token = get_api_token($user->uid);
			$user->save();

			return $user;
		});

		if ($vtype == 1) {
			$user->phone = request('username');
			$user->save();
		} elseif ($vtype == 2) {
			$user->email = request('username');
			$user->save();
		}

		return response_json(1, [
			'uid' => $user->uid,
			'username' => $user->username,
			'api_token' => $user->api_token
		]);
	}

	/**
	 * 第三方账号登录
	 *
	 * @author lxp 20170915
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /users/register_bind 7. 第三方账号登录
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} openid 第三方id
	 * @apiParam {string="wx","wb","qq"} b_from 来源
	 * @apiParam {string} b_nickname 第三方昵称
	 * @apiParam {string} [b_avatar] 第三方头像url
	 * @apiSuccess {int} uid 用户ID
	 * @apiSuccess {string} phone 手机号
	 * @apiSuccess {string} api_token 用户签名
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":{"uid":2,"username":"13112341234","phone":"13112341234","api_token":"a40c76e4bc07a77f7f322530987d818e"},"msg":""}
	 */
	public function register_bind()
	{
		$this->validate([
			'openid' => 'required',
			'b_from' => 'required|in:wx,qq,wb',
			'b_nickname' => 'required',
		]);

		// 取出用户绑定数据
		$userbind = UsersBind::where('openid', request('openid'))->where('b_from', request('b_from'))->first();

		if (is_null($userbind)) {
			$user = DB::transaction(function () {
				// 处理昵称
				$nickname = trim(strip_tags(request('b_nickname')));
				if (Users::where('nickname', $nickname)->count() > 0) {
					$nickname = UsersDao::get_nickname($nickname);
				}
				// 生成密码盐
				$salt = Str::random(6);

				// 添加用户
				$user = new Users();
				$user->username = $nickname;
				$user->password = get_password(Str::random(6), $salt);
				$user->nickname = $nickname;
				$user->salt = $salt;
				$user->lastloginip = app('request')->ip();
				$user->plat = request('p');
				$user->save();
				if (!$user->uid) {
					throw new ApiErrorException('注册失败，请稍后重试');
				}
				// 保存头像
				if (request('b_avatar')) {
					$file = UploadedFileDao::saveRemoteFile(request('b_avatar'), 'FT_AVATAR', $user->uid);
					if ($file['status']) {
						$user->avatar = $file['data']->file_path . '/' . $file['data']->file_name;
					}
				}
				// 生成API验证token
				$user->api_token = get_api_token($user->uid);
				$user->save();

				// 添加绑定信息
				$userBind = new UsersBind();
				$userBind->uid = $user->uid;
				$userBind->openid = request('openid');
				$userBind->b_from = request('b_from');
				$userBind->b_nickname = request('b_nickname');
				$userBind->b_avatar = request('b_avatar', '');
				$userBind->save();

				return $user;
			});
		} else {
			$user = Users::findOrFail($userbind->uid);
			// 登录成功，生成api token
			$user->api_token = get_api_token($user->uid);
			if (request('deviceno')) {
				$user->deviceno = request('deviceno');
			}
			$user->save();
		}
		return response_json(1, [
			'uid' => $user->uid,
			'api_token' => $user->api_token
		]);
	}

	/**
	 * 第三方账号登录
	 *
	 * @author lxp 20170915
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /users/login_bind 9. 第三方账号登录
	 * @apiGroup Users
	 * @apiIgnore
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} openid 第三方id
	 * @apiParam {string="wx","wb","qq"} b_from 来源
	 * @apiParam {string} [deviceno] 设备号
	 * @apiSuccess {int} uid 用户ID
	 * @apiSuccess {string} api_token 用户签名
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":{"uid":2,"api_token":"a40c76e4bc07a77f7f322530987d818e"},"msg":""}
	 */
	public function login_bind()
	{
		$this->validate([
			'openid' => 'required',
			'b_from' => 'required|in:wx,qq,wb',
		]);

		// 取出用户绑定数据
		$userbind = UsersBind::where('openid', request('openid'))->where('b_from', request('b_from'))->first();
		if (is_null($userbind)) {
			throw new ApiErrorException('用户不存在');
		}

		$user = Users::findOrFail($userbind->uid);
		// 登录成功，生成api token
		$user->api_token = get_api_token($user->uid);
		if (request('deviceno')) {
			$user->deviceno = request('deviceno');
		}
		$user->save();

		return response_json(1, [
			'uid' => $user->uid,
			'api_token' => $user->api_token
		]);
	}

	/**
	 * 验证码认证，忘记（修改）密码
	 *
	 * @author lxp 20170113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /users/check_vcode 4. 验证验证码是否正确
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 请求平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} phoneOremail 手机号/邮箱
	 * @apiParam {string} smscode 验证码
	 * @apiSuccess {string} data data等于1表示验证通过，其他验证不通过
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":{1},"msg":""}
	 */
	public function check_vcode()
	{

		$phoneOremail = trim(request('phoneOremail'));
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

		$this->validate([
			'smscode' => 'required'
		]);

		// 验证验证码
		SmsVerifyDao::code_check($phoneOremail, request('smscode'), $vtype);

		return response_json(1, 1);
	}

	/**
	 * 修改密码
	 *
	 * @author lxp 20170113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /users/password 9. 修改密码
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 请求平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} username 用户名（手机号/邮箱）
	 * @apiParam {string} password 新密码
	 * @apiParam {string} password_confirmation 确认密码
	 * @apiParam {string} [password_old] 旧密码
	 * @apiSuccess {string} username 用户名
	 * @apiSuccess {string} api_token 用户签名
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":{"username":"admin","api_token":"708a71f7be9987d5e02b5ba23b144121"},"msg":""}
	 */
	public function password()
	{

		$phoneOremail = trim(request('username'));
		if (empty($phoneOremail)) {
			throw new ApiErrorException('username不能为空');
		}

		$preg_email = '/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
		$preg_phone = '/^1[34578]\d{9}$/ims';

		if (preg_match($preg_phone, $phoneOremail)) {
			$vtype = 1;
		} elseif (preg_match($preg_email, $phoneOremail)) {
			$vtype = 2;
		} else {
			throw new ApiErrorException('请填写正确的用户名');
		}

		$this->validate([
			'password' => 'required|min:8|confirmed',
			'password_confirmation' => 'required'
		]);

		$user = Users::where('username', request('username'))->first();

		if (!$user) {
			throw new ApiErrorException('用户不存在');
		}

		if (request('password_old')) {
			if (request('password_old')==request('password')) {
				throw new ApiErrorException('输入的新密码和旧密码重复');
			}

			if (get_password(request('password_old'), $user->salt) != $user->password) {
				throw new ApiErrorException('原密码错误');
			}

		}

		// 新密码不与老密码相同，允许修改密码
		if (get_password(request('password'), $user->salt) != $user->password) {
			$user->timestamps = false;
			$user->salt = Str::random(6);
			$user->password = get_password(request('password'), $user->salt);
			$user->remember_token = Str::random(60);
			$user->api_token = get_api_token($user->uid);
			$user->save();
		}

		return response_json(1, [
			'uid' => $user->uid,
			'username' => $user->username,
			'api_token' => $user->api_token
		]);
	}

	/**
	 * 登出（清除设备号）
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /users/logout 6. 用户登出（清除设备号）
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":[],"msg":""}
	 */
	public function logout()
	{
		$uid = Auth::user()->uid;
		$user = Users::findOrFail($uid);
		$user->deviceno = null;
		$user->api_token = null;
		$user->save();

		return response_json(1, 1);
	}

	/**
	 * 登出（清除设备号）
	 *
	 * @author lxp 20170905
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /users/check_token 8. 验证token登录状态
	 * @apiGroup Users
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户签名
	 * @apiSuccess {int} data  1登录中   -1未登录
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":[],"msg":""}
	 */
	public function check_token()
	{
		$user = Users::where('api_token', request('api_token'))->first();
		if ($user) {
			return response_json(1, 1);
		} else {
			return response_json(1, -1);
		}
	}

}
