@php
$userInfo=Session::get('user');
$profileInfo=Session::get('profile_info');
$tenantInfo=Session::get('tenant_info');
$settingInfo=Session::get('setting_info');
$tenantShortName=Session::get('tenant_short_name');
//print_r($tenantInfo);
//print_r($settingInfo);
@endphp
@extends('layouts.default')
@section('title', 'Study Groups')
@section('pagecss')

<style type="text/css">
#btnContainer {
    float: inline-end;

}

.overview_card_single_col_one {
    height: 100%;
    width: 100%;
    /* box-shadow: 1px 2px; */
}

.three_masc_left_card .text_with_info {
    width: 650px !important;
}


.overview_card_single {
    margin-top: 0px !important;
    padding: 0px 0px !important;

}


#recactive {
    background: #FFFFFF;
    border: 1px solid #DBDBDB;
    display: inline-block;
    flex-direction: row;
    border-radius: 5px;
    text-align: center;
    width: 182px;
    height: 35px;
    /* margin-right: 1%; */
}

.new_button {
    position: absolute;
    bottom: 0;
    padding: 10px 0;
    width: 92%;
}

.new_button button {

    border-radius: 5px;
    opacity: 1;
    color: #FFFFFF;
}

@media only screen and (max-width: 820px) {
    .new_button button {
        width: 100%;
        font-size: small;
    }
}

.three_masc_left_card {
    padding: 0px 20px;
}

