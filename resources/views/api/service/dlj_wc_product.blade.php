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
    <title>文创产品</title>
    <link rel="stylesheet" href="{{cdn('css/h5view/dlj/reset.css')}}">
    <link rel="stylesheet" href="{{cdn('css/h5view/dlj/wccp.css')}}">
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
</head>
<body>
<div class="wrap clearfloat">
    <div class="swiper-container">
        <img src="{{get_file_url($data->pro_img)}}">
    </div>
    <div class="info">
        <p class="title">
            {{$data->pro_title}}
        </p>
        {!! $data->pro_content !!}
        {{--<div style="background: rgba(255,255,255,.8);position: absolute;width: 100%;height: 30px;bottom: 0"></div>--}}
    </div>
</div>
</body>


