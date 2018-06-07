@extends('layouts.public')

@section('head')
<style type="text/css">
    .form-horizontal{
        display: inline-block;
    }
    .btn-div{
        display: inline-block;
    }
    .layer-msg-group{
        display: inline-block;
    }
    .form-control{
        display: inline-block;
    }
</style>
@endsection
@section('body')
    <div class="wrap wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{Route('admin.navigation.edit_point',[$map_id,$id])}}">点位信息编辑</a></li>
                        <li><a href="{{Route('admin.navigation.edit_navigation',[$map_id,$id])}}">点位关联管理</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div name='position' id='position' class="input2" style="margin: 0px 10px 0px;"></div>

        <div class="edit-point layer-div" style="margin:10px">
            <form method="post" class="form-horizontal ajaxForm" action="{{Route('admin.navigation.edit_point_save')}}">
                <div class="layer-msg-group">
                    <label class="layer-msg-key">点位X轴坐标</label>
                    <input type="text" name="x" id='x' class="form-control" style="width: 160px;" value='{{$info['x']}}' class="layer-msg-value" required/>
                    <label class="layer-msg-key">点位Y轴坐标</label>
                    <input type="text" name="y"  id='y' class="form-control" style="width: 160px;" value='{{$info['y']}}' class="layer-msg-value" required/>
                </div>
                <div class="btn-div" >
                    <input type="hidden" name="id" value="{{$info['id']}}" />
                    <input type="hidden" name="map_id" value="{{$info['map_id']}}" />
                    <button type="submit" class="btn operate-btn btn-primary js-ajax-submit">提交</button>
                </div>
            </form>
            <form method="post" class="form-horizontal ajaxForm" action="{{Route('admin.navigation.edit_point_save')}}">
                <input type="hidden" name="del" value="{{$info['id']}}">
                <input type="hidden" name="map_id" value="{{$info['map_id']}}" />
                <button type="submit" class="btn operate-btn ajaxForm">删除该节点</button>
            </form>
        </div>
    </div>

@endsection
@section('script')
    <script src="{{cdn('js/plugins/echarts/echarts.js')}}"></script>
    <script type="text/javascript">
        layui.use('element', function () {
            var element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
        });

    </script>
<script>
    $(window).resize(function () {
        $("#position").width($(window).width() - 60);
        $("#position").height($(window).height() - 145);
    }).resize();
    var new_point=[];
    new_point.push({
        name:'aaa',
        value:"{{$info['id']}}",
        itemStyle:{
            normal:{
                color:'#57c8f2'
            }
        },
        geoCoord:[{{$info['x']}},{{$info['y']}}]
    });
    require.config({
        paths: {
            echarts: '{{cdn('js/plugins/echarts')}}'
        }
    });
    require([
            'echarts',
            'echarts/chart/map'// 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            myChart = ec.init(document.getElementById('position'));
            require('echarts/util/mapData/params').params.map1024 = {
                getGeoJson: function (callback) {
                    $.ajax({
                        url:"{{$map_path}}",
                        dataType: 'xml',
                        success: function(xml) {
                            callback(xml);
                        }
                    });
                }
            }
            var ecConfig = require('echarts/config');
            myChart.on(ecConfig.EVENT.CLICK, eConsole);
            function eConsole(data){
                if(data.name=='导航'){
                    edit_point(data.value);
                }
                else if(data.name!='aaa'){
                    var new_point=[];
                    myChart.delMarkPoint(0,'aaa');
                    $("#x").val(data.posx.toFixed(0));
                    $("#y").val(data.posy.toFixed(0));
                    new_point.push({
                        name:'aaa',
                        value:'{{$info['id']}}',
                        itemStyle:{
                            normal:{
                                color:'#57c8f2'
                            }
                        },
                        geoCoord:[data.posx.toFixed(0),data.posy.toFixed(0)]
                    });

                    myChart.addMarkPoint(0,{data:new_point});
                    myChart.refresh();
                }
            }
            var option = {
                backgroundColor:'#F8F1E1',
                title : {
                    //text : dituname,
                    //subtext: 地图SVG扩展
                    textStyle: {
                        color: '#000000'
                    }
                },
                series : [
                    {
                        name: '�Ŷ�:',
                        type: 'map',
                        mapType: 'map1024', // 自定义扩展图表类型
                        roam:true,
                        itemStyle:{
                            normal:{label:{show:true}},
                            emphasis:{label:{show:true}}
                        },
                        data:[
                            {name: '', hoverable: false, itemStyle:{normal:{label:{show:false}}}}
                        ],
                        markPoint : {
                            clickable: true,
                            symbol:'pin',
                            symbolSize : 10,
                            itemStyle:{
                                normal:{
                                    color:"#FFB400"
                                }
                            },
                            data :new_point
                        },
                        scaleLimit:{max:10, min:0.5},
                    }
                ]
            };
            // 为echarts对象加载数据
            myChart.setOption(option);
        }
    );


</script>
@endsection
