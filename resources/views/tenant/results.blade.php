@extends('layouts.default')
@section('title', 'All Marks')
@section('pagecss')

@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Marks
                </h4>

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
                        <select name="sayid" id="sayid" class="form-control pull-right" style="width: fit-content;"><option value="">Select Academic Year</option></select>
                        <select name="sygid" id="sygid" class="form-control pull-right" style="width: fit-content;"><option value="">Select Year Group</option></select>
                        <select name="ssid" id="ssid" class="form-control pull-right" style="width: fit-content;"><option value="">Select Subject</option></select>
                            <input type="text" name="search_text" value="{{$search_text}}"
                                class="form-control pull-right" style="width: fit-content;" placeholder="Search text...">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    <!-- <i class="fa fa-search"></i> -->
                                    Search
                                </button>

                                <a class="btn btn-light"
                                    href="{{route('ta_resultlist',Session()->get('tenant_info')['subdomain'])}}">Reset</a>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body admin_content">
            <div class="table-responsive">
                <table id="datatable" class="table table-striped  nowrap" width="100%">
                    <thead>
                        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                            <th>Student Name</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Marks Obtained</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                            <th>Exam Date</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $showData=0;  @endphp
                        @if(count($response['result']['listing'])>0)
                        @foreach($response['result']['listing'] as $record)
                        @if($record['examination'] !=null) @php $showData=1;  @endphp
                        <tr>
                            <td>{{$record['consumer']['first_name']??''}} {{$record['consumer']['last_name']??''}}</td>
                            <td>{{$record['examination']['subject']['subject_name']??''}}<br><small>({{$record['examination']['subject']['yeargroup']['name']??''}} : {{$record['examination']['subject']['academicyear']['academic_year']??''}})</small></td>
                            <td>
                                @if($record['examination']['examination_type']=='Q')
                                    Quiz
                                @endif
                                @if($record['examination']['examination_type']=='A')
                                    Assessment
                                @endif
                            </td>
                            <td>{{$record['marks_obtained']}}</td>
                            <td>{{$record['total_marks']}}</td>
                            <td>{{round(($record['marks_obtained']/$record['total_marks'])*100,2)}}</td>
                            <td>
                                {{date('d-m-Y',strtotime($record['created_at']))}}
                            </td>
                            <td>
                            {{$record['grade']}}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="text-center">No data found.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>

            </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')

<script>

        //initDataTable('basic-datatable');


</script>
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
        var option = '<option value="">Select Academic Year</option>';
        response.result.academic_year_list.forEach(function(item) {
            if(item.status=='Active'){
                    option=option+'<option value="' + item.academic_year_id + '">' +
                        item.academic_year + '</option>';
                }
        });
        $('#sayid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#sayid').html('<option value="">Select Academic Year</option>');
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
        var option = '<option value="">Select Year Group</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + '</option>';
        });
        $('#sygid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#sygid').html('<option value="">Select Year Group</option>');
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
        var option = '<option value="">Select A Subject</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name + '</option>';
        });
        $('#ssid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#ssid').html('<option value="">Select A Subject</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#ssid').attr("disabled", false);
        @if($ssid!=null)
        $('#ssid').val("{{$ssid}}").trigger('change');
        @endif
    }
    doAjax(params);
});
</script>
@endsection
