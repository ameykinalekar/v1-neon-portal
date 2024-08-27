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
                    Quiz: {{$examination_details['examination']['name']}}
                    @if($examination_details['page_count']>0 && $examination_details['examination']['status']=='In Design')
                    <select name="examination_status" id="examination_status" class="form-control" style="float: inline-end; width:25%">
                        <option value=""></option>
                        @foreach($exam_status as $val)
                        @if($examination_details['examination']['status']==$val)
                        <option value="{{$val}}" selected>{{$val}}</option>
                        @else
                        <option value="{{$val}}">{{$val}}</option>
                        @endif
                        @endforeach
                    </select>
                    @else
                    <small style="float: inline-end">Status: {{$examination_details['examination']['status']}}
                        <a href="#" class="btn btn-sm btn-default" title=""><i class="fa fa-backward"></i> Back</a>
                    </small>

                    @endif
                    <input type="hidden" name="exam_id" id="exam_id"
                        value="{{\Helpers::encryptId($examination_details['examination']['examination_id'])}}">
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="col-md-12 ">
            @if($examination_details['page_count']>0)
            @for($page=1;$page<=$examination_details['page_count'];$page++) <div class="card">

                <div class="card-header" style="height:50px;background-color: #f2f2f2; text-align:left">
                    <span>Page {{$page}}</span>
                    @if($examination_details['examination']['status']=='In Design')
                    <span class="float-end">
                        <ul class="list-inline">

                            <li class="list-inline-item">

                                <a href="javascript:void(0);" class="deletequizpage"
                                    data-exam-id="{{\Helpers::encryptId($examination_details['examination']['examination_id'])}}"
                                    data-id="{{\Helpers::encryptId($page)}}">

                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"
                                        width="14" height="14" x="0" y="0" viewBox="0 0 64 64"
                                        style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                        <g>
                                            <path
                                                d="M4.59 59.41a2 2 0 0 0 2.83 0L32 34.83l24.59 24.58a2 2 0 0 0 2.83-2.83L34.83 32 59.41 7.41a2 2 0 0 0-2.83-2.83L32 29.17 7.41 4.59a2 2 0 0 0-2.82 2.82L29.17 32 4.59 56.59a2 2 0 0 0 0 2.82z"
                                                fill="#000000" data-original="#000000"></path>
                                        </g>
                                    </svg>

                                </a>

                            </li>

                        </ul>
                    </span>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($examination_details['examination']['examquestions']) &&
                    count($examination_details['examination']['examquestions'])>0)
                    @for($i=0;$i < count($examination_details['examination']['examquestions']);$i++)
                        @if($examination_details['examination']['examquestions'][$i]['page_id']==$page)
                            @php
                                $questionInfo=json_decode($examination_details['examination']['examquestions'][$i]['question_info']);
                            @endphp
                            <strong style="width:95%">{!!$questionInfo->question!!}</strong>
                            @if($examination_details['examination']['status']=='In Design')
                            <a class="float-end"
                                href="javascript:void(0);"
                                onclick="questionModal('{{route('tut_editquizquestion',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($examination_details['examination']['examination_id']),\Helpers::encryptId($page),\Helpers::encryptId($examination_details['examination']['examquestions'][$i]['examination_question_id'])])}}', 'Edit Question')"><i
                                    class="fa fa-edit"></i></a>
                            @endif
                            @if(strtolower(trim($questionInfo->question_type))=='radio')
                            <p>
                                @foreach($questionInfo->options as $option)
                                <input type="radio" class="form-control-radio"
                                    name="{{$examination_details['examination']['examquestions'][$i]['question_id']}}">
                                {{$option->option_value}}<br>
                                @endforeach
                            </p>
                            @endif
                            @if(strtolower(trim($questionInfo->question_type))=='checkbox')
                            <p>
                                @foreach($questionInfo->options as $option)
                                <input type="checkbox" class="form-control-checkbox"
                                    name="{{$examination_details['examination']['examquestions'][$i]['question_id']}}">
                                {{$option->option_value}}<br>
                                @endforeach
                            </p>
                            @endif
                            @if(strtolower(trim($questionInfo->question_type))=='select')
                            <p>
                                <select class="form-control" style="width:50%;">
                                    <option></option>
                                    @foreach($questionInfo->options as $option)
                                    <option>{{$option->option_value}}</option>
                                    @endforeach
                                </select>
                            </p>
                            @endif
                            @if(strtolower(trim($questionInfo->question_type))=='text')
                            <p>
                                <textarea class="form-control" cols="30" rows="10"></textarea>
                            </p>
                            @endif

                            @if($questionInfo->require_file_upload)
                            <p>
                                Attachment: <input type="file">
                            </p>
                            @endif
                        @endif
                    @endfor
                    @endif
                        @if($examination_details['examination']['status']=='In Design')
                        <div class="btn-group"><button type="button" class="btn btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">Add item</button><span
                                class="containerid" data-value="{{$page}}"></span>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item" type="button"
                                        onclick="questionModal('{{route('tut_addquizquestion',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($examination_details['examination']['examination_id']),\Helpers::encryptId($page)])}}', 'Add Question')">Question</button>
                                </li>
                                <li><button class="dropdown-item" type="button" onclick="questionModal('{{route('tut_importquizquestion',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($examination_details['examination']['examination_id']),\Helpers::encryptId($page)])}}', 'Import Question')">Import Question</button></li>
                            </ul>
                        </div>
                        @endif
                </div>

        </div>
        @endfor
        @endif
        <div class="col-md-12 addnewpage"></div>
        @if($examination_details['examination']['status']=='In Design')
        <div class="col-md-12 text-center">

            <div class="btn-group">

                <button type="button" class="btn btn-secondary dropdown-toggle btn_color_coppergreen"
                    data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 5px;padding:13px 18px">

                    Add Page

                </button>

                <ul class="dropdown-menu dropdown-menu-end">

                    <li><button class="dropdown-item newquizpage" type="button">Quiz Page</button>

                    </li>

                </ul>

            </div>

        </div>
        @endif
    </div>
