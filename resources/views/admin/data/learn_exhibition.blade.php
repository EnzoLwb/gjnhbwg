@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form method="post" class="form-horizontal" id="myform" action="{{route('admin.data.exhibition.save_learn')}}">
                    <input type="text" name="uids_old" class="form-control" value="">
                    <table class="table table-striped table-new table-hover infoTables-example infoTable">
                            <thead>
                            <tr role="row">
                                <th width="2%"><input type="checkbox" value="" class="checkAll check_all"></th>
                                <th>题目名称</th>
                            </tr>
                            </thead>
                            @foreach($list as $k=>$v)
                                <tr class="gradeA">
                                    <td><input type="checkbox" name="items[]" class="checkItem" @if(isset($v['exhibition_id']))checked @endif value="{{$v['id']}}"></td>
                                    <td>{{$v['title']}}</td>

                                </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="text" id="uidss" value="{{$arr_new}}">
                                <input type="button" id="ajax_submit" class="btn btn-primary"  value="确定" />
                                {{--<button type="button" class="btn btn-danger btn-sm checkBtn" uri="{{route('admin.data.exhibition.save_learn',[$exhibition_id,''])}}" msg="是否选择学习单题目">保存</button>--}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div>共 {{ $list->total() }} 条记录</div>
                                {!! $list->links() !!}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function () {
            {{--$("#uidss").val({{$arr_new}});--}}
            var strObj = {};
            $(".check_all").click(function(){
                var uids = "";
                var old = parent.document.getElementById("uids").value;
                $("input:checkbox").each(function(){
                    var check = $(this).is(':checked');
                    var uid =  $(this).val();
                    if(check==true){
                        strObj[uid] = uid;
                        for(key in strObj) {
                            uids += key + ",";
                        }
                        parent.document.getElementById("uids").value=old+uids;
                        parent.document.getElementById("uids_new").value=uids;
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                    }else{
                        delete strObj[uid];
                        del(uid);
                    }
                });

            });


            $('.checkItem').click(function () {
                var old = parent.document.getElementById("uids").value;

                //判断是否选中
                var check = $(this).is(':checked');
                var uid =  $(this).val();
                var phone =  $(this).attr("phone");
                if(check==true){
                    strObj[uid] = phone;
                    var str = "";
                    var uids = "";
                    for(key in strObj) {
                        uids += key + ",";
//                    str += "<li data-uid='" + key + "' id='id_" + key + "'>" + "" + strObj[key] + "<span class='del' onclick='del(" + key + ")'>×</span></li>";
                    }
                    parent.document.getElementById("uids").value=old+uids;
                    parent.document.getElementById("uids_new").value=uids;
//                parent.document.getElementById("user-ul").innerHTML=str;
                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                }else{
                    delete strObj[uid];
                    del(uid);
                }


            });

            function del(uid) {
                var uids = $('#uids', parent.document).val();
                var arr = uids.split(",");
                var uid_new = "";
                if(arr.length<=2){
                    uid_new = "";
                }else{
                    $.each(arr,function (index,v) {
                        if(uid != v){
                            uid_new += v + ",";
                        }
                    });
                }
                $('#uids', parent.document).val(uid_new);
            }


            $("#ajax_submit").click(function(){
                alert(parent.document.getElementById("uids_old").value);
                var uids= parent.document.getElementById("uids").value;
                var ajax_url=$('#myform').attr('action');
                $.ajax({
                    cache: true,
                    type: "POST",
                    url:ajax_url,
//                    data:$('#myform').serialize(),// 你的formid
                    data:{uids:uids},
                    async: false,
                    error: function(request) {
                        layer.msg("服务连接错误",{icon: 5,scrollbar: false,time: 2000,shade: [0.3, '#393D49']});
                    },

                });
            })
        })
    </script>
@endsection
