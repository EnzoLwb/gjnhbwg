<?php

namespace App\Dao;

use App\Models\BaseMdl;

/**
 * Class MenuDao
 *
 * @author lxp 20180123
 * @package App\Dao
 */
class MenuDao extends BaseMdl
{
	/**
	 * 菜单及权限配置
	 *
	 * 最大支持三级菜单
	 * 每个菜单项要包括：
	 * text 名称
	 * priv 权限名称
	 *        例：控制器路径为 App/Http/Controllers/User/UsersController.php，则权限名称为 user-users
	 *           如果要对应方法，则用冒号拼接，user-users:getlist
	 *           对应多个方法（目前没有实现验证），user-users:getlist|getedit|postsave
	 *        如果不对应具体控制器，名称则随意，但不能重复
	 *
	 * url 链接（可选）
	 * nodes 子菜单（可选）
	 * icon 图标（可选）
	 *
	 * @author lxp 20180123
	 * @return array
	 */
	public static function get_admin_menu()
	{
		$base_menu = [
			[
				'text' => '主页',
				'priv' => 'home',
				'url' => route('admin.welcome'),
				'order_num' => 10,
			],
			[
				'text' => '设置',
				'priv' => 'setting',
				'icon' => 'fa fa-cog',
				'order_num' => 10,
				'nodes' => [
					[
						'text' => '网站设置',
						'url' => route('admin.setting.basesetting'),
						'priv' => 'admin-setting-basesetting',
						'order_num' => 10,
					],
					[
						'text' => '系统日志',
						'url' => route('admin.setting.systemlog'),
						'priv' => 'admin-setting-systemlog',
						'order_num' => 10,
					],
					/*[
						'text' => '模块装载管理',
						'url' => route('admin.load.load_list'),
						'priv' => 'admin-load-load',
						'order_num' => 10,
					],*/

					[
						'text' => '线路导航管理',
						'url' => route('admin.navigation.show'),
						'priv' => 'admin-navigation-navigation',
						'order_num' => 11,
					],
				],
			],
			[
				'text' => '用户',
				'priv' => 'user',
				'icon' => 'fa fa-users',
				'order_num' => 10,
				'nodes' => [
					[
						'text' => '用户管理',
						'url' => route('admin.user.users'),
						'priv' => 'admin-user-users',
						'order_num' => 10,
					],
					[
						'text' => '管理员管理',
						'url' => route('admin.setting.adminusers'),
						'priv' => 'admin-setting-adminusers',
						'order_num' => 10,
					],
					[
						'text' => '用户组管理',
						'url' => route('admin.setting.admingroup'),
						'priv' => 'admin-setting-admingroup',
						'order_num' => 10,
					]
				]
			],
			[
				'text' => '日志管理',
				'priv' => 'admin-log-loginlog',
				'icon' => 'fa fa-book',
				'url' => route('admin.log.login_log'),
				'order_num' => 10,
			],
			[
				'text' => '服务信息',
				'priv' => 'service',
				'icon' => 'fa fa-book',
				'order_num' => 10,
				'nodes' => [
					[
						'text' => '场馆简介',
						'url' => route('admin.service.intro'),
						'priv' => 'admin-service-intro',
						'order_num' => 10,
					],
					[
						'text' => '参观指南',
						'url' => route('admin.service.cgzn'),
						'priv' => 'admin-setting-adminusers',
						'order_num' => 10,
					],
					[
						'text' => '文创系列',
						'url' => route('admin.service.wenchuangxl'),
						'priv' => 'admin-setting-admingroup',
						'order_num' => 10,
					],
					[
						'text' => '文创产品',
						'url' => route('admin.service.wenchuang'),
						'priv' => 'admin-setting-admingroup',
						'order_num' => 10,
					],
					[
						'text' => '宣教活动',
						'url' => route('admin.service.xjhd'),
						'priv' => 'admin-setting-admingroup',
						'order_num' => 10,
					],
				]
			],
			[
				'text' => '互动管理',
				'priv' => 'interaction',
				'icon' => 'fa fa-users',
				'order_num' => 10,
				'nodes' => [
					[
						'text' => '学习单',
						'url' => route('admin.interaction.learn.question_list'),
						'priv' => 'admin-interaction-learn-question_list',
						'order_num' => 10,
					],
//					[
//						'text' => '问卷调查',
//						'url' => route('admin.interaction.question'),
//						'priv' => 'admin-interaction-question',
//						'order_num' => 10,
//					],

				]
			],

		];

		//功能模块配置加载
		if (file_exists(base_path() . '/config/load_menu/base.php')) {
			$load_menu = include base_path() . '/config/load_menu/base.php';
			foreach ($base_menu as $k => $g) {
				if (isset($load_menu['update'][$g['priv']])) {
					$base_menu[$k]['nodes'] = array_merge($g['nodes'], $load_menu['update'][$g['priv']]);
				}
			}
			$list_menu = array_merge($base_menu, $load_menu['add']);
		} else {
			$load_menu = [];
			$list_menu = array_merge($base_menu, $load_menu);
		}
		return arraySort($list_menu, 'order_num');
	}


}
