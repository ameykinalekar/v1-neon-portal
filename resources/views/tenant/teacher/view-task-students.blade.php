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

<div class="row">
    <div class="col-md-12">{{$task_details['task']??''}}<br><small>{{$task_details['description']??''}}</small></div>
    <div class="col-md-6"><small>Start Date: {{$task_details['start_date']??''}}</small></div>
    <div class="col-md-6"><small>End Date: {{$task_details['end_date']??''}}</small></div>
</div>
<div class="table-responsive">
    @if(count($task_details)>0)
    <table id="datatable" class="table table-striped  nowrap" width="100%">
        <thead>
            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                <th>Student</th>
                <th>Examination</th>
                <th>Attempt Date</th>
                <th>Status</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            
            @foreach($task_details['allocations'] as $record)
            <tr>
                <td rowspan="{{count($task_details['exams'])}}">{{$record['students']['first_name']??''}} {{$record['students']['last_name']??''}}
                </td>
            
                @foreach($task_details['exams'] as $k=>$recexam)
                    @php
                        $userResult=\Helpers::getUserResult($record['user_id'],$recexam['examination_id']);
                       $status='Not Attempted';
                       $is_reviewed='';
                       $attemptedOn='';
                       $grade='';
                       if(!empty($userResult)){
                            $status='Attempted';
                            $is_reviewed=$userResult['user_result']['is_reviewed'];
                            $attemptedOn=date('d-m-Y',strtotime($userResult['user_result']['created_at']));
                            $grade=$userResult['user_result']['grade']??'';
                            if($is_reviewed=='N'){
                                $status='In Review';
                            }
                       }
                    @endphp
                    @if($k>0)
                    <tr>
                    @endif
                        <td>{{$recexam['details']['name']??''}}</td>
                        <td>{{$attemptedOn??''}}</td>
                        <td>{{$status??''}}</td>
                        <td>{{$grade??''}}</td>
                    </tr>
                @endforeach
            
            @endforeach
            
            
        </tbody>
    </table>
@endif
</div>
@endsection
@section('pagescript')

@endsection