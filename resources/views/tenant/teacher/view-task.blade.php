@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<style type="text/css">
#dvSpecificUsers {
    display: none;
}
#dvSpecificExam {
    display: none;
}
</style>
@endsection
@section('content')
@php
$userInfo=Session::get('user');

@endphp
<div class="row">
    <div class="col-md-12">
        <label for="">Task</label>
        {{$task_details['task']}}
    </div>
    <div class="col-md-12">
        <label for="">Start Date</label>
        {{$task_details['start_date']}}
    </div>
    <div class="col-md-12">
        <label for="">End Date</label>
        {{$task_details['end_date']}}
    </div>
    @if(($task_details['task_type']=='Q' || $task_details['task_type']=='A' || $task_details['task_type']=='H') && count($task_details['exams'])>0)
    <div class="col-md-12">
        <label for="">Examinations</label>
        <ol>
        @foreach($task_details['exams'] as $exam)
        <li><a>{{$exam['details']['name']}}</a></li>
        @endforeach
        </ol>
    </div>
    @endif
</div>
<hr>
@if($userInfo['user_id']==$task_details['created_by'])
<button class="btn btn-sm btn-primary" data-bs-dismiss="modal" title="Edit Task" onclick="questionModal('{{route('tut_edittask',[Session()->get('tenant_info')['subdomain'],$task_details['task_id']])}}', 'Edit Allocations')">Edit Task</button>

<button class="btn btn-sm btn-danger delete_task" data-id="{{\Helpers::encryptId($task_details['task_id'])}}" data-text="{{$task_details['task']}}"  title="Delete Task" onclick="">Delete Task</button>
@endif
@endsection
@section('pagescript')
<script>

$('.delete_task').on('click', function() {
    var examName = $(this).data('text');

    if (confirm("Do you want to delete " + examName + "?")) {

        var task_id = $(this).data('id');
        // alert($(this).data('id'));
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/delete'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            task_id: task_id,
        });
        params['successCallbackFunction'] = function(response) {
           window.location.href = "{{route('tut_calendar',Session()->get('tenant_info')['subdomain'])}}";
        }
        params['errorCallBackFunction'] = function(httpObj) {
            console.log(httpObj);
        }
        params['completeCallbackFunction'] = function(response) {

        }


        doAjax(params);

    }
});

</script>
@endsection
