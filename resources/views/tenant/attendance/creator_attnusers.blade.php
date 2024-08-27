@extends('layouts.default')
@section('title', 'Attendance Recorded')
@section('pagecss')
<style type="text/css">
    .fa-green{
        color:green;
    }
    .fa-red{
        color:red;
    }
</style>
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> Attendance Recorded
                </h4>
                <a type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end" href="{{route('tut_attendance_add', Session()->get('tenant_info')['subdomain'])}}"> <i class="mdi mdi-plus"></i> Record attendance</a>
                <a href="{{route('tut_attendances',Session()->get('tenant_info')['subdomain'])}}"
                            class="btn btn-outline-primary btn-rounded align-middle mt-1 mx-2 float-end" title="Back"><i class="fa fa-backward"></i> Go Back</a>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body admin_content">
                <div class="table-responsive">
                <table id="datatable" class="table table-striped nowrap" width="100%">
                    <thead>
                        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                            <th width="8%">Image</th>
                            <th width="10%">Name</th>
                            <th width="10%">Date</th>
                            <th width="10%">Subject</th>
                            <th width="10%">Lesson</th>
                            <th width="10%">Year Group</th>
                            <th width="13%" class="text-center">Attendance</th>
                            <th width="12%">Comment</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if(count($listing)>0)
                        @foreach($listing as $record)
                            <td>
                            @if($record['user_logo']!='')
                                <span>
                                <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"  href="{{config('app.api_asset_url') . $record['user_logo']}}"><img src="{{config('app.api_asset_url') . $record['user_logo']}}" height="auto" width="35px" /></a>
                                </span>
                            @else
                                <span>
                                <img src="{{config('app.api_asset_url') . $no_image}}" height="auto" width="35px" />
                                </span>
                            @endif
                            </td>
                            <td>{{$record['first_name'].' '.$record['last_name']}}<br/>
                                <small>{{$record['code']}}</small>
                            </td>
                            <td>{{$record['attendance_date']}}</td>
                            <td>{{$record['subject_name']}}</td>
                            <td>{{$record['lesson_name']}}</td>
                            <td>{{$record['yeargroup_name']}}<br/>
                            <small>{{$record['academic_year']}}</small></td>
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
                            <td>{{$record['remarks']}}</td>
                        </tr>
                        @endforeach

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

<script type="text/javascript">
    initDataTable('basic-datatable');
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
