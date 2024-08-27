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
    action="{{route('ta_updatesubject',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="subject_id" id="subject_id" value="{{$subject_details['subject_id']??''}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="academic_year_id">Academic Year</label>
            <select name="academic_year_id" id="academic_year_id" class="form-control select2" required>
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="year_group_id">Year Group</label>
            <select name="year_group_id" id="year_group_id" class="form-control select2" required>
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="board_id">Board</label>
            <select name="board_id" id="board_id" class="form-control select2" required>
                <option value="">Select Board</option>
                @foreach($boards as $record)
                @if($record['status']==GlobalVars::ACTIVE_STATUS || $record['board_id']==$subject_details['board_id'])
                <option value="{{$record['board_id']}}">{{$record['board_name']}}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="name">Subject</label>
            <input type="text" class="form-control" id="subject_name" name="subject_name" required value="{{ $subject_details['subject_name'] ?? '' }}">
        </div>
        <div class="form-group mb-1">
            <label for="name">Description</label>
            <textarea class="form-control" id="description" name="description">{{ $subject_details['description'] ?? '' }}</textarea>
        </div>
        <div class="form-group mb-1">
            <label for="name">Subject Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="subject_image" name="subject_image"
                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                    @if($subject_details['subject_image']!='')
                <h6>Please upload file size 300 x 300 (Pixels)</h6>

                <span>
            <a class="fancy-box-a" data-fancybox="demo" data-caption="Background Image"  href="{{config('app.api_asset_url') . $subject_details['subject_image']}}"><img style="padding-top: 13px;"
                    src="{{config('app.api_asset_url') . $subject_details['subject_image']}}" height="auto"
                    width="70px" /></a>
            </span>
            @endif
                <img id="subject_image_show_image" width='100%'>
                <input id="imagedata_subject_image" type="hidden" name="imagedata_subject_image" value="">
                <input id="req_width_subject_image" type="hidden" name="req_width_subject_image" value="300">
                <input id="req_height_subject_image" type="hidden" name="req_height_subject_image" value="300">
            </div>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $subject_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit"  onclick="cropme();">Update Subject</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('rcrop/dist/rcrop.min.js') }}"></script>
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();
    onPageLoad();
    $('#board_id').val("{{ $subject_details['board_id'] ?? '' }}").trigger('change');

    $("#subject_image").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "subject_image_show_image";
                cropParams['dataImageId'] = "imagedata_subject_image";
                cropParams['requiredImageWidth'] = $('#req_width_subject_image').val();
                cropParams['requiredImageHeight'] = $('#req_height_subject_image').val();


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

function onPageLoad() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-academic-years'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Academic Year</option>';
        response.result.academic_year_list.forEach(function(item) {
            if(item.status=='Active' || item.academic_year_id=="{{ $subject_details['academic_year_id'] ?? '' }}"){
                    option=option+'<option value="' + item.academic_year_id + '">' +
                        item.academic_year + '</option>';
                }
        });
        $('#academic_year_id').html(option);
        $('#academic_year_id').val("{{ $subject_details['academic_year_id'] ?? '' }}").trigger('change');
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#academic_year_id').html('<option value="">Select Academic Year</option>');
    }

    doAjax(params);

}

$("#academic_year_id").on('change',function(){
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-academicyearid-yeargroups'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        academic_year_id: $(this).val()
            });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#year_group_id').html(option);
        $('#year_group_id').attr("disabled","disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Year Group</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + '</option>';
        });
        $('#year_group_id').html(option);
        $('#year_group_id').val("{{ $subject_details['year_group_id'] ?? '' }}").trigger('change');
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#year_group_id').html('<option value="">Select Year Group</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#year_group_id').attr("disabled",false);
    }
    doAjax(params);
});

function cropme() {
    if (document.getElementById('subject_image_show_image').src == '') {} else {
        var requiredWidth = $('#req_width_subject_image').val();
        var requiredHeiht = $('#req_height_subject_image').val();
        var srcOriginal = $('#subject_image_show_image').rcrop('getDataURL', requiredWidth,requiredHeiht);
        $('#imagedata_subject_image').val(srcOriginal);
        $('#subject_image').val('');
    }

}

</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
