@extends('layouts.public')
@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
@endsection
@section('bodyattr')class=""@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.service.wenchuang')}}">产品列表</a></li>
                        @if(isset($data))
                        <li class="active"><a href="{{route('admin.service.wenchuang.edit',$id)}}">编辑产品</a></li>
                        @else
                        <li class="active"><a href="{{route('admin.service.wenchuang.add')}}">添加产品</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="{{route('admin.service.wenchuang.save')}}" method="post" class="form-horizontal ajaxForm">
                        <input type="hidden" name="id" value="{{$data['id'] or 0}}"/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">标题</label>
                            <div class="col-sm-4">
                               <input name="title" class="form-control"  required value="{{$data['pro_title'] or ''}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">所属系列</label>
                            <div class="col-sm-4">
                                <select name="xl_id" class="form-control" required >
                                    <option value="">请选择系列</option>
                                    @foreach($xl as $k=>$v)
                                        <option value="{{$v->id}}" @if(isset($data['xl_id'])&&$data['xl_id']==$v->id) selected @endif >{{$v->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">显示顺序（降序排列）</label>
                            <div class="col-sm-4">
                                <input name="order_no" class="form-control"  required value="{{$data['order_no'] or 255}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">图片</label>
                            <div class="col-sm-10" id="extfile_box">
                                <div id="extfile_picker">选择图片</div>
                                @if(isset($data))
                                    <div class="img-div">
                                        <img src="{{$data['pro_img']}}" val="{{$data['pro_img']}}"/>
                                        <span class="cancel">×</span>
                                    </div>
                                @endif
                            </div>

                            <input type="hidden" id="extfile" name="img" value="{{$data['pro_img'] or ''}}"/>
                            <input type="hidden" id="extfile_file_id" name="extfile_file_id" value=""/>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">状态</label>
                            <div class="col-sm-10">
                                <div class="input-group m-t-xs-2">
                                    <input type="radio" name="is_show" value="1"
                                           @if ((isset($data->is_show) ? $data->is_show : '') != '2') checked="checked"@endif/>显示
                                    <input type="radio" name="is_show" value="2"
                                           @if ((isset($data->is_show) ? $data->is_show : '') == '2') checked="checked"@endif/>不显示
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">简介</label>
                            <div class="col-sm-4">
                                <script type="text/plain" id="content" name="content">{!! $data['pro_content'] or '' !!}</script>
                            </div>
                        </div>


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
                editorcontent= new baidu.editor.ui.Editor({pasteplain:true,initialFrameWidth:initialWidth,initialFrameHeight:initialHeight,wordCount:false,elementPathEnabled:false,autoHeightEnabled:false,initialStyle:'img{width:20%;}',toolbars: [[
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
        editorcontent.render('content');
        editorcontent.ready(function () {
            editorcontent.execCommand('serverparam', {
                '_token': '{{csrf_token()}}',
                'filetype': 'FT_EXHIBIT_ONE',
                'itemid': '0'
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
                maximg: 1
            });
            $('#extfile_box').find('.img-div>span').click(function () {
                sUploadDel($(this), 'extfile', true);
            });
        });
    </script>
@endsection