.text_with_info1 img {
    float: left;
    margin: 5px 5px 3px 5px;
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

.size img {
    border-radius: 15px 15px 0 0;
    border: 1px solid #EBEBEB;
}

p {
    text-align: justify;
    font-size: 10px;
    padding: 0 10px 10px 10px;
}

/* ribbon css */
.ribbon-active {
    --f: 10px;
    /* control the folded part*/
    --r: 15px;
    /* control the ribbon shape */
    --t: 10px;
    /* the top offset */

    position: absolute;
    inset: var(--t) calc(-1*var(--f)) auto auto;
    padding: 0 10px var(--f) calc(10px + var(--r));
    clip-path:
        polygon(0 0, 100% 0, 100% calc(100% - var(--f)), calc(100% - var(--f)) 100%,
            calc(100% - var(--f)) calc(100% - var(--f)), 0 calc(100% - var(--f)),
            var(--r) calc(50% - var(--f)/2));
    background: green;
    box-shadow: 0 calc(-1*var(--f)) 0 inset #0005;
    color: white;
}

.ribbon-inactive {
    --f: 10px;
    /* control the folded part*/
    --r: 15px;
    /* control the ribbon shape */
    --t: 10px;
    /* the top offset */

    position: absolute;
    inset: var(--t) calc(-1*var(--f)) auto auto;
    padding: 0 10px var(--f) calc(10px + var(--r));
    clip-path:
        polygon(0 0, 100% 0, 100% calc(100% - var(--f)), calc(100% - var(--f)) 100%,
            calc(100% - var(--f)) calc(100% - var(--f)), 0 calc(100% - var(--f)),
            var(--r) calc(50% - var(--f)/2));
    background: #BD1550;
    box-shadow: 0 calc(-1*var(--f)) 0 inset #0005;
    color: white;
}
.sg-nav a.active {
    font-weight: 600;
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<!-- end page title -->

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body py-1">

                    <span class="float-start pt-1 sg-nav"><a @if($url_view=='all' || $url_view==null) class="active" @endif
                                href="{{route('tus_studygroups',[Session()->get('tenant_info')['subdomain'],'v' =>\Helpers::encryptId('all'),'s' =>\Helpers::encryptId($active_status)])}}">All
                                Internal Groups</a> | <a @if($url_view=='external') class="active" @endif
                                href="{{route('tus_studygroups',[Session()->get('tenant_info')['subdomain'],'v' => \Helpers::encryptId('external'),'s' =>\Helpers::encryptId($active_status)])}}">External Groups</a>  | <a @if($url_view=='my') class="active" @endif
                                href="{{route('tus_studygroups',[Session()->get('tenant_info')['subdomain'],'v' => \Helpers::encryptId('my'),'s' =>\Helpers::encryptId('all')])}}">My
                                Groups</a> | <a href="javascript:void(0);"
                                onclick="rightModal('{{route('tus_addstudygroup',Session()->get('tenant_info')['subdomain'])}}', 'Add Study Group')">Create
                                a Group</a></span>

                        <span id="btnContainer">
                            <button class="btn active" onclick="gridView()"><i class="fa fa-th-large"></i>
                                Grid</button>
                            <button class="btn" onclick="listView()"><i class="fa fa-bars"></i>
                                List</button>
                            <!-- <select name="Recently Active" id="recactive" style="color:#C0C0C0">
                                <option value="option">Recently Active</option>
                            </select> -->
                        </span>

                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
        @if(count($listing)>0)
        <div class="row">

            @foreach($listing as $record)
            <div class="col-md-2 py-2 grid">
                <div class="overview_card_single " style="margin-left: auto; margin-right:auto">
                    <div class="ribbon-{{strtolower($record['status'])}}">{{$record['status']}}</div>
                    <div class="overview_card_single_col_wrap">
                        <div class="overview_card_single_col_one">
                            <div class="size">
                                @if($record['group_image'] != null)
                                <img src="{{config('app.api_asset_url') .$record['group_image']}}" alt="group image"
                                    width="100%">
                                @else
                                <img src="{{config('app.api_asset_url') .$no_image}}" alt="group image" width="100%">
                                @endif
                            </div>
                            <div
                                style="color: #434343; margin-bottom: 5px; font-weight:bold; padding: 10px 10px 5px 10px">
                                {{$record['name']}}
                            </div>
                            <div class="square" style="height:180px">
                                <div class="image_wrap rounded-circle text_with_info1 " width="50" height="50">
                                    @if($record['creator']['user_logo']=='')
                                    <img src="{{config('app.api_asset_url').'/img/system/auth-placeholder.jpg'}}"
                                        alt="user-image" class="rounded-circle" height="42">
                                    @else
                                    <img src="{{config('app.api_asset_url').$record['creator']['user_logo']}}"
                                        alt="user-image" class="rounded-circle" height="42">
                                    @endif
                                    <span>
                                        {{$record['creator']['profile']['first_name']??''}}
                                        {{$record['creator']['profile']['last_name']??''}}
                                    </span>
                                </div>
                                <p class="text_with_info">
                                    {{ \Helpers::excerpt($record['description'],200) }}
                                </p>
                            </div>

                            <div class="three_masc_left_card">

                                <div class="new_button">
                                    @if($userInfo['user_id']==$record['created_by'] ||
                                    in_array($record['study_group_id'],$studygroupsin))
                                    <a href="{{route('tus_viewstudygroup',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['study_group_id'])])}}" class="btn btn-primary btn-sm">View</a>
                                    @else
                                    <button type="button" data-name="{{$record['name']}}"
                                        data-id="{{\Helpers::encryptId($record['study_group_id'])}}"
                                        class="btn btn-primary btn-sm join-group">Join Group!</button>
                                    @endif
                                    @if($userInfo['user_id']==$record['created_by'])
                                    <a href="javascript:void(0);" class="float-end btn btn-sm btn-default"
                                        onclick="rightModal('{{route('tus_editstudygroup',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['study_group_id'])])}}', 'Edit Study Group')"><i
                                            class="fa fa-edit"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach

        </div>
        @endif
        <div class="list" style="width:100%">
        <div class="table-responsive">
            <table id="datatable" class="table table-striped  nowrap table " width="100%">
                <thead>
                    <tr style="background-color: rgba(90, 194, 185, 1); color: #ffffff;">
                        <th class="nosort">Image</th>
                        <th>Group Name</th>
                        <th>Owner</th>
                        <th>Created On</th>
                        <th>Status</th>
                        <th class="nosort">Action</th>
                    </tr>
                </thead>
                @if(count($listing)>0)
                <tbody>
                    @foreach($listing as $record)
                    <tr>
                        <td>
                            @if($record['group_image'] != null)
                            <img src="{{config('app.api_asset_url') .$record['group_image']}}" alt="group image"
                                width="50%">
                            @else
                            <img src="{{config('app.api_asset_url') .$no_image}}" alt="group image" width="50%">
                            @endif
                        </td>
                        <td>{{$record['name']}}</td>
                        <td>{{$record['creator']['profile']['first_name']??''}}
                            {{$record['creator']['profile']['last_name']??''}}</td>
                        <td>{{date('d-m-Y',strtotime($record['created_at']))}}</td>
                        <td>{{$record['status']}}</td>
                        <td>

                            @if($userInfo['user_id']==$record['created_by'] ||
                            in_array($record['study_group_id'],$studygroupsin))
                            <a href="{{route('tus_viewstudygroup',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['study_group_id'])])}}" class="btn btn-primary btn-sm">View Group</a>
                            @else
                            <button type="button" data-name="{{$record['name']}}"
                                        data-id="{{\Helpers::encryptId($record['study_group_id'])}}"
                                        class="btn btn-primary btn-sm join-group">Join Group!</button>
                            @endif
                            @if($userInfo['user_id']==$record['created_by'])
                            <a href="javascript:void(0);" class="float-end btn btn-sm btn-default"
                                onclick="rightModal('{{route('tus_editstudygroup',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['study_group_id'])])}}', 'Edit Study Group')"><i
                                    class="fa fa-edit"></i></a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
        </div>
        </div>
    </div>

</div>


@endsection
@section('pagescript')
<script>
function listView() {
    $('.grid').css('display', 'none');
    $('.list').css('display', 'inline-table');
}

function gridView() {
    $('.list').css('display', 'none');
    $('.grid').css('display', 'block');
}

$('.join-group').on('click', function() {
    var gname = $(this).data('name');
    var gid = $(this).data('id');
    if (confirm("Do you want to join the group namely - " + gname + "?")) {
        var token = "{{Session::get('usertoken')}}";
        // alert(token);
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/studygroup/join'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            study_group_id: gid
        });
        params['beforeSendCallbackFunction'] = function(response) {

            $(this).attr("disabled","disabled");
        }

        params['successCallbackFunction'] = function(response) {
            alert(response.result.message);
            console.log(response.result.message);
            window.location.href = "{{route('tus_studygroups', Session()->get('tenant_info')['subdomain'])}}";
        }
        params['errorCallBackFunction'] = function(httpObj) {
            console.log(httpObj.responseJSON.error.message);
            alert(httpObj.responseJSON.error.message);
        }
        params['completeCallbackFunction'] = function(response) {

            $(this).attr("disabled",false);
        }
        doAjax(params);
    }
});

/*
initDataTable('basic-datatable');
*/
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
