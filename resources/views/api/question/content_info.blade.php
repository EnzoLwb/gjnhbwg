@extends('api.common.public')

@section('title')学习单开发中@endsection

@section('head')
    
@endsection

@section('body')
    <div class="content">
        <div class="content-1">
            <div class="content-info menk-area">
               开发中
            </div>
        </div>


    </div>
@endsection
@section('script')
    <script>
        $(".content-info").css({"height":$("body").height()-$(".content-title").height()-20,"width":$("body").width()});
    </script>
@endsection

