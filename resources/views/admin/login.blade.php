@extends('layouts.public')

@section('title', '登录')

@section('body')
    <div class="middle-box text-center loginscreen">
        <div>
            <h3>{{$system_name}}</h3>

            <form class="m-t" role="form" method="POST" action="{{ route('admin.login') }}">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    <input type="text" name="username" class="form-control" placeholder="手机/邮箱/用户名" required="" value="{{ old('username') }}"/>
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input type="password" name="password" class="form-control" placeholder="密码" required="">
                </div>
                @if ($captchaadminlogin)
                    <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                        <input type="text" name="captcha" class="form-control" placeholder="验证码" required="" style="width: 150px; float: left;"/>
                        <img src="{{ url('cpt/show') }}" onclick="this.src='{{ url('cpt/show') }}?r=' + Math.random();" title="看不清，换一个" style="cursor:pointer;"/>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary block full-width m-b">登 录</button>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        @if ($errors->has('username'))
        layer.msg('{{ $errors->first('username') }}', {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
        @endif
        @if ($errors->has('password'))
        layer.msg('{{ $errors->first('password') }}', {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
        @endif

        @if ($errors->has('captcha'))
        layer.msg('{{ $errors->first('captcha') }}', {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
        @endif
    </script>
@endsection