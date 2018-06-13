@extends('layouts.public')

@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
    <link rel="stylesheet" href="{{cdn('js/plugins/ueditor/themes/default/css/ueditor.css')}}">
    <link rel="stylesheet" href="{{cdn('js/plugins/layui/css/layui.css')}}">
    <style type="text/css">
        input[type=checkbox]{
            margin-top: 8px;
        }
    </style>
@endsection

@section('bodyattr')class="gray-bg"@endsection
<style>
    form {
        margin: 30px 50px;
    }

    a.btn {
        height: 23px;
        line-height: 23px;
    }

    .layer-msg-key {
        vertical-align: top;
    }

    /*input type=file 美化*/
    .a-upload {
        padding: 3px 150px;
        height: 25px;
        line-height: 25px;
        position: relative;
        cursor: pointer;
        color: #888;
        background: #F0F0F0;
        border: 1px solid #737373;
        /* border-radius: 4px; */
        overflow: hidden;
        display: inline-block;
        width: 180px;
        font-size: 16px;
    }

    .a-upload input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
        filter: alpha(opacity=0);
        cursor: pointer
    }

    .a-upload:hover {
        color: #444;
        background: #eee;
        border-color: #ccc;
        text-decoration: none
    }

    .layer-msg-group > div.layer-radio-div {
        width: 280px;
    }
</style>
<body>

<div class="wrapper wrapper-content">

    <div class="row m-b">
        <div class="col-sm-12">
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li><a href="{{route('admin.interaction.learn.question_list')}}">题库列表</a></li>
                    <li class="active"><a href="{{route('admin.interaction.learn.add_question')}}">添加题目</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <form action="{{route('admin.interaction.learn.save_question')}}" method="post" class="form-horizontal ajaxForm">
                        <input type="hidden" name="id" value="{{$learn['id'] or 0}}"/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">题目名称</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="title" value="{{$learn['title'] or ''}}" required/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">选项</label>
                            <div class="col-sm-4" id="optionbox">
                                @if(isset($learn_option))
                                @foreach($learn_option as $key=>$option)
                                    <div class="answer-item">
                                        <label class="radio-label" for="radio-{{$key}}">
                                            <input type="radio" name="isanswer" id="radio-{{$key}}" value="{{$key}}" @if($option['isanswer']==1) checked="" @endif >此项为答案</label>
                                        <input type="text" name="option[]" value="{{$option['option']}}" required>
                                        @if($key > 2)
                                            <i class="fa fa-remove remove-anwser" title='删除此项'></i>
                                        @endif
                                    </div>
                                @endforeach
                                @endif
                                <input type="button" value="添加选项" id="addoption">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <button class="btn btn-white" type="button" onclick="window.history.back()">返回</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script src="{{cdn('js/plugins/webuploader/webuploader.nolog.min.js')}}"></script>
    <script src="{{cdn('js/plugins/webuploader/webuploader_public.js')}}"></script>
    <script type="text/javascript">
        jQuery(function ($) {
            $('#addoption').click(function () {
                var ai = $('#optionbox input:text').length;
                if(ai >= 10){
                    layer.msg("最多只能添加十个选项！",{icon: 5,scrollbar: false,time: 2000,shade: [0.3, '#393D49']});
                    return false;
                }
                $(this).before("<div class='answer-item'><label class='radio-label' for='radio-"+ai+"'><input type='radio' name='isanswer' id='radio-"+ai+"' value='"+ai+"' required>此项为答案</label><input type='text' name='option[]' required /><i class='fa fa-remove remove-anwser' title='删除此项'></i></div>");
                intClick();
            });
            $('label[for="'+ $('input[type="radio"]:checked').attr('id') +'"]').addClass('check');
            $("#optionbox").on("click",".radio-label",function(){
                var that = $(this);
                if (that.hasClass("check")) {
                    return false;
                }else{
                    if(that.hasClass("radio-label")){
                        $('.radio-label').removeClass('check');
                        that.addClass('check');
                        that.find('input').attr('checked','checked');
                    }
                }
            });

            function intClick(){
                $('.remove-anwser').click(function(){
                    var that = $(this);
                    that.parent().remove();
                });
            }
            intClick();
        });
    </script>
@endsection