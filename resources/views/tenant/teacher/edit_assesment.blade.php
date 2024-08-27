@extends('layouts.default')
@section('title', 'Edit Quiz')
@section('pagecss')

<style>
.card.main_top_overview_card {
    padding: 1px 20px !important;
}

.card-body {
    padding: 5px;
}

/* #btnContainer {
    text-align: right;
    margin-bottom: 15px;
    background: white;
} */
#btnContainer {
    float: inline-end;

}

.thead-dark {
    background: #5BC2B9;
}

.thead-dark th {
    color: white;
    font-weight: 600;
    font-size: 12px;
}

.list {
    display: none;
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> Edit Assesment
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body admin_content"> <?php //dd($examination_details);?>
                <form method="POST" class="d-block ajaxForm" id="frmAddQuestion"
                    action="{{route('tut_saveassesment',Session()->get('tenant_info')['subdomain'])}}">
                    @csrf
                    <input type="hidden" name="examination_id" value="{{$examination_id}}">

                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <div class="form-group required">
                                <label for="year_group_id" class="form-label">Assesment Name</label>
                                <input type="text" class="form-control" name="assesment_name" id="assesment_name"
                                    required value="{{ $examination_details['examination']['name'] }}">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group required">
                                <label for="examination_status" class="form-label">Status</label>
                                <select name="examination_status" id="examination_status" class="form-control">
                                    <option value=""></option>
                                    @foreach($exam_status as $val)
                                    @if($examination_details['examination']['status']==$val)
                                    <option value="{{$val}}" selected>{{$val}}</option>
                                    @else
                                    <option value="{{$val}}">{{$val}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group required">
                                <label for="year_group_id" class="form-label">Year Group</label>
                                <select name="year_group_id" id="year_group_id" class="form-control select2_el"
                                    required>
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



                        <div id="answer_fields" class="optsec">
                            @php $rowid=0; $totalQuestion=0;$totalQuestionSub=0; @endphp


                            @if(isset($examination_details['examination']['examquestions']) &&
                            is_array($examination_details['examination']['examquestions']))
                            @for($i=0; $i<count($examination_details['examination']['examquestions']); $i++) @php
                                $question_info=json_decode($examination_details['examination']['examquestions'][$i]['question_info']);
                                //print_r($question_info->question_type);
                                //print_r($examination_details['examination']['examquestions'][$i]);
                                @endphp
                                @if($question_info->question_type=='linked')
                                @php $parent=$totalQuestion; $totalQuestion++; $rowid++;$totalQuestionSub=0; @endphp
                                <div id="q{{$totalQuestion}}" class="form-group removeclass{{$rowid}}">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group required">
                                                    <label for="question{{$rowid}}" class="form-label">Question</label>
                                                    <textarea name="question[]" id="question{{$rowid}}" cols="30"
                                                        rows="3" class="form-control ckeditor"
                                                        required>{{$question_info->question??''}}</textarea>
                                                    <input type="text" name="question_id[]"
                                                        value="{{$examination_details['examination']['examquestions'][$i]['question_id']}}">
                                                    <input type="text" name="examination_question_id[]"
                                                        value="{{$examination_details['examination']['examquestions'][$i]['examination_question_id']}}">

                                                    <input type="hidden" name="level[]" id="level{{$rowid}}"><input
                                                        type="hidden" name="time_inseconds[]"
                                                        id="time_inseconds{{$rowid}}">
                                                    <input type="hidden" name="require_file_upload[]"
                                                        id="require_file_upload{{$rowid}}">
                                                    <input type="hidden" name="point[]" id="point{{$rowid}}">
                                                    <input type="hidden" name="question_type[]"
                                                        id="question_type{{$rowid}}" value="linked">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="col-md-12"><button class="btn btn-sm btn-success pull-left mx-1"
                                                    title="Add Sub Question" type="button"
                                                    onclick="addSubQuestion('{{$totalQuestion}}');"> <i
                                                        class="fa fa-plus"></i>
                                                </button><button class="btn btn-sm btn-danger pull-left"
                                                    title="Remove Question" type="button"
                                                    onclick="remove_row('{{$rowid}}');"><i
                                                        class="fa fa-minus"></i></button></div>
                                        </div>
                                    </div>


                                    @else
                                    @if($examination_details['examination']['examquestions'][$i]['parent_examination_question_id']>0)
                                    @php $totalQuestionSub++; $rowid++; @endphp
                                    <div id="sq{{$totalQuestionSub}}" class="form-group removeclass{{$rowid}}">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group required"><label for="question{{$rowid}}"
                                                            class="form-label">Sub Question</label><textarea
                                                            name="subquestion{{$parent}}[]" id="question{{$rowid}}"
                                                            cols="30" rows="3" class="form-control ckeditor"
                                                            required>{{$question_info->question??''}}</textarea>
                                                        <input type="text" name="examination_question_id{{$parent}}[]"
                                                            value="{{$examination_details['examination']['examquestions'][$i]['examination_question_id']}}">
                                                        <input type="text" name="question_id{{$parent}}[]"
                                                            value="{{$examination_details['examination']['examquestions'][$i]['question_id']}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group required"><label for="level{{$rowid}}"
                                                            class="form-label">Difficulty Level</label><select
                                                            name="level{{$parent}}[]" id="level{{$rowid}}"
                                                            class="form-control select2_el" required>
                                                            <option value="">Select your choice</option>
                                                        </select>
                                                        <input type="hidden" id="hdlevel{{$rowid}}"
                                                            value="{{$question_info->level??''}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group required"><label
                                                            for="time_inseconds{{$rowid}}" class="form-label">Time
                                                            Allocated <small>(in Minutes)</small></label><input
                                                            type="number" step="any" name="time_inseconds{{$parent}}[]"
                                                            id="time_inseconds{{$rowid}}" class="form-control" required
                                                            value="{{round(($examination_details['examination']['examquestions'][$i]['time_inseconds']/60),2)??'0'}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group required"><label
                                                            for="require_file_upload{{$rowid}}"
                                                            class="form-label">Require File Submission</label><select
                                                            name="require_file_upload{{$parent}}[]"
                                                            id="require_file_upload{{$rowid}}"
                                                            class="form-control select2_el" required>
                                                            <option value="">Select your choice</option>
                                                            <option value="1" @if($question_info->
                                                                require_file_upload==1)
                                                                selected @endif>Yes</option>
                                                            <option value="0" @if($question_info->
                                                                require_file_upload==0)
                                                                selected @endif>No</option>
                                                        </select></div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group required"><label for="point{{$rowid}}"
                                                            class="form-label">Marks</label><input type="number"
                                                            step="any" name="point{{$parent}}[]" id="point{{$rowid}}"
                                                            class="form-control" required
                                                            value="{{$examination_details['examination']['examquestions'][$i]['point']??'0'}}">
                                                        <input type="hidden" name="question_type{{$parent}}[]"
                                                            id="question_type{{$rowid}}" value="text">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1"><button class="btn btn-sm btn-danger pull-left"
                                                    title="Remove Question" type="button"
                                                    onclick="remove_row({{$rowid}});"><i
                                                        class="fa fa-minus"></i></button></div>
                                        </div>
                                    </div>

                                    @if((isset($examination_details['examination']['examquestions'][$i+1]['parent_examination_question_id'])
                                    &&
                                    $examination_details['examination']['examquestions'][$i+1]['parent_examination_question_id']==0)
                                    || count($examination_details['examination']['examquestions'])==($rowid))
                                    <input type="text" name="total_subquestions{{$parent}}"
                                        id="total_subquestions{{$parent}}" value="{{$totalQuestionSub}}">
                                </div>

                                @endif
                                @else
                                @php $totalQuestion++; $rowid++; @endphp
                                <div id="q{{$totalQuestion}}" class="form-group removeclass{{$rowid}}">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group required">
                                                    <label for="question{{$rowid}}" class="form-label">Question</label>
                                                    <textarea name="question[]" id="question{{$rowid}}" cols="30"
                                                        rows="3" class="form-control ckeditor"
                                                        required>{{$question_info->question??''}}</textarea>
                                                    <input type="text" name="examination_question_id[]"
                                                        value="{{$examination_details['examination']['examquestions'][$i]['examination_question_id']}}">
                                                    <input type="text" name="question_id[]"
                                                        value="{{$examination_details['examination']['examquestions'][$i]['question_id']}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group required"><label for="level{{$rowid}}"
                                                        class="form-label">Difficulty Level</label><select
                                                        name="level[]" id="level{{$rowid}}"
                                                        class="form-control select2_el" required>
                                                        <option value="">Select your choice</option>
                                                    </select>
                                                    <input type="hidden" id="hdlevel{{$rowid}}"
                                                        value="{{$question_info->level??''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group required"><label for="time_inseconds{{$rowid}}"
                                                        class="form-label">Time
                                                        Allocated <small>(in
                                                            Minutes)</small></label><input type="number" step="any"
                                                        name="time_inseconds[]" id="time_inseconds{{$rowid}}"
                                                        class="form-control" required
                                                        value="{{round(($examination_details['examination']['examquestions'][$i]['time_inseconds']/60),2)??'0'}}">
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group required"><label
                                                        for="require_file_upload{{$rowid}}" class="form-label">Require
                                                        File Submission</label><select name="require_file_upload[]"
                                                        id="require_file_upload{{$rowid}}"
                                                        class="form-control select2_el" required>
                                                        <option value="">Select your choice</option>
                                                        <option value="1" @if($question_info->
                                                            require_file_upload==1)
                                                            selected @endif>Yes</option>
                                                        <option value="0" @if($question_info->
                                                            require_file_upload==0)
                                                            selected @endif>No</option>
                                                    </select></div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group required"><label for="point{{$rowid}}"
                                                        class="form-label">Marks</label><input type="number" step="any"
                                                        name="point[]" id="point{{$rowid}}" class="form-control"
                                                        required
                                                        value="{{$examination_details['examination']['examquestions'][$i]['point']??'0'}}">
                                                    <input type="hidden" name="question_type[]"
                                                        id="question_type{{$rowid}}" value="text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1"><button class="btn btn-sm btn-danger pull-left"
                                                title="Remove Question" type="button"
                                                onclick="remove_row({{$rowid}});"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                </div>

                                @endif
                                @endif
                                @endfor

                                @endif

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mt-2 col-md-12">
                                    <button class="btn btn-block btn-primary float-end" type="button" id="btnSq">Add New
                                        Single Question</button>
                                    <button class="btn btn-block btn-primary float-end mx-2" type="button"
                                        id="btnLq">Add
                                        New Linked Question</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mt-2 col-md-12">
                                    <input type="text" name="total_questions" id="total_questions"
                                        value="{{$totalQuestion}}">
                                    <input type="text" name="total_rows" id="total_rows" value="{{$rowid}}">
                                    <button class="btn btn-block btn-primary" type="submit" id="btnSubmit">Save</button>
                                    <button class="btn btn-block btn-primary" type="button" id="btnCheck">check</button>

                                </div>
                            </div>
                        </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('pagescript')
<script src="{{asset('plugins/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('plugins/ckeditor/plugins/ckfinder/ckfinder.js')}}"></script>
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();
    onPageLoad();
    loadEditor();



});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({

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
        var ctlCount = $('#total_rows').val();
        for (c = 0; c <= ctlCount; c++) {

            $('#level' + c).html(option);
            var existingValue = $('#hdlevel' + c).val();
            if (existingValue != '') {
                $('#level' + c).val(existingValue).trigger('change');

            }
        }
    }
    params['errorCallBackFunction'] = function(httpObj) {
        var ctlCount = $('#total_rows').val();
        for (c = 0; c <= ctlCount; c++) {
            $('#level' + c).html('<option value="">Select Question Type</option>');
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
                item.subject_name + '</option>';
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

var optionRow = $('#total_rows').val();

function addOptionRow() {

    optionRow++;
    var objTo = document.getElementById('answer_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclass" + optionRow);
    var rdiv = 'removeclass' + optionRow;

    var token = "{{Session::get('usertoken')}}";
    $.ajax({

        url: "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/add-assesment-question'; ?>/" +
            optionRow,
        type: "GET",
        headers: {
            Authorization: 'Bearer ' + token
        },

        success: function(response) {
            console.log(response);
            divtest.innerHTML = response;
            objTo.appendChild(divtest);

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
                $('#level' + optionRow).html(option);
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#level' + optionRow).html('<option value="">Select Question Type</option>');
            }

            doAjax(params);
            loadEditor();
        }
    });

    // objTo.appendChild(divtest);
    // reload();

}

function addSingleQuestion() {
    optionRow++;
    var tot_question = $('#total_questions').val();
    tot_question++;
    var objTo = document.getElementById('answer_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("id", "q" + tot_question);
    divtest.setAttribute("class", "form-group removeclass" + optionRow);
    var rdiv = 'removeclass' + optionRow;

    var token = "{{Session::get('usertoken')}}";
    $.ajax({

        url: "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/add-assesment-question-single'; ?>/" +
            optionRow,
        type: "GET",
        headers: {
            Authorization: 'Bearer ' + token
        },

        success: function(response) {
            console.log(response);
            divtest.innerHTML = response;
            objTo.appendChild(divtest);
            $('#total_questions').val(tot_question);
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
                $('#level' + optionRow).html(option);
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#level' + optionRow).html('<option value="">Select Question Type</option>');
            }

            doAjax(params);
            loadEditor();
        }
    });

    // divtest.innerHTML = "single question"+tot_question;
    // objTo.appendChild(divtest);
    // $('#total_questions').val(tot_question);
}

function addLinkQuestion() {
    optionRow++;
    var tot_question = $('#total_questions').val();
    tot_question++;
    var objTo = document.getElementById('answer_fields')
    var divtest = document.createElement("div");
    var divtestId = "q" + tot_question;
    divtest.setAttribute("id", divtestId);
    divtest.setAttribute("class", "form-group removeclass" + optionRow);
    var rdiv = 'removeclass' + optionRow;

    // divtest.innerHTML = "link question"+tot_question+'<br><input type="text" name="total_subquestions'+tot_question+'" id="total_subquestions'+tot_question+'" value="0"><button class="btn btn-sm btn-success" style="margin-right: 15px;" type="button"  onclick="addSubQuestion(' + tot_question+');"> <i class="fa fa-plus"></i> </button>';
    // objTo.appendChild(divtest);
    // $('#total_questions').val(tot_question);
    var token = "{{Session::get('usertoken')}}";
    $.ajax({

        url: "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/add-assesment-question-link'; ?>/" +
            optionRow + '/' + tot_question,
        type: "GET",
        headers: {
            Authorization: 'Bearer ' + token
        },

        success: function(response) {
            console.log(response);
            divtest.innerHTML = response;
            objTo.appendChild(divtest);
            $('#total_questions').val(tot_question);

            loadEditor();
        }
    });
}

function addSubQuestion(parentdiv) {
    // alert(parentdiv);
    optionRow++;
    var total_subquestions = $('#total_subquestions' + parentdiv).val();
    total_subquestions++;
    var objId = "q" + parentdiv;
    var objTo = document.getElementById(objId)
    var divtest = document.createElement("div");

    var divtestId = "sq" + parentdiv;
    divtest.setAttribute("id", divtestId);

    divtest.setAttribute("class", "form-group removeclass" + optionRow);
    var rdiv = 'removeclass' + optionRow;

    var token = "{{Session::get('usertoken')}}";
    $.ajax({

        url: "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/add-assesment-question-sub'; ?>/" +
            optionRow + '/' + parentdiv,
        type: "GET",
        headers: {
            Authorization: 'Bearer ' + token
        },

        success: function(response) {
            console.log(response);
            divtest.innerHTML = response;
            objTo.appendChild(divtest);
            $('#total_subquestions' + parentdiv).val(total_subquestions);
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
                $('#level' + optionRow).html(option);
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#level' + optionRow).html('<option value="">Select Question Type</option>');
            }

            doAjax(params);

            loadEditor();
        }
    });


}

function loadEditor() {
    $('.ckeditor').each(function() {

        //alert("aaa");
        id = $(this).attr('id');
        if (!CKEDITOR.instances[id])
            CKEDITOR.replace(id);
        //delete CKEDITOR.instances[id];
    });
}


function remove_row(rid) {
    $('.removeclass' + rid).remove();
}

$('#btnSq').on('click', function() {
    addSingleQuestion();
});

$('#btnLq').on('click', function() {
    addLinkQuestion();
});
$('#btnCheck').on('click', function() {
    checkEditors();
});



$('#frmAddQuestion').on('submit', function() {
    var noname = 0;
    var content;
    var allSub = 0;
    for (instance in CKEDITOR.instances) {
        content = null;
        content = CKEDITOR.instances[instance].getData();
        console.log("content:" + content + " id: " + id);
        if (content == '') {
            noname = 1;
        }
    }

    let input = document.getElementsByName('question_type[]');

    for (let i = 0; i < input.length; i++) {
        let a = input[i];
        if (a.value == 'linked') {
            if ($('#total_subquestions' + (i + 1)).val() == 0) {
                allSub++;
            }
        }
    }

    if (noname > 0) {
        alert('Please provide the question!');
        return false;
    }
    if (allSub > 0) {
        alert('Please provide the sub question!');
        return false;
    }
    let total_questions = $('#total_questions').val();
    if (total_questions == 0) {
        alert('Please add the question!');
        return false;
    }

    $('#year_group_id').attr("disabled", false);
    $('#subject_id').attr("disabled", false);
    $('#lesson_id').attr("disabled", false);
    $('#btnSubmit').val('Submitting...').attr("disabled", true);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
