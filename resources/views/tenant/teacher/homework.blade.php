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
                            <th >Assigned To</th>
                            <th >Examinations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($listing)>0)
                        @foreach($listing as $record)
                        <tr>
                            <td>{{$record['task']}}
                            </td>
                            <td>{{$record['start_date']}}</td>
                            <td>{{$record['end_date']}}</td>
                            <td><a href="javascript:void(0);" class="btn btn-sm btn-default" title="Add Task" onclick="questionModal('{{route('tut_viewtaskstudent',[Session()->get('tenant_info')['subdomain'],$record['task_id']])}}', 'View Allocations')">{{$record['created_for']}}</a></td>
                            <td>
                                @if(count($record['exams'])>0)
                                <small>
                                <ol>
                                @foreach($record['exams'] as $exam)
                                @php
                                    $etype='';
                                    if($exam['details']['examination_type']=='Q'){
                                        $etype='Quiz-';
                                    }
                                    if($exam['details']['examination_type']=='A'){
                                        $etype='Assessment-';
                                    }
                                @endphp
                                <li><a>{{$etype.$exam['details']['name']}}</a></li>
                                @endforeach
                                </ol>
                                </small>
                                @endif
                            </td>
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
