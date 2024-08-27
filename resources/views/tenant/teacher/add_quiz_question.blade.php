@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" id="frmAddQuestion"
    action="{{route('tut_savequizquestion',Session()->get('tenant_info')['subdomain'])}}">
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
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="question_type" class="form-label">Question Type</label>
                <select name="question_type" id="question_type" class="form-control select2_el" required>
                    <option value="">Select your choice</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="level" class="form-label">Difficulty Level</label>
                <select name="level" id="level" class="form-control select2_el" required>
                    <option value="">Select your choice</option>
                </select>
            </div>

        </div>

        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="require_file_upload" class="form-label">Require File Submission</label>
                <select name="require_file_upload" id="require_file_upload" class="form-control select2_el" required>
                    <option value="">Select your choice</option>
                    <option value="1">Yes</option>
                    <option value="0" selected>No</option>
                </select>
            </div>

        </div>
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="point" class="form-label">Marks</label>
                <input type="number" step="any" min=0 name="point" id="point" class="form-control" required>
            </div>

        </div>
        <div class="col-md-4 mb-2">
            <div class="form-group required">
                <label for="time_inseconds" class="form-label">Time Allocated <small>(in Minutes)</small></label>
                <input type="number" step="any" min=0 name="time_inseconds" id="time_inseconds" class="form-control"
                    required>
            </div>
        </div>
        <div class="col-md-8 mb-2">
            <div class="form-group required">
                <label for="topic_id" class="form-label">Skill Tags</label>
                <div class="row">
                    <div class="col-md-3 mb-2"><input type="checkbox" name="tc" value="1" class="float-start"><label
                            class="float-start px-1" style=";font-weight:500;">TC</label></div>
                    <div class="col-md-3 mb-2"><input type="checkbox" name="ms" value="1" class="float-start"><label
                            class="float-start px-1" style="font-weight:500;">MS</label></div>
                    <div class="col-md-3 mb-2"><input type="checkbox" name="ps" value="1" class="float-start"><label
                            class="float-start px-1" style="font-weight:500;">PS</label></div>
                    <div class="col-md-3 mb-2"><input type="checkbox" name="at" value="1" class="float-start"><label
                            class="float-start px-1" style="font-weight:500;">AT</label></div>
                </div>

            </div>
        </div>
        <div class="col-md-12 mb-2">
            <div class="form-group required">
                <label for="question" class="form-label">Question</label>
                <textarea name="question" id="question" cols="30" rows="3" class="form-control ckeditor"
                    required></textarea>
            </div>
        </div>
        <label class="form-label optsec"
            style="border-bottom: 1px solid #ccc;padding: 15px 0;margin-bottom: 16px;">Options</label>

        <div id="answer_fields" class="optsec">
            <div class="row mb-2">
                <div class="col-md-7">
                    <input type="text" class="form-control" name="qoptions[]">
                    <input type="hidden" class="form-control" name="qcorrect[]" value="0" id="qcorrect0">
                </div>
                <div class="col-md-3">
                    <input type="radio" id="anschk0" name="is_correct[]" data-id="0" class="form-check"
                        onclick="check(this);"> <label>Correct</label>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-success" type="button" onclick="addOptionRow();"> <i
                            class="fa fa-plus"></i> </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mt-2 col-md-12">
                <button class="btn btn-block btn-primary" type="submit" id="btnSubmit">Save</button>
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
    $('.ckeditor').each(function() {
        id = $(this).attr('id');
        CKEDITOR.replace(id);
    });



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
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + ' (' + item.academic_year + ')' + '</option>';
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
    //set & disable year group based on existing


    params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/dropdown/portal-question-types'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        X_NEON: "{{config('app.api_key')}}"
    };

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Question Type</option>';
        response.result.question_types.forEach(function(item) {
            option = option + '<option value="' + item.type_key + '">' +
                item.type_text + '</option>';
        });
        $('#question_type').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#question_type').html('<option value="">Select Question Type</option>');
    }

    doAjax(params);

    params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/dropdown/portal-question-levels'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        X_NEON: "{{config('app.api_key')}}"
    };

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Difficulty Level</option>';
        response.result.question_levels.forEach(function(item) {
            option = option + '<option value="' + item.level_key + '">' +
                item.level_text + '</option>';
        });
        $('#level').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#level').html('<option value="">Select Question Type</option>');
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


var optionRow = 0;

function addOptionRow() {

    optionRow++;
    var objTo = document.getElementById('answer_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclass" + optionRow);
    var rdiv = 'removeclass' + optionRow;
    divtest.innerHTML =
        '<div class="row mb-2"><div class="col-md-7"><input type="text" class="form-control" name="qoptions[]"><input type="hidden" class="form-control" name="qcorrect[]" value="0" id="qcorrect' +
        optionRow + '"></div><div class="col-md-3"><input type="radio" id="anschk' + optionRow +
        '" name="is_correct[]" data-id="' + optionRow +
        '" class="form-check" onclick="check(this);"> <label>Correct</label></div><div class="col-md-2"><div class="input-group-btn btn-up"><button class="btn btn-sm btn-success" style="margin-right: 15px;" type="button"  onclick="addOptionRow();"> <i class="fa fa-plus"></i> </button><button class="btn btn-sm btn-danger" type="button" onclick="remove_education_fields(' +
        optionRow + ');"><i class="fa fa-minus"></i></button></div></div></div>';

    objTo.appendChild(divtest)
}

function remove_education_fields(rid) {
    $('.removeclass' + rid).remove();
}

function check(obj) {
    var recid = $('#' + obj.id).data("id");
    // alert(obj.checked);
    if (obj.checked) {
        $('#qcorrect' + recid).val('1');
    } else {
        $('#qcorrect' + recid).val('0');
    }
}
$('#frmAddQuestion').on('submit', function() {
    $('#year_group_id').attr("disabled", false);
    $('#subject_id').attr("disabled", false);
    $('#lesson_id').attr("disabled", false);
    $('#btnSubmit').val('Submitting...').attr("disabled", true);
});
$('#question_type').on('change', function() {
    if ($(this).val() == 'text') {
        $('.optsec').hide();
    } else {
        $('.optsec').show();
    }
});
</script>
@endsection