<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,height=device-height, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
    <meta name="applicable-device" content="mobile">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>{{$info->exhibit_name or ''}}</title>
    <link rel="stylesheet" href="{{cdn('css/h5view/app/reset.css')}}">
    <link rel="stylesheet" href="{{cdn('css/h5view/app/fxym.css')}}">
    <link rel="stylesheet" href="{{cdn('css/audioplayer.css')}}">
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
    <script src="{{cdn('js/audioplayer.js')}}"></script>
</head>
<body>
<div class="wrap">
    <h1>{{$info->exhibit_name or ''}}</h1>
    @if(!empty($info->exhibit_img))
        <img src="{{get_file_url(json_decode($info->exhibit_img,true)['exhibit_imgs'])}}">
    @endif
   {{-- <img src="{{cdn('img/h5view/996.png')}}" alt="">--}}
    <div class="videoWrap">
        <audio preload="auto" controls="" src="{{$info->audio or ''}}"></audio>
    </div>
    <div class="content">
        {!! $info->content or '' !!}
    </div>
</div>
<script>
    $('audio').audioPlayer();
</script>
</body>
