<?php
//随手拍列表
Route::get('pai_list', 'PaiController@pai_list');
//在线征集列表
Route::get('online_article_list', 'PaiController@online_article_list');
//获取文章详情web页面
Route::get('online_article_url', 'PaiController@online_article_url');
//在线征集文章web详情
Route::get('online_article_detail', 'PaiController@online_article_detail')->name('api.online_article.detail');
//随手拍评论列表
Route::get('pai_comment_list', 'PaiController@pai_comment_list');
//留言发布
Route::post('send_words', 'PaiController@send_words');

Route::group([
	'middleware' => 'auth:api'
], function () {
	//随手拍图片上传
	Route::post('pai_uploadimg', 'PaiController@pai_uploadimg');
	//微信图片上传
	Route::post('wx_upload_img', 'PaiController@wx_upload_img');
	//随手拍发布
	Route::post('send_pai', 'PaiController@send_pai');
	//评论随手拍
	Route::post('pai_comment', 'PaiController@pai_comment');
	//随手拍评论点赞取消
	Route::get('pai_dolike', 'PaiController@pai_dolike');
	//我的随手拍列表
	Route::get('my_pai_list', 'PaiController@my_pai_list');
	//我的随手拍删除接口
	Route::get('del_my_pai_list', 'PaiController@del_my_pai_list');

	//----------在线征集----------
	//在线征集发布
	Route::post('article_publish', 'PaiController@article_publish');
	//我的在线征集文章删除
	Route::get('del_my_online_article', 'PaiController@del_my_online_article');
	//我的文章列表
	Route::get('my_online_article_list', 'PaiController@my_online_article_list');

});
