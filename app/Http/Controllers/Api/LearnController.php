<?php

namespace App\Http\Controllers\Api;
use App\Dao\ExhibitDao;
use App\Models\Exhibit;
use App\Models\Learn;
use App\Models\LearnData;
use App\Models\LearnOption;
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
	 * 获取学习单页面
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /learn_content_info 1.获取学习单页面
	 * @apiGroup Learn
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 * @apiParam {int} exhibition_id 展厅id
	 * @apiParam {string} [api_token] IOS和安卓必填
	 * @apiSuccess {json} data 数据详情
	 */
	public function learn_content_info()
	{
		$exhibition_id = request('exhibition_id');
		//随机抽选10题
		$list = Learn::leftJoin('learn_relation','learn.id','learn_relation.learn_id')->where('learn_relation.exhibition_id',$exhibition_id)
			->select('learn.id','learn.title','learn_relation.exhibition_id')->orderBy(DB::raw('RAND()'))->limit(10)->get();

		foreach($list as $k=>$v){
			$list[$k]['option'] = LearnOption::where('learn_id',$v['id'])->get()->toArray();
		}
		if(request('p')=='d'){
			$uid=0;
			return view('api.learn.dlj_learn_content_info',[
				'list'=>$list,
				'p'=>request('p'),
				'exhibition_id'=>$exhibition_id,
				'uid'=>$uid,
				'option_title'=> ['A','B','C','D','E','F','G','H','I','J']
			]);
		}else{

//			$uid = Auth::user()->uid;
			$uid=0;
//			return response_json(0, '','请先登录');
			return view('api.learn.learn_content_info',[
				'list'=>$list,
				'p'=>request('p'),
				'exhibition_id'=>$exhibition_id,
				'uid'=>$uid,
				'option_title'=> ['A','B','C','D','E','F','G','H','I','J']
			]);
		}


	}
	public function save_answer(){
		$uid = 0;
		$answer = request('answer');
		$timecost = request('timecost');
		$exhibition_id = request('exhibition_id');
		// 处理答题数据，得到分数
		$score = 0;
		foreach ($answer as $v) {
			$tempids = explode('_', $v);
			if (LearnOption::where('id',$tempids[1])->where('learn_id',$tempids[0])->where('isanswer',1)->count() > 0) {
				$score += 10;
			}
		}

		$newid = LearnData::insertGetId([
			'exhibition_id'=>$exhibition_id,
			'uid'=>$uid,
			'score'=>$score,
			'datalog'=>json_encode($answer),
			'add_time'=>date("Y-m-d H:i:s"),
			'timecost'=>$timecost
		]);
		echo $newid;

	}

	/**
	 * 成绩单排行榜
	 *
	 * @author lxp
	 */
	public function answer_list() {
		$exhibition_id = request('exhibition_id');
		$newid = request('newid');
		$list = LearnData::where('exhibition_id',$exhibition_id)->orderBy('score','desc')->orderBy('timecost','asc')->limit(10)->get();
		foreach($list as $k=>$v){
			$ids[]=$v['id'];
		}

		if ($newid && !in_array($newid, $ids)) {
			// 如果没在前10名，则取出最近记录的名次
			$sql = "SELECT * FROM (SELECT * ,@rownum :=@rownum + 1 AS rownum FROM `nh_learn_data`, (SELECT(@rownum := 0)) b ORDER BY score DESC, timecost ASC, add_time ASC) r WHERE id = {$newid}";
			$myrecord = DB::select($sql)[0];
		}else{
			$myrecord=[];
		}
		return view('api.learn.answerlist',[
			'list'=>$list,
			'newid'=>$newid,
			'myrecord'=>$myrecord
		]);

	}


}