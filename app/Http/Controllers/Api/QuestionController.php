<?php

namespace App\Http\Controllers\Api;

use App\Models\Queslist;
use App\Models\QuesinfoList;
use App\Models\QuesinfoOption;
use App\Models\QuesTextinfo;

class QuestionController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 问卷调查列表
	 *
	 * @author lwb 20180619
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /question_list 问卷调查列表
	 * @apiGroup Question
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓
	 * @apiSuccess {int} statues 状态码
	 * @apiSuccess {array} data 数组
	 * @apiSuccess {int} data.id 问卷ID
	 * @apiSuccess {string} data.title 问卷名称
	 * @apiSuccess {string} data.description 问卷调查描述
	 * @apiSuccess {string} data.detail_url 问卷调查详情链接
	 * @apiSuccess {string} msg 文字信息
	 * @apiSuccessExample {json} 返回值
	 *
	 * {"status":1,"data":[{"id":1,"title":"\u6d4b\u8bd51","description":"\u6d4b\u8bd51","detail_url":"http:\/\/http://192.168.10.158:8309\/api\/quesinfo?p=i&question_id=1"}],"msg":""}
	 */
	public function question_list(){
		$question_list = Queslist::where('status',1)->select('id','title','description')->get();
		foreach($question_list as $k=>$v){
			$question_list[$k]['detail_url'] = route("api.question.info",['p'=>request('p'),'question_id'=>$v['id']]);
		}
		return response_json(1,$question_list,"");
	}


	public function quesinfo()
	{
		if (request()->ajax()) {
			$num = request('num');
			$ques_id = request('ques_id');
			$arr['code'] = 'error';
			if (empty($num) || empty($ques_id)) {
				$arr['info'] = '参数错误';
				return $arr;
			}
			//已选择的选项id
			$add_id = [];
			//填写的文字信息
			$add_data = [];
			for ($i = 0; $i <= $num; $i++) {
				$error_i = $i+1;
				$type = request('ques_type' . $i);
				if ($type == 1 || $type == 2) {
					if ($type == 1) {

						if(request('ques_option' . $i)){
							$ls = explode('_', request('ques_option' . $i));

							if ($ls[0] == 'r') {
								$chose = $ls[1];
								$t_chose = 0;
							} else {
								$chose = 0;
								$t_chose = $ls[1];
							}

						}else{
							$arr['info'] = "第".$error_i. '题尚未选择答案';
							return $arr;
						}


					} else {
						$chose = request('ques_option' . $i);
						$t_chose = request('t_ques_option' . $i);
					}

					if (empty($chose) && empty($t_chose)) {

						$arr['info'] = '第' . $error_i . '题尚未选择答案';

						return $arr;
					}

					if (!empty($t_chose)) {
						$text = request('ques_option_text' . $i);
						if (empty($text)) {

							$arr['info'] = '请填写第' . $error_i . '题的其他选项';

							return $arr;
						} else {
							$add_data[] = [
								'ques_id' => $ques_id,
								'quesinfo_id' => $i+1,
								'text_info' => $text,
								'date_time' => date('Y-m-d H:i:s', time())
							];
							if (is_array($t_chose)) {
								array_push($add_id, $t_chose[0]);
							} else {
								array_push($add_id, $t_chose);
							}
						}
					}
				}
				if ($type == 1) {

					if (!empty($chose)) {
						array_push($add_id, $chose);
					}
				} elseif ($type == 2) {

					if (!empty($chose)) {
						foreach ($chose as $g) {
							array_push($add_id, $g);
						}
					}
				} elseif ($type == 3) {

					$text = request('ques_option_text' . $i);
					if (empty($text)) {

						$arr['info'] = '请填写第' . $error_i . '题的其他选项';
						return $arr;
					} else {
						$add_data[] = [
							'ques_id' => $ques_id,
							'quesinfo_id' => $i+1,
							'text_info' => $text,
							'date_time' => date('Y-m-d H:i:s', time())
						];
					}
				}
			}

			QuesTextinfo::insert($add_data);

			QuesinfoOption::whereIn('id',$add_id)->increment('option_num',1);

			Queslist::where('id',$ques_id)->increment('num',1);
			$arr['code'] = 'success';
			return $arr;
		} else {
			//当前问卷ID
			$question_id = request('question_id');
			$end =request('end');
			$ques_info = Queslist::where('status',1)->where('id',$question_id)->first();
			$arr['a'] = '您好';
			$arr['b'] = $ques_info['description'];
			if ($end == 1) {
				$arr['msg'] = '您已完成问卷的所有问题,感谢您的参与';
				return view('api.question.ques_html_end', [
					'arr' => $arr,
					'p'=>request('p'),
					'question_id'=>$question_id
				]);
				exit;
			}
			//获取当前语种的问卷
			if (empty($ques_info)) {
				$arr['msg'] = '尚未进行';
				return view('api.question.ques_html_end', [
					'arr' => $arr,
					'p'=>request('p'),
					'question_id'=>$question_id
				]);
				exit;
			}
			$ques_id=$ques_info['id'];
			$arr['title'] = '问卷调查';
			$arr['c'] = '单选';
			$arr['d'] = '可多选';
			$arr['e'] = '其他';
			$arr['f'] = '上一题';
			$arr['g'] = '下一题';
			$arr['h'] = '提交';
			$arr['i'] = '加载中';
			$arr['j'] = '不满意（请注明原因）';

			//判断问卷下是否有题目
			$info = QuesinfoList::where('ques_id',$ques_id)->where('is_save',1)->orderBy('id','asc')->get();

			foreach ($info as $k => $g) {
				$info[$k]['option_info'] = QuesinfoOption::where('quesinfo_id',$g['quesinfo_id'])
					->where('ques_id',$ques_id)->orderBy('option_type','asc')->orderBy('id','asc')->get()->toArray();
			}
			$num = count($info);

			if (empty($num)) {
				$arr['msg'] = '该问卷暂无题目';
				return view('api.question.ques_html_end', [
					'arr' => $arr,
					'p'=>request('p'),
					'question_id'=>$question_id
				]);
				exit;
			}
			//			print_r($info);
			return view('api.question.ques_html_start', [
				'arr' => $arr,
				'num'=>$num,
				'ques_id'=>$ques_id,
				'p'=>request('p'),
				'question_id'=>$question_id,
				'info'=>$info
			]);
		}
	}

}
