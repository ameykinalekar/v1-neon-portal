@extends('layouts.default')
@section('title', 'Manage Profile')
@section('pagecss')
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
<link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css"
    />
@endsection
@section('content')
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class=""> <i class="mdi mdi-settings title_icon"></i>Manage Profile</h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end page title -->

<div class="row">
    <div id="profile_content" class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
        <div class="row justify-content-md-center">
            <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Update Profile</h4>
                        <form method="POST" class="col-12 profileForm" id="profileForm" enctype="multipart/form-data">
                            @csrf
                            <div class="col-12">
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label" for="first_name">
                                        Name</label>
                                    <div class="col-md-3">
                                        <input type="text" id="first_name" name="first_name" class="form-control"
                                            value="{{$profileInfo['first_name']??''}}" placeholder="First Name"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" id="middle_name" name="middle_name" class="form-control"
                                            value="{{$profileInfo['middle_name']??''}}" placeholder="Middle Name">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" id="last_name" name="last_name" class="form-control"
                                            value="{{$profileInfo['last_name']??''}}" placeholder="Last Name">
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label" for="email">Email</label>
                                    <div class="col-md-9">
                                        <input type="email" id="email" name="email" class="form-control"
                                            value="{{$userInfo['email']??''}}" required placeholder="Email" readonly>
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label" for="phone">
                                        Phone</label>
                                    <div class="col-md-9">
                                        <input type="tel" id="phone" name="phone" class="form-control"
                                            onkeypress="return isPhone(event);" value="{{$userInfo['phone']??''}}"
                                            placeholder="Phone No."  title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}" ><small><i>10 digit telephone number with no dashes or dots.</i></small>
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label" for="address">
                                        Address</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" id="address" name="address" rows="5"
                                            placeholder="Address">{{$profileInfo['address']??''}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label" for="profile_image">Profile Image</label>
                                    <div class="col-md-7">
                                        <div class="custom-file-upload">
                                            <input type="file" class="form-control" id="profile_image"
                                                name="profile_image"
                                                accept="image/x-png,image/jpeg,image/png;capture=camera">

                                            <img id="imgshowactualpic" width='100%' src="">
                                            <input id="imagedata_profile_image" type="hidden"
                                                class="form-control input-border-bottom" name="imagedata_profile_image"
                                                value="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        @if($userInfo['user_logo']!='')
                                        <span>
                                        <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"  href="{{config('app.api_asset_url') . $userInfo['user_logo']}}"><img style="padding-top: 13px;"
                                                src="{{config('app.api_asset_url') . $userInfo['user_logo']}}"
                                                height="auto" width="70px" /></a>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" name="btnUpdateProfile"
                                        class="btn btn-secondary col-xl-4 col-lg-4 col-md-12 col-sm-12"
                                        onclick="cropme();">Update Profile</button>
                                </div>
                            </div>
                        </form>

                    </div> <!-- end card body onclick="updateProfileInfo()"-->
                </div> <!-- end card -->
            </div>

            <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Change Password</h4>
                        <form method="POST" class="col-12 changePasswordAjaxForm"
                            action="" id="changePasswordAjaxForm"
                            enctype="multipart/form-data" onsubmit="return validateChangePassword();">
                            @csrf
                            <div class="col-12">
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label" for="current_password">
                                        Current Password</label>
                                    <div class="col-md-9">
                                        <input type="password" id="current_password" name="current_password"
                                            class="form-control" value="" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label" for="new_password">
                                        New Password</label>
                                    <div class="col-md-9">
                                        <input type="password" id="new_password" name="new_password"
                                            class="form-control" value="" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label" for="confirm_password">
                                        Confirm Password</label>
                                    <div class="col-md-9">
                                        <input type="password" id="confirm_password" name="confirm_password"
                                            class="form-control" value="" required>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" name="btnChangePassword"
                                        class="btn btn-secondary col-xl-4 col-lg-4 col-md-12 col-sm-12"
                                        >Change Password</button>
                                </div>
                            </div>
                        </form>

                    </div> <!-- end card body onclick="changePassword()"-->
                </div> <!-- end card -->
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script src="{{ asset('rcrop/dist/rcrop.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#profile_image").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.rcrop-preview-wrapper').remove();
                    $('#imgshowactualpic').rcrop('destroy');
                    $('#imgshowactualpic').removeAttr('src');
                    $('#imgshowactualpic').attr('src', e.target.result);
                    $('#imgshowactualpic').rcrop({
                        minSize: [300, 300],
                        preserveAspectRatio: true,

                        preview: {
                            display: false,
                            size: [100, 100],
                        }
                    });
                    //$('#btnCrop').show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        }
    });
});

function cropme() {
    if (document.getElementById('profile_image').files.length >0) {
        var srcOriginal = $('#imgshowactualpic').rcrop('getDataURL',300,300);
        $('#imagedata_profile_image').val(srcOriginal);
    }
}
function validateChangePassword(){
    if($('#current_password').val()==$('#new_password').val()){
        alert('New & current password cannot be same.');
        return false;
    }
    if($('#confirm_password').val()!=$('#new_password').val()){
        alert('New & confirm password does not match.');
        return false;
    }
    return true;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
