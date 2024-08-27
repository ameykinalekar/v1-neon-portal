@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
<link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css"
    />
<style type="text/css">
    .fancybox__container {
        z-index: 10000 !important;
    }
</style>
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_updateemployee',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="employee_id" id="employee_id" value="{{$employee_details['user_id']??''}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name"
                value="{{$employee_details['first_name']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name"
                value="{{$employee_details['last_name']??''}}">
        </div>

        <div class="form-group mb-1">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{$employee_details['phone']??''}}"
                onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Select Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                @foreach($genders as $record)
                @if(isset($employee_details['gender']) && $employee_details['gender']==$record)
                <option value="{{$record}}" selected>{{$record}}</option>
                @else
                <option value="{{$record}}">{{$record}}</option>
                @endif
                @endforeach
            </select>
        </div>

        <div class="form-group mb-1">
            <label for="last_name">Department</label>
            <select class="form-control" id="department_id" name="department_id" required>
                <option value="">Select Department</option>
                @foreach($department_list as $record)
                @if(isset($employee_details['department_id']) && $employee_details['department_id']==$record['department_id'])
                <option value="{{$record['department_id']}}" selected>{{$record['department_name']}}</option>
                @else
                <option value="{{$record['department_id']}}">{{$record['department_name']}}</option>
                @endif

                @endforeach
            </select>
        </div>

        <div class="form-group mb-1">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address"
                rows="5">{{$employee_details['address']??''}}</textarea>
        </div>

        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $employee_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mb-1">
            <label for="profile_image">Profile Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="profile_image" name="profile_image"
                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                <h6>Please upload file size 200 x 200 (Pixels)</h6>

                @if($employee_details['user_logo']!='')
                <span>
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"
                        href="{{config('app.api_asset_url') . $employee_details['user_logo']}}"><img
                            style="padding-top: 13px;"
                            src="{{config('app.api_asset_url') . $employee_details['user_logo']}}" height="auto"
                            width="70px" /></a>
                </span>
                @endif
                <img id="imgshowactualpic" width='100%'>
                <input id="imagedata_profile_image" type="hidden" name="imagedata_profile_image" value="">
                <input id="req_width_profile_image" type="hidden" name="req_width_profile_image" value="200">
                <input id="req_height_profile_image" type="hidden" name="req_height_profile_image" value="200">
            </div>

        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" onclick="cropme();">Update
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
                if ($('#valid_email').val() > 0 && $('#valid_domain').val() > 0) {
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
                if ($('#valid_email').val() > 0 && $('#valid_domain').val() > 0) {
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
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
