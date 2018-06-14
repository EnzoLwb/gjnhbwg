<?php

namespace App\Http\Controllers\Api;
use App\Dao\ExhibitDao;
use App\Models\Exhibit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 学习单相关接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class QuestionController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	

	/**
	 * 问卷调查详情页
	 *
	 * @author yyj 20171111
	 * @param  int $p 终端
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function question_content_info($p)
	{
		return view('api.question.content_info');
	}


}