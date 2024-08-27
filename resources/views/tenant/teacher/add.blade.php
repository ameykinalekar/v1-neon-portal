@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_saveteacher',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="valid_email" id="valid_email" value="0">
    <input type="hidden" name="valid_ni" id="valid_ni" value="0">
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
            <label for="email">Teacher Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div id="emailValidationError" class="invalid-feedback"></div>
        </div>

        <div class="form-group mb-1">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <input type="checkbox" style="margin: 5px 5px 5px 2px;" onclick="showPassword()">Show Password
        </div>
        <div class="form-group mb-1">
            <label for="last_name">Department</label>
            <select class="form-control" id="department_id" name="department_id">
                <option value="">Select Department</option>
                @foreach($department_list as $record)
                <option value="{{$record['department_id']}}">{{$record['department_name']}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label>Subjects</label>
            <div class="col-md-12" id="section_subject">
                @if(count($subject_list))
                @foreach($subject_list as $record)
                <input type="checkbox" class="float-start" name="subject_id[]" id="sub_{{$record['subject_id']}}"
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
            <label for="last_name">Is Qualified Teacher?</label>
            <select class="form-control" id="is_qualified_faculty" name="is_qualified_faculty" required>
                <option value="">Select your choice</option>
                
                <option value="1">Yes</option>
                <option value="0">No</option>
                
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="ni_number">NI Number</label>
            <input type="text" class="form-control" id="ni_number" name="ni_number" required>
            <div id="niValidationError" class="invalid-feedback"></div>
        </div>

        <div class="form-group mb-1">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" rows="5"></textarea>
        </div>
        <div class="form-group mb-1">
            <label for="about">About</label>
            <textarea class="form-control" id="about" name="about" rows="5"></textarea>
        </div>
        <div class="form-group mb-1">
            <label for="profile_image">Profile Image</label>
                <div class="custom-file-upload">
                    <input type="file" class="form-control" id="profile_image" name="profile_image"
                        accept="image/x-png,image/jpeg,image/png;capture=camera">
                    <h6>Please upload file size 200 x 200 (Pixels)</h6>

                    <img id="imgshowactualpic" width='100%'>
                    <input id="imagedata_profile_image" type="hidden" name="imagedata_profile_image" value="">
                    <input id="req_width_profile_image" type="hidden" name="req_width_profile_image" value="200">
                    <input id="req_height_profile_image" type="hidden" name="req_height_profile_image" value="200">
                </div>

        </div>
        <div class="form-group mb-1">
            <label for="end_date_id">End Date National(government) Id</label>
            <input type="date" class="form-control" id="end_date_id" name="end_date_id">
        </div>
        <div class="form-group mb-1">
            <label for="id_file">National(government) Id Image</label>
                <div class="custom-file-upload">
                    <input type="file" class="form-control" id="id_file" name="id_file"
                        accept="image/x-png,image/jpeg,image/png;capture=camera">

                    <img id="imgshow_id_file" width='100%'>
                    <input id="imagedata_id_file" type="hidden" name="imagedata_id_file" value="">
                    <input id="req_width_id_file" type="hidden" name="req_width_id_file" value="300">
                    <input id="req_height_id_file" type="hidden" name="req_height_id_file" value="300">
                </div>

        </div>

        <div class="form-group mb-1">
            <label for="end_date_dbs">End Date Dbs Certificate</label>
            <input type="date" class="form-control" id="end_date_dbs" name="end_date_dbs">
        </div>
        <div class="form-group mb-1">
            <label for="dbs_certificate_file">Upload Dbs Certificate Image</label>
                <div class="custom-file-upload">
                    <input type="file" class="form-control" id="dbs_certificate_file" name="dbs_certificate_file"
                        accept="image/x-png,image/jpeg,image/png;capture=camera">

                    <img id="imgshow_dbs_certificate_file" width='100%'>
                    <input id="imagedata_dbs_certificate_file" type="hidden" name="imagedata_dbs_certificate_file" value="">
                    <input id="req_width_dbs_certificate_file" type="hidden" name="req_width_dbs_certificate_file" value="300">
                    <input id="req_height_dbs_certificate_file" type="hidden" name="req_height_dbs_certificate_file" value="300">
                </div>

        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" onclick="cropme();" disabled>Save
                Teacher</button>
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

    $('#ni_number').on("focusout", function() {
        var ni_number = $("#ni_number").val();
        $('#emailValidationError').hide();
        if (ni_number != '' && typeof ni_number != 'undefined') {
            var params = $.extend({}, doAjax_params_default);
            params['url'] =
                "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/nino/exist'; ?>";
            params['requestType'] = "POST";
            params['dataType'] = "json";
            params['contentType'] = "application/json; charset=utf-8";
            params['data'] = JSON.stringify({
                ni_number: ni_number
            });
            params['successCallbackFunction'] = function(response) {
                $('#niValidationError').html('');
                $('#ni_number').removeClass('is-invalid');
                $('#valid_ni').val('1');
                if ($('#valid_ni').val() > 0) {
                    $('#submitBtn').attr('disabled', false);

                }
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#niValidationError').show();
                $('#niValidationError').html(httpObj.responseJSON.error.message);
                $('#ni_number').addClass('is-invalid');
            }
            params['completeCallbackFunction'] = function(data) {
                $("#ni_number").attr('disabled', false);
                if ($('#valid_ni').val() > 0) {
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
        var existing_end_date_id=$('#end_date_id').val();
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "imgshow_id_file";
                cropParams['dataImageId'] = "imagedata_id_file";
                cropParams['requiredImageWidth'] = $('#req_width_id_file').val();
                cropParams['requiredImageHeight'] = $('#req_height_id_file').val();
                if(existing_end_date_id==''){
                    $("#end_date_id").attr('required',true);
                    $('#end_date_id').addClass('is-invalid');
                }
                doCrop(cropParams);
            }
        }else{
            $("#end_date_id").attr('required',false);
            $('#end_date_id').removeClass('is-invalid');
        }
    });
    $("#end_date_id").change(function(){
        var existing_id_file=$('#id_file').val();
        // console.log('end_date_id on change '+existing_id_file);
        if(existing_id_file == ''){
            // console.log('inside');
            $("#id_file").attr('required',true);
            $('#id_file').addClass('is-invalid');
        }else{
            $("#id_file").attr('required',false);
            $('#id_file').removeClass('is-invalid');
        }
    });
    $("#dbs_certificate_file").change(function() {
        var existing_end_date_dbs=$('#end_date_dbs').val();
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "imgshow_dbs_certificate_file";
                cropParams['dataImageId'] = "imagedata_dbs_certificate_file";
                cropParams['requiredImageWidth'] = $('#req_width_dbs_certificate_file').val();
                cropParams['requiredImageHeight'] = $('#req_height_dbs_certificate_file').val();

                // if(existing_end_date_dbs==''){
                //     $("#end_date_dbs").attr('required',true);
                //     $('#end_date_dbs').addClass('is-invalid');
                // }
                doCrop(cropParams);
            }
        }else{
            // $("#end_date_dbs").attr('required',false);
            // $('#end_date_dbs').removeClass('is-invalid');
        }
    });
    // $("#end_date_dbs").change(function(){
    //     var existing_dbs_certificate_file=$('#dbs_certificate_file').val();
    //     if(existing_dbs_certificate_file == ''){
    //         $("#dbs_certificate_file").attr('required',true);
    //         $('#dbs_certificate_file').addClass('is-invalid');
    //     }else{
    //         $("#dbs_certificate_file").attr('required',false);
    //         $('#dbs_certificate_file').removeClass('is-invalid');
    //     }
    // });
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
@endsection
