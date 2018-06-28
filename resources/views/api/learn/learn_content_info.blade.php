<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <!-- Link Swiper's CSS -->
    <link href="{{cdn('js/dist/css/swiper.min.css')}}" rel="stylesheet" type="text/css">
    {{--<link href="__PUBLIC__/simpleboot/themes/{:C('SP_ADMIN_STYLE')}/theme.min.css" rel="stylesheet">--}}
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
    <script src="{{cdn('js/bootstrap.min.js')}}"></script>
    <script src="{{cdn('js/plugins/layer/layer.js')}}"></script>
    <link href="{{cdn('/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <!-- Demo styles -->
    <style>
        html {
            background-color: #fff;
        }
        body {
            background: #fff;
            font-family: "Microsoft Yahei";
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 0;
            -moz-user-select: -moz-none;
            -moz-user-select: none;
            -o-user-select: none;
            -khtml-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        ul, li {
            list-style-type: none;
        }
        .layui-layer {
            border-radius: 8px;
        }
        .swiper-container {
            position: relative;
            width: 90%;
            min-height: 400px;
            margin: 0px auto 20px;
            background-color: #f5f5f5;
        }

        div, a, button, input, textarea {
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
        }

        label {
            display: inline-block;
        }

        #option-div .score-div {
            height: 100px;
            margin: 20px;
        }

        #option-div .score-div > .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 40px;
            background-color: #57B4FE;
            float: left;
        }

        .score-circle-div {
            display: table;
            margin: auto;
            height: 80px;
            line-height: 80%;
            text-align: center;
        }

        .score-div > .score-circle label {
            color: #fff
        }

        .score-div > .score-circle .score-label {
            margin-top: 30px;
            margin-left: 10px;
            font-size: 30px;
        }

        #option-div .score-div > .score-title {
            float: right;
            height: 100px;

        }

        #option-div .score-div > .score-title label {
            color: #555;
            margin-top: 30px;
            font-size: 22px;
        }

        .qa-div {
            width: 95%;
            margin: auto;
        }

        ul {
            margin: 20px;
        }

        li {
            line-height: 30px;
            background-color: #a1a1a1;
            border-radius: 15px;
            color: #fff;
            margin: 10px auto;
        }

        li.right-focus {
            background-color: #57B4FE;
        }

        li.wrong-focus {
            background-color: #ee4f5b;
        }

        li > .option-label {
            margin-top: 3px;
            height: 24px;
            width: 24px;
            line-height: 24px;
            border-radius: 20px;
            background-color: #57B4FE;
            float: left;
            margin-left: 3px;
        }

        li > .wrong-option-label {
            background-color: #ee4f5b;
        }

        li > .right-option-label {
            background-color: #57B4FE;
        }

        .answer-label {
            width: 85%;
            word-break: break-all;
        }

        .num-div {
            position: absolute;
            bottom: 0;
            right: 10px;
            color: #555;
        }

        .num-div label {
            font-size: 20px;
        }

       /* .btn-div > .btn {
            display: block;
            width: 40%;
            height: 40px;
            margin: 20px auto;
            border-radius: 20px;
        }

        .btn-div > .yellow-btn {
            background: #57B4FE;
            color: #fff;
            border: 1px solid #57B4FE;
            font-size: 18px;
        }*/

        .layer-div {
            display: none;
            width: 100%;
            height: 100px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 14px;
        }

        .layer-div > .next {
            display: block;
            color: #57B4FE;
            border-top: 1px solid #57B4FE;
        }

        .layer-div > .layer-div-img {
            display: block;
            margin: 10px auto;
        }

        .layer-div > label {
            display: block;
            margin: 10px auto 0;
            text-align: center;
            font-size: 20px;
        }

        .layer-div > label.next {
            padding-top: 10px;
            font-size: 16px;
        }

        .back-div1 {
            width: 70%;
            margin: 50px auto 0;
            background-color: rgba(249, 249, 249, 0.9);
            height: 5px;
        }

        .back-div2 {
            width: 80%;
            margin: auto;
            background-color: rgba(249, 249, 249, 0.9);
            height: 5px;
        }

        .fa {
            color: #fff;
        }

        .answer-div {
            margin-bottom: 50px;
        }

        #option-div {
            /*display: none;*/
        }

        #list-div {
            display: none;
            background-color: #f7f7f7;
            color: #aaa;
            margin-top: 20px;
        }

        #list-div label {
            display: inline-block;
        }

        #list-div .score-div {
            width: 300px;
            height: 100px;
            margin: 0px auto;
        }

        #list-div .score-div > .score-circle {
            width: 100px;
            height: 100px;
            border-radius: 50px;
            background-color: #57B4FE;
            float: left;
        }

        #list-div .score-div > .score-circle > .center-label {
            margin: auto 36px;
        }

        #list-div .score-div > .score-circle > .time-label {
            margin-left: 22px;
        }

        #list-div .score-title {
            float: left;
            height: 100px;
        }

        #list-div .score-div > .score-title label {
            color: #57B4FE;
            margin-top: 30px;
            margin-left: 15px;
            font-size: 23px;

        }

        #list-div .score-label {
            margin-top: 20px;
        }

        .table-div {
            width: 90%;
            margin: auto;
            margin-top: 20px;
            border: 1px solid #eee;
            box-shadow: 0px 0px 4px #ccc;
        }

        .table-div > .table {
            margin-bottom: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-div > .table th, .table-div > .table td {
            text-align: center;
            padding: 5px;
        }

        .table-div > .table > caption {
            height: 40px;
            color: #57B4FE;
            font-size: 18px;
            line-height: 40px;
        }

        .table-div > .table > thead > tr {
            background-color: #57B4FE;
            color: #fff;
        }

        .table-div > .table > tbody > tr:nth-child(even) {
            background-color: #f0eeec;
        }

        .table-div > .table > tbody > tr:nth-child(odd) {
            background-color: #fff;
        }

        .table-div > .table > tbody > .self-tr {
            color: #57B4FE;
        }
        .btn-div{
            text-align: center;
        }
        .btn-div > .btn {
            /*display: block;*/
            width: 45%;
            height: 40px;
            margin: 10px auto 0;
            border-radius: 20px;
            font-size: 18px;
            outline: none;
        }

        .btn-div > .yellow-btn {
            background: #57B4FE;
            color: #fff;
            border: 1px solid #57B4FE;
        }

        .btn-div > .white-btn {
            background: #BBBBBB;
            border: 1px solid #BBBBBB;
            color: #fff;
        }
        #again-btn,#answer-btn{
            width:40%;
            display: inline-block;
        }
        #return-btn {
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
<div id="option-div">
    <div class="back-div1"></div>
    <div class="back-div2"></div>
    <div class="swiper-container">
        <div class="score-div">
            {{--<div class="score-circle">
                <div class="score-circle-div"><label class="score-label" id="score">0</label><label>分</label></div>
            </div>--}}
            <div class="score-title">
                <label>耗时：</label><label class="time-label" id="timershow">00:00:00</label>
            </div>
        </div>
        <div class="swiper-wrapper">
            @foreach($list as $k=>$v)
                <div class="swiper-slide">
                    <div class="qa-div">
                        <div class="question-div">{{$v['title']}}</div>
                        <div class="answer-div">
                            <ul>
                                @foreach($v['option'] as $kk=>$vv)
                                    <li class="option-li @if($vv['isanswer']==1) right-li @endif" id="{{$v['id']}}_{{$vv['id']}}">
                                        <label class="option-label">{{$option_title[$kk]}}</label>
                                        <label class="answer-label">{{$vv['option']}}</label>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="num-div"><label class="num-label">1</label><label>/10</label></div>
    </div>
    <div class="btn-div">
        <button type="button" id="prev-btn" class="btn white-btn" style="display:none;">
            上一题
        </button>
        <button type="button" id="next-btn" class="btn yellow-btn" style="display:none;">
            下一题
        </button>
    </div>
    {{--<div class="btn-div">
        <button type="button" id="next-btn" class="btn yellow-btn" style="display:none;">
            下一题
        </button>
    </div>--}}
    <div class="btn-div">
        <button type="button" id="return-btn" class="btn yellow-btn" style="display:none;">
            返回榜单
        </button>
    </div>
    <div id="right-layer-div" class="layer-div">
        <img class="layer-div-img" src="{{cdn('morder/images/right.png')}}"/>
        <label>恭喜你答对了！</label>
        <label class="next">下一题 </label>
    </div>
    <div id="wrong-layer-div" class="layer-div">
        <img class="layer-div-img" src="{{cdn('morder/images/wrong.png')}}"/>
        <label>很遗憾，你答错了...</label>
        <label class="next">下一题</label>
    </div>
