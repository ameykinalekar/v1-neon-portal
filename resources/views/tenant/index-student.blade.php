@extends('layouts.default')
@section('title', 'Dashboard')
@section('pagecss')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
<style type="text/css">
.dashboard-calendar {
    width: 100%;
    padding: 17px;
}
.fc .fc-daygrid-body-unbalanced .fc-daygrid-day-events {
    min-height: 0em;
}
tr th, tr td {
    font-family: Poppins;
    font-weight: 400;
    font-size: 10px;
}

</style>
@endsection
@section('content')
@php
$userInfo=Session::get('user');
$profileInfo=Session::get('profile_info');
$tenantInfo=Session::get('tenant_info');
$settingInfo=Session::get('setting_info');
$tenantShortName=Session::get('tenant_short_name');
//print_r($tenantInfo);
//print_r($settingInfo);
@endphp
<div class="px-2">
<div class="row">
        <div class="col-md-12">
            <?php
$path = 'https://images.pexels.com/photos/6789778/pexels-photo-6789778.jpeg';
if ($profileInfo['cover_picture'] != '') {
    $path = config('app.api_asset_url') . $profileInfo['cover_picture'];
}

$user_pic1 = config('app.api_asset_url') . '/img/system/auth-placeholder.jpg';

if ($userInfo['user_logo'] != '') {
    $user_pic1 = config('app.api_asset_url') . $userInfo['user_logo'];
}

