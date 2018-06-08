@extends('api.common.public')

@section('title'){{$info->exhibit_name or ''}}@endsection

@section('head')
    <style>
        .clear{
            clear: both;
        }
        .exhibition-info{
            position: relative;
        }
        .exhibition-info .info-img{
            width: 100%;
            position: absolute;
            top: 0;
            z-index: -1;
        }
        .exhibition-info .info-name{
            max-height: 100px;
            color: #fff;
            text-align: center;
            padding: 20px 5%;
            background: linear-gradient(to bottom,rgba(0,0,0,.5) 0,rgba(0,0,0,0) 100%);
            overflow: hidden;
        }
        .exhibition-info .info-content{
            width: 90%;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            padding: 15px 20px 10px;
            -webkit-box-shadow: 0 0 10px #a2a2a2;
            -moz-box-shadow: 0 0 10px #a2a2a2;
            -ms-box-shadow: 0 0 10px #a2a2a2;
            -o-box-shadow: 0 0 10px #a2a2a2;
            box-shadow: 0 0 10px #a2a2a2;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }
        .exhibition-info .info-content .info-con{
            height: 125px;;
            font-size: .95rem;
            line-height: 25px;
            text-align: justify;
            overflow: hidden;
            -webkit-transition: all .3s ease-in-out;
            -moz-transition: all .3s ease-in-out;
            -ms-transition: all .3s ease-in-out;
            transition: all .3s ease-in-out;
        }
        .exhibition-info .info-content .open{
            height: auto;
        }
        .exhibition-info .info-content .info-content-btn{
            width: 30px;
            height: 30px;
            display: block;
            margin: 5px auto 0;
            background: url("{{cdn('img/html/ico_arrow_down.png')}}") no-repeat;
            -webkit-transition: all .3s ease-in-out;
            -moz-transition: all .3s ease-in-out;
            -ms-transition: all .3s ease-in-out;
            transition: all .3s ease-in-out;
        }
        .exhibition-info .info-content .up{
            -webkit-transform: rotate(180deg);
            -moz-transform: rotate(180deg);
            -ms-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .exhibition-title{
            padding: 0 5%;
            font-size: 1.2rem;
            margin: 15px 0;
        }

        .exhibition-list{
            padding: 0 3% 20px;
        }
        .exhibition-list li{
            width: 46%;
            margin: 0 2%;
            float: left;
        }
        .exhibition-list .list-img{
            width: 100%;
            display: block;
            border-radius: 10px;
        }
        .exhibition-list .list-tit{
            color: #404040;
            padding: 0 5px;
            height: 30px;;
            line-height: 30px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
        }
    </style>

@endsection

@section('body')
    <div class="content">
        <div class="exhibition-info">
            <img class="info-img" src="{{$info['exhibition_info']['exhibition_img'] or ''}}">
            <div class="info-name">{{$info['exhibition_info']['exhibition_name'] or ''}}</div>
            @if(isset($language)&&$language!=10)
                <div class="info-content">
                    <p style="font-size: 1.2rem;margin-bottom: 5px">{{trans("base.exhibit_ztjs")}}</p>
                    <div class="info-con">
                        {!! $info['exhibition_info']['content'] or '' !!}
                    </div>
                    <a class="info-content-btn"></a>
                </div>
            @endif
        </div>
        <div class="exhibition-title">{{trans("base.exhibit_rmzp")}}</div>
        <ul class="exhibition-list">
            @foreach($info['exhibit_list'] as $k=>$g)
            <li>
                <a href="{{$g['url']}}" target="_blank">
                    <img class="list-img" src="{{$g['exhibit_list_img']}}">
                    <p class="list-tit">{{$g['exhibit_name']}}</p>
                </a>
            </li>
            @endforeach
            <div class="clear"></div>
        </ul>


    </div>
@endsection
@section('script')
    <script>
        $(".exhibition-info .info-img").css("height",($("body").width()*416)/750);
        $(".exhibition-info .info-content").css({"margin-top":$(".exhibition-info .info-img").height()-120,"width":$("body").width()});
        $(".info-content-btn").click(function(){
            $(this).toggleClass("up");
            $(".exhibition-info .info-content .info-con").toggleClass("open");
        });
        $(".exhibition-list .list-img").css("height",($(".exhibition-list .list-img").width()*2)/3);
    </script>
@endsection
