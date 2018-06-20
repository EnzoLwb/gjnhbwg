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
    <title>场馆简介</title>
    <link rel="stylesheet" href="{{cdn('css/h5view/dlj/reset.css')}}">
    <link rel="stylesheet" href="{{cdn('css/h5view/dlj/cgjj.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.0.2/css/swiper.min.css">
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.0.2/js/swiper.min.js"></script>
</head>
<body>
<div class="wrap clearfloat">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @if(!empty($data->d_imgs))
                @foreach($data->d_imgs as $k=>$v)
                    <div class="swiper-slide">
                        <img src="{{get_file_url($v)}}">
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="info">
        <p class="title">
            {!! $data->title !!}
        </p>
        {!! $data->content !!}
        <div style="background: rgba(255,255,255,.8);position: fixed;width: 100%;height: 30px;bottom: 0"></div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var mySwiper = new Swiper('.swiper-container', {
            autoplay: true//可选选项，自动滑动
        })
    })
</script>
</body>


