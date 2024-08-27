@extends('layouts.default')
@section('title', 'All Attendances')
@section('pagecss')
<style type="text/css">
.size-21px{
    font-size:21px
}
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
                    <i ></i> All Attendances
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
                    <a style="color:#6c757d"
                    href="javascript:void(0);" onclick="rightModal('{{route('tus_attendances_filter',Session()->get('tenant_info')['subdomain'])}}?fp={{$form_params}}', 'Filter')">Filter <i class="fa fa-filter" aria-hidden="true"></i></a>
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
                                <th >Attendance</th>
                                <th >Comments</th>
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
                                <td >
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
                                <td >{{$record['remarks']??''}}</td>

                            </tr>
                            @endforeach
                            @else
                            <tr>

                                <td colspan="6" >No data found.</td>
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
                                        href="{{route('tus_attendances',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tus_attendances',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tus_attendances',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('tus_attendances',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
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

@endsection
