@extends('layouts.default')
@section('title', 'Dashboard')
@section('pagecss')
<style type="text/css">


/*    .overview_card_single_col_two {
        top: 19%;
    }
    .three_masc_left_card {
        justify-content: left;
    }
    .three_masc_left_card .text_with_info {
        width: 81%;
    }*/
</style>
@endsection
@section('content')

<div class="px-2">
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
                        <div class="progress_amount">
                            <p>
                                0%
                            </p>
                        </div>
                    </div>
                    <div class="overview_card_single_col_two  d-flex align-items-center"
                         style="top: 60px;">
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
                <div class="col-md-12" style="padding-top:10px">
                    <h4 style="">
                        Upcomming Tasks</h4>
                </div>
                <div class="col-md-12 three_masc_left_card_main_col">
                    <div class="three_masc_left_card">
                        <div class="image_wrap" width="50" height="50">
                            <img src="{{ asset('img/system/live-class.png') }}" width="50" height="50"
                                alt="Client Profile Picture">
                        </div>
                        <div class="text_with_info mx-2">
                            <strong>Check Live Class</strong>
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
                            <strong>Check Quiz Result</strong>
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
                            <strong>Check Assessment Result</strong>
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
                </div>
            </div>
        </div>


    </div>
</div>

@endsection

@section('pagescript')


@endsection