</div>
<div id="list-div">
    <div class="score-div">
        <div class="score-circle">
            <div class="score-circle-div">
                <label class="score-label" id="result"></label>
                <label>%</label>
                <br/><br/>
                <label class="center-label">耗时</label>
                <br/><br/>
                <label class="time-label" id="lasttime">00:00:00</label>
            </div>
        </div>
        <div class="score-title">
            <label>恭喜，完成答题！</label>
        </div>
    </div>
    <div class="table-div">
        <table class="table table-bordered table-striped">
            <caption>总排行榜</caption>
            <thead>
            <tr>
                <th>排名</th>
                <th>姓名</th>
                <th>得分</th>
                <th>日期</th>
            </tr>
            </thead>
            <tbody id="list">
            </tbody>
        </table>
    </div>
    <div style="text-align: center" class="btn-div">
        <button type="button" id="again-btn" class="btn white-btn">
            再来一次
        </button>
        <button type="button" id="answer-btn" class="btn yellow-btn" >
            查看答案
        </button>
    </div>
</div>
<!-- Swiper JS -->
<script src="{{cdn('js/dist/js/swiper.min.js')}}"></script>

<!-- Initialize Swiper -->
<script>
    //$("#next-btn,#prev-btn").hide();
    $(function () {
        var time = 0;
        var timer = setInterval(function () {
            time = time + 1;
            $('#timershow').html(timerFormat(time));
        }, 1000);
        var answer = [];
        var count = 1;
        var swiper = new Swiper('.swiper-container', {
            onlyExternal: true,
            effect: 'fade',
            fade: {
                crossFade: true,
            }
        });
        $(".swiper-wrapper").on("click", ".option-li", function () {
            if (!!$(this).parent().attr("data-read")) {
                return false;
            }
            $(this).parent().attr("data-read", true);

            var content = $('#right-layer-div');
            if (/right-li/.test($(this).attr("class"))) {
                showRight($(this));
                $('#score').html(parseInt($('#score').html()) + 10);
                answer.push(this.id);
            } else {
                content = $('#wrong-layer-div');
                showWrong($(this));
                showRight($(this).parent().find(".right-li"));
            }

            if (count == 10) {
                content.find(".next").text("完成答题");
            }
            layer.open({
                type: 1,
                title: false,
                closeBtn: 0,
                area: ['80%', '154px'],
                offset: ["110px"],
                shade: [0.3, '#393D49'],
                content: content //这里content是一个DOM，这个元素要放在body根节点下
            });
        });
        $(".next").click(function () {
            if (count < 10) {
                count++;
                layer.closeAll();
                swiper.slideNext();
                $(".num-label").text(count);
                $("#prev-btn").show();
            } else {
                $.ajax({
                    type: "POST",
                    url: "{{route('api.learn.save_answer')}}",
                    data: {
                        timecost: time,
                        answer: answer,
                        p:"{{$p}}",
                        rela_id:{{$rela_id}},
                        type_id:{{$type_id}},
                        uid:{{$uid}}
                    },
                    success: function (newid) {
                        $.get("{{route('api.learn.answer_list')}}", {type_id:{{$type_id}},rela_id:{{$rela_id}},p:"{{$p}}",newid:newid}, function (data) {
                            $('#list').append(data);
                            $("#option-div").hide();
                            $("#list-div").show();
                            $("html").css("background-color", "#f5f5f5");
                        });
                    }
                });
                layer.closeAll();
                clearInterval(timer);
                $("#result").html($('#score').html());
                $('#lasttime').html(timerFormat(time));
            }
        });

        $("#next-btn").click(function () {
            if (count < 10) {
                $(".num-label").text(++count);
                $("#prev-btn").show();
            }
            swiper.slideNext();
            if ($("ul[data-read]").length == $(".num-label").text() - 1 || count == 10) {
                $("#next-btn").hide();
            }
        });

        $("#prev-btn").click(function () {
            $("#next-btn").show();
            if (count > 1) {
                $(".num-label").text(--count);
            }
            swiper.slidePrev();
            if (count == 1) {
                $("#prev-btn").hide();
            }
        });

        $("#answer-btn").click(function () {
            $("#list-div").hide();
            $("#option-div").show();
            swiper.slideTo(0);
            count = 1;
            $(".num-label").text(1);
            $("#prev-btn").hide();
            $("#next-btn,#return-btn").show();
            $("html").css("background-color", "#fff");
        });
        $("#return-btn").click(function () {
            $("#option-div").hide();
            $("#list-div").show();
            $("html").css("background-color", "#f7f7f7");
        });
        $("#again-btn").click(function () {
            window.location.reload();
        });
        function showRight(ul) {
            ul.addClass("right-focus").find(".option-label").empty().append("<i class='fa fa-check'></i>").addClass("right-option-label");
        }

        function showWrong(ul) {
            ul.addClass("wrong-focus").find(".option-label").empty().append("<i class='fa fa-close'></i>").addClass("wrong-option-label");
        }
    });

    function timerFormat(time) {
        var hour = parseInt(time / 3600);
        var min = parseInt(time / 60) % 60;
        var sec = time % 60;
        return zero(hour) + ':' + zero(min) + ':' + zero(sec)
    }

    function zero(c) {
        if (c < 10) {
            return '0' + c;
        } else {
            return c;
        }
    }
</script>
</body>
</html>