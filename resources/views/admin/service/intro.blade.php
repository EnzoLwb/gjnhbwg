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
                        <li class="active"><a href="{{route('admin.service.intro')}}">场馆简介</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form action="{{route('admin.service.intro.save')}}" method="post" class="form-horizontal ajaxForm">
                            <input type="hidden" name="id" value="{{$data->id or 0}}"/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">APP图片</label>
                                <div class="col-sm-10" id="extfile_box">
                                    <div id="extfile_picker">选择图片（支持多图）</div>
                                    @if(isset($data) && is_array($data->extfile_img))
                                        @foreach($data->extfile_img as $ext_img)
                                            <div class="img-div">
                                                <img src="{{get_file_url($ext_img)}}" val="{{$ext_img}}"/>
                                                <span class="cancel">×</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <input type="hidden" id="extfile" name="imgs" value="{{$data->imgs or ''}}"/>
                                <input type="hidden" id="extfile_file_id" name="extfile_file_id" value=""/>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">导览机场馆简介图片</label>
                                <div class="col-sm-10" id="t_extfile_box">
                                    <div id="t_extfile_picker">选择图片（支持多图）</div>
                                    @if(isset($data) && is_array($data->d_extfile_img))
                                        @foreach($data->d_extfile_img as $ext_img)
                                            <div class="img-div">
                                                <img src="{{get_file_url($ext_img)}}" val="{{$ext_img}}"/>
                                                <span class="cancel">×</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <input type="hidden" id="t_extfile" name="d_imgs" value="{{$data->d_imgs or ''}}"/>
                                <input type="hidden" id="t_extfile_file_id" name="t_extfile_file_id" value=""/>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">标题</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="title" value="{{$data->title or ''}}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">内容</label>
                                <div class="col-sm-6">
                                    <textarea name="content"  id="content">{{$data->content or ''}}</textarea>
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
    <script type="text/javascript">
        jQuery(function ($) {
            var ue = UE.getEditor('content');

            ue.ready(function () {
                ue.execCommand('serverparam', {
                    '_token': '{{csrf_token()}}',
                    'filetype': 'FT_ARTICLE_DESC',
                    'itemid': '{{$data->id or 0}}'
                });
            });
        });
    </script>
    <script src="{{cdn('js/plugins/webuploader/webuploader.nolog.min.js')}}"></script>
    <script src="{{cdn('js/plugins/webuploader/webuploader_public.js')}}"></script>
    <script type="text/javascript">

        jQuery(function ($) {
            singleUpload({
                _token: '{{csrf_token()}}',
                type_key: 'FT_INTRO',
                item_id: '{{$data->id or 0}}',
                pick: 'extfile_picker',
                boxid: 'extfile_box',
                file_path: 'extfile',
                file_id: 'extfile_file_id',
                multi: true,
                maximg: 3
            });
            $('#extfile_box').find('.img-div>span').click(function () {
                sUploadDel($(this), 'extfile', true);
            });
        });
        jQuery(function ($) {
            singleUpload({
                _token: '{{csrf_token()}}',
                type_key: 'FT_INTRO',
                item_id: '{{$data->id or 0}}',
                pick: 't_extfile_picker',
                boxid: 't_extfile_box',
                file_path: 't_extfile',
                file_id: 't_extfile_file_id',
                multi: true,
                maximg: 3
            });
            $('#t_extfile_box').find('.img-div>span').click(function () {
                sUploadDel($(this), 't_extfile', true);
            });
        });
    </script>
@endsection