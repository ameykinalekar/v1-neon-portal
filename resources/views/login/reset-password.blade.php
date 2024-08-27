@extends('layouts.login')
@section('pagecss')
<link href="{{asset('css/init/login.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="super_logo">
    <img src="{{asset('img/system/logo/logo-dark.png')}}" alt="" height="45px">
</div>

<div class="auth-fluid">

    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex">
            <div class="card-body">
                <!-- Logo -->
                <div class="text-center text-lg-left mb-3">
                    <span><img src="{{asset('img/system/logo/neon_logo.png')}}" alt="" height="80"></span>
                    <!--</a>-->
                    <h4 class="mt-0">Reset Password</h4>
                </div>
                <!-- form -->
                <form action="{{ URL::Route('update_newpassword') }}" method="post" id="loginForm">
				@csrf
                <input type="hidden" name="reset_token" value="{{$token}}">
                    <div class="form-group mb-3">
                        <label for="emailaddress">New Password</label>
                        <input class="form-control" type="password" name="password" id="password" required=""
                            placeholder="Enter your password">
                    </div>
                    <div class="form-group mb-3">

                        <label for="password">Confirm Password</label>
                        <input class="form-control" type="password" name="confirm_password" required="" id="confirm_password"
                            placeholder="Enter confirm password">
                        <span class="text-danger" id="error_message"></span>
                    </div>

                    <div class="form-group mb-3 mb-0 text-center">
                        <button class="btn btn-block btnlogin" style="width:100%" type="submit"><i class="mdi mdi-login"></i>
                            Submit </button>
                    </div>
                </form>


                <!-- end form-->
            </div> <!-- end .card-body -->
        </div> <!-- end .align-items-center.d-flex.h-100-->
    </div>

    <!-- end auth-fluid-form-box-->

    <!-- Auth fluid right content -->
    <!-- end Auth fluid right content -->
</div>
@endsection
@section('pagescript')
<script>
    $('.btnlogin').on('click',function(){
        $(this).attr('disabled',true);
        $('#loginForm').submit();
    });
</script>

@endsection
