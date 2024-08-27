@extends('layouts.default')
@section('title', 'All Topics')
@section('pagecss')
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Topics
                </h4>
                <button type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    onclick="rightModal('{{route('ta_addtopic',Session()->get('tenant_info')['subdomain'])}}', 'Add Topic')">
                    <i class="mdi mdi-plus"></i> Add Topic</button>
                    <a class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end mx-2"
                                    href="javascript:void(0);" onclick="rightModal('{{route('ta_importtopic',Session()->get('tenant_info')['subdomain'])}}', 'Import Topic')"><i class="mdi mdi-upload"></i> Import</a>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="float-end">
                    <form>
                        @csrf
                        <div class="input-group input-group-sm">
                            <select name="sayid" id="sayid" class="form-control pull-right" style="width: fit-content;"><option value="">All Academic Year</option></select>
                            <select name="sygid" id="sygid" class="form-control pull-right" style="width: fit-content;"><option value="">All Year Group</option></select>
                            <select name="ssid" id="ssid" class="form-control pull-right" style="width: fit-content;"><option value="">All Subject</option></select>
                            <select name="slid" id="slid" class="form-control pull-right" style="width: fit-content;"><option value="">All Lessons</option></select>
                            <input type="text" name="search_text" value="{{$search_text}}"
                                class="form-control pull-right" style="width: fit-content;" placeholder="Search text...">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    Search
                                    <!-- <i class="fa fa-search"></i> -->
                                </button>
                                <a class="btn btn-light"
                                    href="{{route('ta_topiclist',Session()->get('tenant_info')['subdomain'])}}">Reset</a>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body admin_content">
                <div class="table-responsive">
                    <table id="basic-datatable1" class="table table-striped p10 nowrap" width="100%">
                        <thead>
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th width="15%">Topic</th>
                                <th width="20%">Lesson Name</th>
                                <th width="15%">Subject</th>
                                <th width="10%">Board</th>
                                <th width="15%">Yr Group</th>
                                <th width="15%">Academic Yr</th>

                                <th width="8%">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($response['result']['listing']['data'])>0)
                            @foreach($response['result']['listing']['data'] as $record)
                            @php $board_id=$record['subject']['board_id']??'0' @endphp
                            <tr>
                                <td>{{$record['topic']}}</td>
                                <td>{{$record['lesson']['lesson_name']}}</td>
                                <td>{{$record['subject']['subject_name']??''}}</td>
                                <td> @if($board_id>0) {{ $boards[$board_id]}} @endif</td>
                                <td>{{$record['subject']['yeargroup']['name']??''}}</td>
                                <td>{{$record['subject']['academicyear']['academic_year']??''}}</td>

                                <td><div class="status status-{{strtolower($record['status'])}}">
                                        {{$record['status']}}
                                    </div></td>
                                <td><a href="javascript:void(0);" title="Lesson"
                                        onclick="rightModal('{{route('ta_edittopic',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['topic_id'])])}}', 'Edit Topic')"><i
                                            class="fa fa-pencil"></i></a></td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="8" class="text-center">No data found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if(isset($current_page))
                @php
                    $initialQS='?search_text='.$search_text.'&sayid='.$sayid.'&sygid='.$sygid.'&ssid='.$ssid;
                @endphp
                <div class="row">
                    <div class="col-md-6">Page : {{$current_page}} of {{$numOfpages}}</div>
                    <div class="col-md-6">
                        <nav class="float-end">
                            <ul class="pagination">
                                @if(($has_next_page == true) && ($has_previous_page == false))
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('ta_topiclist',Session()->get('tenant_info')['subdomain']).'/'.$initialQS.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('ta_topiclist',Session()->get('tenant_info')['subdomain']).'/'.$initialQS.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('ta_topiclist',Session()->get('tenant_info')['subdomain']).'/'.$initialQS.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('ta_topiclist',Session()->get('tenant_info')['subdomain']).'/'.$initialQS.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>

                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();
    onPageLoad();
});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2();
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
        var option = '<option value="">All Academic Year</option>';
        response.result.academic_year_list.forEach(function(item) {
            //if(item.status=='Active'){
                    option=option+'<option value="' + item.academic_year_id + '">' +
                        item.academic_year + '</option>';
               // }
        });
        $('#sayid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#sayid').html('<option value="">All Academic Year</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        @if($sayid!=null)
        $('#sayid').val("{{$sayid}}").trigger('change');
        @endif
    }
    doAjax(params);

}

$("#sayid").on('change',function(){
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
        $('#sygid').html(option);
        $('#sygid').attr("disabled","disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Year Group</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + '</option>';
        });
        $('#sygid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#sygid').html('<option value="">All Year Group</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#sygid').attr("disabled",false);
        @if($sygid!=null)
        $('#sygid').val("{{$sygid}}").trigger('change');
        @endif
    }
    doAjax(params);
});

$("#sygid").on('change', function() {
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
        $('#ssid').html(option);
        $('#ssid').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Subject</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name + '</option>';
        });
        $('#ssid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#ssid').html('<option value="">All Subject</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#ssid').attr("disabled", false);
        @if($ssid!=null)
        $('#ssid').val("{{$ssid}}").trigger('change');
        @endif
    }
    doAjax(params);
});

$("#ssid").on('change', function() {
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
        $('#slid').html(option);
        $('#slid').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Lessons</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.lesson_id + '">' +
                item.lesson_name + '</option>';
        });
        $('#slid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#slid').html('<option value="">All Lessons</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#slid').attr("disabled", false);
        @if($slid!=null)
        $('#slid').val("{{$slid}}").trigger('change');
        @endif
    }
    doAjax(params);
});
</script>
@endsection
