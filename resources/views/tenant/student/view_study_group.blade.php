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

<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
<style type="text/css">
    .nav {
  padding-left: 0;
  margin-bottom: 0;
  list-style: none;
}
.nav > li {
  position: relative;
  display: block;
}
.nav > li > a {
  position: relative;
  display: block;
  padding: 10px 15px;
}
.nav > li > a:hover,
.nav > li > a:focus {
  text-decoration: none;
  background-color: #eeeeee;
}
.nav > li.disabled > a {
  color: #777777;
}
.nav > li.disabled > a:hover,
.nav > li.disabled > a:focus {
  color: #777777;
  text-decoration: none;
  cursor: not-allowed;
  background-color: transparent;
}
.nav .open > a,
.nav .open > a:hover,
.nav .open > a:focus {
  background-color: #eeeeee;
  border-color: #337ab7;
}
.nav .nav-divider {
  height: 1px;
  margin: 9px 0;
  overflow: hidden;
  background-color: #e5e5e5;
}
.nav > li > a > img {
  max-width: none;
}
.nav-tabs {
  border-bottom: 1px solid #ddd;
}
.nav-tabs > li {
  float: left;
  margin-bottom: -1px;
}
.nav-tabs > li > a {
  margin-right: 2px;
  line-height: 1.42857143;
  border: 1px solid transparent;
  border-radius: 4px 4px 0 0;
}
.nav-tabs > li > a:hover {
  border-color: #eeeeee #eeeeee #ddd;
}
.nav-tabs > li.active > a,
.nav-tabs > li.active > a:hover,
.nav-tabs > li.active > a:focus {
  color: #555555;
  cursor: default;
  background-color: #fff;
  border: 1px solid #ddd;
  border-bottom-color: transparent;
}
.nav-tabs.nav-justified {
  width: 100%;
  border-bottom: 0;
}
.nav-tabs.nav-justified > li {
  float: none;
}
.nav-tabs.nav-justified > li > a {
  margin-bottom: 5px;
  text-align: center;
}
.nav-tabs.nav-justified > .dropdown .dropdown-menu {
  top: auto;
  left: auto;
}
@media (min-width: 768px) {
  .nav-tabs.nav-justified > li {
    display: table-cell;
    width: 1%;
  }
  .nav-tabs.nav-justified > li > a {
    margin-bottom: 0;
  }
}
.nav-tabs.nav-justified > li > a {
  margin-right: 0;
  border-radius: 4px;
}
.nav-tabs.nav-justified > .active > a,
.nav-tabs.nav-justified > .active > a:hover,
.nav-tabs.nav-justified > .active > a:focus {
  border: 1px solid #ddd;
}
@media (min-width: 768px) {
  .nav-tabs.nav-justified > li > a {
    border-bottom: 1px solid #ddd;
    border-radius: 4px 4px 0 0;
  }
  .nav-tabs.nav-justified > .active > a,
  .nav-tabs.nav-justified > .active > a:hover,
  .nav-tabs.nav-justified > .active > a:focus {
    border-bottom-color: #fff;
  }
}
.nav-pills > li {
  float: left;
}
.nav-pills > li > a {
  border-radius: 4px;
}
.nav-pills > li + li {
  margin-left: 2px;
}
.nav-pills > li.active > a,
.nav-pills > li.active > a:hover,
.nav-pills > li.active > a:focus {
  color: #fff;
  background-color: #337ab7;
}
.nav-stacked > li {
  float: none;
}
.nav-stacked > li + li {
  margin-top: 2px;
  margin-left: 0;
}
.nav-justified {
  width: 100%;
}
.nav-justified > li {
  float: none;
}
.nav-justified > li > a {
  margin-bottom: 5px;
  text-align: center;
}
.nav-justified > .dropdown .dropdown-menu {
  top: auto;
  left: auto;
}
@media (min-width: 768px) {
  .nav-justified > li {
    display: table-cell;
    width: 1%;
  }
  .nav-justified > li > a {
    margin-bottom: 0;
  }
}
.nav-tabs-justified {
  border-bottom: 0;
}
.nav-tabs-justified > li > a {
  margin-right: 0;
  border-radius: 4px;
}
.nav-tabs-justified > .active > a,
.nav-tabs-justified > .active > a:hover,
.nav-tabs-justified > .active > a:focus {
  border: 1px solid #ddd;
}
@media (min-width: 768px) {
  .nav-tabs-justified > li > a {
    border-bottom: 1px solid #ddd;
    border-radius: 4px 4px 0 0;
  }
  .nav-tabs-justified > .active > a,
  .nav-tabs-justified > .active > a:hover,
  .nav-tabs-justified > .active > a:focus {
    border-bottom-color: #fff;
  }
}
.tab-content > .tab-pane {
  display: none;
}
.tab-content > .active {
  display: block;
}
.nav-tabs .dropdown-menu {
  margin-top: -1px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
    </style>
<style type="text/css">
#btnContainer {
    float: inline-end;

}

