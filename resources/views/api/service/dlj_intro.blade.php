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
</head>
<body>
<h1>名称</h1>
{!! $data->title !!}
<h1>简介</h1>
{!! $data->content !!}
@if(!empty($data->d_imgs))
    @foreach($data->d_imgs as $k=>$v)
        <img src="{{get_file_url($v)}}">
    @endforeach
@endif
</body>


