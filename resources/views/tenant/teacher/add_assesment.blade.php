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
                <h4 class="page-title">
                    <i ></i> Add Assesment

                </h4>
                <span id="btnContainer"><a href="#" class="btn btn-sm btn-default" title="">
                            <i class="fa fa-backward"></i> Back</a>
                    </span>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body admin_content">
                <form method="POST" class="d-block ajaxForm" id="frmAddQuestion"
                    action="{{route('tut_saveassesment',Session()->get('tenant_info')['subdomain'])}}">
                    @csrf

                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="form-group required">
                                <label for="year_group_id" class="form-label">Assesment Name</label>
                                <input type="text" class="form-control" name="assesment_name" id="assesment_name" required>
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
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group required">
                                            <label for="question0" class="form-label">Question</label>
                                            <textarea name="question[]" id="question0" cols="30" rows="3"
                                                class="form-control ckeditor question" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group required">
                                            <label for="level0" class="form-label">Difficulty Level</label>
                                            <select name="level[]" id="level0" class="form-control select2_el" required>
                                                <option value="">Select your choice</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group required">
                                            <label for="time_inseconds0" class="form-label">Time Allocated <small>(in
                                                    Minutes)</small></label>
                                            <input type="number" step="any" name="time_inseconds[]" id="time_inseconds0"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group required">
                                            <label for="require_file_upload0" class="form-label">Require File
                                                Submission</label>
                                            <select name="require_file_upload[]" id="require_file_upload0"
                                                class="form-control select2_el" required>
                                                <option value="">Select your choice</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group required">
                                            <label for="point0" class="form-label">Marks</label>
                                            <input type="number" step="any" name="point[]" id="point0"
                                                class="form-control" required>
                                            <input type="hidden" name="question_type[]" id="question_type0" value="text">
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-sm btn-success my-5" type="button" onclick="addOptionRow();">
                                        <i class="fa fa-plus"></i> </button>
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
        $('#level0').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#level0').html('<option value="">Select Question Type</option>');
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

        $('#subject_id').attr("disabled", false);

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

        $('#lesson_id').attr("disabled", false);
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
                $('#level'+ optionRow).html(option);
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#level'+ optionRow).html('<option value="">Select Question Type</option>');
            }

            doAjax(params);
            loadEditor();
        }
    });

    // objTo.appendChild(divtest);
    // reload();

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

function remove_education_fields(rid) {
    $('.removeclass' + rid).remove();
}



$('#frmAddQuestion').on('submit', function() {
    var noname=0;var content;
    $('.ckeditor').each(function() {
        id = $(this).attr('id');
        content = null;
        content=$("#cke_"+id+" iframe").contents().find("body").text();
        console.log("content:"+content+" id: "+id);
        //return false;
        if(content==''){
            noname=1;}
    });
    if(noname>0){
        alert('Please provide the question!');
        return false;
    }
    $('#year_group_id').attr("disabled", false);
    $('#subject_id').attr("disabled", false);
    $('#lesson_id').attr("disabled", false);
    $('#btnSubmit').val('Submitting...').attr("disabled", true);
    return true;
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
