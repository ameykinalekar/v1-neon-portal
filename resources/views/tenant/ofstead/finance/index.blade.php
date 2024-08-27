@extends('layouts.default')
@section('title', 'All Finance Ofsteads')
@section('pagecss')

@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Finance Ofsteds
                </h4>
                <button type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    onclick="rightModal('{{route('ta_importofinance',Session()->get('tenant_info')['subdomain'])}}', 'Import Finance Ofstead')">
                    <i class="mdi mdi-upload"></i> Import Finance Ofsted</button>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <!--<div class="card-header">
                <div class="float-end">
                   <form>
                  @csrf
                    <div class="input-group input-group-sm">
                        <input type="text" name="search_text" value="{{$search_text}}" class="form-control pull-right"
                            placeholder="Search text..." style="width: fit-content;">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-primary">
                                Search

                            </button>
                            <a class="btn btn-light" href="{{route('ta_academicyearlist',Session()->get('tenant_info')['subdomain'])}}">Reset</a>
                        </div>
                    </div>
                  </form>
                </div>
            </div>-->
            <div class="card-body admin_content">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped  nowrap" width="100%">
                        <thead>
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th>Year</th>
                                <th>Sub Indicator Count</th>
                                <th>Created On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($response['result']['listing']['data'])>0)
                            @foreach($response['result']['listing']['data'] as $record)
                            @php
                            $modalTitle="View Ofsted Finance Year ".$record['year'];
                            @endphp
                            <tr>
                                <td><a href="javascript:void(0);"
                                        onclick="rightModal('{{route('ta_viewofinanceyear',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['year'])])}}', '{{$modalTitle}}')">{{$record['year']}}</a>
                                </td>
                                <td>{{$record['count_subindicator']}}</td>
                                <td>{{date('d-m-Y',strtotime($record['created_at']))}}</td>

                                <td>
                                    <a href="javascript:void(0);" data-year="{{$record['year']}}"
                                        data-id="{{\Helpers::encryptId($record['year'])}}" class="delete-finance"><i
                                            class="mdi mdi-delete"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>

                                <td colspan="4" class="text-center">No data found.</td>
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
                                        href="{{route('ta_ofinancelist',Session()->get('tenant_info')['subdomain']).'/?page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('ta_ofinancelist',Session()->get('tenant_info')['subdomain']).'/?page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('ta_ofinancelist',Session()->get('tenant_info')['subdomain']).'/?page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('ta_ofinancelist',Session()->get('tenant_info')['subdomain']).'/?page='.$next_page}}"><span
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
$('.delete-finance').on('click', function() {
    var year = $(this).data('year');
    var yearid = $(this).data('id');
    // alert(year);
    // alert(yearid);
    if (confirm("Do you want to delete this " + year + " record?") == true) {
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/ofstead/delete-finance'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            year: yearid
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
