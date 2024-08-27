@extends('layouts.default')
@section('title', 'Lessons')
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
                    <h4><i ></i> Lessons</h4>
                    <span id="btnContainer">
                        <a href="{{route('tut_mycourses',Session()->get('tenant_info')['subdomain'])}}" class="btn btn-sm btn-default" title="My Courses"><i class="fa fa-backward"></i> Back</a>

                    </span>
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
                            <th >Lesson Name</th>
                            <th >Lesson Number</th>
                            <th >Subject</th>
                            <th >Year Group</th>
                            <th >Academic Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($response['result']['details'])>0)
                        @foreach($response['result']['details'] as $record)
                        <tr>
                            <td>{{$record['lesson_name']}}
                                <p><small>
                                <strong>Quiz: {{$record['quizcnt']}}</strong><br>
                                <strong>Assesments: {{$record['assesmentcnt']}}</strong>
                                </small>
                                </p>
                            </td>
                            <td>{{$record['lesson_number']}}</td>
                            <td>{{$record['subject_name']}} ({{$boards[$record['board_id']]}})</td>
                            <td>{{$record['yeargroup']}}</td>
                            <td>{{$record['academic_year']}}</td>
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

        initDataTable('basic-datatable-teacher-lesson');


</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
