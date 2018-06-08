@extends('layouts.public')


@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.approad.road_list')}}">路线列表</a></li>
                        <li><a href="{{route('admin.approad.edit',['add'])}}">添加路线</a></li>
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
                            <th width="100">路线ID</th>
                            <th width="100">路线名称</th>
                            <th width="100">编辑时间</th>
                            <th width="100">操作</th>
                        </tr>
                        </thead>
                        @foreach($info as $k=>$v)
                            <tr class="gradeA">
                                <td>{{$v->id}}</td>
                                <td>{!! $v->road_name !!}</td>
                                <td>{{$v->updated_at}}</td>
                                <td>
                                    <a class="btn-edit" href="{{route('admin.approad.edit',[$v['id']])}}" title="编辑"><i class="fa fa-edit"></i></a>

                                    <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.approad.delete',[$v['id']])}}" title="删除" msg="是否删除？"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $info->links() !!}
                            <span>共 {{ $info->total() }} 条记录</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection


