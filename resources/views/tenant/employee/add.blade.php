@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_saveemployee',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="valid_email" id="valid_email" value="0">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name">
        </div>
        <div class="form-group mb-1">
            <label for="email">Employee Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div id="emailValidationError" class="invalid-feedback"></div>
        </div>

        <div class="form-group mb-1">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <input type="checkbox" style="margin: 5px 5px 5px 2px;" onclick="showPassword()">Show Password
        </div>

        <div class="form-group mb-1">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                @foreach($genders as $record)
                <option value="{{$record}}">{{$record}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Department</label>
            <select class="form-control" id="department_id" name="department_id" required>
                <option value="">Select Department</option>
                @foreach($department_list as $record)
                <option value="{{$record['department_id']}}">{{$record['department_name']}}</option>
                @endforeach
            </select>
        </div>


        <div class="form-group mb-1">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" rows="5"></textarea>
        </div>

        <div class="form-group mb-1">
            <label for="profile_image">Profile Image</label>
                <div class="custom-file-upload">
                    <input type="file" class="form-control" id="profile_image" name="profile_image"
                        accept="image/x-png,image/jpeg,image/png;capture=camera">
                    <small>Please upload file size 200 x 200 (Pixels)</small>

                    <img id="imgshowactualpic" width='100%'>
                    <input id="imagedata_profile_image" type="hidden" name="imagedata_profile_image" value="">
                    <input id="req_width_profile_image" type="hidden" name="req_width_profile_image" value="200">
                    <input id="req_height_profile_image" type="hidden" name="req_height_profile_image" value="200">
                </div>
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" onclick="cropme();" disabled>Save
                Employee</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script src="{{ asset('rcrop/dist/rcrop.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();

    $('#email').on("focusout", function() {
        var email = $("#email").val();
        $('#emailValidationError').hide();
        if (email != '' && typeof email != 'undefined') {
            var params = $.extend({}, doAjax_params_default);
            params['url'] =
                "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/email/exist'; ?>";
            params['requestType'] = "POST";
            params['dataType'] = "json";
            params['contentType'] = "application/json; charset=utf-8";
            params['data'] = JSON.stringify({
                email: email
            });
            params['successCallbackFunction'] = function(response) {
                $('#emailValidationError').html('');
                $('#email').removeClass('is-invalid');
                $('#valid_email').val('1');
                if ($('#valid_email').val() > 0) {
                    $('#submitBtn').attr('disabled', false);

                }
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#emailValidationError').show();
                $('#emailValidationError').html(httpObj.responseJSON.error.message);
                $('#email').addClass('is-invalid');
            }
            params['completeCallbackFunction'] = function(data) {
                $("#email").attr('disabled', false);
                if ($('#valid_email').val() > 0) {
                    $('#submitBtn').attr('disabled', false);
                }
            }


            doAjax(params);

        }
    });

    $("#profile_image").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "imgshowactualpic";
                cropParams['dataImageId'] = "imagedata_profile_image";
                cropParams['requiredImageWidth'] = $('#req_width_profile_image').val();
                cropParams['requiredImageHeight'] = $('#req_height_profile_image').val();

                doCrop(cropParams);
            }
        }
    });
});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({
        dropdownParent: $("#right-modal")
    });
}

function cropme() {
    if (document.getElementById('imgshowactualpic').src == '') {} else {
        var requiredWidth = $('#req_width_profile_image').val();
        var requiredHeiht = $('#req_height_profile_image').val();
        var srcOriginal = $('#imgshowactualpic').rcrop('getDataURL', requiredWidth,requiredHeiht);
        $('#imagedata_profile_image').val(srcOriginal);
        $('#profile_image').val('');
    }
    console.log(srcOriginal);
}
</script>
@endsection
