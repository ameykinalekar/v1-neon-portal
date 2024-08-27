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
    action="{{route('ta_updateteacherassistant',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="teacher_assistant_id" id="teacher_assistant_id" value="{{$teacher_assistant_details['user_id']??''}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name"
                value="{{$teacher_assistant_details['first_name']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name"
                value="{{$teacher_assistant_details['last_name']??''}}">
        </div>


        <div class="form-group mb-1">
            <label>Subjects</label>
            <div class="col-md-9" id="section_subject">
                @php
                $existing_subject_ids=null;
                if(isset($teacher_assistant_details['subject_ids']) && $teacher_assistant_details['subject_ids']!=null){
                $existing_subject_ids=explode(',',$teacher_assistant_details['subject_ids']);
                }
                //print_r($existing_subject_ids);
                @endphp
                @if(count($subject_list))
                @foreach($subject_list as $record)
                @if($existing_subject_ids!=null && in_array($record['subject_id'],$existing_subject_ids))
                <input type="checkbox" name="subject_id[]" checked
                    value="{{$record['subject_id']}}">&nbsp;{{$record['subject_name'] }}
                ({{$record['name'] .' - '. $record['academic_year']}})<br>
                @else
                <input type="checkbox" name="subject_id[]"
                    value="{{$record['subject_id']}}">&nbsp;{{$record['subject_name'] }}
                ({{$record['name'] .' - '. $record['academic_year']}})<br>
                @endif
                @endforeach
                @endif
            </div>
        </div>
        <div class="form-group mb-1">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{$teacher_assistant_details['phone']??''}}"
                onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Select Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                @foreach($genders as $record)
                @if(isset($teacher_assistant_details['gender']) && $teacher_assistant_details['gender']==$record)
                <option value="{{$record}}" selected>{{$record}}</option>
                @else
                <option value="{{$record}}">{{$record}}</option>
                @endif
                @endforeach
            </select>
        </div>

        <div class="form-group mb-1">
            <label for="ni_number">NI Number</label>
            <input type="text" class="form-control" id="ni_number" name="ni_number"
                value="{{$teacher_assistant_details['ni_number']??''}}" required>
        </div>

        <div class="form-group mb-1">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address"
                rows="5">{{$teacher_assistant_details['address']??''}}</textarea>
        </div>
        <div class="form-group mb-1">
            <label for="about">About</label>
            <textarea class="form-control" id="about" name="about" rows="5">{{$teacher_assistant_details['about']??''}}</textarea>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $teacher_assistant_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mb-1">
            <label for="profile_image">Profile Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="profile_image" name="profile_image"
                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                <h6>Please upload file size 200 x 200 (Pixels)</h6>
                @if($teacher_assistant_details['user_logo']!='')
                <span>
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"
                        href="{{config('app.api_asset_url') . $teacher_assistant_details['user_logo']}}"><img
                            style="padding-top: 13px;"
                            src="{{config('app.api_asset_url') . $teacher_assistant_details['user_logo']}}" height="auto"
                            width="70px" /></a>
                </span>
                @endif
                <img id="imgshowactualpic" width='100%'>
                <input id="imagedata_profile_image" type="hidden" name="imagedata_profile_image" value="">
                <input id="req_width_profile_image" type="hidden" name="req_width_profile_image" value="200">
                <input id="req_height_profile_image" type="hidden" name="req_height_profile_image" value="200">
            </div>

        </div>
        <div class="form-group mb-1">
            <label for="end_date_id">End Date National(government) Id</label>
            <input type="date" class="form-control" id="end_date_id" name="end_date_id" value="{{$teacher_assistant_details['end_date_id']??null}}">
        </div>
        <div class="form-group mb-1">
            <label for="id_file">National(government) Id Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="id_file" name="id_file"
                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                @if($teacher_assistant_details['id_file']!='')
                <span>
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="National(government) Id Image"
                        href="{{config('app.api_asset_url') . $teacher_assistant_details['id_file']}}"><img
                            style="padding-top: 13px;"
                            src="{{config('app.api_asset_url') . $teacher_assistant_details['id_file']}}" height="auto"
                            width="70px" /></a>
                </span>
                @endif
                <img id="imgshow_id_file" width='100%'>
                <input id="imagedata_id_file" type="hidden" name="imagedata_id_file" value="">
                <input id="req_width_id_file" type="hidden" name="req_width_id_file" value="300">
                <input id="req_height_id_file" type="hidden" name="req_height_id_file" value="300">
            </div>

        </div>

        <div class="form-group mb-1">
            <label for="end_date_dbs">End Date Dbs Certificate</label>
            <input type="date" class="form-control" id="end_date_dbs" name="end_date_dbs" value="{{$teacher_assistant_details['end_date_dbs']??null}}">
        </div>
        <div class="form-group mb-1">
            <label for="dbs_certificate_file">Upload Dbs Certificate Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="dbs_certificate_file" name="dbs_certificate_file"
                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                @if($teacher_assistant_details['dbs_certificate_file']!='')
                <span>
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Dbs Certificate Image"
                        href="{{config('app.api_asset_url') . $teacher_assistant_details['dbs_certificate_file']}}"><img
                            style="padding-top: 13px;"
                            src="{{config('app.api_asset_url') . $teacher_assistant_details['dbs_certificate_file']}}"
                            height="auto" width="70px" /></a>
                </span>
                @endif
                <img id="imgshow_dbs_certificate_file" width='100%'>
                <input id="imagedata_dbs_certificate_file" type="hidden" name="imagedata_dbs_certificate_file" value="">
                <input id="req_width_dbs_certificate_file" type="hidden" name="req_width_dbs_certificate_file" value="300">
                <input id="req_height_dbs_certificate_file" type="hidden" name="req_height_dbs_certificate_file" value="300">
            </div>

        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" onclick="cropme();">Update
                Teacher Assistant</button>
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
    $("#id_file").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "imgshow_id_file";
                cropParams['dataImageId'] = "imagedata_id_file";
                cropParams['requiredImageWidth'] = $('#req_width_id_file').val();
                cropParams['requiredImageHeight'] = $('#req_height_id_file').val();

                $("#end_date_id").attr('required',true);
                $('#end_date_id').addClass('is-invalid');
                doCrop(cropParams);
            }
        }else{
            $("#end_date_id").attr('required',false);
            $('#end_date_id').removeClass('is-invalid');
        }
    });
    $("#dbs_certificate_file").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "imgshow_dbs_certificate_file";
                cropParams['dataImageId'] = "imagedata_dbs_certificate_file";
                cropParams['requiredImageWidth'] = $('#req_width_dbs_certificate_file').val();
                cropParams['requiredImageHeight'] = $('#req_height_dbs_certificate_file').val();

                // $("#end_date_dbs").attr('required',true);
                // $('#end_date_dbs').addClass('is-invalid');
                doCrop(cropParams);
            }
        }else{
            // $("#end_date_dbs").attr('required',false);
            // $('#end_date_dbs').removeClass('is-invalid');
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
        var requiredWidthPI = $('#req_width_profile_image').val();
        var requiredHeihtPI = $('#req_height_profile_image').val();
        var srcOriginal = $('#imgshowactualpic').rcrop('getDataURL', requiredWidthPI,requiredHeihtPI);
        $('#imagedata_profile_image').val(srcOriginal);
        $('#profile_image').val('');
    }
    console.log(srcOriginal);
    if (document.getElementById('imgshow_id_file').src == '') {} else {
        var requiredWidthIDF = $('#req_width_id_file').val();
        var requiredHeihtIDF = $('#req_height_id_file').val();
        var srcOriginal = $('#imgshow_id_file').rcrop('getDataURL', requiredWidthIDF,requiredHeihtIDF);
        $('#imagedata_id_file').val(srcOriginal);
    }
    console.log(srcOriginal);
    if (document.getElementById('imgshow_dbs_certificate_file').src == '') {} else {
        var requiredWidthDBS = $('#req_width_dbs_certificate_file').val();
        var requiredHeihtDBS = $('#req_height_dbs_certificate_file').val();
        var srcOriginal = $('#imgshow_dbs_certificate_file').rcrop('getDataURL', requiredWidthDBS,requiredHeihtDBS);
        $('#imagedata_dbs_certificate_file').val(srcOriginal);
    }
    console.log(srcOriginal);
    //document.forms[0].submit();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
