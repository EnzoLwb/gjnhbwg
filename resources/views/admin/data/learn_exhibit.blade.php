@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <form role="form" class="form-inline" method="get">
                            <div class="form-group">
                                <input type="text" name="learn_title" placeholder="题目" class="form-control" value="{{request('learn_title')}}" style=" width: 200px;" maxlength="20">
                            </div>
                            &nbsp;&nbsp;
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.data.exhibit.add_learn',$exhibit_id)}}'">重置</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <table class="table table-striped table-new table-hover infoTables-example infoTable">
                            <thead>
                            <tr role="row">
                                <th width="2%"></th>
                                <th>题目名称</th>
                            </tr>
                            </thead>
                            @foreach($list as $k=>$v)
                                <tr class="gradeA">
                                    <td><input type="checkbox" name="items[]" class="checkItem" @if(isset($v['exhibit_id']))checked @endif value="{{$v['id']}}"></td>
                                    <td>{{$v['title']}}</td>

                                </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-12">
                                <div>共 {{ $list->total() }} 条记录</div>
                                {!! $list->links() !!}
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function () {
            var exhibit_id = {{$exhibit_id}};
            $('.checkItem').click(function () {
                //判断是否选中
                var check = $(this).is(':checked');
                var learn_id =  $(this).val();
                if(check==true){
                    $.ajax({
                        cache: true,
                        type: "POST",
                        url:"{{route('admin.data.exhibit.save_learn')}}",
                        data:{learn_id:learn_id,exhibit_id:exhibit_id},
                        async: false,
                    });
                }else{
                    $.ajax({
                        cache: true,
                        type: "POST",
                        url:"{{route('admin.data.exhibit.del_learn')}}",
                        data:{learn_id:learn_id,exhibit_id:exhibit_id},
                        async: false,


                    });

                }


            });


        })
    </script>
@endsection
