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
    <link rel="stylesheet" href="{{cdn('css/h5view/app/zpxq.css')}}">
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
</head>
<body>
{{--<h1>展品名称</h1>
{{$info->exhibit_name or ''}}--}}
{{--<h1>展品内容</h1>--}}
<div>
    {!! $info->content or '' !!}
</div>
</body>