</div>

@endsection
@section('pagescript')
<script>
var pagecount = "{{$examination_details['page_count']??0}}";
$('body').on('click', '.newquizpage', function() {

    pagecount++;
    var page_id = pagecount; //Math.floor((Math.random()*1000000000)+1);
    var exam_id = $('#exam_id').val();

    var token = "{{Session::get('usertoken')}}";

    $.ajax({

        url: "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/add-exam-page'; ?>/" +
            page_id + '/' + exam_id,
        type: "GET",
        headers: {
            Authorization: 'Bearer ' + token
        },

        success: function(response) {
            console.log(response);
            $('.addnewpage').append(response);
        }
    });

});

$('.deletequizpage').on('click', function() {
    if (confirm("Do you want to remove this page?")) {
        var page_id = $(this).data('id');
        var examination_id = $(this).data('exam-id');
        // alert($(this).data('id'));
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/remove-question-page'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            examination_id: examination_id,
            page_id: page_id
        });
        params['successCallbackFunction'] = function(response) {
            window.location.href = "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/edit-quiz'; ?>/"+examination_id;
        }
        params['errorCallBackFunction'] = function(httpObj) {
            console.log(httpObj);
        }
        params['completeCallbackFunction'] = function(response) {

        }


        doAjax(params);

    }
});

$('#examination_status').on('change', function(){
    if (confirm("Do you want to change the examination status?")) {
        var status = $(this).val();
        var examination_id = $('#exam_id').val();
        // alert($(this).data('id'));
        var token = "{{Session::get('usertoken')}}";
        // alert(examination_id);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/update-status'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            examination_id: examination_id,
            status: status
        });
        params['successCallbackFunction'] = function(response) {
            console.log(response);
            window.location.href = "<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/edit-quiz'; ?>/"+examination_id;
        }
        params['errorCallBackFunction'] = function(httpObj) {
            console.log(httpObj);
        }
        params['completeCallbackFunction'] = function(response) {

        }


        doAjax(params);

    }else{
        $('#examination_status').val("{{$examination_details['examination']['status']}}");
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
