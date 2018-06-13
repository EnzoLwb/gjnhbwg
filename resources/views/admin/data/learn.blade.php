@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">

                        <li><a href="{{route('admin.data.exhibition')}}">展厅列表</a></li>
                        <li><a href="{{route('admin.data.exhibition.edit','add')}}">添加展厅</a></li>
                        <li class="active"><a href="{{route('admin.data.exhibition.add_learn',$exhibition_id)}}">添加学习单</a></li>

                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <table class="table table-striped table-bordered table-hover dataTables-example dataTable">
                            <thead>
                            <tr role="row">
                                <th width="2%"><input type="checkbox" class="checkAll"></th>
                                <th>题目名称</th>
                            </tr>
                            </thead>
                            @foreach($list as $k=>$v)
                                <tr class="gradeA">
                                    <td><input type="checkbox" class="checkItem"  value="{{$v['id']}}"></td>
                                    <td>{{$v['title']}}</td>

                                </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-danger btn-sm checkBtn" uri="{{route('admin.data.exhibition.save_learn',[$exhibition_id,$v['id']])}}" msg="是否选择学习单题目">保存</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div>共 {{ $list->total() }} 条记录</div>
                                {!! $list->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
