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
    <title>参观指南</title>
    <link rel="stylesheet" href="{{cdn('css/h5view/dlj/reset.css')}}">
    <link rel="stylesheet" href="{{cdn('css/h5view/dlj/cgzn.css')}}">
    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="active">
            交通周边
        </div>
        <div>
            参观须知
        </div>
        <div>
            设备租赁
        </div>
    </div>
    <!--交通周边-->
    <div class="content content1">
        <ul>
            <li style="margin-bottom: 40px" class="clearfloat">
                <img src="{{cdn('img/h5view/j1.png')}}" alt=""/>
                <div class="info1">
                    {!! $jt->jiaotong !!}
                </div>
            </li>
            <li class="clearfloat">
                <img src="{{cdn('img/h5view/j2.png')}}" alt=""/>
                <div class="info2">
                    {!! $jt->contact !!}
                </div>
            </li>
        </ul>
    </div>
    <!--参观须知-->
    <div class="content content2">
        <ul>
            <li style="margin-bottom: 49px" class="clearfloat">
                <img src="{{cdn('img/h5view/c1.png')}}" alt=""/>
                <div class="info1">
                    {!! $xz->shuoming !!}
                </div>
            </li>
            <li class="clearfloat">
                <img src="{{cdn('img/h5view/c2.png')}}" alt=""/>
                <div class="info2">
                    {!! $xz->zysx !!}
                </div>
            </li>
        </ul>
    </div>
    <!--设备租赁-->
    <div class="content content3">
        <div class="flex">
            <div class="item">
                <img src="{{cdn('img/h5view/item1.png')}}" alt=""/>
                <p>{!! $zl->step1 !!}</p>
            </div>
            <div class="item">
                <img src="{{cdn('img/h5view/item2.png')}}" alt=""/>
                <p>{!! $zl->step2 !!}</p>
            </div>
            <div class="item">
                <img src="{{cdn('img/h5view/item3.png')}}" alt=""/>
                <p>{!! $zl->step3 !!}/台</p>
            </div>
        </div>
        <div class="flex2">
            <div class="item">
                <img src="{{cdn('img/h5view/item4.png')}}" alt=""/>
                <p>{!! $zl->step4 !!}/轮椅/婴儿车</p>
            </div>
            <div class="item">
                <img src="{{cdn('img/h5view/item5.png')}}" alt=""/>
                <p> {!! $zl->step5 !!}</p>
            </div>
        </div>
        <p class="remarks"> <sup>*</sup> 最终解释权归国家（海南）南海博物馆所有</p>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $(".header div").on("click",function () {
            $(this).addClass("active").siblings().removeClass("active");
            $(".content").eq($(this).index()).show().siblings(".content").hide();
            document.body.scrollTop = document.documentElement.scrollTop = 0;
        });
    })
</script>
</body>


