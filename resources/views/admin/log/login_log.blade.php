@extends('layouts.public')

@section('bodyattr')class=""@endsection

@section('body')

    <div class="wrapper wrapper-content">
		
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.log.login_log')}}">登录日志</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" method="get">
                        <div class="form-group">
                            <label>查询日期</label>
                            <input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start" type="text" name="created_at_from" value="{{request('created_at_from')}}">
                            &nbsp;至&nbsp;
                            <input placeholder="结束日期" class="form-control layer-date laydate-icon" id="end" type="text" name="created_at_to" value="{{request('created_at_to')}}">
                        </div>
                        &nbsp;&nbsp;
                        <div class="form-group">
                            <input type="text" name="username" placeholder="用户名/姓名" class="form-control" value="{{request('username')}}" style=" width: 240px;" maxlength="20">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.log.login_log')}}'">重置</button>
                    </form>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                        <table class="table table-striped table-new table-hover dataTables-example dataTable">
                            <thead>
                            <tr role="row">
                                <th width="200">用户名</th>
                                <th width="200">姓名</th>
                                <th width="270">邮箱</th>
                                <th width="270">登录时间</th>
                                <th>登录IP</th>
                            </tr>
                            </thead>
                            @foreach($users as $user)
                                <tr class="gradeA">
                                    <td>{{$user['username']}}</td>
                                    <td>{{$user['nickname']}}</td>
                                    <td>{{$user['email']}}</td>
                                    <td>{{$user['created_at']}}</td>
                                    <td>{{$user['ip']}}</td>
                                </tr>
                            @endforeach
                        </table>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $users->links() !!}
                            <span>共 {{ $users->total() }} 条记录</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script type="text/javascript" src="{{cdn('js/plugins/laydate/laydate.js')}}"></script>
    <script type="text/javascript">
        var start = {
            elem: "#start", format: "YYYY-MM-DD",
            max: laydate.now(),
            isclear: false,
            istoday: false,
            issure: false,
            choose: function (datas) {
                if($('#end').val()!==null&&$('#end').val()!==''&&$('#end').val()<$('#start').val()){
                    $('#end').val(datas);
                }
                end.min = datas;
                end.start = datas;
            }
        };
        var end = {
            elem: "#end", format: "YYYY-MM-DD",
            max: laydate.now(),
            @if(request('created_at_from'))
            min:$('#start').val(),
            @endif
            isclear: false,
            istoday: false,
            issure: false,
            choose: function (datas) {
                /*end.min = datas;
                end.start = datas*/
            }
        };
        laydate(start);
        laydate(end);
    </script>
@endsection
