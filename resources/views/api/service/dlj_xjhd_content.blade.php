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
    <title>宣教活动</title>
    <link rel="stylesheet" href="{{cdn('css/h5view/dlj/reset.css')}}">
    <link rel="stylesheet" href="{{cdn('css/h5view/dlj/xjhd.css')}}">
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
</head>
<body>
<div class="wrap clearfloat">
    <div class="leftImg">
        <img src="{{get_file_url($data['img'])}}">
    </div>
    <div class="info">
        <h1 class="title">
            {{$data['title']}}
        </h1>
        <h2>
            活动时间：{!! $data->active_date !!}
        </h2>
        <h2>
            活动地点：{!! $data->active_place !!}
        </h2>
        <h2>
            活动时长：{!! $data->active_date !!}
        </h2>
        <h2>
            活动价格：{!! $data->active_price !!}
        </h2>
        {!! $data->content !!}
        <div style="background: rgba(255,255,255,.8);position: fixed;width: 100%;height: 30px;bottom: 0"></div>
    </div>
</div>

</body>


