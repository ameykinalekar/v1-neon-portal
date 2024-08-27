@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_savestudent',Session()->get('tenant_info')['subdomain'])}}">
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
            <label for="email">Student Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div id="emailValidationError" class="invalid-feedback"></div>
        </div>

        <div class="form-group mb-1">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <input type="checkbox" style="margin: 5px 5px 5px 2px;" onclick="showPassword()">Show Password
        </div>

        <div class="form-group mb-1">
            <label class="mb-1">Subjects</label>
            <div class="col-md-12" id="section_subject">
                @if(count($subject_list))
                @foreach($subject_list as $record)
                <input type="checkbox" class="float-start" id="sub_{{$record['subject_id']}}" name="subject_id[]"
                    value="{{$record['subject_id']}}"><label for="sub_{{$record['subject_id']}}" class="float-start px-1" style="font-size:11px;font-weight:500;">{{$record['subject_name'] }} - {{$shortboards[$record['board_id']]}}<small>
                ({{$record['name'] .' - '. $record['academic_year']}})</small></label><br>
                @endforeach
                @endif
            </div>
        </div>
        <div class="form-group mb-1">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Select Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                @foreach($genders as $record)
                <option value="{{$record}}">{{$record}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="batch_type_id">Batch Details</label>
            <select class="form-control" id="batch_type_id" name="batch_type_id" required>
                <option value="">Select Batch Type</option>
                @foreach($batch_types as $record)
                <option value="{{$record['batch_type_id']}}">{{$record['name']=='one:one'?'One To One':'Group'}}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="parent_name">Parent Name</label>
            <input type="text" class="form-control" id="parent_name" name="parent_name" required>
        </div>
        <div class="form-group mb-1">
            <label for="parent_phone">Parent Phone</label>
            <input type="text" class="form-control" id="parent_phone" name="parent_phone" onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>
        <div class="form-group mb-1">
            <label for="parent_email">Parent Email</label>
            <input type="parent_email" class="form-control" id="parent_email" name="parent_email" required>
        </div>
        <div class="form-group mb-1">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" rows="5"></textarea>
        </div>
        <div class="form-group mb-1">
            <label class="mb-1">Other Information</label>
            <div class="col-md-12">
                <input type="checkbox" class="float-start" name="have_sensupport_healthcare_plan" id="have_sensupport_healthcare_plan"
                    value="Y"><label for="have_sensupport_healthcare_plan" class="float-start px-1" style="font-size:11px;font-weight:500;">Have SEN Support, health and care plan?</label><br>
                <input type="checkbox" class="float-start" name="first_lang_not_eng" id="first_lang_not_eng"
                    value="Y"><label for="first_lang_not_eng" class="float-start px-1" style="font-size:11px;font-weight:500;">Have first language is not English?</label><br>
                <input type="checkbox" class="float-start" name="freeschool_eligible" id="freeschool_eligible"
                    value="Y"><label for="freeschool_eligible" class="float-start px-1" style="font-size:11px;font-weight:500;">Eligible for free school meals at any time/ Pupil premium?</label><br>
                    <input type="radio" class="float-start" name="commute_transport" id="commute_transport1"
                    value="1"><label for="commute_transport1" class="float-start px-1" style="font-size:11px;font-weight:500;">Take Commute?</label><br>
                    <input type="radio" class="float-start" name="commute_transport" id="commute_transport2"
                    value="2"><label for="commute_transport2" class="float-start px-1" style="font-size:11px;font-weight:500;">Take Transport?</label><br>
            </div>
        </div>
        <div class="form-group mb-1">
            <label for="profile_image">Profile Image</label>
                <div class="custom-file-upload">
                    <input type="file" class="form-control" id="profile_image" name="profile_image"
                        accept="image/x-png,image/jpeg,image/png;capture=camera">
                    <h6>Please upload file size 200 x 200 (Pixels)</h6>

                    <img id="imgshowactualpic" width='100%' src="">
                    <input id="imagedata_profile_image" type="hidden" name="imagedata_profile_image" value="">
                    <input id="req_width_profile_image" type="hidden" name="req_width_profile_image" value="200">
                    <input id="req_height_profile_image" type="hidden" name="req_height_profile_image" value="200">
                </div>

        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" onclick="cropme();" disabled>Save
                Student</button>
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
                if ($('#valid_email').val() > 0 ) {
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
                if ($('#valid_email').val() > 0 ) {
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
    //document.forms[0].submit();
}
</script>
@endsection
