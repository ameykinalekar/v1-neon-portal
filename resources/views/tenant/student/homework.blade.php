@extends('layouts.default')
@section('title', 'Homework')
@section('pagecss')
<style>
    #btnContainer {
    float: inline-end;
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
@php
$userInfo=Session::get('user');

@endphp
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <div class="page-title">
                    <h4><i ></i> Homework</h4>
                    
                </div>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body admin_content">
            <div class="table-responsive">
                <table id="datatable" class="table table-striped  nowrap" width="100%">
                    <thead>
                        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;" >
                            <th >Task</th>
                            <th >Start Date</th>
                            <th >End Date</th>
                            <th>Examination</th>
                            <th>Attempt Date</th>
                            <th>Status</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($listing)>0)
                        @foreach($listing as $record)
                        <tr>
                            <td rowspan="{{count($record['exams'])}}">{{$record['task']}}
                            </td>
                            <td rowspan="{{count($record['exams'])}}">{{$record['start_date']}}</td>
                            <td rowspan="{{count($record['exams'])}}">{{$record['end_date']}}</td>
                            
                            @foreach($record['exams'] as $k=>$recexam)
                    @php
                        $userResult=\Helpers::getUserResult($userInfo['user_id'],$recexam['examination_id']);
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
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="9" class="text-center">No data found.</td>
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

        initDataTable('datatable');


</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
