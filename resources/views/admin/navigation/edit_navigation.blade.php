@extends('layouts.public')

@section('head')
    <style type="text/css">
        fieldset{
            display: none;
        }
        fieldset:first-of-type{
            display: block;
        }
        /*定时推送css*/
        .content-timeline{
            position: relative;
            padding: 10px 10px 0 10px;
            margin: 70px 160px 0;
            height: 660px;
            width: 80%;
            /*box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);*/
        }
        /*定点推送css*/
        .content-point{
            position: relative;
            padding: 10px 10px 0 10px;
            margin: 70px 160px 0;
            height: 660px;
            width: 80%;
            /*box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);*/
        }
        /*基于兴趣统计推送css*/
        .content-interest{
            position: relative;
            padding: 10px 10px 0 10px;
            margin: 70px 160px 0;
            height: 660px;
            width: 80%;
            /*box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);*/
        }
        .nav-map{
            margin: 20px 70px 0 70px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .info-edit{
            display: none;
            position: absolute;
            border: 1px solid #ffb400;
            line-height: 60px;
            text-align: center;
            width: 200px;
            height: 120px;
            background-color: rgba(255,255,255,1.0);
        }
        .info-edit:before {
            left: 42%;
            border-color: transparent;
            border-left-color: #E9F0F5;
            content: '';
            position: absolute;
            top: 120px;
            right: 100%;
            height: 0;
            width: 0;
            border: 16px solid transparent;
            border-top: 16px solid #fff;
        }
        .info-edit h3, .info-edit h4{
            color: #9b9999;
            padding: 0;
            margin: 0;
        }
        .info-edit i{
            cursor: pointer;
        }
        .info-edit .close-info {
            position: absolute;
            right: 5px;
            top: 6px;
            font-size: 18px;
        }

        .info-add{
            display: none;
            position: absolute;
            width: 50px;
            height: 50px;
            text-align: center;
            background-color: rgba(255,255,255,1.0);
            border-radius:5px;
            line-height: 60px;
        }
        .info-add:before {
            left: 35%;
            border-color: transparent;
            border-left-color: #E9F0F5;
            content: '';
            position: absolute;
            top: 50px;
            right: 100%;
            height: 0;
            width: 0;
            border: 8px solid transparent;
            border-top: 8px solid #fff;
        }
        .info-add i{
            cursor: pointer;
        }

        .edit-nav{
            position: absolute;
            top: 100px;
            right: 10px;
            width:390px;
        }
        .edit-nav .layer-msg-group{
            margin-bottom: 40px;
        }
        .edit-nav .layer-msg-key{
            margin-left: 0px;
        }
        .edit-nav input[type="button"]{
            width: 90px;
            margin-left: 10px;
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
                        <li><a href="{{Route('admin.navigation.edit_point',[$map_id,$id])}}">点位信息编辑</a></li>
                        <li class="active"><a href="{{Route('admin.navigation.edit_navigation',[$map_id,$id])}}">点位关联管理</a></li>

                    </ul>
                </div>
            </div>
        </div>
        <div name='position' id='position' class="input2" style="margin: 0px 10px 0px;"></div>
        <div class="edit-nav layer-div">
            <form method="post" class="form-horizontal ajaxForm" action="{{Route('admin.navigation.edit_navigation_save')}}">
                @if(count($info)==1)
                    @foreach($info as $k=>$v)
                        <div class="layer-msg-group">
                            <label class="layer-msg-key">关联接点{{$k+1}}</label>
                            <input type="text" class="form-control" name="ids[]" readonly="readonly" id="id{{$k+1}}" class="layer-msg-value" style="width: 120px;" maxlength="20" value="@if($v['id']!=$id){{$v['id']}}@endif" />
                            <input type="button" class="btn" onclick="rest_point('id{{$k+1}}')" value="取消关联">
                        </div>
                    @endforeach
                    <div class="layer-msg-group">
                        <label class="layer-msg-key">关联接点2</label>
                        <input type="text" class="form-control" name="ids[]" readonly="readonly" id="id2" style="width: 120px;" value="" class="layer-msg-value" />
                        <input type="button" class="btn" onclick="rest_point('id2')" value="取消关联">
                    </div>
                    <div class="layer-msg-group">
                        <label class="layer-msg-key">关联接点3</label>
                        <input type="text" class="form-control" name="ids[]" readonly="readonly" id='id3' style="width: 120px;" value="" class="layer-msg-value" />
                        <input type="button" class="btn" onclick="rest_point('id3')"  value="取消关联">
                    </div>
                    <div class="layer-msg-group">
                        <label class="layer-msg-key">关联接点4</label>
                        <input type="text" class="form-control" name="ids[]" readonly="readonly" id='id4' style="width: 120px;" value="" class="layer-msg-value" />
                        <input type="button" class="btn" onclick="rest_point('id4')" value="取消关联">
                    </div>
                @elseif(count($info)==2)
                    @foreach($info as $k=>$v)
                        <div class="layer-msg-group">
                            <label class="layer-msg-key">关联接点{{$k+1}}</label>
                            <input type="text" class="form-control" name="ids[]" readonly="readonly" id="id{{$k+1}}" class="layer-msg-value" style="width: 120px;" maxlength="20" value="@if($v['id']!=$id){{$v['id']}}@endif" />
                            <input type="button" class="btn" onclick="rest_point('id{{$k+1}}')" value="取消关联">
                        </div>
                    @endforeach
                    <div class="layer-msg-group">
                        <label class="layer-msg-key">关联接点3</label>
                        <input type="text" class="form-control" name="ids[]" readonly="readonly" id='id3' style="width: 120px;" value="" class="layer-msg-value" />
                        <input type="button" class="btn" onclick="rest_point('id3')"  value="取消关联">
                    </div>
                    <div class="layer-msg-group">
                        <label class="layer-msg-key">关联接点4</label>
                        <input type="text" class="form-control" name="ids[]" readonly="readonly" id='id4' style="width: 120px;" value="" class="layer-msg-value" />
                        <input type="button" class="btn" onclick="rest_point('id4')" value="取消关联">
                    </div>
                @elseif(count($info)==3)
                    @foreach($info as $k=>$v)
                        <div class="layer-msg-group">
                            <label class="layer-msg-key">关联接点{{$k+1}}</label>
                            <input type="text" class="form-control" name="ids[]" readonly="readonly" id="id{{$k+1}}" class="layer-msg-value" style="width: 120px;" maxlength="20" value="@if($v['id']!=$id){{$v['id']}}@endif" />
                            <input type="button" class="btn" onclick="rest_point('id{{$k+1}}')" value="取消关联">
                        </div>
                    @endforeach
                    <div class="layer-msg-group">
                        <label class="layer-msg-key">关联接点4</label>
                        <input type="text" class="form-control" name="ids[]" readonly="readonly" id='id4' style="width: 120px;" value="" class="layer-msg-value" />
                        <input type="button" class="btn" onclick="rest_point('id4')" value="取消关联">
                    </div>
                @else

                    @foreach($info as $k=>$v)
                        <div class="layer-msg-group">
                            <label class="layer-msg-key">关联接点{{$k+1}}</label>
                            <input type="text" class="form-control" name="ids[]" readonly="readonly" id="id{{$k+1}}" class="layer-msg-value" style="width: 120px;" maxlength="20" value="@if($v['id']!=$id){{$v['id']}}@endif" />
                            <input type="button" class="btn" onclick="rest_point('id{{$k+1}}')" value="取消关联">
                        </div>
                    @endforeach
                @endif
                <div class="btn-div" >
                    <input type="hidden" name="id" value="{{$id}}" />
                    <input type="hidden" name="map_id" value="{{$map_id}}" />
                    <button type="submit" class="btn btn-primary ajaxForm">提交</button>
                </div>
            </form>
        </div>
    </div>


@endsection
@section('script')
    <script src="{{cdn('js/plugins/echarts/echarts.js')}}"></script>
    <script type="text/javascript">
        $(window).resize(function(){
            $("#position").width($(window).width()-440);
            $("#position").height($(window).height()-100);
        }).resize();

        function rest_point(id){
            if($("#"+id).val()==''){
                return false;
            }
            $.post("{{Route('admin.navigation.ajax_axis')}}", {
                        id:$("#"+id).val(),
                        map_id:{{$map_id}}
                    },
                    function(data_info){
                        var posx=data_info.x;
                        var posy=data_info.y;
                        var new_point2=[];
                        myChart.delMarkPoint(0,$("#"+id).val());
                        new_point2.push({
                            name:$("#"+id).val(),
                            value:$("#"+id).val(),
                            itemStyle:{
                                normal:{
                                    color:'#ffb400'
                                }
                            },
                            //ajax获取当前点位坐标
                            geoCoord:[posx,posy]
                        });
                        myChart.addMarkPoint(0,{data:new_point2});
                        myChart.refresh();
                        $("#"+id).attr("value","");
                    });
        }
        //设置导航点

        var new_point=[];
        @foreach($info as $k=>$v)
        @if($v['id']==$id)
        new_point.push({
            name:'{{$v['id']}}',
            value:'{{$v['id']}}',
            itemStyle:{
                normal:{
                    color:'#f30632'
                }
            },
            geoCoord:[{{$v['x']}},{{$v['y']}}]
        });
        @else
        new_point.push({
            name:'{{$v['id']}}',
            value:'{{$v['id']}}',
            itemStyle:{
                normal:{
                    color:'#57c8f2'
                }
            },
            geoCoord:[{{$v['x']}},{{$v['y']}}]
        });
        @endif
        @endforeach
        @foreach($info2 as $k=>$v)
        @if($v['id']==$id)
        new_point.push({
            name:'{{$v['id']}}',
            value:'{{$v['id']}}',
            itemStyle:{
                normal:{
                    color:'#f30632'
                }
            },
            geoCoord:[{{$v['x']}},{{$v['y']}}]
        });
        @else
        new_point.push({
            name:'{{$v['id']}}',
            value:'{{$v['id']}}',
            itemStyle:{
                normal:{
                    color:'#ffb400'
                }
            },
            geoCoord:[{{$v['x']}},{{$v['y']}}]
        });
        @endif
        @endforeach
        //设置需要关联的点
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
                                url:"{{$map_path}}",
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
                        console.log("点击事件");
                        console.log(data);
                        var id1=$("#id1").val();
                        var id2=$("#id2").val();
                        var id3=$("#id3").val();
                        var id4=$("#id4").val();
                        if(data.name!='{{$id}}'&&data.name!=id1&&data.name!=id2&&data.name!=id3&&data.name!=id4){
                            if(id1!=''&&id2!=''&&id3!=''&&id4!=''){
                                layer.msg("ֻ只能最多关联4个节点",{icon: 5,scrollbar: false,time: 2000,shade: [0.3, '#393D49']});
                            }
                            else{
                                $.post("{{Route('admin.navigation.ajax_axis')}}", {
                                            id:data.name,
                                            map_id:{{$map_id}}
                                        },
                                        function(data_info){
                                            var posx=data_info.x;
                                            var posy=data_info.y;
                                            var new_point2=[];
                                            myChart.delMarkPoint(0,data.name);
                                            new_point2.push({
                                                name:data.name,
                                                value:data.name,
                                                itemStyle:{
                                                    normal:{
                                                        color:'#57c8f2'
                                                    }
                                                },
                                                //ajax获取当前点位坐标
                                                geoCoord:[posx,posy]
                                            });
                                            myChart.addMarkPoint(0,{data:new_point2});
                                            myChart.refresh();
                                            if(id1==''){
                                                $("#id1").attr("value",data.name);
                                            }
                                            else if(id2==''){
                                                $("#id2").attr("value",data.name);
                                            }
                                            else if(id3==''){
                                                $("#id3").attr("value",data.name);
                                            }
                                            else if(id4==''){
                                                $("#id4").attr("value",data.name);
                                            }
                                        });

                            }
                        }
                    }
                    var option = {
                        backgroundColor:'#F8F1E1',
                        title : {
                            //text : dituname,
                            //subtext: SVG地图扩展
                            textStyle: {
                                color: '#000000'
                            }
                        },
                        series : [
                            {
                                type: 'map',
                                mapType: 'map1024', // 自定义扩展图标类型
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
