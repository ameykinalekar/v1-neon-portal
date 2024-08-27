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
                    <h4><i class="mdi mdi-account-circle title_icon"></i> Student Targets</h4>
                    <a type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    href="{{route('tut_starget_add',Session()->get('tenant_info')['subdomain'])}}">
                    <i class="mdi mdi-plus"></i> Add Target</a>
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
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th>Image</th>
                                <th>Name</th>
                                <th>Year Group</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($response['result']['listing']['data'])>0)
                        @foreach($response['result']['listing']['data'] as $record)
                        <tr class="text-center">
                            <td>
                                @if($record['student']['user_logo']!='')
                                <span>
                                <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"  href="{{config('app.api_asset_url') . $record['student']['user_logo']}}"><img src="{{config('app.api_asset_url') . $record['student']['user_logo']}}" height="auto" width="35px" /></a>
                                </span>
                            @else
                                <span>
                                <img src="{{config('app.api_asset_url') . $no_image}}" height="auto" width="35px" />
                                </span>
                            @endif
                        </td>
                            <td>{{$record['student']['first_name'].' '.$record['student']['last_name']}}<br/>
                            <small>{{$record['student']['code']}}</small></td>
                            <td>{{$record['yeargroup']['name']}}</td>
                            <td> {{$record['set_date']}}</td>
                            <td>
                                <div class="status status-{{strtolower($record['status'])}}">
                                    {{$record['status']}}
                                </div>
                            </td>
                            <td>
                            <a href="{{route('tut_starget_edit',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['target_id'])])}}" title="Edit Target"
                                    ><i
                                        class="fa fa-pencil"></i></a>
                            </td>
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
                @if(isset($current_page))
                <div class="row">
                    <div class="col-md-6">Page : {{$current_page}} of {{$numOfpages}}</div>
                    <div class="col-md-6">
                        <nav class="float-end">
                            <ul class="pagination">
                                @if(($has_next_page == true) && ($has_previous_page == false))
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('tut_starget',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tut_starget',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tut_starget',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('tut_starget',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
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
<script>




</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
