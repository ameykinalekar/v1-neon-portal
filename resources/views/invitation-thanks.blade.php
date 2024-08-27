@extends('layouts.login')
@section('title', 'Invitation')
@section('pagecss')
<link href="{{asset('css/init/login.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<div class="super_logo">
    <img src="{{asset('img/system/logo/logo-dark.png')}}" alt="" height="45px">
</div>
<?php //print_r($result);?>
<div class="auth-fluid">

    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex">

                <div class="card-body text-center" style="font-size:15px;font-weight:600;">
                <p>You have successfully taken action on invitation request.<p>Thanks</p></p>
                </div> <!-- end .card-body -->

        </div> <!-- end .align-items-center.d-flex.h-100-->
    </div>

    <!-- end auth-fluid-form-box-->

    <!-- Auth fluid right content -->
    <!-- end Auth fluid right content -->
</div>
@endsection
@section('pagescript')

@endsection
