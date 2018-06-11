<?php

namespace App\Http\Controllers\Api;
use App\Dao\ExhibitDao;
use App\Models\Exhibit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 展品导览相关接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class LearnController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	

	/**
	 * 学习单详情页
	 *
	 * @author yyj 20171111
	 * @param  int $p 终端
	 * @param  int $exhibition_id 展厅编号
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function learn_content_info($p,$exhibition_id)
	{
		return view('api.learn.learn_content_info');
	}


}