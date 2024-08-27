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
                    <span id="btnContainer">
                        <a href="{{route('tut_assesments',Session()->get('tenant_info')['subdomain'])}}" class="btn btn-sm btn-default" title=""><i class="fa fa-backward"></i> Back</a>

                </h4>

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
                    <input type="hidden" name="total_questions" id="total_questions" value="0">
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <div class="form-group required">
                                <label for="year_group_id" class="form-label">Assesment Name</label>
                                <input type="text" class="form-control" name="assesment_name" id="assesment_name" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group required">
                                <label for="homework" class="form-label">Is Homework?</label>
                                <select class="form-control" id="homework" name ="homework" required>
                                    <option value="">Select your choice</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
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

                        </div>



                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-2 col-md-12">
                                <button class="btn btn-block btn-primary float-end" type="button" id="btnSq">Add New Single Question</button>
                                <button class="btn btn-block btn-primary float-end mx-2" type="button" id="btnLq">Add New Linked Question</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-2 col-md-12">
                                <button class="btn btn-block btn-primary" type="submit" id="btnSubmit">Save</button>
                                <!-- <button class="btn btn-block btn-primary" type="button" id="btnCheck">check</button> -->

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
    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#year_group_id').html(option);
        $('#year_group_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select your choice</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + ' (' + item.academic_year + ')' + '</option>';
        });
        $('#year_group_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#year_group_id').html('<option value="">Select your choice</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#year_group_id').attr("disabled", false);

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
        var option = '<option value="">Select your choice</option>';
        response.result.question_levels.forEach(function(item) {
            option = option + '<option value="' + item.level_key + '">' +
                item.level_text + '</option>';
        });
        $('#level0').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#level0').html('<option value="">Select your choice</option>');
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
        var option = '<option value="">Select your choice</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name + ' ('+ response.result.boards[item.board_id]+')'+ '</option>';
        });
        $('#subject_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#subject_id').html('<option value="">Select your choice</option>');
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
        var option = '<option value="">Select your choice</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.lesson_id + '">' +
                item.lesson_name + '</option>';
        });
        $('#lesson_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#lesson_id').html('<option value="">Select your choice</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#lesson_id').attr("disabled", false);
    }
    doAjax(params);
});

function loadSubTopic(rowid) {
    //console.log('topic on change called.'+rowid);
    // alert($(this).attr("data-id"));
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
        topic_id: $('#topic_id'+rowid).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#sub_topic_id'+rowid).html(option);
        $('#sub_topic_id'+rowid).attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select your choice</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.sub_topic_id + '">' +
                item.sub_topic + '</option>';
        });
        $('#sub_topic_id'+rowid).html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#sub_topic_id'+rowid).html('<option value="">Select your choice</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#sub_topic_id'+rowid).attr("disabled", false);
    }
    doAjax(params);
}

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

