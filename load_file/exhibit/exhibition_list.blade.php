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
                        <li class="active"><a href="{{route('admin.data.exhibition')}}">展厅列表</a></li>
                        <li><a href="{{route('admin.data.exhibition.edit','add')}}">添加展厅</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" id="form_submit" method="get">
                        @if(config('exhibit_config.exhibition.is_linzhan'))
                        <div class="form-group">
                            展厅类别
                            <select name="type" id="select_calss1">
                                <option value="0">全部</option>
                                <option @if(request('type')==1) selected @endif value="1">主题展厅</option>
                                <option @if(request('type')==2) selected @endif value="2">临时展厅</option>
                            </select>
                        </div>
                        @endif
                        @if(config('exhibit_config.exhibition.is_lb'))
                        <div class="form-group">
                            是否轮播
                            <select name="is_lb" id="select_calss2">
                                <option value="0">全部</option>
                                <option @if(request('is_lb')==1) selected @endif value="1">轮播</option>
                                <option @if(request('is_lb')==2) selected @endif value="2">不轮播</option>
                            </select>
                        </div>
                        @endif
                        <div class="form-group">
                            <input type="text" name="exhibition_name" placeholder="展厅名称" class="form-control" value="{{request('exhibition_name')}}" style=" width: 200px;" maxlength="20">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.data.exhibition')}}'">重置</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="exhibition">
                    <ul class="exhibition-list">
                        @foreach($info as $g)
                        <li>
                            <div class="list-pic">
                                <img src="{{json_decode($g['exhibition_img'],true)[config('exhibit_config.exhibition.imgs.0.key')]}}">
                                <div class="list-type">@if($g['type']==1)主题展厅@else临时展厅@endif</div>
                                <a class="btn-edit" href="{{route('admin.data.exhibition.edit', $g['id'])}}">编辑</a>
                                <a class="ajaxBtn btn-delete" href="javascript:void(0);" uri="{{route('admin.data.exhibition.delete',$g['id'])}}" msg="是否删除该展厅？">删除</a>
                                @if(config('exhibit_config.exhibition.is_lb'))
                                @if($g['is_lb']==1)
                                    <a class="ajaxBtn btn-set" href="javascript:void(0);" uri="{{route('admin.data.exhibition.unset_lb',$g['id'])}}" msg="是否取消轮播？">取消轮播</a>
                                @else
                                    <a class="ajaxBtn btn-set" href="javascript:void(0);" uri="{{route('admin.data.exhibition.set_lb' ,$g['id'])}}" msg="是否设为轮播？">设为轮播</a>
                                @endif
                                @endif
                            </div>
                            <div class="list-tit">{{$g['exhibition_name']}}</div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="clearfix"></div>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $info->links() !!}
                            <span>共 {{ $info->total() }} 条记录</span>
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
@endsection
