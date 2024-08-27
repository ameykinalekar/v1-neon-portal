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
            <form action="" method="post" id="loginForm">
                @csrf
                <input type="hidden" name="invitation_token" value="{{$token}}">
                <div class="card-body">
                    <p>Hello {{$result['invitee']['name']??''}},</p>
                    <p>{{$result['invitor']['first_name']??''}} {{$result['invitor']['last_name']??''}} has invited you to join </p>
                    <p>
                    <b>{{$result['details']['name']}}</b><br><small>{{$result['details']['description']}}</small>
                    </p>
                    <p>Please take necessary action by clicking on button of your interest provided below.</p>
                    <p>
                        <button name="btnAccept" class="mx-2 btn" type="submit">Accept</button>
                        <button name="btnDecline" class="btn" type="submit">Decline</button>
                    </p>
                </div> <!-- end .card-body -->
            </form>
        </div> <!-- end .align-items-center.d-flex.h-100-->
    </div>

    <!-- end auth-fluid-form-box-->

    <!-- Auth fluid right content -->
    <!-- end Auth fluid right content -->
</div>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();

});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2();
}
</script>
@endsection
