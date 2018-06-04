@extends('layouts.public')

@section('head')

@endsection

@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal ajaxForm">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">系统名称</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="system_name" value="{{$setting['system_name'] or ''}}" style=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">系统版本</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="system_version" value="{{$setting['system_version'] or ''}}" style=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <label class="col-sm-2 control-label" style=" width: 20.666667%;">log上传</label>
                                    <div class="webuploader-pick" onclick="upload_resource('log上传','FT_COMMON','logo',1);" style=" float: left; display: inline-block; width: auto;">点击上传图片</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-4">
                                    <div id="logo">
                                        @if(!empty($setting['logo']))
                                            <div class="img-div">
                                                <img src="{{get_file_url($setting['logo'])}}">
                                                <span onclick="del_img($(this))">×</span>
                                                <input type="hidden" name="logo" value="{{$setting['logo'] or ''}}">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{--<div class="form-group">
                                <label class="col-sm-2 control-label">Logo</label>
                                <div class="col-sm-10" id="logo_box">
                                    <div id="logo_picker">选择图片</div>
                                    @if(isset($setting['logo']) && $setting['logo'] != '')
                                        <div class="img-div">
                                            <img src="{{get_file_url($setting['logo'])}}"/>
                                            <span class="cancel">×</span>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="logo" id="logo" value="{{$setting['logo'] or ''}}"/>
                            </div>--}}


                            <div class="form-group">
                                <label class="col-sm-2 control-label">系统简介</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="system_desc">{{$setting['system_desc'] or ''}}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">验证码</label>
                                <div class="col-sm-6 set-lh">
                                    <input class="mt0" type="checkbox" name="captchaadminlogin" value="1" @if(isset($setting['captchaadminlogin']))checked="checked"@endif />
                                    后台登录
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="text-center">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

