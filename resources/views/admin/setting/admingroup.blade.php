@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.setting.admingroup')}}">角色列表</a></li>
                        <li class=""><a href="{{route('admin.setting.admingroup.add')}}">添加角色</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                        <table class="table table-striped table-new table-hover">
                            <thead>
                            <tr class="gradeA">
                                <th></th>
                                <th>角色名称</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            @foreach($userGroup as $i => $group)
                                <tr class="gradeA">
                                    <td>{{$i+1}}</td>
                                    <td>{{$group['groupname']}}</td>
                                    <td>{{$group['created_at']}}</td>
                                    <td>{{$group['updated_at']}}</td>
                                    <td>
                                        @if ($group['privs'] != 'all'&&($group['groupid']>2||Auth::id()==1))
                                            <a href="{{route('admin.setting.admingroup.edit',$group->groupid)}}" title="编辑"><i class="fa fa-edit"></i></a>
                                            | <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.setting.admingroup.delete',$group->groupid)}}" title="删除" msg="是否删除该用户组？"><i class="fa fa-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $userGroup->links() !!}
                            <span>共 {{ $userGroup->total() }} 条记录</span>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