?>

            <div class="card " style="background-image:url('<?php echo $path ?>?auto=compress&cs=tinysrgb&w=1600'); background-repeat: no-repeat;background-size: cover;">
                <div class="card-header pt-3" style="background: none; text-align: left; border: none;">
                    <div class="me-2" style="display: inline-block;font-size:13px; color:#434343; padding-left: 15px;">

                        <a href="javascript:void(0);" class="" onclick="rightModal('{{route('tus_editstudentci',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($userInfo['user_id'])])}}', 'Edit Cover Image')">
                            <svg xmlns="http://www.w3.org/2000/svg" id="camera_1_" data-name="camera (1)" width="18.764" height="14.743" viewBox="0 0 18.764 14.743" style="position: absolute;left: 15px;top: 8px;">
                                <circle id="Ellipse_143" data-name="Ellipse 143" cx="2.759" cy="2.759" r="2.759" transform="translate(6.623 5.361)" fill="#1f2125"></circle>
                                <path id="Path_245" data-name="Path 245" d="M48.753,82.681H46.282a.611.611,0,0,1-.4-.209l-1.086-1.715a.651.651,0,0,0-.057-.077A1.866,1.866,0,0,0,43.309,80H39.455a1.866,1.866,0,0,0-1.427.679.651.651,0,0,0-.057.077l-1.086,1.717a.548.548,0,0,1-.361.209v-.335a.67.67,0,0,0-.67-.67H34.848a.67.67,0,0,0-.67.67v.335H34.01A2.013,2.013,0,0,0,32,84.693v8.039a2.013,2.013,0,0,0,2.01,2.01H48.753a2.013,2.013,0,0,0,2.01-2.01V84.691A2.013,2.013,0,0,0,48.753,82.681Zm-7.372,9.382A4.021,4.021,0,1,1,45.4,88.042,4.021,4.021,0,0,1,41.382,92.063Z" transform="translate(-32 -80)" fill="#1f2125"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="card-body py-0">
                    <div class="row">
                        <div class="col-md-6 ps-91 pb-53 ">
                            <div class="avatar_wrap" style="width: 147px;height:147px;display: inline-block ;float: left;">
                                <a href="javascript:void(0);" onclick="rightModal('{{route('tus_editstudentpi',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($userInfo['user_id'])])}}', 'Edit Profile Image')"
                                    class="camera-icon_wrap dropdown-item"
                                    style="text-align: center; width:25px; height:44px;background:#E4E6EB">
                                    <svg xmlns="http://www.w3.org/2000/svg" id="camera_1_"
                                        data-name="camera (1)" width="18.764" height="14.743"
                                        viewBox="0 0 18.764 14.743" style="position: absolute;left: 15px;top: 8px;">
                                        <circle id="Ellipse_143" data-name="Ellipse 143"
                                            cx="2.759" cy="2.759" r="2.759"
                                            transform="translate(6.623 5.361)" fill="#1f2125" />
                                        <path id="Path_245" data-name="Path 245"
                                            d="M48.753,82.681H46.282a.611.611,0,0,1-.4-.209l-1.086-1.715a.651.651,0,0,0-.057-.077A1.866,1.866,0,0,0,43.309,80H39.455a1.866,1.866,0,0,0-1.427.679.651.651,0,0,0-.057.077l-1.086,1.717a.548.548,0,0,1-.361.209v-.335a.67.67,0,0,0-.67-.67H34.848a.67.67,0,0,0-.67.67v.335H34.01A2.013,2.013,0,0,0,32,84.693v8.039a2.013,2.013,0,0,0,2.01,2.01H48.753a2.013,2.013,0,0,0,2.01-2.01V84.691A2.013,2.013,0,0,0,48.753,82.681Zm-7.372,9.382A4.021,4.021,0,1,1,45.4,88.042,4.021,4.021,0,0,1,41.382,92.063Z"
                                            transform="translate(-32 -80)" fill="#1f2125" />
                                    </svg>
                                </a>
                                <div class="avatar" style="background-image:url('<?php echo $user_pic1 ?>'); background-repeat: no-repeat;background-size: cover;bacdisplay: inline-block ;float: left;">

                                </div>
                            </div>
                            <div class="student_name_sub_stat" style="">
                                <h3
                                    style="font: normal normal bold 17px/23px Open Sans;color: #FFFFFF;">
                                    {{ $profileInfo['first_name']??'' }} {{ $profileInfo['middle_name']??'' }} {{ $profileInfo['last_name']??'' }}</h3>
                                <hr style="color:#FFFFFF">
                                <p
                                    style="font: normal normal normal 17px/23px Open Sans;color: #FFFFFF;">
                                    {{GlobalVars::USER_TYPES[$userInfo['user_type']]}}</p>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="single_circle_progress py-2  margin_auto_left_t_d"
                                style="width: 50%; text-align: center; ">
                                <!-- Change data-value in svg element to impact progress -->
                                <svg class="ct" viewBox="0 0 100 100"
                                    xmlns="http://www.w3.org/2000/svg"
                                    preserveAspectRatio="none" data-value="{{$profile_completion??0}}"
                                    style="width:100px; height:100px">
                                    <circle r="45" cx="50" cy="50" />
                                    <!-- 282.78302001953125 is auto-calculated by path.getTotalLength() -->
                                    <path class="meter"
                                        d="M5,50a45,45 0 1,0 90,0a45,45 0 1,0 -90,0"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        stroke-dashoffset="282.78302001953125"
                                        stroke-dasharray="282.78302001953125" />
                                    <!-- Value automatically updates based on data-value set above -->
                                    <text x="50" y="50" text-anchor="middle"
                                        dominant-baseline="central" font-size="17"
                                        style="color:#FFFFFF;fill: #ffffff;"></text>
                                </svg>
                                <br>
                                <a type="button" class="btn btn-outline-light" href="{{route('myaccount', Session()->get('tenant_info')['subdomain'])}}"
                                    style="border-radius: 15px; margin-top: 14px;">Complete
                                    Profile
                                    &nbsp;&nbsp;&nbsp;&nbsp;></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h4 style="">
                Overview</h4>
        </div>
    </div>



    <div class="row " style="">
        <div class="col-md-3 text-center d-mob-pad-b-10 overview_card_container_main_col">
            <div class="overview_card_single overview_card_single_768" style="margin-left: auto; margin-right:auto">
                <div class="overview_card_single_col_wrap">
                    <div class="overview_card_single_col_one float-start text-start"
                        style="display:flex; flex-direction: column;">
                        <div class="overview_card_single_card-title">
                            Training Completion
                        </div>
                        <div class="progress_amount" >
                            <p>
                                0%
                            </p>
                        </div>
                    </div>
                    <div class="overview_card_single_col_two  d-flex align-items-center" style="top: 60px;">
                        <img src="{{ asset('img/system/Group350.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 text-center d-mob-pad-b-10 overview_card_container_main_col">
            <div class="overview_card_single overview_card_single_768" style="margin-left: auto; margin-right:auto">
                <div class="overview_card_single_col_wrap">
                    <div class="overview_card_single_col_one float-start text-start"
                        style="display:flex; flex-direction: column;">
                        <div class="overview_card_single_card-title">
                            Effectiveness Score
                        </div>
                        <div class="progress_amount">
                            <p>
                                0%
                            </p>
                        </div>

                    </div>
                    <div class="overview_card_single_col_two  d-flex align-items-center">
                        <img src="{{ asset('img/system/diagram.png') }}" alt="" height="60px">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 text-center d-mob-pad-b-10 overview_card_container_main_col">
            <div class="overview_card_single mx-auto overview_card_single_768">
                <div class="overview_card_single_col_wrap">
                    <div class="overview_card_single_col_one float-start"
                        style="display:flex; flex-direction: column; text-align: left;">
                        <div class="overview_card_single_card-title">
                            Teacher's Review &nbsp;
                        </div>
                        <div class="progress_amount">
                            <p>
                                0/1
                            </p>
                        </div>

                    </div>
                    <div class="overview_card_single_col_two  d-flex align-items-center" style="top: 47px;">
                        <img src="{{ asset('img/system/Group423.png') }}" alt="" width="50px" height="30px">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 text-center d-mob-pad-b-10 overview_card_container_main_col">
            <div class="overview_card_single mx-auto overview_card_single_768">
                <div class="overview_card_single_col_wrap">
                    <div class="overview_card_single_col_one float-start"
                        style="display:flex; flex-direction: column; text-align: left;">
                        <div class="overview_card_single_card-title">
                            Add a new KPI
                        </div>
                        <div class="progress_amount">
                            <p>
                                &nbsp;
                            </p>
                        </div>

                    </div>
                    <div class="overview_card_single_col_two  d-flex align-items-center" style="top:33px">
                        <!-- SVG WRAP -->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50"
                            height="50" viewBox="0 0 68 68">
                            <defs>
                                <filter id="add-circle_1_" x="0" y="0" width="68" height="68"
                                    filterUnits="userSpaceOnUse">
                                    <feOffset dx="1" dy="2" input="SourceAlpha"></feOffset>
                                    <feGaussianBlur stdDeviation="2.5" result="blur">
                                    </feGaussianBlur>
                                    <feFlood flood-color="#5bc2b9" flood-opacity="0.302">
                                    </feFlood>
                                    <feComposite operator="in" in2="blur"></feComposite>
                                    <feComposite in="SourceGraphic"></feComposite>
                                </filter>
                            </defs>
                            <g transform="matrix(1, 0, 0, 1, 0, 0)" filter="url(#add-circle_1_)">
                                <path id="add-circle_1_2" data-name="add-circle (1)"
                                    d="M74.5,48A26.5,26.5,0,1,0,101,74.5,26.53,26.53,0,0,0,74.5,48ZM84.692,76.538H76.538v8.154a2.038,2.038,0,1,1-4.077,0V76.538H64.308a2.038,2.038,0,1,1,0-4.077h8.154V64.308a2.038,2.038,0,1,1,4.077,0v8.154h8.154a2.038,2.038,0,1,1,0,4.077Z"
                                    transform="translate(-41.5 -42.5)" fill="#5bc2b9">
                                </path>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="">
        <div class="col-md-6 tab_width_100" style="">
            <div class="row">
                <div class="col-md-12" style="padding:10px">
                    <h4 style="">
                        Upcomming Tasks</h4>
                </div>
                @if(count($upcoming_tasks)>0)
                @foreach($upcoming_tasks as $ut)
                <div class="col-md-12 three_masc_left_card_main_col">
                    <div class="three_masc_left_card">
                        <div class="image_wrap" width="50" height="50">
                            <img src="{{ asset('img/system/need-assessment.png') }}" width="50" height="50"
                                alt="Task Picture">
                        </div>
                        <div class="text_with_info">
                            <strong>{{$ut['task']??''}}</strong>
                            <p style="font: normal normal normal 14px/21px Poppins;color: #434343;margin-bottom: 5px;">
                                {{$ut['description']??''}}
                            </p>
                            <p style="font: normal normal normal 14px/21px Poppins;color: #434343; margin-bottom: 0;">
                                Date {{$ut['start_date']??''}}
                            </p>
                        </div>

                        <a href="javascript:void(0);" class="view_utask" style="vertical-align: middle;" data-id="{{$ut['task_id']}}"><b>View
                                Details &nbsp; &nbsp;></b></a>
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-md-12 three_masc_left_card_main_col">No upcoming task found.</div>
                @endif
                <!-- <div class="col-md-12 three_masc_left_card_main_col">
                    <div class="three_masc_left_card">
                        <div class="image_wrap" width="50" height="50">
                            <img src="{{ asset('img/system/live-class.png') }}" width="50" height="50"
                                alt="Client Profile Picture">
                        </div>
                        <div class="text_with_info">
                            <strong>Live Class</strong>
                            <p style="font: normal normal normal 14px/21px Poppins;color: #434343;margin-bottom: 5px;">
                                Chemistry - Photosynthesis
                            </p>
                            <p style="font: normal normal normal 14px/21px Poppins;color: #434343; margin-bottom: 0;">
                                Date 28.07.2024; UK Time 10 AM
                            </p>
                        </div>

                        <a href="#" style="vertical-align: middle;"><b>View
                                Details &nbsp; &nbsp;></b></a>
                    </div>
                </div>
                <div class="col-md-12 three_masc_left_card_main_col">
                    <div class="three_masc_left_card">
                        <div class="image_wrap" width="50" height="50">
                            <img src="{{ asset('img/system/online-meeting.png') }}" width="50" height="50"
                                alt="Client Profile Picture">
                        </div>
                        <div class="text_with_info">
                            <strong>Quiz</strong>
                            <p style="font: normal normal normal 14px/21px Poppins;color: #434343;margin-bottom: 5px;">
                                Energy
                            </p>
                            <p style="font: normal normal normal 14px/21px Poppins;color: #434343; margin-bottom: 0;">
                                Date 28.07.2024; UK Time 11 AM
                            </p>
                        </div>

                        <a href="#" style="vertical-align: middle;"><b>View
                                Details &nbsp; &nbsp;></b></a>
                    </div>
                </div>
                <div class="col-md-12 three_masc_left_card_main_col">
                    <div class="three_masc_left_card">
                        <div class="image_wrap" width="50" height="50">
                            <img src="{{ asset('img/system/need-assessment.png') }}" width="50" height="50"
                                alt="Client Profile Picture">
                        </div>
                        <div class="text_with_info">
                            <strong>Assessment Review</strong>
                            <p style="font: normal normal normal 14px/21px Poppins;color: #434343;margin-bottom: 5px;">
                                Photosynthesis
                            </p>
                            <p style="font: normal normal normal 14px/21px Poppins;color: #434343; margin-bottom: 0;">
                                Date 20.08.2024; UK Time 1 PM
                            </p>
                        </div>

                        <a href="#" style="vertical-align: middle;"><b>View
                                Details &nbsp; &nbsp;></b></a>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="col-md-6 tab_width_100" style="padding: 10px;">
            <div class="row">
                <div class="col-md-12" style="">
                    <h4 style="">
                        Schedule</h4>
                </div>
                <div class="col-md-12 text-center">
                    <div class="card">
                        <div class="card-body" style="padding: 0; border-radius: 15px; ">
                            <div id='calendar' class="dashboard-calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('pagescript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendar = $('#calendar').fullCalendar({
        events: "{{route('tut_calendar',Session()->get('tenant_info')['subdomain'])}}",
        displayEventTime: false,
        editable: false,
        initialView: 'dayGridMonth',
        height:'100%',

        eventRender: function(event, element, view) {
            console.log('load event', event);
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },

        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day',
            listMonth: 'List Month',
            listYear: 'List Year',
            listWeek: 'List Week',
            listDay: 'List Day'
        },
        eventClick: function(event) {
            // alert(event.id);
            var callUrl="<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/view-task'; ?>";
            rightModal(callUrl+'/'+event.id, 'View Task');

        }
    });

});
$('.view_utask').on('click',function(){
    var id=$(this).attr("data-id");
    var callUrl="<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/view-task'; ?>";
            rightModal(callUrl+'/'+id, 'View Task');
});
</script>
<script src="{{asset('js/script.js')}}"></script>
@endsection
