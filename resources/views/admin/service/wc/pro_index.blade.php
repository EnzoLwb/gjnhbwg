@extends('layouts.public')

@section('head')
    <link rel="stylesheet" href="{{cdn('css/add/exhibit.css')}}">
@endsection

@section('body')

    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.service.wenchuang')}}">产品列表</a></li>
                        <li><a href="{{route('admin.service.wenchuang.add')}}">添加产品</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" id="form_submit" method="get">
                        <div class="form-group">
                            <select name="xl_id" class="form-control"  id="select_calss1">
                                <option value="">请选择系列</option>
                                @foreach($xl as $k=>$v)
                                    <option value="{{$v->id}}" @if(request('xl_id')==$v->id) selected @endif >{{$v->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            是否显示
                            <select name="is_show" id="select_calss2">
                                <option value="0">全部</option>
                                <option @if(request('is_show')==1) selected @endif value="1">显示</option>
                                <option @if(request('is_show')==2) selected @endif value="2">不显示</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="title" placeholder="系列名称" class="form-control" value="{{request('title')}}" style=" width: 200px;" maxlength="20">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.service.wenchuangxl')}}'">重置</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="exhibition">
                    <ul class="exhibition-list">
                        @foreach($data as $g)
                        <li>
                            <div class="list-pic">
                                <img src="{{$g['pro_img']}}">
                                <div class="list-type">{{$g['title']}}</div>
                                <a class="btn-edit" href="{{route('admin.service.wenchuang.edit', $g['id'])}}">编辑</a>
                                <a class="ajaxBtn btn-delete" href="javascript:void(0);" uri="{{route('admin.service.wenchuang.delete',$g['id'])}}" msg="是否删除该系列及系列下的产品？">删除</a>

                                @if($g['is_show']==1)
                                    <a class="ajaxBtn btn-set" href="javascript:void(0);" uri="{{route('admin.service.wenchuang.unset_show',$g['id'])}}" msg="是否取消显示？">取消显示</a>
                                @else
                                    <a class="ajaxBtn btn-set" href="javascript:void(0);" uri="{{route('admin.service.wenchuang.set_show' ,$g['id'])}}" msg="是否设为显示？">设置显示</a>
                                @endif
                            </div>
                            <div class="list-tit">{{$g['pro_title']}}</div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="clearfix"></div>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $data->links() !!}
                            <span>共 {{ $data->total() }} 条记录</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    $('#select_calss1,#select_calss2').change(function(){
        $('#form_submit').submit();
    })
</script>
@endsection
