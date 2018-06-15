@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection
@section('head')
<style type="text/css">
	.layer-div input[type="text"]{
		width:300px;
	}
  .layer-msg-group{
    margin-bottom: 10px;
  }
  .layer-msg-group .layer-msg-key{
    vertical-align: top;
  }
</style>
@endsection
@section('body')
<body>
	<div class="js-check-wrap layer-div">
	  <form method="post" class="form-horizontal" id="myform" action="{{url('/admin/interaction/question/edit_ques')}}">
			<fieldset>
				<div class="layer-msg-group">
					<label class="layer-msg-key">问卷调查主题</label>
					<input type="text" value="{{$info['title'] or ''}}" name="title" maxlength="20" required>
				</div>

				<div class="layer-msg-group">
					<label class="layer-msg-key">问卷调查描述</label>
					<textarea name="description" style='width:700px;height:120px;resize:none;' maxlength="800" required>{{$info['description'] or ''}}</textarea>
				</div>
            </fieldset>
            <div class="btn-div">
                <input type="hidden" name="id" value="{{$id}}" />
                <input type="button" id="ajax_submit" class="btn btn-primary" style="width: 100px;margin-left: 40%;margin-top: 5%;" value="提交" />
            </div>
		</form>
   </div>
@endsection
@section('script')
   <script>
   $(function () {
       $("#ajax_submit").click(function(){
           var ajax_url=$('#myform').attr('action');
           $.ajax({
               cache: true,
               type: "POST",
               url:ajax_url,
               data:$('#myform').serialize(),// 你的formid
               async: false,
               error: function(request) {
                   layer.msg("服务连接错误",{icon: 5,scrollbar: false,time: 2000,shade: [0.3, '#393D49']});
               },
               success: function(data) {
                  if(data.status=='false'){
                      layer.msg(data.msg,{icon: 5,scrollbar: false,time: 2000,shade: [0.3, '#393D49']});
                  }
                  else if(data.status=='true'){
                      layer.msg(data.msg,{icon: 6,scrollbar: false,time: 1000,shade: [0.3, '#393D49']});
                      setTimeout(function(){
                          parent.layer.close(index);//关闭弹出层
                          var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                          //刷新父层页面
                          parent.location.href ="{{url('/admin/interaction/question/')}}";

                      }, 1000);
                  }
               }
           });
        })
     })
    </script>
@endsection
