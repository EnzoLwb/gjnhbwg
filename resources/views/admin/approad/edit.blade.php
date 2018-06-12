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
            float: left;
            width: 150px;
            height: 90px;
            line-height: 22px;
            padding: 10px;
            margin-right: 15px;
            margin-bottom: 15px;
            border: 1px dashed #959595;
            overflow: hidden;
        }
        input.quanzhi{ display:inline-block; width:30px; border: none; border-bottom: 1px solid #999;}
        input.quanzhi:hover{ border: 1px solid #999;}
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
                                        <div class="exhibit_box">
                                            <input type="checkbox" name="road_exhibit_id{{$g['exhibition_id']}}[]" value="{{$gg['exhibit_id']}}" @if($gg['is_check']==1)checked @endif />{{$gg['exhibiti_name']}}<br/>

                                            <!--排序权值-->
                                            @if(isset($road_raw_info[$gg['exhibit_id']]))
                                                权值：<input class="quanzhi" type="number" name="{{'weight_'.$gg['exhibit_id']}}" value="{{$road_raw_info[$gg['exhibit_id']]}}"/>
                                            @else
                                                权值：<input class="quanzhi" type="number" name="{{'weight_'.$gg['exhibit_id']}}" value="0"/>
                                            @endif

                                        </div>

                                    @endforeach
                                @endforeach
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">游览时长</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="road_long" value="{{$info['road_long'] or ''}}" required/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">路线图上传</label>
                            <div class="col-sm-4">
                            <button type="button" onclick="upload_resource('路线图上传','FT_ONE_RESOURCE','road_img',1);" class="btn btn-white">路线图上传</button>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">&nbsp;</label>
                            <div class="col-sm-4">
                                <input type="hidden" name="road_img_old" value="{{$info['road_img'] or ''}}">
                                <div id="road_img">
                                    @if(!empty($info['road_img'])&&isset($info['road_img']))
                                        <div class="img-div">
                                            <img src="{{$info['road_img']}}">
                                            <span onclick="del_img($(this))">×</span>
                                            <input type="hidden" name="road_img" value="{{$info['road_img']}}">
                                        </div>
                                    @endif
                                </div>
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
//        $(".exhibit_box input[type='checkbox']").click(function(){
        //          var check = $(this).find("input");
        var check = $(this);
        console.log(check.prop('checked'))
        check.prop('checked', check.prop('checked'));
        })
    </script>
@endsection
