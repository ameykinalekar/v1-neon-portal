@extends('layouts.default')
@section('title', 'All Teacher Assistants')
@section('pagecss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Teacher Assistants
                </h4>
                <button type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    onclick="rightModal('{{route('ta_addteacherassistant',Session()->get('tenant_info')['subdomain'])}}', 'Add Teacher Assistant')">
                    <i class="mdi mdi-plus"></i> Add Teacher Assistant</button>
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
                            <select name="sayid" id="sayid" class="form-control pull-right" style="width: fit-content;"><option value="">All Academic Year</option></select>
                            <select name="sygid" id="sygid" class="form-control pull-right" style="width: fit-content;"><option value="">All Year Group</option></select>
                            <select name="ssid" id="ssid" class="form-control pull-right" style="width: fit-content;"><option value="">All Subject</option></select>
                            <input type="text" name="search_text" value="{{$search_text}}"
                                class="form-control pull-right" style="width: fit-content;" placeholder="Search text...">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    Search
                                    <!-- <i class="fa fa-search"></i> -->
                                </button>
                                <a class="btn btn-light"
                                    href="{{route('ta_teacherassistantlist',Session()->get('tenant_info')['subdomain'])}}">Reset</a>
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
                                <th>Photo</th>
                                <th>Teacher Assistant Name</th>
                                <th>Teacher Assistant Email</th>
                                <th>Year Group</th>
                                <th>Subject</th>
                                <th>DBS</th>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($response['result']['teacher_assistant_list']['data'])>0)
                            @foreach($response['result']['teacher_assistant_list']['data'] as $record)
                            @php
                            $yrgrp=array();
                            $subjects=array();
                            if($record['year_group_names']??''!=''){
                                $yrgrp=explode(',',$record['year_group_names']);
                            }
                            if($record['subject_names']??''!=''){
                                $subjects=explode(',',$record['subject_names']);
                            }
                        @endphp
                            <tr>
                                <td>
                                    @if($record['user_logo']!='')
                                    <span>
                                        <a class="fancy-box-a" data-fancybox="demo"
                                            data-caption="{{$record['first_name'].' '.$record['last_name']}} - Profile Image"
                                            href="{{config('app.api_asset_url') . $record['user_logo']}}"><img
                                                src="{{config('app.api_asset_url') . $record['user_logo']}}" height="auto"
                                                width="35px" /></a>
                                    </span>
                                    @else
                                    <span>
                                        <img src="{{config('app.api_asset_url') . $no_image}}" height="auto" width="35px" />
                                    </span>
                                    @endif
                                </td>
                                <td>{{$record['first_name'].' '.$record['last_name']}}

                                </td>
                                <td>{{$record['email']}}</td>
                                <td>
                                @if(count($yrgrp)>0)
                                <small>

                                @foreach($yrgrp as $yr)
                                <div>{{$yr}}</div>
                                @endforeach
                                </small>
                                @endif
                            </td>
                            <td>
                                @if(count($subjects)>0)
                                <small>
                                @foreach($subjects as $sub)
                                <div>{{$sub}}</div>
                                @endforeach
                                </small>
                                @endif
                            </td>
                            <td>
                                @if($record['dbs_certificate_file']==null)
                                <i class="far fa-eye-slash" style="font-size:22px;"></i>
                                @else
                                @php
                                $dbsdate='';
                                if($record['end_date_dbs']!=null){
                                    $dbsdate='Expires on '.date('d-m-Y',strtotime($record['end_date_dbs']));
                                }
                                @endphp
                                <a class="fancy-box-a" data-fancybox="demo"
                                    data-caption="{{$record['first_name'].' '.$record['last_name']}} - Dbs Certificate Image {{$dbsdate}}"
                                    href="{{config('app.api_asset_url') . $record['dbs_certificate_file']}}"><i class="far fa-file" style="font-size:22px;color:#428f88;"></i></a>
                                @endif

                            </td>
                            <td>
                                @if($record['id_file']==null)
                                <i class="far fa-eye-slash" style="font-size:22px;"></i>
                                @else
                                @php
                                $iddate='';
                                if($record['end_date_id']!=null){
                                    $iddate='Expires on '.date('d-m-Y',strtotime($record['end_date_id']));
                                }
                                @endphp
                                <a class="fancy-box-a" data-fancybox="demo"
                                    data-caption="{{$record['first_name'].' '.$record['last_name']}} - National(government) Id Image  {{$iddate}}"
                                    href="{{config('app.api_asset_url') . $record['id_file']}}"><i class="far fa-file" style="font-size:22px;color:#428f88;"></i></a>
                                @endif


                            </td>
                                <td><div class="status status-{{strtolower($record['status'])}}">
                                        {{$record['status']}}
                                    </div></td>
                                <td>
                                    <a href="javascript:void(0);" title="Edit Teacher Assistant"
                                        onclick="rightModal('{{route('ta_editteacherassistant',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['user_id'])])}}', 'Edit Teacher Assistant')"><i
                                            class="fa fa-pencil"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="9" class="text-center">No data found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if(isset($current_page))
                @php
                    $initialQS='?search_text='.$search_text.'&sayid='.$sayid.'&sygid='.$sygid.'&ssid='.$ssid;
                @endphp
                <div class="row">
                    <div class="col-md-6">Page : {{$current_page}} of {{$numOfpages}}</div>
                    <div class="col-md-6">
                        <nav class="float-end">
                            <ul class="pagination">
                                @if(($has_next_page == true) && ($has_previous_page == false))
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('ta_teacherassistantlist',Session()->get('tenant_info')['subdomain']).'/'.$initialQS.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('ta_teacherassistantlist',Session()->get('tenant_info')['subdomain']).'/'.$initialQS.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('ta_teacherassistantlist',Session()->get('tenant_info')['subdomain']).'/'.$initialQS.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('ta_teacherassistantlist',Session()->get('tenant_info')['subdomain']).'/'.$initialQS.'&page='.$next_page}}"><span
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
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-academic-years'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Academic Year</option>';
        response.result.academic_year_list.forEach(function(item) {
            //if(item.status=='Active'){
                    option=option+'<option value="' + item.academic_year_id + '">' +
                        item.academic_year + '</option>';
               // }
        });
        $('#sayid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#sayid').html('<option value="">All Academic Year</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        @if($sayid!=null)
        $('#sayid').val("{{$sayid}}").trigger('change');
        @endif
    }
    doAjax(params);

}

$("#sayid").on('change',function(){
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-academicyearid-yeargroups'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        academic_year_id: $(this).val()
            });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#sygid').html(option);
        $('#sygid').attr("disabled","disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Year Group</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + '</option>';
        });
        $('#sygid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#sygid').html('<option value="">All Year Group</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#sygid').attr("disabled",false);
        @if($sygid!=null)
        $('#sygid').val("{{$sygid}}").trigger('change');
        @endif
    }
    doAjax(params);
});

$("#sygid").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-yeargroup-subjects'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        year_group_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#ssid').html(option);
        $('#ssid').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Subject</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name + '</option>';
        });
        $('#ssid').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#ssid').html('<option value="">All Subject</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#ssid').attr("disabled", false);
        @if($ssid!=null)
        $('#ssid').val("{{$ssid}}").trigger('change');
        @endif
    }
    doAjax(params);
});


</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
