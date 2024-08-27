@extends('layouts.login')
@php
$userInfo=Session::get('user');
$profileInfo=Session::get('profile_info');
$tenantInfo=Session::get('tenant_info');
$settingInfo=Session::get('setting_info');
$tenantShortName=Session::get('tenant_short_name');
//print_r($tenantShortName);
//print_r($settingInfo);
@endphp
@section('title', 'Login')
@section('pagecss')
<link href="{{asset('css/init/login.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="super_logo">
    <span><img src="{{config('app.api_asset_url').$tenantInfo['logo']}}" alt="logo" height="80" class="login-logo"></span>
</div>

<div class="auth-fluid">
    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex">
            <div class="card-body">

                <!-- Logo -->
                <div class="text-left text-lg-left mb-1">

                  <!--  @if(isset($tenantInfo) && $tenantInfo!=null && $tenantInfo['logo']!='')
                    <span><img src="{{config('app.api_asset_url').$tenantInfo['logo']}}" alt="logo" height="80" class="login-logo"></span>
                    @else
                    <span><img src="{{asset('img/system/logo/logo-dark.png')}}" alt="logo" height="80" class="landing-logo"></span>
                    @endif-->

<!--                    <h4 class="mt-0">Log In</h4>
-->                 <small style="font-weight:bold">Powered by</small> <br>
                    <span><img src="{{config('app.api_asset_url').'/img/system/logo/logo-dark.png'}}" alt="logo" height="35" class=""></span>

                </div>
                <!-- form -->
                <form action="{{ URL::Route('do_login_tenant',Session()->get('tenant_info')['subdomain']) }}" method="post" id="loginForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="usertype">Type</label>
                        <select name="usertype" id="usertype" class="form-control select2" required>
                            <option value="TA">School Admin</option>
                            <option value="TU">Other School Users</option>
                            <option value="P">Parent</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="emailaddress">Email</label>
                        <input class="form-control" type="email" name="email" id="emailaddress" required=""
                            placeholder="Enter your email">
                    </div>
                    <div class="form-group mb-3">

                        <label for="password">Password</label>
                        <input class="form-control" type="password" name="password" required="" id="password"
                            placeholder="Enter your password">
                    </div>

                    <div class="form-group mb-3">
                        <a href="javascript: void(0);" class="text-muted float-end mb-1" onclick="forgotPass();"><small
                                style="color:#FF0000">Forgot Your Password?</small></a>
                    </div>
                    <div class="form-group mb-3 mb-0 text-center">
                        <button class="btn btn-block btnlogin" style="width:100%" type="submit"><i class="mdi mdi-login"></i>
                            Log In </button>
                    </div>
                </form>

                <form action="{{ URL::Route('do_forgot_password_tenant',Session()->get('tenant_info')['subdomain']) }}" method="post" id="forgotForm" style="display: none;">
                @csrf
                    <div class="form-group mb-3">
                        <a href="javascript: void(0);" class="text-muted float-end" onclick="backToLogin();"><small>Back
                                To Login</small></a>
                                <div class="form-group mb-3">
                        <label for="password">Type</label>
                        <select name="usertype" id="usertype" class="form-control select2" required>
                            <option value="TA">School Admin</option>
                            <option value="TU">Other School Users</option>
                        </select>
                    </div>
                        <label for="forgotEmail">Email</label>
                        <input class="form-control" type="email" name="email" required="" id="forgotEmail"
                            placeholder="Enter your email">
                    </div>
                    <div class="form-group mb-3 mb-0 text-center">
                        <button class="btn btn-primary btn-block btnforgotpass" type="submit"><i class="mdi mdi-login"></i> Reset Password </button>
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
<!-- App js -->
<!-- <script src="{{asset('js/app.min.js')}}"></script> -->

<!--Notify for ajax-->

<script>
$('.btnlogin').on('click',function(){
        $(this).attr('disabled',true);
        $('#loginForm').submit();
    });
    $('.btnforgotpass').on('click',function(){
        $(this).attr('disabled',true);
        $('#forgotForm').submit();
    });
function forgotPass() {
    $('#loginForm').hide();
    $('#forgotForm').show();
}

function backToLogin() {
    $('#forgotForm').hide();
    $('#loginForm').show();
}
$(".super_logo").on("click",function(){
    // alert("hi");
    window.location.href="{{route('front_bye')}}";
});
</script>
@endsection
