@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('tut_updatelesson',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="lesson_id" id="lesson_id" value="{{$lesson_details['lesson_id']??''}}">
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
            <label for="year_group_id">Subject</label>
            <select name="subject_id" id="subject_id" class="form-control select2" required>
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="lesson_number">Lesson Number</label>
            <input type="number" step="any" class="form-control" id="lesson_number" name="lesson_number" required
                value="{{ $lesson_details['lesson_number'] ?? '' }}">
        </div>
        <div class="form-group mb-1">
            <label for="lesson_name">Lesson Name</label>
            <input type="text" class="form-control" id="lesson_name" name="lesson_name" required
                value="{{ $lesson_details['lesson_name'] ?? '' }}">
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $lesson_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
            <small class="form-text text-muted">Provide Lesson status</small>
        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit">Update Lesson</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();
    onPageLoad();
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
            option = option + '<option value="' + item.academic_year_id + '">' +
                item.academic_year + '</option>';
        });
        $('#academic_year_id').html(option);
        $('#academic_year_id').val("{{ $lesson_details['subject']['academic_year_id'] ?? '' }}").trigger('change');
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#academic_year_id').html('<option value="">Select Academic Year</option>');
    }

    doAjax(params);

}

$("#academic_year_id").on('change', function() {
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
        $('#year_group_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Year Group</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + '</option>';
        });
        $('#year_group_id').html(option);
        $('#year_group_id').val("{{ $lesson_details['subject']['year_group_id'] ?? '' }}").trigger('change');
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#year_group_id').html('<option value="">Select Year Group</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#year_group_id').attr("disabled", false);
    }
    doAjax(params);
});

$("#year_group_id").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-yeargroup-subjects'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        year_group_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#subject_id').html(option);
        $('#subject_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select A Subject</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name+ ' ('+ response.result.boards[item.board_id]+')' + '</option>';
        });
        $('#subject_id').html(option);
        $('#subject_id').val("{{ $lesson_details['subject_id'] ?? '' }}").trigger('change');
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#subject_id').html('<option value="">Select A Subject</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#subject_id').attr("disabled", false);
    }
    doAjax(params);
});
</script>
@endsection