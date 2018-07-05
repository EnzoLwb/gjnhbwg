<?php

namespace App\Models;

/**
 * 在线征集模型
 *
 * @author lwb 20180705
 */
class OnlineArticle extends BaseMdl
{
	protected $table = 'online_article';
	protected $primaryKey = 'article_id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'article_id'
	];
}
