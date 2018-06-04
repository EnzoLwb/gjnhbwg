@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('head')
    <style type="text/css">
        .treeview span.indent {
            margin-left: 10px;
            margin-right: 10px;
        }

        .treeview span.icon {
            width: 12px;
            margin-right: 5px;
        }
    </style>
@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.setting.systemlog')}}">系统日志</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="col-sm-12 treeview">
                            <ul class="list-group">
                                <li class="list-group-item node-tree">
                                    {{storage_path('logs')}}
                                </li>
                                @foreach($dirlist as $dir)
                                    <li class="list-group-item node-tree">
                                        <span class="icon expand-icon glyphicon glyphicon-minus"></span>
                                        {{$dir['name']}}
                                        <input type="hidden" name="path" value="{{$dir['path']}}">
                                        @if($dir['type'] == 'dir')
                                            <a href="javascript:void(0);" class="logview">展开</a>
                                        @else
                                            <a class="chakan" href="javascript:;" _href="{{route('admin.setting.systemlog.view')}}?path={{urlencode($dir['path'])}}">查看</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="row"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        jQuery(function ($) {
            $(document).on('click', 'a[class^=logview]', function () {
                var that = $(this);
                // debugger
                if(that.hasClass('open')){
                    return false;
                }else{
                    var p = $(this).parent();
                    var path = p.find('[name=path]').val();
                    $.getJSON('{{route('admin.setting.systemlog.getdir')}}', {path: path}, function (data) {
                        var e = p;
                        var ehtml, ehref;
                        that.addClass('open');
                        // that.text('收起');
                        for (var i in data) {
                            if (data[i].type == 'dir') {
                                ehtml = '<span class="icon expand-icon glyphicon glyphicon-minus"></span>';
                                ehref = '<a href="javascript:void(0);" class="logview">展开</a>';
                            } else {
                                ehtml = '<span class="icon glyphicon"></span>';
                                ehref = '<a class="chakan" href="javascript:;" _href="{{route('admin.setting.systemlog.view')}}?path=' + encodeURI(data[i].path) + '">查看</a>';
                            }
                            e = e.after(['<li class="list-group-item node-tree">' + '<span class="indent"></span>' + ehtml + data[i].name +
                            '<input type="hidden" name="path" value="' + data[i].path + '"> ' + ehref + '</li>'].join(""));
                        }
                    })
                }
            });

            $(document).on('click', 'a[class^=chakan]', function () {
                var that = $(this);
                var url=that.attr('_href');
                layer.open({
                    title: '添加脚本',
                    type: 2,
                    area: ['600px', '500px'],
                    fix: true, //固定
                    maxmin: false,
                    move: false,
                    resize: false,
                    zIndex: 1,
                    content: url
                });
            });
        });
    </script>
@endsection