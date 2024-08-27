@extends('layouts.default')
@section('title', 'All Lessons')
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
                    <h4><i ></i> All Lessons</h4>
                    <span id="btnContainer"><button type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    onclick="rightModal('{{route('tut_addlesson',Session()->get('tenant_info')['subdomain'])}}', 'Add Lesson')">
                    <i class="mdi mdi-plus"></i> Add Lesson</button> <a class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end mx-2"
                                    href="javascript:void(0);" onclick="rightModal('{{route('tut_importlesson',Session()->get('tenant_info')['subdomain'])}}', 'Import Lesson')"><i class="mdi mdi-upload"></i> Import</a>
                    </span> </div>

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
                        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                            <th >Lesson Name</th>
                            <th >Lesson Number</th>
                            <th >Subject</th>
                            <th >Year Group</th>
                            <th >Academic Year</th>
                            <th >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $userInfo=Session::get('user');
                        @endphp
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
                            <td>
                                @if($record['created_by']==$userInfo['user_id'] && $record['creator_type']==$userInfo['user_type'])
                                <a href="javascript:void(0);" title="Edit Lesson"
                                    onclick="rightModal('{{route('tut_editlesson',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id'])])}}', 'Edit Lesson')"><i
                                        class="fa fa-pencil"></i></a>
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

        initDataTable('basic-datatable');


</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
