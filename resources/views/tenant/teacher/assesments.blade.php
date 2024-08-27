@extends('layouts.default')
@section('title', 'Assesments')
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
                    <h4><i ></i> Assesments</h4>
                    <span id="id="btnContainer"><a type="button" class="btn btn-sm btn_color_coppergreen"
                        style="font-weight:600; border-radius:5px;float: inline-end"
                        href="{{route('tut_addassesment',Session()->get('tenant_info')['subdomain'])}}">New Assesment</a>
                </div></span>
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
                        <div class="input-group input-group-sm" style="width: 330px;">
                            <input type="text" name="search_text" value="{{$search_text}}"
                                class="form-control pull-right" placeholder="Search text...">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    Search
                                    <!-- <i class="fa fa-search"></i> -->
                                </button>
                                <a class="btn btn-light"
                                    href="{{route('tut_assesments',Session()->get('tenant_info')['subdomain'])}}">Reset</a>
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
                                <th width="30%">Assesment Name</th>
                                <th width="25%">Year Group</th>
                                <th width="25%">Subject</th>
                                <th width="10%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if(count($response['result']['listing']['data'])>0)
                            @foreach($response['result']['listing']['data'] as $record)

                            <tr>
                                <td>{{$record['name']??''}}</td>
                                <td>{{$record['subject']['yeargroup']['name']??''}} : {{$record['subject']['academicyear']['academic_year']??''}}</td>
                                <td>{{$record['subject']['subject_name']??''}}</td>
                                <td>{{$record['status']}}</td>
                                <td>
                                @if($record['status']!='Active')
                                <a href="{{route('tut_editassesment',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['examination_id'])])}}"><i class="fa fa-edit"></i></a>
                                @endif
                                </td>
                            </tr>

                            @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="text-center">No data found.</td>
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
                                        href="{{route('tut_assesments',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tut_assesments',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tut_assesments',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('tut_assesments',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
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
$('.delete_quiz').on('click', function() {
    var examName = $(this).data('text');

    if (confirm("Do you want to deactivate " + examName + "?")) {

        var examination_id = $(this).data('id');
        // alert($(this).data('id'));
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/update-status'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            examination_id: examination_id,
            status: 'Inactive'
        });
        params['successCallbackFunction'] = function(response) {
           window.location.href = "{{route('tut_quizes',Session()->get('tenant_info')['subdomain'])}}";
        }
        params['errorCallBackFunction'] = function(httpObj) {
            console.log(httpObj);
        }
        params['completeCallbackFunction'] = function(response) {

        }


        doAjax(params);

    }
});
$('.reque_quiz').on('click', function() {
    var examName = $(this).data('text');

    if (confirm("Do you want to activate " + examName + "?")) {

        var examination_id = $(this).data('id');
        // alert($(this).data('id'));
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/update-status'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            examination_id: examination_id,
            status: 'Active'
        });
        params['successCallbackFunction'] = function(response) {
           window.location.href = "{{route('tut_quizes',Session()->get('tenant_info')['subdomain'])}}";
        }
        params['errorCallBackFunction'] = function(httpObj) {
            console.log(httpObj);
        }
        params['completeCallbackFunction'] = function(response) {

        }


        doAjax(params);

    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
