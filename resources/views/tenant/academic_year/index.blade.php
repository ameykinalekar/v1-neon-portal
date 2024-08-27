@extends('layouts.default')
@section('title', 'All Academic Years')
@section('pagecss')

@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Academic Years
                </h4>
                <button type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    onclick="rightModal('{{route('ta_addacademicyear',Session()->get('tenant_info')['subdomain'])}}', 'Add Academic Year')"> <i class="mdi mdi-plus"></i> Add
                    Academic Year</button>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="float-end">
                  <form>
                  @csrf
                    <div class="input-group input-group-sm">
                        <input type="text" name="search_text" value="{{$search_text}}" class="form-control pull-right"
                            placeholder="Search text..." style="width: fit-content;">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-primary">
                                Search
                                <!-- <i class="fa fa-search"></i> -->
                            </button>
                            <a class="btn btn-light" href="{{route('ta_academicyearlist',Session()->get('tenant_info')['subdomain'])}}">Reset</a>
                        </div>
                    </div>
                  </form>
                </div>
            </div>
            <div class="card-body admin_content">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped  nowrap" width="100%">
                        <thead>
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th>Academic year</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($response['result']['academic_years']['data'])>0)
                            @foreach($response['result']['academic_years']['data'] as $record)
                            <tr>
                                <td>{{$record['academic_year']}}</td>
                                <td><div class="status status-{{strtolower($record['status'])}}">
                                        {{$record['status']}}
                                    </div></td>
                                <td><a href="javascript:void(0);" title="Academic Year" onclick="rightModal('{{route('ta_editacademicyear',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['academic_year_id'])])}}', 'Edit Academic Year')"><i class="fa fa-pencil"></i></a></td>
                            </tr>
                            @endforeach
                            @else
                            <tr>

                                <td colspan="3" class="text-center">No data found.</td>
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
                                        href="{{route('ta_academicyearlist',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('ta_academicyearlist',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('ta_academicyearlist',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('ta_academicyearlist',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
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
