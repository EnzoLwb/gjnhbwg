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
                            <input type="hidden" name="id" value="{{$data['id'] or 0}}"/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">APP图片</label>
                                <div class="col-sm-10" id="extfile_box">
                                    <div id="extfile_picker">选择图片（支持多图）</div>
                                    @if(isset($data) && is_array($data['extfile_img']))
                                        @foreach($data['extfile_img'] as $ext_img)
                                            <div class="img-div">
                                                <img src="{{get_file_url($ext_img)}}" val="{{$ext_img}}"/>
                                                <span class="cancel">×</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <input type="hidden" id="extfile" name="imgs" value="{{$data['imgs'] or ''}}"/>
                                <input type="hidden" id="extfile_file_id" name="extfile_file_id" value=""/>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">导览机场馆简介图片</label>
                                <div class="col-sm-10" id="t_extfile_box">
                                    <div id="t_extfile_picker">选择图片（支持多图）</div>
                                    @if(isset($data) && is_array($data['d_extfile_img']))
                                        @foreach($data['d_extfile_img'] as $ext_img)
                                            <div class="img-div">
                                                <img src="{{get_file_url($ext_img)}}" val="{{$ext_img}}"/>
                                                <span class="cancel">×</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <input type="hidden" id="t_extfile" name="d_imgs" value="{{$data['d_imgs'] or ''}}"/>
                                <input type="hidden" id="t_extfile_file_id" name="t_extfile_file_id" value=""/>
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
                                                <label class="col-sm-2 control-label">名称({{$g['name']}})</label>
                                                <div class="col-sm-4">
                                                    <input type="text" name="title_{{$k}}" value="{{$data['language'][$k]['title'] or ''}}" class="form-control"  />
                                                </div>
                                            </div>

                                            @foreach(config('exhibit_config.exhibit.content_arr') as $kkk=>$ggg)
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">简介({{$g['name']}})</label>
                                                    <div class="col-sm-4">
                                                        <script type="text/plain" id="{{$k}}_content" name="content_{{$k}}">{!! $data['language'][$k]['content'] or '' !!}</script>
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
        //编辑器路径定义
        var initialWidth = $(window).width() > 1366 ? 950 : 705;
        var initialHeight = $(window).width() > 1366 ? 350 : 200;
        @foreach(config('language') as $k=>$g)
            editorcontent_{{$k}}= new baidu.editor.ui.Editor({pasteplain:true,initialFrameWidth:950,initialFrameHeight:300,wordCount:false,elementPathEnabled:false,autoHeightEnabled:false,initialStyle:'img{width:20%;}',@if($k==10)iframeCssUrl:'{{cdn('js/plugins/ueditor/themes/vertical_mengyu.css')}}', @endif toolbars: [[
            'fullscreen', 'source', '|','undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'simpleupload','emotion', '|',
            'horizontal', 'date', 'time', 'spechars', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts'
        ]]});
        editorcontent_{{$k}}.render('{{$k}}_content');
        editorcontent_{{$k}}.ready(function () {
            editorcontent_{{$k}}.execCommand('serverparam', {
                '_token': '{{csrf_token()}}',
                'filetype': 'FT_EXHIBIT_ONE',
                'itemid': '{{$article->article_id or 0}}'
            });
        });
        @endforeach

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