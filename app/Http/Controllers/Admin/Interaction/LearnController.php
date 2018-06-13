<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Dao\LearnQuestionDao;
use App\Models\LearnOption;
use App\Models\Learn;
use App\Models\LearnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\BaseAdminController;
use \Illuminate\Support\Facades\Validator;

class LearnController extends BaseAdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 题目列表
	 *
	 * @author ljy 20180613
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function question_list()
	{
		$query = Learn::orderBy('id');
		$list  = $query->paginate(parent::PERPAGE);
		// 将查询参数拼接到分页链接中
		$list->appends(app('request')->all());
		return view('admin.interaction.learn.question_list', [
			'list'=>$list
		]);
	}


	/**
	 * 显示添加问题界面
	 *
	 * @author zjy 20170920
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function add_question()
	{

		return view('admin.interaction.learn.add_question');
	}

	/**
	 * 保存问题
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function save_question()
	{
		$id = request('id',0);
		$learn = Learn::findOrNew($id);
		$learn->title = request('title');
		$learn->save();
		$learn_id = $learn->id;

		LearnOption::where('learn_id',$learn_id)->delete();

		$info['learn_id'] = $learn_id;
		$options = request('option');

		foreach($options as $key=> $option){
			$info['option'] = $option;
			$info['isanswer'] = 0;
			if($key == request('isanswer')){
				$info['isanswer'] = 1;
			}
			LearnOption::insert($info);
		}


		return $this->success(get_session_url('question_list'));
	}

	/**
	 * 删除问题
	 * @author zjy 20170920
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function delete_question($id){
		Learn::where('id', $id)->delete();
		LearnOption::where('learn_id', $id)->delete();
		return $this->success(get_session_url('question_list'));
	}

	/**
	 * 显示问题编辑页面
	 * @author zjy  20170920
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit_question($id){
		$learn = Learn::where('id',$id)->first();
		$learn_option = LearnOption::where('learn_id',$id)->get();
		return view('admin.interaction.learn.add_question', [
			'learn_option'=>$learn_option,
			'learn'=>$learn
		]);
	}
}
