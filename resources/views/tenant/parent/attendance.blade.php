@extends('layouts.default')
@section('title', 'Attendance')
@section('pagecss')
<style type="text/css">
.size-21px {
    font-size: 21px
}

.fa-green {
    color: green;
}

.fa-red {
    color: red;
}
</style>
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i></i> Attendance
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="float-end ">
                    Filter <a style="color:#6c757d" href="javascript:void(0);"
                        onclick="rightModal('{{route('p_attendancelist_filter',Session()->get('tenant_info')['subdomain'])}}?fp={{$form_params}}', 'Filter')"><i
                            class="fa fa-filter" aria-hidden="true"></i></a>
                </div>

            </div>
            <div class="card-body admin_content">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped nowrap" width="100%">
                        <thead>
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th width="10%">Student</th>
                                <th width="10%">Date</th>
                                <th width="10%">Subject</th>
                                <th width="10%">Lesson</th>
                                <th width="12%">Year Group</th>
                                <th width="12%" class="text-center">Attendance</th>
                                <th width="10%" class="text-center">Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($response['result']['listing'])>0)
                            @foreach($response['result']['listing'] as $record)
                            <tr>
                                <td>{{$record['user']['first_name']??''}}
                                    {{$record['user']['last_name']??''}}<br><small>({{$record['user']['code']??''}})</small>
                                </td>
                                <td>{{$record['attendance_date']??''}}</td>
                                <td>{{$record['lesson']['subject']['subject_name']??''}}</td>
                                <td>{{$record['lesson']['lesson_name']??''}}</td>
                                <td>{{$record['lesson']['subject']['yeargroup']['name']??''}}<br />
                                    <small>{{$record['lesson']['subject']['academicyear']['academic_year']}}</small>
                                </td>
                                <td class="text-center">
                                    @switch($record['is_present']??'')
                                    @case('1')
                                    <i class="fa fa-circle-check fa-2x fa-green"></i>
                                    @break

                                    @case('0')
                                    <i class="fa fa-times-circle fa-2x fa-red"></i>
                                    @break

                                    @default
                                    <i class="fa fa-times-circle fa-2x fa-grey"></i>
                                    @endswitch

                                </td>
                                <td class="text-center">{{$record['remarks']??''}}</td>

                            </tr>
                            @endforeach
                            @else
                            <tr>

                                <td colspan="6" class="text-center">No data found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('pagescript')


    @endsection