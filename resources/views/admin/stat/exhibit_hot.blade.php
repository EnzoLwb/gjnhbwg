@extends('layouts.public')

@section('head')
<script src="{{cdn('js/plugins/echarts3/echarts.min.js')}}"></script>
<style type="text/css">

    #main{
        height:680px;
    }
    /**max width 1600px**/
    @media screen and (max-width:1600px) {
        #main{
            height:600px;
        }
    }
    /**max width 1366px**/
    @media screen and (max-width:1366px) {
        #main{
            height:550px;
        }
    }
</style>
@endsection
@section('body')
    <div class="wrap wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li @if($type =="look_num")class="active"@endif><a href="{{route('admin.stat.stat.exhibit_hot' , ['type'=>'look_num'])}}">浏览统计</a></li>
                        <li @if($type =="like_num")class="active"@endif ><a href="{{route('admin.stat.stat.exhibit_hot' , ['type'=>'like_num'])}}">点赞统计</a></li>
                        <li @if($type =="comment_num")class="active"@endif ><a href="{{route('admin.stat.stat.exhibit_hot' , ['type'=>'comment_num'])}}">评论统计</a></li>
                    </ul>
                    <div class="form-inline form-screen" >
                        <button class="btn btn-primary" type="button" onclick="window.location='{{route('admin.stat.stat.export_exhibit_hot' , ['type'=>$type])}}'">导出</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="wrap js-check-wrap">

            <div class="table-actions">

            </div>
            <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
        <div id="main" style="width: 90%;padding: 20px 30px 0px 30px;"></div>
        <script type="text/javascript">
            // 基于准备好的dom，初始化echarts实例
            var myChart = echarts.init(document.getElementById('main'));

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '{{$arr["title"]}}',
                    textStyle: {
                        color: '#9b9999'
                    }
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                    right:30,
                    data:['{{$arr["name"]}}']
                },
                grid: {
                        // left: '10%',
                        bottom: '15%'
                    },
                xAxis: {
                    // offset:-80,
                    axisLabel:{
                        interval:0,
                        rotate:-15,
                        textStyle:{
                            color: '#9b9999'
                        }
                    },
                    nameTextStyle:{
                        color: '#9b9999'
                    },
                    axisTick: {
                        show: false
                    },
                    axisLine:{
                        show: false
                    },
                    data: [
                    @foreach($info as $k=>$g)
                            "{{$g->exhibit_name}}",
                    @endforeach
                    ]
                },
                yAxis: {
                    minInterval:1,
                    axisTick: {
                        show: false
                    },
                    axisLine:{
                        show: false
                    },
                    axisLabel:{
                        textStyle:{
                            color: '#9b9999'
                        }
                    },
                    axisLabel:{
                        textStyle:{
                            color: '#9b9999'
                        }
                    }
                },
                series: [{
                    name: '{{$arr["name"]}}',
                    type: 'bar',
                    // barWidth: '40%',
                    barWidth:'50px',
                    itemStyle:{
                        normal:{
                            color:['#57C8F2'],
                            barBorderRadius:5,
                            opacity:1
                        }
                    },
                    data: [ @foreach($info as $k=>$g)
                    {{$g->num}},
                        @endforeach]
                }]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
        </script>
            <div class="row recordpage" style="padding: 0 30px">
                <div class="col-sm-12">
                    {!! $info->links() !!}
                    <span>共 {{ $info->total() }} 条记录</span>
                </div>
            </div>
        </div>
    </div>
@endsection
