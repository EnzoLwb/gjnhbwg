@extends('layouts.public')
@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
    <script src="{{cdn('js/plugins/upload_resource/upload_resource.js')}}"></script>
    <style>
        .exhibit_list{
            margin-top: -13px;
        }
        .exhibit_list h1{
            font-size: 16px;
            font-weight: bold;
            padding: 20px 0 10px;
            clear: both;
        }
        .exhibit_list .exhibit_box{
            color: #959595;
            float: left;
            width: 150px;
            height: 90px;
            line-height: 22px;
            padding: 10px;
            margin-right: 15px;
            margin-bottom: 15px;
            border: 2px dashed #e5e6e7;
            overflow: hidden;
            cursor: pointer;
        }
        .exhibit_list .exhibit_box input{
            display: none;
        }
        .exhibit_list .checked{
            color: #676a6c;
            border: 2px dashed #44b6eb;
        }
    </style>
@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.approad.road_list')}}">路线列表</a></li>
                        <li class="active"><a href="{{route('admin.approad.edit',['add'])}}">@if($id=='add')添加路线@else编辑路线 @endif </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="" method="post" class="form-horizontal ajaxForm">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">展品选择</label>
                            <div class="col-sm-8 exhibit_list">
                                @foreach($exhibit_list as $k=>$g)
                                    <h1>{{$g['exhibition_name']}}</h1>
                                    @foreach($g['exhibit_list'] as $kk=>$gg)
                                        <div class="exhibit_box"><input type="checkbox" name="road_exhibit_id[]" value="{{$gg['exhibit_id']}}" @if($gg['is_check']==1)checked @endif />{{$gg['exhibiti_name']}}</div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>


                        <div class="layui-tab">
                            <ul class="layui-tab-title">
                                @foreach(config('language') as $k=>$g)
                                    <li @if($k==1) class="layui-this" @endif>{{$g['name']}}</li>
                                @endforeach
                            </ul>
                            <div class="layui-tab-content">
                                @foreach(config('language') as $k=>$g)
                                    <div class="layui-tab-item @if($k==1) layui-show @endif">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">路线名称({{$g['name']}})</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="road_name_{{$k}}" value="{{$info['language'][$k]['road_name'] or ''}}" class="form-control" maxlength="500">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6 col-md-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <button class="btn btn-white" type="button" onclick="window.history.back()">返回</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script>
        layui.use('element', function () {
            var $ = layui.jquery
                    , element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
        });
        $(".exhibit_box").each(function(){
            if($(this).find("input").prop('checked')){
                $(this).addClass("checked");
            }
        }).click(function(){
            var check =$(this).find("input");
            check.prop('checked', !check.prop('checked'));
            $(this).toggleClass("checked");
        });
    </script>
@endsection
