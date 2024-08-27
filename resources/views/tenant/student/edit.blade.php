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
    action="{{route('ta_updatestudent',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="student_id" id="student_id" value="{{$student_details['user_id']??''}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{$student_details['first_name']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{$student_details['last_name']??''}}">
        </div>
        <div class="form-group mb-1">
            <label class="mb-1">Subjects</label>
            <div class="col-md-12" id="section_subject">
                @php
                $existing_subject_ids=null;
                if(isset($student_details['subject_ids']) && $student_details['subject_ids']!=null){
                    $existing_subject_ids=explode(',',$student_details['subject_ids']);
                }
                //print_r($existing_subject_ids);
                @endphp
                @if(count($subject_list))
                @foreach($subject_list as $record)
                @if($existing_subject_ids!=null && in_array($record['subject_id'],$existing_subject_ids))
                <input type="checkbox" class="float-start" id="sub_{{$record['subject_id']}}" name="subject_id[]" checked
                    value="{{$record['subject_id']}}"><label for="sub_{{$record['subject_id']}}" class="float-start px-1" style="font-size:11px;font-weight:500;">{{$record['subject_name'] }} - {{$shortboards[$record['board_id']]}}<small>
                ({{$record['name'] .' - '. $record['academic_year']}})</small></label><br>
                @else
                <input type="checkbox" class="float-start" id="sub_{{$record['subject_id']}}" name="subject_id[]"
                    value="{{$record['subject_id']}}"><label for="sub_{{$record['subject_id']}}" class="float-start px-1" style="font-size:11px;font-weight:500;">{{$record['subject_name'] }} - {{$shortboards[$record['board_id']]}}<small>
                ({{$record['name'] .' - '. $record['academic_year']}})</small></label><br>
                @endif
                @endforeach
                @endif
            </div>
        </div>
        <div class="form-group mb-1">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" onkeypress="return isPhone(event);" value="{{$student_details['phone']??''}}" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Select Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                @foreach($genders as $record)
                @if(isset($student_details['gender']) && $student_details['gender']==$record)
                <option value="{{$record}}" selected>{{$record}}</option>
                @else
                <option value="{{$record}}">{{$record}}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="batch_type_id">Batch Details</label>
            <select class="form-control" id="batch_type_id" name="batch_type_id" required>
                <option value="">Select Batch Type</option>
                @foreach($batch_types as $record)
                @if(isset($student_details['batch_type_id']) && $student_details['batch_type_id']==$record['batch_type_id'])
                <option value="{{$record['batch_type_id']}}" selected>{{$record['name']=='one:one'?'One To One':'Group'}}
                </option>
                @else
                <option value="{{$record['batch_type_id']}}">{{$record['name']=='one:one'?'One To One':'Group'}}
                </option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="parent_name">Parent Name</label>
            <input type="text" class="form-control" id="parent_name" name="parent_name" value="{{$student_details['parent_name']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="parent_phone">Parent Phone</label>
            <input type="text" class="form-control" id="parent_phone" name="parent_phone" value="{{$student_details['parent_phone']??''}}" onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>
        <div class="form-group mb-1">
            <label for="parent_email">Parent Email</label>
            <input type="parent_email" class="form-control" id="parent_email" name="parent_email" value="{{$student_details['parent_email']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3">{{$student_details['address']??''}}</textarea>
        </div>
        <div class="form-group mb-1">
            <label class="mb-1">Other Information</label>
            <div class="col-md-12">
                <input type="checkbox" class="float-start" name="have_sensupport_healthcare_plan" id="have_sensupport_healthcare_plan" @if($student_details['have_sensupport_healthcare_plan']=='Y') checked @endif
                    value="Y"><label for="have_sensupport_healthcare_plan" class="float-start px-1" style="font-size:11px;font-weight:500;">Have SEN Support, health and care plan?</label><br>

                <input type="checkbox" class="float-start" name="first_lang_not_eng" id="first_lang_not_eng"
                    value="Y"  @if($student_details['first_lang_not_eng']=='Y') checked @endif><label for="first_lang_not_eng" class="float-start px-1" style="font-size:11px;font-weight:500;">Have first language is not English?</label><br>

                <input type="checkbox" class="float-start" name="freeschool_eligible" id="freeschool_eligible"
                    value="Y"  @if($student_details['freeschool_eligible']=='Y') checked @endif><label for="freeschool_eligible" class="float-start px-1" style="font-size:11px;font-weight:500;">Eligible for free school meals at any time/ Pupil premium?</label><br>
                    <input type="radio" class="float-start" name="commute_transport" id="commute_transport1"
                    value="1"  @if($student_details['take_commute']==1) checked @endif><label for="commute_transport1" class="float-start px-1" style="font-size:11px;font-weight:500;">Take Commute?</label><br>
                    <input type="radio" class="float-start" name="commute_transport" id="commute_transport2"
                    value="2"  @if($student_details['take_transport']==1) checked @endif><label for="commute_transport2" class="float-start px-1" style="font-size:11px;font-weight:500;">Take Transport?</label><br>
            </div>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $student_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mb-1">
            <label for="profile_image">Profile Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="profile_image" name="profile_image"
                    accept="image/x-png,image/jpeg,image/png;capture=camera">

                @if($student_details['user_logo']!='')
                <span>
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"
                        href="{{config('app.api_asset_url') . $student_details['user_logo']}}"><img
                            style="padding-top: 13px;"
                            src="{{config('app.api_asset_url') . $student_details['user_logo']}}" height="auto"
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
                $('#submitBtn').attr('disabled', false);
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
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