.overview_card_single_col_one {
    height: 100%;
    width: 100%;
    /* box-shadow: 1px 2px; */
}

.three_masc_left_card .editor {
    width: 555px !important;
}

.three_masc_left_card .text_with_info {
    width: 650px !important;
}



.overview_card_single {
    margin-top: 0px !important;
    padding: 0px 0px !important;

}

.member-card {
    line-height: 50px;
    padding: 4px;
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

/* .three_masc_left_card {
    padding: 0px 20px;
} */

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

.content-toggle {
    cursor: pointer;
}

.content_user {
    text-wrap: nowrap;
    font-size: 11px;
}

.fulltext {
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
                    <h4><i class="fa fa-people-group title_icon"></i> {{$details['name']}}</h4>
                    <span id="btnContainer"><button type="button" class="btn btn-sm btn_color_coppergreen"
                        style="font-weight:600; border-radius:5px;float: inline-end" onclick="rightModal('{{route('tus_invitetostudygroup',[Session()->get('tenant_info')['subdomain'],$study_group_id])}}', 'Invite External Friends To Study Group')">Invite External Friends</button>

                    </span>
                    <a href="#" class="btn btn-sm btn-default" title=""><i class="fa fa-backward"></i> Back</a>

                </div>
                <p>{{$details['description']}}</p>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-md-8">
        <div class="col-sm-12 space">
            <div class="overview_card_single"
                style="margin-left: auto; margin-right:auto; border-radius:1px; border-top-left-radius: 15px;border-top-right-radius: 15px">
                <div class="overview_card_single_col_wrap">
                    <div class="overview_card_single_col_one float-start text-start"
                        style="display:flex; flex-direction: column;">
                        <div class="three_masc_left_card">

                            <div class="image_wrap rounded-circle px-2">
                                @if($userInfo['user_logo']=='')
                                <img src="{{config('app.api_asset_url').'/img/system/auth-placeholder.jpg'}}"
                                    alt="user-image" class="rounded-circle" height="42">
                                @else
                                <img src="{{config('app.api_asset_url').$userInfo['user_logo']}}" alt="user-image"
                                    class="rounded-circle" height="42">
                                @endif
                                <span class="content_user">
                                    {{$profileInfo['first_name']??''}}
                                    {{$profileInfo['last_name']??''}}
                                </span>
                            </div>
                            <form method="POST" class="ajaxForm" id="frmAddContent"
                                action="{{route('tus_addcontentstudygroup',Session()->get('tenant_info')['subdomain'])}}">
                                @csrf
                                <input type="hidden" name="study_group_id" value="{{$study_group_id}}">
                                <div class="editor">
                                    <textarea name="content" id="content" cols="30" rows="3"
                                        class="form-control ckeditor" placeholder="Share whats in your mind!"
                                        required></textarea>
                                    <input type="submit" value="Post" id="btnSubmit"
                                        class="btn btn-primary my-2 float-end submit_content">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 space">
            <div class="overview_card_single"
                style="margin-left: auto; margin-right:auto; border-radius:1px; border-top-left-radius: 15px;border-top-right-radius: 15px">
                <div class="overview_card_single_col_wrap">
                    <div class="overview_card_single_col_one float-start text-start"
                        style="display:flex; flex-direction: column;">
                        <h5>Group Messages</h5>
                        @if(count($content_list['data'])>0)
                        @foreach($content_list['data'] as $k=>$record)
                        <div class="three_masc_left_card">
                            <div class="image_wrap rounded-circle " width="42" height="42">
                                @if($record['contentowner']['memberinfo']['user_logo']=='')
                                <img src="{{config('app.api_asset_url').'/img/system/auth-placeholder.jpg'}}"
                                    alt="user-image" class="rounded-circle" height="42">
                                @else
                                <img src="{{config('app.api_asset_url').$record['contentowner']['memberinfo']['user_logo']}}"
                                    alt="user-image" class="rounded-circle" height="42">
                                @endif
                                <span class="content_user">
                                    {{$record['contentowner']['memberinfo']['profile']['first_name']??''}}
                                    {{$record['contentowner']['memberinfo']['profile']['last_name']??''}}
                                </span>
                            </div>

                            <div class="text_with_info excerpt content-toggle" data-id="{{$k}}" id="excerpt_{{$k}}">
                                {!! \Helpers::excerpt($record['content'],200) !!}

                            </div>
                            <div class="text_with_info fulltext content-toggle" data-id="{{$k}}" id="fulltext_{{$k}}">
                                {!!$record['content']!!}

                            </div>
                        </div>
                        @endforeach
                        @if(isset($current_page))
                        <div class="row py-2">
                            <div class="col-md-6">
                                @if($current_page != $numOfpages)
                                Page : {{$current_page}} of {{$numOfpages}}
                                @endif
                            </div>
                            <div class="col-md-6">
                                <nav class="float-end">
                                    <ul class="pagination">
                                        @if(($has_next_page == true) && ($has_previous_page == false))
                                        <li class="page-item"><a class="page-link" title="Next Page"
                                                href="{{route('tus_viewstudygroup',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($details['study_group_id'])]).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                                    aria-hidden="true">&raquo;</span></a></li>
                                        @elseif(($has_next_page == false) && ($has_previous_page == true))
                                        <li class="page-item"><a class="page-link" title="Previous Page"
                                                href="{{route('tus_viewstudygroup',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($details['study_group_id'])]).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                                    aria-hidden="true">&laquo;</span></a></li>
                                        @elseif(($has_next_page == true) && ($has_previous_page == true))
                                        <li class="page-item"><a class="page-link" title="Previous Page"
                                                href="{{route('tus_viewstudygroup',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($details['study_group_id'])]).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                                    aria-hidden="true">&laquo;</span></a></li>
                                        <li class="page-item"><a class="page-link" title="Next Page"
                                                href="{{route('tus_viewstudygroup',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($details['study_group_id'])]).'/?search_text='.$search_text.'&page='.$next_page}}"><span
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
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-sm-12 space">
                <div class="overview_card_single" style="margin-left: auto; margin-right:auto">
                    <div class="overview_card_single_col_wrap">
                        <div style="text-align:center;font-weight:bold;padding:10px">
                            Members</div>

                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#internalmem">Internal</a></li>
                            <li><a data-toggle="tab" href="#externalmem">External</a></li>

                        </ul>

                        <div class="tab-content">
                            <div id="internalmem" class="tab-pane active">
                            @if(count($details['internal_members'])>0)
                        @foreach($details['internal_members'] as $member)
                        <div class="member-card">
                            <div class="image_wrap rounded-circle text_with_info1 " width="50" height="50">
                            @if($member['user_logo']=='')
                                <img src="{{config('app.api_asset_url').'/img/system/auth-placeholder.jpg'}}"
                                    alt="user-image" class="rounded-circle" height="42">
                                @else
                                <img src="{{config('app.api_asset_url').$member['user_logo']}}"
                                    alt="user-image" class="rounded-circle" height="42">
                                @endif
                            </div>
                            <div class="text_with_info">{{$member['first_name']??''}} {{$member['last_name']??''}}</div>
                        </div>
                        @endforeach
                        @endif
                            </div>
                            <div id="externalmem" class="tab-pane">
                            @if(count($details['external_members'])>0)
                        @foreach($details['external_members'] as $member)
                        <div class="member-card">
                            <div class="image_wrap rounded-circle text_with_info1 " width="50" height="50">
                            <img src="{{config('app.api_asset_url').'/img/system/auth-placeholder.jpg'}}"
                                    alt="user-image" class="rounded-circle" height="42">
                            </div>
                            <div class="text_with_info">{{$member['name']??''}} <small>({{$member['email']??''}})</small></div>
                        </div>
                        @endforeach
                        @endif
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script src="{{asset('plugins/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('plugins/ckeditor/plugins/ckfinder/ckfinder.js')}}"></script>
<script>
$(document).ready(function() {
    initailizeSelect2();
    //onPageLoad();
    $('.ckeditor').each(function() {
        id = $(this).attr('id');
        CKEDITOR.replace(id);
    });



});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({
        dropdownParent: $("#question-modal")
    });
}
$('#frmAddContent').on('submit', function() {
    var noname = 0;
    var content;
    $('.ckeditor').each(function() {
        id = $(this).attr('id');
        content = null;
        content = $("#cke_" + id + " iframe").contents().find("body").text();
        console.log("content:" + content + " id: " + id);
        //return false;
        if (content == '') {
            noname = 1;
        }
    });
    if (noname > 0) {
        alert('Please provide the content!');
        return false;
    }

    $('#btnSubmit').val('Submitting...').attr("disabled", true);
    return true;
});

$('.content-toggle').on('click', function() {
    var dataid = $(this).data('id');
    var id = $(this).attr('id');

    let excerptid = 'excerpt_' + dataid;
    let fulltextid = 'fulltext_' + dataid;

    if (id == excerptid) {
        $('#' + excerptid).hide();
        $('#' + fulltextid).show();
    }
    if (id == fulltextid) {
        $('#' + excerptid).show();
        $('#' + fulltextid).hide();
    }
});
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
