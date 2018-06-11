@extends('layouts.public')

@section('head')
    <link rel="stylesheet" href="{{cdn('css/add/exhibit.css')}}">
@endsection

@section('body')

    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.service.xjhd')}}">宣教活动列表</a></li>
                        <li><a href="{{route('admin.service.xjhd.add')}}">添加宣教活动</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" id="form_submit" method="get">
                        <div class="form-group">
                            <label>添加时间</label>
                            <input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start" type="text" name="created_at_from" value="{{request('created_at_from')}}"
                                   style="width: 140px;">
                            ~ <input placeholder="结束日期" class="form-control layer-date laydate-icon" id="end" type="text" name="created_at_to" value="{{request('created_at_to')}}"
                                     style="width: 140px;">
                        </div>
                        <div class="form-group">
                            是否显示
                            <select name="is_show" id="select_calss2">
                                <option value="0">全部</option>
                                <option @if(request('is_show')==1) selected @endif value="1">显示</option>
                                <option @if(request('is_show')==2) selected @endif value="2">不显示</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="title" placeholder="名称" class="form-control" value="{{request('title')}}" style=" width: 200px;" maxlength="20">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.service.xjhd')}}'">重置</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="exhibition">
                    <ul class="exhibition-list">
                        @foreach($data as $g)
                        <li>
                            <div class="list-pic">
                                <img src="{{$g['img']}}">
                                <div class="list-type">{{$g['title_1']}}</div>
                                <a class="btn-edit" href="{{route('admin.service.xjhd.edit', $g['id'])}}">编辑</a>
                                <a class="ajaxBtn btn-delete" href="javascript:void(0);" uri="{{route('admin.service.xjhd.delete',$g['id'])}}" msg="是否删除该系列及系列下的产品？">删除</a>

                                @if($g['is_show']==1)
                                    <a class="ajaxBtn btn-set" href="javascript:void(0);" uri="{{route('admin.service.xjhd.unset_show',$g['id'])}}" msg="是否取消显示？">取消显示</a>
                                @else
                                    <a class="ajaxBtn btn-set" href="javascript:void(0);" uri="{{route('admin.service.xjhd.set_show' ,$g['id'])}}" msg="是否设为显示？">设置显示</a>
                                @endif
                            </div>
                            <div class="list-tit">{{$g['title']}}</div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="clearfix"></div>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $data->links() !!}
                            <span>共 {{ $data->total() }} 条记录</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    $('#select_calss1,#select_calss2').change(function(){
        $('#form_submit').submit();
    })
</script>
<script src="{{cdn('js/plugins/laydate/laydate.js')}}"></script>
<script type="text/javascript">
    var start = $.extend({}, laydateOptions, {
        elem: "#start",
        choose: function (datas) {
            end.min = datas;
            end.start = datas;
        }
    });
    var end = $.extend({}, laydateOptions, {
        elem: "#end",
        min: laydate.now(),
        choose: function (datas) {
            start.max = datas
        }
    });
    laydate(start);
    laydate(end);
</script>
@endsection
