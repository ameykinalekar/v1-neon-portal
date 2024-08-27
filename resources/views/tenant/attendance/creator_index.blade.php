@extends('layouts.default')
@section('title', 'All Attendances')
@section('pagecss')
<style type="text/css">
.size-21px{
    font-size:21px
}
</style>
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Attendances
                </h4>
                <a type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    href="{{route('tut_attendance_add', Session()->get('tenant_info')['subdomain'])}}"> <i
                        class="mdi mdi-plus"></i> Record attendance</a> <a class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end mx-2"
                                    href="javascript:void(0);" onclick="rightModal('{{route('tut_attendances_import',Session()->get('tenant_info')['subdomain'])}}', 'Import Attendance')"><i class="mdi mdi-upload"></i> Import</a>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="float-end ">
                    <a style="color:#6c757d"
                    href="javascript:void(0);" onclick="rightModal('{{route('tut_attendances_filter',Session()->get('tenant_info')['subdomain'])}}?fp={{$form_params}}', 'Filter')">Filter <i class="fa fa-filter" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="card-body admin_content">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped  nowrap" width="100%">
                        <thead>
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th >Attendance Date</th>
                                <th >Subject</th>
                                <th >Lesson</th>
                                <th >Year Group</th>
                                <th >Total Present</th>
                                <th >Total Absent</th>
                                <th >Total Enrolled</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($response['result']['listing']['data'])>0)
                            @foreach($response['result']['listing']['data'] as $record)
                            <tr>
                                <td>{{$record['attendance_date']??''}}</td>
                                <td>{{$record['lesson']['subject']['subject_name']??''}}</td>
                                <td>{{$record['lesson']['lesson_name']??''}}</td>
                                <td>{{$record['lesson']['subject']['yeargroup']['name']??''}}<br />
                                    <small>{{$record['lesson']['subject']['academicyear']['academic_year']}}</small>
                                </td>
                                <td >{{$record['total_present']??''}}</td>
                                <td >{{$record['total_absent']??''}}</td>
                                <td >{{$record['total_enrolled']??''}}</td>
                                <td >
                                    <a
                                        href="{{route('tut_attendance_users', [Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['attendance_date']),\Helpers::encryptId($record['lesson_id'])])}}"><i
                                            class="mdi mdi-eye"></i></a> |
                                    <a href="javascript:void(0);"
                                        data-lesson="{{\Helpers::encryptId($record['lesson_id'])}}"
                                        data-date="{{\Helpers::encryptId($record['attendance_date'])}}"
                                        data-text="{{ $record['attendance_date'] }}" class="delete-attendance"><i
                                            class="mdi mdi-delete "></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>

                                <td colspan="8" >No data found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if(isset($current_page))
                <div class="row">
                    <div class="col-md-6">Page : {{$current_page}} of {{$numOfpages}}</div>
                    <div class="col-md-6">
                        <nav class="float-end">
                            <ul class="pagination">
                                @if(($has_next_page == true) && ($has_previous_page == false))
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('tut_attendances',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tut_attendances',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tut_attendances',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('tut_attendances',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
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
$('.delete-attendance').on('click', function() {
    var attendance_date = $(this).data('date');
    var alert_text = $(this).data('text');
    var lesson_id = $(this).data('lesson');
    // alert(year);
    // alert(yearid);
    if (confirm("Do you want to delete this " + alert_text + " record?") == true) {
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/attendance/delete'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            attendance_date: attendance_date,
            lesson_id: lesson_id
        });

        params['beforeSendCallbackFunction'] = function(response) {

        }
        params['successCallbackFunction'] = function(response) {
            alert(response.result.message);
            window.location.reload();
        }
        params['errorCallBackFunction'] = function(httpObj) {
            console.log(httpObj)
        }
        params['completeCallbackFunction'] = function(response) {


        }
        doAjax(params);
    }
});
</script>
@endsection
