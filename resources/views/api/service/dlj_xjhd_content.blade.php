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
<p>标题</p>
{{$data['title']}}
<p>副标题</p>
{!! $data->title_1 !!}
<p>图片</p>
<img src="{{get_file_url($data['img'])}}">
<p>地点</p>
{!! $data->active_place !!}
<p>活动时间</p>
{!! $data->active_date !!}
<p>价格</p>
{!! $data->active_price !!}
<p>时长</p>
{!! $data->active_time !!}
<p>简介</p>
{!! $data->content !!}



</body>


