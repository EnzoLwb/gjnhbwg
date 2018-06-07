@extends('layouts.public')

@section('head')
    <style>
        .add-point {
            position: absolute;
            top: 200px;
            right: 60px;
            width: 400px;
        }
        .add-point h3{
            font-size: 16px;
            font-weight: bold;
        }
        .add-point .layer-msg-group{
            margin-top: 10px;
        }
        .add-point .layer-msg-group .layer-msg-key{
            display: inline-block;
            width: 80px;
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
                    @foreach($maps as $key=>$g)
                        <li @if($map_id==$g['id']) class="active"@endif><a href='{{route('admin.navigation.show',[$g['id']])}}'>{{$g['map_name']}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
    @if(empty($maps))
        <h1>请添加地图</h1>
    @else
        <div name='position' id='position' style="margin:20px auto;"></div>
    @endif
    <div class="add-point layer-div">
        <form method="post" class="form-horizontal ajaxForm" action="{{route('admin.navigation.add_point')}}">
            <h3>添加导航辅助点</h3>

            <div class="layer-msg-group">
                <label class="layer-msg-key">点位X轴坐标</label>
                <input type="text" name="x" id='x' class="form-control" style="width: 160px;" value='' class="layer-msg-value" required/>
            </div>
            <div class="layer-msg-group">
                <label class="layer-msg-key">点位Y轴坐标</label>
                <input type="text" name="y" id='y' class="form-control" style="width: 160px;" value='' class="layer-msg-value" required/>
            </div>
            <div class="layer-msg-group">
                <label class="layer-msg-key">x轴关联点</label>
                <input type="text" name="x_point" class="form-control" style="width: 160px;" value='' class="layer-msg-value" />
            </div>
            <div class="layer-msg-group">
                <label class="layer-msg-key">y轴关联点</label>
                <input type="text" name="y_point" class="form-control" style="width: 160px;" value='' class="layer-msg-value"/>
            </div>
            <div class="btn-div" style="margin-top:10px;">
                <input type="hidden" name="map_id" id="map_id" value="{{$map_id}}"/>
                <button type="submit" class="btn btn-primary ajaxForm" id="submitBtn">提交</button>
            </div>
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
            $("#position").width($(window).width() - 140);
            $("#position").height($(window).height() - 175);

        }).resize();

        // 第一次load标志位
        function map(path,infoArr,lineObject){
            require.config({
                paths: {
                    echarts: '{{cdn('js/plugins/echarts')}}'
                }
            });
            require([
                        'echarts',
                        'echarts/chart/map' // 使用柱状图就加载bar模块，按需加载
                    ],
                    function (ec) {
                        // 基于准备好的dom，初始化echarts图表
                        myChart = ec.init(document.getElementById('position'));
                        require('echarts/util/mapData/params').params.map1024 = {
                            getGeoJson: function (callback) {
                                $.ajax({
                                    url: path,
                                    dataType: 'xml',
                                    success: function(xml) {
                                        callback(xml);
                                    }
                                });
                            }
                        }
                        //marker点击事件
                        var ecConfig = require('echarts/config');
                        myChart.on(ecConfig.EVENT.CLICK, eConsole);
                        function eConsole(data){
                            if(data.name=='导航'){
                                edit_point(data.value);
                            }
                            else if(data.name=='讲解'){

                            }
                            else if(data.name!='aaa'){
                                var new_point=[];
                                myChart.delMarkPoint(0,'aaa');
                                $("#x").val(data.posx.toFixed(0));
                                $("#y").val(data.posy.toFixed(0));
                                new_point.push({
                                    name:'aaa',
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
                                //subtext: '地图SVG扩展',
                                textStyle: {
                                    color: '#000000'
                                }
                            },
                            series : [
                                {
                                    name: '团队:',
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
                                    //点
                                    markPoint : {
                                        clickable: true,
                                        symbol:'pin',
                                        symbolSize : 10,
                                        itemStyle:{
                                            normal:{
                                                color:"#FFB400"
                                            }
                                        },
                                        data :infoArr
                                    },
                                    //轨迹路线
                                    markLine:{
                                        name: "time1",
                                        itemStyle : {
                                            normal: {
                                                color:"#FFB400",
                                                borderWidth:1,
                                                lineStyle: {
                                                    type: 'solid'
                                                }
                                            }
                                        },
                                        smooth:true,
                                        data : lineObject
                                    },
                                    scaleLimit:{max:10, min:0.5},
                                }
                            ]
                        };
                        // 为echarts对象加载数据
                        myChart.setOption(option);
                    }
            );
        }
        show_map({{$map_id}});
        //ajax切换地图
        function show_map(map_id){
            $("#map_id").val(map_id);
            $("#x").val('');
            $("#y").val('');
            $.post("{{Route('admin.navigation.ajax_map2')}}", {
                map_id:map_id
            },
            function(data){
                var infoArr=[];
                for(var i=0;i<data.count;i++){
                    infoArr.push({
                        name:'讲解',
                        value:data.pos_info[i].auto_num,
                        itemStyle:{
                            normal:{
                                color:'#ffb400'
                            }
                        },
                        geoCoord:[data.pos_info[i]['x'],data.pos_info[i]['y']]
                    });
                }
                for(var i=0;i<data.dh_count;i++){
                    infoArr.push({
                        name:'导航',
                        value:data.dh_info[i].id,
                        itemStyle:{
                            normal:{
                                color:'#57c8f2'
                            }
                        },
                        geoCoord:[data.dh_info[i]['x'],data.dh_info[i]['y']]
                    });
                }
                var lineObject=[];
                for(var i=0;i<data.road_num;i++){
                    lineObject.push([{name:"11",geoCoord:[data.road_info[i][0]['x'],data.road_info[i][0]['y']]},{name:"11",geoCoord:[data.road_info[i][1]['x'],data.road_info[i][1]['y']]}]);
                }
                map(data.map_path,infoArr,lineObject);//地图加载初始化
            });
        }

        // 编辑节点
        //导航点位设置
        function edit_point(id){
            var map_id=$("#map_id").val();
            layer.open({
                title:'导航点位编辑',
                type: 2,
                area: ['1000px', '530px'],
                fix: true, //固定
                maxmin: true,
                move: false,
                full:function(){
                    $("iframe")[0].contentWindow.location.reload();
                }, 
                min:function(){
                    $("iframe")[0].contentWindow.location.reload();
                },
                restore:function(){
                    $("iframe")[0].contentWindow.location.reload();
                },
                content:"{{Route('admin.navigation.edit_point',['',''])}}/"+map_id+"/"+id,
            });
        }
    </script>
@endsection
