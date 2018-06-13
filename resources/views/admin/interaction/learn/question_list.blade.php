@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li  class="active"><a href="{{route('admin.interaction.learn.question_list')}}">题库列表</a></li>
                        <li><a href="{{route('admin.interaction.learn.add_question')}}">添加题目</a></li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                        <table class="table table-striped table-new table-hover infoTables-example infoTable">
                            <thead>
                            <tr role="row">
                                <th>题目编号</th>
                                <th>题目名称</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            @foreach($list as $v)
                                <tr class="gradeA">
                                    <td>{{$v->id}}</td>
                                    <td>{{$v->title}}</td>

                                    <td>
                                        <a href="{{route('admin.interaction.learn.edit_question',$v['id'])}}">编辑</a>
                                        | <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.interaction.learn.delete_question',$v['id'])}}" msg="是否删除该题目？">删除</a>

                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-12">
                                {!! $list->links() !!}
                                <span>共 {{ $list->total() }} 条记录</span>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection


