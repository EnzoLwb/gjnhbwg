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
    <link rel="stylesheet" href="{{cdn('css/h5view/app/reset.css')}}">
    <link rel="stylesheet" href="{{cdn('css/h5view/app/wccp.css')}}">
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
</head>
<body>
<img src="{{get_file_url($data->pro_img)}}">
<!--标题--!>
{{$data->pro_title}}
<!--内容--!>
{!! $data->pro_content !!}
{{--<img src="{{get_file_url($data->pro_img)}}">--}}
{{--{!! $data->pro_content !!}--}}
<div>
    <img src="{{get_file_url($data->pro_img)}}" alt=""/>
    {!! $data->pro_content !!}
    {{--<h1>渔家娃娃笔筒</h1>
    <p>
        文创产品简介文创产品简介文创产品简介文创产品简介文创产品简介文创产品简介文创产品简介文创产品简...
    </p>--}}
</div>
</body>


