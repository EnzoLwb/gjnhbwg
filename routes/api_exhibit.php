<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 11:16
 */
//获取所有展厅
Route::get('exhibition_list', 'ExhibitController@exhibition_list');
//获取展厅详情
Route::get('exhibition_info', 'ExhibitController@exhibition_info');
//展厅详情页
Route::get('exhibition_content_info/{language}/{exhibition_id}', 'ExhibitController@exhibition_content_info');

//获取所有展品
Route::get('exhibit_list', 'ExhibitController@exhibit_list');
//获取展品详情
Route::get('exhibit_info', 'ExhibitController@exhibit_info');
//展品详情页
Route::get('exhibit_content_info/{language}/{exhibit_id}', 'ExhibitController@exhibit_content_info');
//展品科普知识页
Route::get('exhibit_knowledge_info/{language}/{exhibit_id}', 'ExhibitController@exhibit_knowledge_info');
//展品分享
Route::get('exhibit_share_info/{language}/{exhibit_id}', 'ExhibitController@exhibit_share_info');

//评论列表
Route::get('comment_list', 'ExhibitController@comment_list');
//展品浏览接口
Route::get('visit_exhibit', 'ExhibitController@visit_exhibit');
//展品搜索接口
Route::get('exhibit_search', 'ExhibitController@exhibit_search');
//热门展品接口
Route::get('exhibit_hot', 'ExhibitController@exhibit_hot');

//获取地图页展品数据
Route::get('map_exhibit', 'MapExhibitController@map_exhibit');
//附近展厅
Route::get('map_near_exhibition', 'MapExhibitController@map_near_exhibition');
//附近展品
Route::get('map_near_exhibit', 'MapExhibitController@map_near_exhibit');
//导航线路生成接口
Route::get('road_navigation', 'MapExhibitController@road_navigation');
//线路生成接口
Route::get('road_info', 'MapExhibitController@road_info');
//线路列表接口
Route::get('road_list', 'MapExhibitController@road_list');
//线路详情接口
Route::get('road_detail', 'MapExhibitController@road_detail');
//线路详情接口
Route::get('road_detail_all', 'MapExhibitController@road_detail_all');

//导览机版本资源更新
Route::get('update_version_resource', 'MapExhibitController@update_version_resource');
//导览机获取数据库最新数据
Route::get('datas_info', 'MapExhibitController@datas_info');

// 需要登录验证的路由
Route::group([
	'middleware' => 'auth:api'
], function () {
	//展品点赞、取消点赞接口
	Route::get('do_like', 'ExhibitController@do_like');
	//展品评论接口
	Route::post('exhibit_comment', 'ExhibitController@exhibit_comment');
	//评论点赞、取消点赞接口
	Route::get('comment_do_like', 'ExhibitController@comment_do_like');
	//个人参观轨迹
	Route::get('my_looked', 'MyExhibitController@my_looked');
	//我的收藏
	Route::get('my_collection', 'MyExhibitController@my_collection');
	//我的足迹
	Route::get('my_footed', 'MyExhibitController@my_footed');
	//我的点赞
	Route::get('my_liked', 'MyExhibitController@my_liked');
	//我的评论
	Route::get('my_comment', 'MyExhibitController@my_comment');
	//删除我的评论
	Route::get('del_my_comment', 'MyExhibitController@del_my_comment');
});
