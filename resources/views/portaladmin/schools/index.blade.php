@extends('layouts.default')
@section('title', 'All Schools')
@section('pagecss')

@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Schools
                </h4>
                <a  class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    href="{{route('pa_addschool')}}"> <i class="mdi mdi-plus"></i> Add
                    School</a>
                <!-- <button type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    onclick="rightModal('{{route('pa_addschool')}}', 'Add School')"> <i class="mdi mdi-plus"></i> Add
                    School</button> -->
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
                        <select name="search_country_id" id="search_country_id" class="form-control pull-right" style="width: fit-content;"><option value="">All Countries</option></select>
                            <input type="text" name="search_text" value="" class="form-control pull-right"
                                placeholder="Search text..." style="width: fit-content;">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    Search
                                    <!-- <i class="fa fa-search"></i> -->
                                </button>
                                <a class="btn btn-light" href="{{route('pa_schoollist')}}">Reset</a>
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
                            <th >Name</th>
                            <th >Email</th>
                            <th >Country</th>
                            <th>Status</th>
                            <th>Subscription</th>
                            <th >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($response['result']['schools']['data'])>0)
                        @foreach($response['result']['schools']['data'] as $record)
                        <tr>
                            <td>{{$record['first_name']}}</td>
                            <td>{{$record['email']}}</td>
                            <td>{{$record['country_name']??''}}</td>
                            <td>
                                <div class="status status-{{strtolower($record['status'])}}">
                                    {{$record['status']}}
                                </div>
                            </td>
                            <td><a title="School Subscriptions" href="{{route('pa_schoolsubscriptions',\Helpers::encryptId($record['user_id']))}}">View Subscription</a></td>
                            <td><a title="Edit School" href="{{route('pa_editschool',\Helpers::encryptId($record['user_id']))}}"><i class="fa fa-pencil"></i></a></td>
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
                @php
                    $initialQS='?search_text='.$search_text.'&search_country_id='.$search_country_id;
                @endphp
                <div class="row">
                    <div class="col-md-6">Page : {{$current_page}} of {{$numOfpages}}</div>
                    <div class="col-md-6">
                        <nav class="float-end">
                            <ul class="pagination">
                                @if(($has_next_page == true) && ($has_previous_page == false))
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('pa_schoollist').'/'.$initialQS.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('pa_schoollist').'/'.$initialQS.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('pa_schoollist').'/'.$initialQS.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('pa_schoollist').'/'.$initialQS.'&page='.$next_page}}"><span
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
    $(document).ready(function() {
    initailizeSelect2();
    onPageLoad();

});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2();
}
    function onPageLoad() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/dropdown/countries'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Countries</option>';
        response.result.listing.forEach(function(item) {
            //if(item.status=='Active'){
                    option=option+'<option value="' + item.country_id + '">' +
                        item.name + '</option>';
               // }
        });
        $('#search_country_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#search_country_id').html('<option value="">All Countries</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        @if($search_country_id!=null)
        $('#search_country_id').val("{{$search_country_id}}").trigger('change');
        @endif
    }
    doAjax(params);

}
</script>
@endsection
