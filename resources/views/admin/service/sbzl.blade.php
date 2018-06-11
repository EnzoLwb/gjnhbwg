@extends('layouts.public')
@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
@endsection
@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.service.cgzn')}}">交通周边</a></li>
                        <li><a href="{{route('admin.service.cgzn.cgxz')}}">参观须知</a></li>
                        <li class="active"><a href="{{route('admin.service.cgzn.sbzl')}}">设备租赁</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form action="{{route('admin.service.cgzn.sbzl_save')}}" method="post" class="form-horizontal ajaxForm">

                            <div class="layui-tab">
                                <ul class="layui-tab-title">
                                    @foreach(config('language') as $k=>$g)
                                        <li @if($k==1) class="layui-this" @endif>{{$g['name']}}</li>
                                    @endforeach
                                </ul>
                                <div class="layui-tab-content">
                                    @foreach(config('language') as $k=>$g)
                                        <div class="layui-tab-item @if($k==1) layui-show @endif">

                                            @foreach(config('exhibit_config.exhibit.content_arr') as $kkk=>$ggg)
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">租赁步骤一({{$g['name']}})</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="step1_{{$k}}" value="{{$data['language'][$k]['step1'] or ''}}" class="form-control"  />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">租赁步骤二({{$g['name']}})</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="step2_{{$k}}" value="{{$data['language'][$k]['step2'] or ''}}" class="form-control"  />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">租赁步骤三({{$g['name']}})</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="step3_{{$k}}" value="{{$data['language'][$k]['step3'] or ''}}" class="form-control"  />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">租赁步骤四({{$g['name']}})</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="step4_{{$k}}" value="{{$data['language'][$k]['step4'] or ''}}" class="form-control"  />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">租赁步骤五({{$g['name']}})</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="step5_{{$k}}" value="{{$data['language'][$k]['step5'] or ''}}" class="form-control"  />
                                                    </div>
                                                </div>

                                            @endforeach

                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    @if (isset($data))
                                        <button class="btn btn-primary" type="submit">保存</button>
                                        <button class="btn btn-white" type="button" onclick="window.history.back()">返回</button>
                                    @else
                                        <button class="btn btn-primary" type="submit">添加</button>
                                        <button class="btn btn-white" type="reset">重置</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')

    <script src="{{cdn('js/plugins/ueditor/ueditor.config.js')}}"></script>
    <script src="{{cdn('js/plugins/ueditor/ueditor.all.min.js')}}"></script>
    <script src="{{cdn('js/plugins/ueditor/lang/zh-cn/zh-cn.js')}}"></script>
    <script>
        layui.use('element', function () {
            var $ = layui.jquery; //Tab的切换功能，切换事件监听等，需要依赖element模块
        });

    </script>
@endsection