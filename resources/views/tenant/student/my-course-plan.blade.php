@extends('layouts.default')
@section('title', 'My Courses - Course Plan')
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
                <div class="page-title">
                    <h4><i ></i> Course Plan</h4>
                    <span id="btnContainer">
                        <a href="{{route('tus_mycourses',Session()->get('tenant_info')['subdomain'])}}" class="btn btn-sm btn-default" title="My Courses"><i class="fa fa-backward"></i> Back</a>

                    </span>
                </div>
                <div class="row py-2">

                    <div class="col-md-6"><label for="">Subject</label> :
                        {{$subject_info['subject_name']}} ({{$subject_info['yeargroup']['name']}} - {{$subject_info['academicyear']['academic_year']}})</div>

                    <div class="col-md-6"><label for="">Board</label> :
                        {{$boards[$subject_info['board_id']]}}</div>

                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-12">
    <div class="table-responsive">
                <table id="datatable" class="table table-striped  nowrap table" width="100%">
                    <thead>
                        <tr style="background-color: rgba(90, 194, 185, 1); color: #ffffff;">
                            <th width="5%">Lesson Number</th>
                            <th>Lesson</th>
                            <th width="18%" class="text-center">Lesson Completed</th>
                            <th width="18%" class="text-center">Grade</th>
                            <th width="18%" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listing as $record)

                        <tr>

                            <td>{{$record['lesson_number']}}</td>
                            <td>{{$record['lesson_name']}}</td>
                            <td class="text-center">{{$record['percentage']}}%</td>
                            <td class="text-center">{{$record['grade']}}</td>
                            <td class="text-center">

                                @switch($record['percentage'])
                                    @case($record['percentage']>80)
                                    <i class="fa fa-award" style="font-size:25px;color:#5bc2b9"></i>
                                        @break

                                    @case($record['percentage']>50 && $record['percentage']< 80)
                                    <i class="fa fa-award" style="font-size:25px;color:#fcc34a"></i>
                                        @break
                                    @case($record['percentage']>0 && $record['percentage']< 50)
                                    <i class="fa fa-award" style="font-size:25px;color:#e87e69"></i>
                                        @break

                                    @default
                                    <i class="fa fa-award" style="font-size:25px;"></i>
                                @endswitch
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
