<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
    <meta name="format-detection" content="telephone=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <script src="/js/jquery-1.12.4.min.js"></script>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script type="text/javascript">
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <title>{{$arr['title']}}</title>
    <style type="text/css">
        button, a {
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }

        /*@font-face {
            font-family: 'pingfang';
            src: url('__ROOT__/public/Common/fonts/pingfang.ttf');
        }*/
        html {
            padding: 0;
            margin: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        body {
            padding: 0;
            margin: 0;
            line-height: 1.4;
            /*background: #080808;*/
            background: rgba(0, 0, 0, 0);
            font: normal 100% pingfang, San Francisco, Roboto, Microsoft YaHei Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .clearfloat:after {
            display: block;
            clear: both;
            content: "";
            visibility: hidden;
            height: 0;
        }

        .clearfloat {
            zoom: 1;
        }

        .content {
            width: 100%;
            height: 100%;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .content .intro {
            width: 90%;
            margin: 0 auto;
            padding: 20px 0;
            font-size: 22px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }

        /**max width 320px**/
        @media only screen and (max-width: 450px) {
            .content .intro {
                font-size: 0.9rem;
            }
        }
        .content .intro p {
            margin: 0;
            color: #57B4FE;
        }

        .content form {
            width: 90%;
            min-height: 60%;
            margin: 0 auto;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            position: relative;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }

        .content form .ques_content {
            width: 100%;
            height: 92%;
            border: none;
            font-size: 20px;
            padding: 20px;
            overflow-x: hidden;
            overflow-y: auto;
            background: #fff;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }

        .content form .ques_content > div.ques_item {
            /*width: 100%;*/
            min-height: 100%;
            display: none;
            background:rgba(187,187,187,0.2);
            padding: 10px;
        }

        .content form .ques_content > div.ques_item h1 {
            font-size: 24px;
            font-weight: normal;
        }

        .content form .ques_content > div.ques_item.current {
            display: block;
        }

        .answer_content {
            font-size: 20px;
            margin: 1rem 0;
            max-height: 90%;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .content form .action_btn {
            /*position: absolute;*/
            width: 100%;
            height: 40px;
            margin-bottom: 20px;
            /*left: 0;*/
            /*bottom: -30px;*/
            text-align: center;
        }

        .content form .action_btn .btn {
            height: 100%;
            width: 34%;
            min-width: 110px;
            /*background: url(__ROOT__/public/Common/images/btn_bg.png);*/
            background: #57B4FE;
            border-radius: 20px;
            border: none;
            color: #fff;
            line-height: 100%;
            font-size: 16px;
            display: none;
        }

        .content form .action_btn .btn.show {
            display: inline-block;
            margin: 0 20px;
        }

        @media screen and (max-width: 450px) {
            .content form .action_btn .btn.show {
                margin: 0 10px;
            }
        }

        .content form textarea:focus, .content form .action_btn .btn:focus, .content form .action_btn .btn:active {
            outline: none;
        }

        /* radio and checkbox */
        .input-wrap {
            position: relative;
            width: 89%;
            line-height: 26px;
            margin-left: 2%;
            padding: 1px 35px 1px 25px;
            word-wrap:break-word;
        }
        .input-wrap textarea{
            position: absolute;
            z-index: 99;
        }
        @media screen and (max-width: 450px) {
            .input-wrap {
                width: 91%;
            }
        }

        input[type="radio"] {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            opacity: 0;
            filter: alpha(opacity=0);
        }

        input[type="radio"] + span {
            display: inline-block;
            border: 1px solid #57B4FE;
            background: #fff;
            border-radius: 15px;
            width: 20px;
            height: 20px;
            z-index: 0;
            margin-top: 2px;
            margin-right: 10px;
            float: left;
        }

        input[type="radio"]:hover + span {
            border: 1px solid #57B4FE;
        }

        input[type="radio"]:checked + span {
            border: 1px solid #57B4FE;
        }

        input[type="radio"]:checked + span:after {
            content: '';
            display: block;
            width: 16px;
            height: 16px;
            margin-top: 2px;
            margin-left: 2px;
            background-color: #57B4FE;
            border-radius: 50%;
            vertical-align: middle;
        }

        input[type="checkbox"] {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 20px;
            z-index: 10;
            opacity: 0;
            filter: alpha(opacity=0);
        }

        input[type="radio"].default,
        input[type="checkbox"].default {
            width: 15px;
            height: 15px;
            opacity: 1;
            filter: alpha(opacity=1);
        }

        input[type="checkbox"] + span {
            position: absolute;
            left: 0;
            top: 0.5rem;
            display: block;
            border: 1px solid #25C1FA;
            background: #fff;
            /*border-radius: 5px;*/
            width: 16px;
            height: 16px;
            z-index: 0;
        }

        input[type="checkbox"]:hover + span,
        input[type="checkbox"]:checked + span {
            /*border: 1px solid #5D9CEC;*/
        }
        input[type="checkbox"]:checked + span:after {
            content: '';
            display: block;
            width: 8px;
            height: 4px;
            border-left: 3px solid #5D9CEC;
            border-bottom: 3px solid #5D9CEC;
            transform: rotate(-45deg);
            margin-left: 3px;
            margin-top: 4px;
        }
        input[type="button"], button {
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            -webkit-appearance: none;
        }
        input[type="text"] {
            display: block;
            width: 80%;
            height: 28px;
            padding: 3px;
            border: 1px solid #e2e2e2;
            -webkit-appearance: none;
        }

        /*input[type="text"] + input[type="radio"], input[type="text"] + input[type="checkbox"]{
            height: 0;
        }
        input[type="text"] + input[type="radio"] + .input-tag, input[type="text"] + input[type="checkbox"] + .input-tag{
            display: none;
        }*/

        @media only screen and (max-width: 450px) {
            .content form .ques_content > div.ques_item h1 {
                font-size: 1rem;
                font-weight: normal;
            }
            .answer_content {
                font-size: 1rem;
            }
            input[type="radio"] + span {
                border-radius: 50%;
                width: 1rem;
                height: 1rem;
                margin-top: 2px;
                margin-right: 10px;
            }
            input[type="radio"]:checked + span:after {
                width: 0.8rem;
                height: 0.8rem;
                margin-top: 0.1rem;
                margin-left: 0.1rem;
                background-color: #57B4FE;
                border-radius: 50%;
                vertical-align: middle;
            }
        }

        .moveToLeft {
            -webkit-animation: moveToLeft .6s ease both;
            -moz-animation: moveToLeft .6s ease both;
            animation: moveToLeft .6s ease both;
        }

        .moveFromRight {
            -webkit-animation: moveFromRight .6s ease both;
            -moz-animation: moveFromRight .6s ease both;
            animation: moveFromRight .6s ease both;
        }

        /*翻页动画效果*/
        /*左出*/
        @-webkit-keyframes moveToLeft {
            to {
                -webkit-transform: translateX(-100%);
            }
        }

        @-moz-keyframes moveToLeft {
            to {
                -moz-transform: translateX(-100%);
            }
        }

        @keyframes moveToLeft {
            to {
                transform: translateX(-100%);
            }
        }

        /*右进*/
        @-webkit-keyframes moveFromRight {
            from {
                -webkit-transform: translateX(100%);
            }
        }

        @-moz-keyframes moveFromRight {
            from {
                -moz-transform: translateX(100%);
            }
        }

        @keyframes moveFromRight {
            from {
                transform: translateX(100%);
            }
        }

        /*左进*/
        @-webkit-keyframes moveFromLeft {
            from {
                -webkit-transform: translateX(-100%);
            }
        }

        @-moz-keyframes moveFromLeft {
            from {
                -moz-transform: translateX(-100%);
            }
        }

        @keyframes moveFromLeft {
            from {
                transform: translateX(-100%);
            }
        }

        /*右出*/
        @-webkit-keyframes moveToRight {
            to {
                -webkit-transform: translateX(100%);
            }
        }

        @-moz-keyframes moveToRight {
            to {
                -moz-transform: translateX(100%);
            }
        }

        @keyframes moveToRight {
            to {
                transform: translateX(100%);
            }
        }

        ::-webkit-scrollbar {
            -webkit-appearance: none;
            width: 0;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0);
            -webkit-box-shadow: 0 0 1px rgba(255, 255, 255, 0);
        }

    </style>
</head>
<body>
<div class="content">
    <div class="intro">
        <p>{{$arr['a']}}：</p>
        <p>{{$arr['b']}}</p>
    </div>
    <form id="myform" action="{{route('api.question.info',['p'=>$p])}}">
        <div class="ques_content">
            <?php
            $ques_arr = ['1' => $arr['c'], '2' => $arr['d'], '3' => ''];
            //                $option_en=['1'=>'A','2'=>'B','3'=>'C','4'=>'D','5'=>'E','6'=>'F','7'=>'G','8'=>'H','9'=>'I','10'=>'J','11'=>'K','12'=>'L','13'=>'M','14'=>'N'];
            $option_en = ['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E', '5' => 'F', '6' => 'G', '7' => 'H', '8' => 'I', '9' => 'J', '10' => 'K', '11' => 'L', '12' => 'M', '13' => 'N'];

            ?>
            @foreach($info as $k=>$vo)
                <div class="ques_item">
                    <h1>{{$k+1}}、{{$vo['question']}}{{$ques_arr[$vo['type']]}}:</h1>
                    <div class="answer_content">
                        @if($vo['type']==3)
                            <div class="answer_content">
                                <div class="input-wrap clearfloat">
                                    <input type="text" value="" name="ques_option_text{{$k}}"/>
                                </div>
                            </div>
                        @elseif($vo['type']==2)
                            @foreach($vo['option_info'] as $kk=>$g)
                                @if($g['option_type']==1)
                                    <div class="input-wrap clearfloat">
                                        <input type="checkbox" value="{{$g['id']}}" name="ques_option{{$k}}[]">
                                        <span class="input-tag"></span>
                                        {{$option_en[$kk]}}.{{$g['option_info']}}
                                    </div>
                                @else
                                    <div class="input-wrap clearfloat">
                                        <input type="checkbox" value="{{$g['id']}}" name="t_ques_option{{$k}}[]">
                                        <span class="input-tag"></span>
                                        @if($g['option_info']=="不满意(请注明原因)____________")
                                            {{$arr['j']}}
                                        @else
                                            {{$arr['e']}}
                                        @endif
                                        {{--<input type="text" value="" name="ques_option_text{{$k}}"/>--}}
                                        <textarea style="vertical-align: top" name="ques_option_text{{$k}}" id="" cols="30" rows="8"></textarea>
                                    </div>
                                @endif
                            @endforeach
                        @elseif($vo['type']==1)
                            @foreach($vo['option_info'] as $kk=>$g)
                                @if($g['option_type']==1)
                                    <div class="input-wrap clearfloat">
                                        <input type="radio" value="r_{{$g['id']}}" name="ques_option{{$k}}">
                                        <span class="input-tag"></span>
                                        {{$option_en[$kk]}}.{{$g['option_info']}}
                                    </div>
                                @else
                                    <div class="input-wrap clearfloat">
                                        <input style="height: 50%" type="radio" value="t_{{$g['id']}}" name="ques_option{{$k}}">
                                        <span class="input-tag"></span>
                                        @if($g['option_info']=="不满意(请注明原因)____________")
                                            {{$arr['j']}}
                                        @else
                                            {{$arr['e']}}
                                        @endif
                                        <input type="text" value="" name="ques_option_text{{$k}}"/>
                                        {{--<textarea style="vertical-align: top" name="ques_option_text{{$k}}" id="" cols="30" rows="8"></textarea>--}}
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        <input type="hidden" value="{{$vo['type']}}" name="ques_type{{$k}}"/>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="action_btn">
            <input type="hidden" name="num" value="{{$num}}">
            <input type="hidden" name="ques_id" value="{{$ques_id}}">
            <input type="button" class="btn-prev btn" value="{{$arr['f']}}">
            <input type="button" class="btn-next btn" value="{{$arr['g']}}">
            <input type="button" id="ajax_submit" class="btn-submit btn" value="{{$arr['h']}}">
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        var clentWidth = window.innerWidth, clientHeight = window.innerHeight;
        $('html, body').css('width', clentWidth).css('height', clientHeight);

        if ($('.ques_item').length == 1) {
            $('.btn-submit').addClass('show');
        } else {
            $('.btn-next').addClass('show');
        }

        $('.ques_item').eq(0).addClass('current');

        // 2017-05-02改为下一题按钮切换
        // $('input[type="radio"]').click(function(){
        $('.btn-next').click(function () {
            var nownum = $('.current').index() + 1, showNum = $('.current').index() + 2,
                allNum = +$('.ques_item').length;
            if ($('.current' + ' :checked').length == 0) {
                layer.msg('请选择答案', {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
                return false;
            } else {
                if (nownum == allNum) {
                } else {
                    var hideDom = $('.current'), showDom = $('.current').next();
                    hideDom.removeClass('current');
                    hideDom.addClass('moveToLeft');
                    showDom.addClass('current').addClass('moveFromRight');
                    setTimeout(function () {
                        hideDom.removeClass('moveToLeft');
                        showDom.removeClass('moveFromRight');
                    }, 600);
                }
                if (showNum == allNum) {
                    $('.btn-next').removeClass('show');
                    $('.btn-prev').addClass('show');
                    $('.btn-submit').addClass('show');
                } else {
                    $('.btn-prev').addClass('show');
                }
            }
        });
        $('.btn-prev').click(function () {
            var nownum = $('.current').index() + 1, showNum = $('.current').index() - 1,
                allNum = +$('.ques_item').length;
            if (nownum == 0) {
            } else {
                var hideDom = $('.current'), showDom = $('.current').prev();
                hideDom.removeClass('current');
                hideDom.addClass('moveToRight');
                showDom.addClass('current').addClass('moveFromLeft');
                setTimeout(function () {
                    hideDom.removeClass('moveToRight');
                    showDom.removeClass('moveFromLeft');
                }, 600);
            }
            if (showNum == 0) {
                $('.btn-prev').removeClass('show');
                $('.btn-submit').removeClass('show');
                $('.btn-next').addClass('show');
            } else {
                $('.btn-next').addClass('show');
                $('.btn-submit').removeClass('show');
            }
        });

        $('input[type="text"]').on('focus', function () {
            var that = $(this);
            if (that.prev().prev().attr('type') == 'radio' || that.prev().prev().attr('type') == 'checkbox') {
                if (!that.prev().prev().is(':checked')) {
                    that.prev().prev().trigger('click');
                }
            }
        });
    });
    $("#ajax_submit").click(function () {
        layer.msg("{{$arr['i']}}", {icon: 16, scrollbar: false, shade: [0.3, '#393D49'], time: 0});
        var ajax_url = $('#myform').attr('action');
        $.post(ajax_url, $('#myform').serialize(),
            function (data) {
                console.log(data);
                layer.closeAll();
                if (data.code == 'error') {
                    layer.msg(data.info, {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
                }
                else if (data.code == 'success') {
                    location.href = '{{route('api.question.info')}}' + '?p={{$p}}&question_id={{$question_id}}' + "&end=1";
                    //layer.msg(data.info,{icon: 6,scrollbar: false,time: 1000,shade: [0.3, '#393D49']});
                }
            })
    })
</script>
<script src="/js/plugins/layer/layer.js"></script>
</body>
</html>