function addSingleQuestion(){
    optionRow++;
    var tot_question=$('#total_questions').val();
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
                $('#level'+ optionRow).html(option);
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#level'+ optionRow).html('<option value="">Select Question Type</option>');
            }

            doAjax(params);
            // var params = $.extend({}, doAjax_params_default);
            params['url'] =
                "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-lessonid-topics'; ?>";
            params['requestType'] = "POST";
            params['dataType'] = "json";
            params['contentType'] = "application/json; charset=utf-8";
            params['headers'] = {
                Authorization: 'Bearer ' + token
            };
            params['data'] = JSON.stringify({
                lesson_id: $('#lesson_id').val()
            });

            params['beforeSendCallbackFunction'] = function(response) {
                var option = '<option value="">Loading.....</option>';
                $('#topic_id'+ optionRow).html(option);
                $('#topic_id'+ optionRow).attr("disabled", "disabled");
            }
            params['successCallbackFunction'] = function(response) {
                var option = '<option value="">Select your choice</option>';
                response.result.listing.forEach(function(item) {
                    option = option + '<option value="' + item.topic_id + '">' +
                        item.topic + '</option>';
                });
                $('#topic_id'+ optionRow).html(option);
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#topic_id'+ optionRow).html('<option value="">Select your choice</option>');
            }
            params['completeCallbackFunction'] = function(response) {

                $('#topic_id'+ optionRow).attr("disabled", false);
            }
            doAjax(params);
            loadEditor();
            initailizeSelect2();
        }
    });

    // divtest.innerHTML = "single question"+tot_question;
    // objTo.appendChild(divtest);
    // $('#total_questions').val(tot_question);
}
function addLinkQuestion(){
    optionRow++;
    var tot_question=$('#total_questions').val();
    tot_question++;
    var objTo = document.getElementById('answer_fields')
    var divtest = document.createElement("div");
    var divtestId="q" + tot_question;
    divtest.setAttribute("id", divtestId);
    divtest.setAttribute("class", "form-group removeclass" + optionRow);
    var rdiv = 'removeclass' + optionRow;

    var token = "{{Session::get('usertoken')}}";
    $.ajax({

        url: "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/add-assesment-question-link'; ?>/" +
            optionRow+'/'+tot_question,
        type: "GET",
        headers: {
            Authorization: 'Bearer ' + token
        },

        success: function(response) {
            console.log(response);
            divtest.innerHTML = response;
            objTo.appendChild(divtest);
            $('#total_questions').val(tot_question);

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
                lesson_id: $('#lesson_id').val()
            });

            params['beforeSendCallbackFunction'] = function(response) {
                var option = '<option value="">Loading.....</option>';
                $('#topic_id'+ optionRow).html(option);
                $('#topic_id'+ optionRow).attr("disabled", "disabled");
            }
            params['successCallbackFunction'] = function(response) {
                var option = '<option value="">Select your choice</option>';
                response.result.listing.forEach(function(item) {
                    option = option + '<option value="' + item.topic_id + '">' +
                        item.topic + '</option>';
                });
                $('#topic_id'+ optionRow).html(option);
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#topic_id'+ optionRow).html('<option value="">Select your choice</option>');
            }
            params['completeCallbackFunction'] = function(response) {

                $('#topic_id'+ optionRow).attr("disabled", false);
            }
            doAjax(params);
            loadEditor();
            initailizeSelect2();
        }
    });
}

function addSubQuestion(parentdiv){
    // alert(parentdiv);
    optionRow++;
    var total_subquestions=$('#total_subquestions'+parentdiv).val();
    total_subquestions++;
    var objId="q" + parentdiv;
    var objTo = document.getElementById(objId)
    var divtest = document.createElement("div");

    var divtestId="sq" + parentdiv;
    divtest.setAttribute("id", divtestId);

    divtest.setAttribute("class", "form-group removeclass" + optionRow);
    var rdiv = 'removeclass' + optionRow;

    var token = "{{Session::get('usertoken')}}";
    $.ajax({

        url: "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/add-assesment-question-sub'; ?>/" +
            optionRow+'/'+parentdiv,
        type: "GET",
        headers: {
            Authorization: 'Bearer ' + token
        },

        success: function(response) {
            console.log(response);
            divtest.innerHTML = response;
            objTo.appendChild(divtest);
            $('#total_subquestions'+parentdiv).val(total_subquestions);
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

$('#btnSq').on('click',function(){
    if($('#lesson_id').val()==''){
        alert('Please select lesson!');
    }else{
        addSingleQuestion();

    }
});

$('#btnLq').on('click',function(){

    if($('#lesson_id').val()==''){
        alert('Please select lesson!');
    }else{
        addLinkQuestion();

    }
});
$('#btnCheck').on('click',function(){
    checkEditors();
});

function checkEditors(){
    for (instance in CKEDITOR.instances) {
        alert(CKEDITOR.instances[instance].getData());
        // CKEDITOR.instances[instance].on('change', function ()
        // {
        //     var editorName = $(this)[0].name;
        //     CKEDITOR.instances[editorName].updateElement();
        // });
    }
}


$('#frmAddQuestion').on('submit', function() {
    var noname=0;
    var nonameId='';
    var content;
    var allSub=0;
    for (instance in CKEDITOR.instances){
        content = null;
        content=CKEDITOR.instances[instance].getData();
        console.log("content:"+content+" id: "+instance);
        if(content==''){
            noname=1;
            nonameId=instance;
           // exit;
        }
    }

    let input = document.getElementsByName('question_type[]');

    for (let i = 0; i < input.length; i++) {
        let a = input[i];
        if(a.value=='linked'){
            if($('#total_subquestions'+(i+1)).val()==0){
                allSub++;
            }
        }
    }

    if(noname>0){
        console.log('##'+nonameId);
        alert('Please provide the question!');

        $('html, body').animate({
            scrollTop: $('#'+nonameId).offset().top
        }, 300);
        return false;
    }
    if(allSub>0){
        alert('Please provide the sub question!');
        return false;
    }
    let total_questions=$('#total_questions').val();
    if(total_questions==0){
        alert('Please add a question!');
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
