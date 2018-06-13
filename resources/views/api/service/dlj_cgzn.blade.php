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
<p>交通路线</p>
{!! $jt->jiaotong !!}
<p>联系方式</p>
{!! $jt->contact !!}
<br><br><br>
{{--参观须知--}}
{!! $xz->shuoming !!}
<br>
{!! $xz->zysx !!}
<br><br><br>
{{--设备租赁--}}
{{--步骤一--}}
{!! $zl->step1 !!}
<br>
{{--步骤二--}}
{!! $zl->step2 !!}
<br>
{{--步骤三--}}
{!! $zl->step3 !!}
<br>
{{--步骤四--}}
{!! $zl->step4 !!}
<br>
{{--步骤五--}}
{!! $zl->step5 !!}
</body>


