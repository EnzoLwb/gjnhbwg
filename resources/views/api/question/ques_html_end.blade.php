<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
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
    <title>问卷结束</title>
    <style type="text/css">
        button, a{
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        /*@font-face {
            font-family: 'pingfang';
            src: url('__ROOT__/public/Common/fonts/pingfang.ttf');
        }*/
        html {
            padding: 0;
            margin: 0;
            overflow: hidden;
        }
        body {
            padding: 0;
            margin: 0;
            line-height: 1.4;
            background: rgba(0, 0, 0, 0);
            font: normal 100% pingfang, San Francisco, Roboto, Microsoft YaHei Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }
        .content{
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .content .intro{
            width: 90%;
            height: 20%;
            margin: 0 auto;
            padding: 20px 0;
            font-size: 1rem;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }
        /**max width 320px**/
        @media only screen and (max-width:450px) {
            .content .intro{
                font-size: 0.9rem;
            }
            .content .ques_content img{
                width: 45%;
            }
        }
        .content .intro p{
            margin: 0;
        }

        .content .ques_content{
            width: 90%;
            /*height: 70%;*/
            margin: 0 auto;
            padding: 60px 30px 5px 30px;
            /*background: url(__ROOT__/public/Common/images/main_bg.png);*/
            background-size: 100% 100%;
            background-repeat: no-repeat;
            position: relative;
            border: none;
            font-size: 1.2rem;
            overflow-x: hidden;
            overflow-y: auto;
            color: #E1BC6D;
            text-align: center;
            /*overflow: hidden;*/
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }
        .content .ques_content img{
            width: 120px;
        }
        .content .ques_content p {
            text-align: center;
            color: #57B4FE;
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
    <div class="ques_content">
        <img src="{{cdn('img/h5view/end.png')}}">
        <p>{{$arr['msg']}}</p>
    </div>
</div>
<script src="/js/plugins/layer/layer.js"></script>
<script type="text/javascript">
    $(function(){
        var clentWidth = window.innerWidth, clientHeight = window.innerHeight;
        $('html, body').css('width', clentWidth).css('height', clientHeight);
    });
</script>
</body>
</html>