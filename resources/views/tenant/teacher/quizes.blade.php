@extends('layouts.default')
@section('title', 'Quizes')
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
                    <h4><i ></i> Quizes</h4>
                    <span id="btnContainer"><button type="button" class="btn btn-sm btn_color_coppergreen"
                        style="font-weight:600; border-radius:5px;float: inline-end"
                        onclick="rightModal('{{route('tut_addquiz',Session()->get('tenant_info')['subdomain'])}}', 'New Quiz')">New
                            Quiz</button></span>

                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        @if(count($response['result']['listing']['data'])>0)
        <div class="row">
            @foreach($response['result']['listing']['data'] as $record)
            @php
                $themeClass="theme_deep_sky_blue";
                if($record['status']=='Inactive'){
                    $themeClass="theme_red";
                }
            @endphp
            <div class="col-md-6">
                <div class="card example_quiz_card p-2 {{$themeClass}}" style="border-radius:5px !important">
                    <div class="example_quiz_card_header">
                        @if($record['status']=='Inactive')
                        <button type="button" class="btn btn-outline-danger float-start example_quiz_card_btn"
                            style="text-transform: uppercase;">{{$record['status']}}</button>
                        @else
                        <button type="button" class="btn btn-outline-primary float-start example_quiz_card_btn"
                            style="text-transform: uppercase;">{{$record['status']}}</button>
                        @endif
                        <div class="date_box">
                            <p class="me-auto mb-0" style="display: inline-block;">Created:
                                {{date('d-m-Y',strtotime($record['created_at']))}}</p>
                        </div>
                        <div class="float-end dropdown" style="display: inline-block;">
                            <a href="#" rolo="button" class="btn dropdown-toggle  p-1" type="button"
                                id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <!-- icon svg -->
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"
                                    width="20" height="20" x="0" y="0" viewBox="0 0 515.555 515.555"
                                    style="enable-background:new 0 0 512 512" xml:space="preserve"
                                    class="hovered-paths">
                                    <g>
                                        <path
                                            d="M496.679 212.208c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138 65.971-25.167 91.138 0M303.347 212.208c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138 65.971-25.167 91.138 0M110.014 212.208c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138 65.971-25.167 91.138 0"
                                            fill="#000000" data-original="#000000" class="hovered-path"></path>
                                    </g>
                                </svg>
                                <!-- icon svg -->
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item"
                                        href="{{route('tut_editquiz',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['examination_id'])])}}">Edit</a>
                                </li>
                                @if($record['status']=='Inactive')
                                <li><a class="dropdown-item reque_quiz"
                                        data-id="{{\Helpers::encryptId($record['examination_id'])}}"
                                        data-text="{{$record['name']}}" href="javascript:void(0);">Activate</a></li>
                                @else
                                <li><a class="dropdown-item delete_quiz"
                                        data-id="{{\Helpers::encryptId($record['examination_id'])}}"
                                        data-text="{{$record['name']}}" href="javascript:void(0);">Deactivate</a></li>
                                @endif
                                @if($record['homework']>0)
                                <li><a class="dropdown-item rehw_quiz"
                                        data-id="{{\Helpers::encryptId($record['examination_id'])}}"
                                        data-text="{{$record['name']}}" href="javascript:void(0);">Remove from homework</a></li>
                                @else
                                <li><a class="dropdown-item hw_quiz"
                                        data-id="{{\Helpers::encryptId($record['examination_id'])}}"
                                        data-text="{{$record['name']}}" href="javascript:void(0);">Mark as homework</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="example_quiz_card_body">
                        <h3>{{$record['name']}}</h3>

                    </div>
                    <div class="example_quiz_card_footer">
                        @if(($record['subject']['subject_name']??'') !='')
                        <a class="btn btn-outline-secondary float-end">{{$record['subject']['subject_name']??''}}</a>
                        @else
                        <a class="btn btn-outline-secondary float-end">UNCATEGORIZED</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if(isset($current_page))
        <div class="row">
            <div class="col-md-6">
                <!-- Page : {{$current_page}} of {{$numOfpages}} -->

            </div>
            <div class="col-md-6">
                <nav class="float-end">
                    <ul class="pagination">
                        @if(($has_next_page == true) && ($has_previous_page == false))
                        <li class="page-item"><a class="page-link" title="Next Page"
                                href="{{route('tut_quizes',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                    aria-hidden="true">&raquo;</span></a></li>
                        @elseif(($has_next_page == false) && ($has_previous_page == true))
                        <li class="page-item"><a class="page-link" title="Previous Page"
                                href="{{route('tut_quizes',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                    aria-hidden="true">&laquo;</span></a></li>
                        @elseif(($has_next_page == true) && ($has_previous_page == true))
                        <li class="page-item"><a class="page-link" title="Previous Page"
                                href="{{route('tut_quizes',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                    aria-hidden="true">&laquo;</span></a></li>
                        <li class="page-item"><a class="page-link" title="Next Page"
                                href="{{route('tut_quizes',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                    aria-hidden="true">&raquo;</span></a></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
        @endif
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
            status: 'In Design'
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

$('.rehw_quiz').on('click', function() {
    var examName = $(this).data('text');

    if (confirm("Do you want to remove from homework - " + examName + "?")) {

        var examination_id = $(this).data('id');
        // alert($(this).data('id'));
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/update-hw'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            examination_id: examination_id,
            homework: 0
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

$('.hw_quiz').on('click', function() {
    var examName = $(this).data('text');

    if (confirm("Do you want to mark as homework - " + examName + "?")) {

        var examination_id = $(this).data('id');
        // alert($(this).data('id'));
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/update-hw'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            examination_id: examination_id,
            homework: 1
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
