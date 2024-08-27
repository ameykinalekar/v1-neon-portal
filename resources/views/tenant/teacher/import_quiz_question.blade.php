@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" id="frmAddQuestion"
    action="{{route('tut_saveimportquizquestion',Session()->get('tenant_info')['subdomain'])}}"  enctype='multipart/form-data'>
    @csrf
    <input type="hidden" name="page_id" value="{{$page_id}}">
    <input type="hidden" name="examination_id" value="{{$examination_id}}">
    <div class="row">
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="year_group_id" class="form-label">Year Group</label>
                <select name="year_group_id" id="year_group_id" class="form-control select2_el" required>
                    <option value="">Select your choice</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="subject_id" class="form-label">Subject</label>
                <select name="subject_id" id="subject_id" class="form-control select2_el" required>
                    <option value="">Select your choice</option>
                </select>
            </div>

        </div>
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="lesson_id" class="form-label">Lesson</label>
                <select name="lesson_id" id="lesson_id" class="form-control select2_el" required>
                    <option value="">Select your choice</option>
                </select>
            </div>

        </div>
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="topic_id" class="form-label">Topic</label>
                <select name="topic_id" id="topic_id" class="form-control select2_el" required>
                    <option value="">Select your choice</option>
                </select>
            </div>

        </div>
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="sub_topic_id" class="form-label">Sub Topic</label>
                <select name="sub_topic_id" id="sub_topic_id" class="form-control select2_el" required>
                    <option value="">Select your choice</option>
                </select>
            </div>

        </div>
        <div class="form-group mb-1">
            {!! Form::label('Choose XLS File to Import') !!} <strong class="error">*</strong>
            {{ Form::file('import_file', ['required','class' => 'form-control validate-file','data-msg-accept'=>"File must be XLS or XLSX",'accept'=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"]) }}
        </div>
        <div class="form-group">
            <a target="_blank" href="{{ config('app.api_asset_url').'/sampledata/quiz-question-import-sample-format.xlsx' }}">Click to view/download sample format</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mt-2 col-md-12">
                <button class="btn btn-block btn-primary" type="submit" id="btnSubmit">Import</button>
            </div>
        </div>
    </div>

</form>
@endsection
@section('pagescript')
<script src="{{asset('plugins/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('plugins/ckeditor/plugins/ckfinder/ckfinder.js')}}"></script>
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();
    onPageLoad();
    
});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({
        dropdownParent: $("#question-modal")
    });
}

function onPageLoad() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-yeargroups'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Year Group</option>';
        response.result.yeargroup_list.forEach(function(item) {
            if(item.academic_year_status=='Active'){
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + ' (' + item.academic_year + ')' + '</option>';
            }
        });
        $('#year_group_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#year_group_id').html('<option value="">Select Year Group</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        var existingValue = "{{$examination_details['examination']['year_group_id'] ?? ''}}";
        if (existingValue != '') {
            $('#year_group_id').val(existingValue).trigger('change');
            $('#year_group_id').attr("disabled", true);
        }
    }


    doAjax(params);

}

$("#year_group_id").on('change', function() {

    var token = "{{Session::get('usertoken')}}";
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
        var option = '<option value="">Select Subject</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name + ' (' + response.result.boards[item.board_id] + ')' +
                '</option>';
        });
        $('#subject_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#subject_id').html('<option value="">Select Subject</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        var existingValue = "{{$examination_details['examination']['subject_id'] ?? ''}}";
        if (existingValue != '') {
            $('#subject_id').val(existingValue).trigger('change');
            $('#subject_id').attr("disabled", true);
        } else {
            $('#subject_id').attr("disabled", false);
        }

    }
    if ($(this).val() != '') {
        doAjax(params);
    }
});

$("#subject_id").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-subjectid-lessons'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        subject_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#lesson_id').html(option);
        $('#lesson_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Lesson</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.lesson_id + '">' +
                item.lesson_name + '</option>';
        });
        $('#lesson_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#lesson_id').html('<option value="">Select Lesson</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        var existingValue = "{{$examination_details['examination']['lesson_id'] ?? ''}}";
        if (existingValue != '') {
            $('#lesson_id').val(existingValue).trigger('change');
            $('#lesson_id').attr("disabled", true);
        } else {
            $('#lesson_id').attr("disabled", false);
        }
    }
    doAjax(params);
});


$("#lesson_id").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-lessonid-topics'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        lesson_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#topic_id').html(option);
        $('#topic_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select your choice</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.topic_id + '">' +
                item.topic + '</option>';
        });
        $('#topic_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#topic_id').html('<option value="">Select your choice</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#topic_id').attr("disabled", false);
    }
    doAjax(params);
});

$("#topic_id").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-topicid-subtopics'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        topic_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#sub_topic_id').html(option);
        $('#sub_topic_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select your choice</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.sub_topic_id + '">' +
                item.sub_topic + '</option>';
        });
        $('#sub_topic_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#sub_topic_id').html('<option value="">Select your choice</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#sub_topic_id').attr("disabled", false);
    }
    doAjax(params);
});


$('#frmAddQuestion').on('submit', function() {
    $('#year_group_id').attr("disabled", false);
    $('#subject_id').attr("disabled", false);
    $('#lesson_id').attr("disabled", false);
    $('#btnSubmit').val('Submitting...').attr("disabled", true);
});

</script>
@endsection