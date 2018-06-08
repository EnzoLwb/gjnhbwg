@extends('api.common.public')

@section('title'){{$info->exhibition_name or ''}}@endsection

@section('head')
    <style>
        body{
            background-size: 100% 100%;
            color: #000000;
        }
        .clear{
            clear: both;
        }
        .content-title{
            width: 70%;
            /*height: 35px;*/
            font-size: 1.25rem;
            line-height: 35px;
            padding: 0 0;
            float: left;
            /*overflow: hidden;*/
            /*text-overflow: ellipsis;*/
            /*display: -webkit-box;*/
            /*-webkit-line-clamp: 1;*/
            -webkit-box-orient: vertical;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }
        .btn_change2{
            width: 25%;
            height: 35px;
            font-size: .8rem;
            line-height: 35px;
            background: url("{{cdn('img/html/ico_knowledge.png')}}") no-repeat center left;
            background-size: auto 70%;
            padding-left: 35px;
            float: right;
            overflow: hidden;
            cursor: pointer;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }
        .btn_change1{
            width: 25%;
            height: 35px;
            font-size: .8rem;
            line-height: 35px;
            background: url("{{cdn('img/html/ico_say.png')}}") no-repeat center left;
            background-size: auto 70%;
            padding-left: 35px;
            float: right;
            overflow: hidden;
            cursor: pointer;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }
        .content-info{
            font-size: 1rem;
            line-height: 1.8;
            text-align: justify;
            padding: 5px 0;
            clear: both;
            margin-top: 15px;
            overflow-y: scroll;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }
    </style>
@endsection

@section('body')
    <div class="content">
        <div class="content-1">
            <div class="content-info menk-area">
                <p>
                    {{$info->exhibition_name or ''}}
                </p>
                <p>
                    {{$info->exhibition_subtitle or ''}}
                </p>
                <p>
                    {{$info->exhibition_address or ''}}
                </p>
                <p>&nbsp;</p>
                {!! $info->content or '' !!}
            </div>
        </div>


    </div>
@endsection
@section('script')
    <script>
        $(".content-info").css({"height":$("body").height()-$(".content-title").height()-20,"width":$("body").width()});
    </script>
@endsection

