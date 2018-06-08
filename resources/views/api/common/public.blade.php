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
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{cdn('css/common_html.css')}}">
    <link rel="stylesheet" href="{{cdn('fonts/mengyu_font/app_mengyu_font.css')}}">
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
    @yield('head')
</head>

<body>
@yield('body')
@yield('script')
<script>
@if(isset($language)&&$language==10)
    $(".menk-area").addClass("vtl");
@endif
</script>


</body>

</html>