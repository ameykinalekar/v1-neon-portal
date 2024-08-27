@extends('layouts.default')
@section('title', 'Dashboard')
@section('pagecss')
<style>
/*.overview_card_single_col_two {
    top: 19%;
}

.three_masc_left_card {
    justify-content: left;
}

.three_masc_left_card .text_with_info {
    width: 81%;
}
.overview_card_single {
    width: 110%;
}*/
@media only screen and (max-width: 600px) {
    #myChart {
        width: 215px !important;

    }
}
</style>
@endsection
@section('content')
<?php //print_r($response['result']['details']);?>
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
                            Total Students
                        </div>
                        <div class="progress_amount">
                            <p>
                                {{$response['result']['details']['student_count']??'0'}}
                            </p>
                        </div>
                    </div>
                    <div class="overview_card_single_col_two  d-flex align-items-center">
                        <img src="{{ asset('img/system/Group7.svg') }}" alt="">
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
                            Total Teachers
                        </div>
                        <div class="progress_amount">
                            <p>
                                {{$response['result']['details']['teacher_count']??'0'}}
                            </p>
                        </div>

                    </div>
                    <div class="overview_card_single_col_two  d-flex align-items-center">
                        <img src="{{ asset('img/system/Group345.svg') }}" alt="" height="60px">
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
                            Invoice Status &nbsp;
                        </div>
                        <div class="progress_amount">
                            <p>
                                &pound; 0
                            </p>
                        </div>

                    </div>
                    <div class="overview_card_single_col_two  d-flex align-items-center">
                        <img src="{{ asset('img/system/Group381.svg') }}" alt="" height="60">
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
                    <div class="overview_card_single_col_two  d-flex align-items-center">
                        <!-- SVG WRAP -->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="60"
                            height="60" viewBox="0 0 68 68">
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
    <div class="row" style="margin-top: 10px;">
        <div class="col-md-6 tab_width_100" style="">
            <div class="row">
                <div class="col-md-12" style="">
                    <h4 style="">
                        Quick Action</h4>
                </div>
                <div class="col-md-12 three_masc_left_card_main_col">
                    <div class="three_masc_left_card">
                        <div class="image_wrap" width="50" height="50">
                            <img src="{{ asset('img/system/live-class.png') }}" width="50" height="50"
                                alt="Client Profile Picture">
                        </div>
                        <div class="text_with_info mx-2">
                            <strong>All Users</strong>

                        </div>

                        <a href="{{route('ta_studentlist', Session()->get('tenant_info')['subdomain'])}}"
                            style="vertical-align: middle;"><b>View
                                Details &nbsp; &nbsp;></b></a>
                    </div>
                </div>
                <div class="col-md-12 three_masc_left_card_main_col">
                    <div class="three_masc_left_card">
                        <div class="image_wrap" width="50" height="50">
                            <img src="{{ asset('img/system/online-meeting.png') }}" width="50" height="50"
                                alt="Client Profile Picture">
                        </div>
                        <div class="text_with_info mx-2">
                            <strong>Records</strong>

                        </div>

                        <a href="{{route('ta_library', Session()->get('tenant_info')['subdomain'])}}"
                            style="vertical-align: middle;"><b>View
                                Details &nbsp; &nbsp;></b></a>
                    </div>
                </div>
                <div class="col-md-12 three_masc_left_card_main_col">
                    <div class="three_masc_left_card">
                        <div class="image_wrap" width="50" height="50">
                            <img src="{{ asset('img/system/need-assessment.png') }}" width="50" height="50"
                                alt="Client Profile Picture">
                        </div>
                        <div class="text_with_info mx-2">
                            <strong>Activities</strong>

                        </div>

                        <a href="{{route('ta_inbox', Session()->get('tenant_info')['subdomain'])}}"
                            style="vertical-align: middle;"><b>View
                                Details &nbsp; &nbsp;></b></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 tab_width_100">
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom:20px">
                            <h3 style="">
                                Ofsted Finance</h3>
                            <div class="col-md-12 three_masc_left_card_main_col">
                                <div class="three_masc_left_card_chart">
                                    <div class="col-md-12" id="chart_section">
                                        <canvas id="myChart" style="width:100%"></canvas>
                                    </div>
                                    <div class="col-md-12  mt-0">
                                        <form action="">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="ofsted_show_group">Show grouping</label>
                                                        <select class="form-control" name="ofsted_show_group"
                                                            id="ofsted_show_group">
                                                            <option value="total_expenditure">Total Expenditure</option>
                                                            <option value="staff_total">Staff Expenditure</option>
                                                            <option value="premises_total">Premises Expenditure</option>
                                                            <option value="occupation_total">Occupation Expenditure
                                                            </option>
                                                            <option value="supplies_and_services_total">Supplies &
                                                                Services Expenditure</option>
                                                            <option value="special_facilities_total">Special Facilites
                                                                Expenditure</option>
                                                            <option value="cost_of_finance_total">Cost of finance
                                                            </option>
                                                            <option value="community_expenditure_total">Community
                                                            </option>
                                                            <option value="total_income">Total Income</option>
                                                            <option value="grant_funding_total">Grant Funding</option>
                                                            <option value="self_generated_funding_total">Self-generated
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="ofsted_show_value">Show values</label>
                                                        <select class="form-control" name="ofsted_show_value"
                                                            id="ofsted_show_value">
                                                            <option value="absolute_total">Absolute Total</option>
                                                            <option value="number_of_pupils">Per Pupil</option>
                                                            <option value="number_of_teachers">Per Teacher</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 tab_width_100" style="">
            <div class="row">
                <div class="col-md-12" style="">
                    <h4 style="">Work Force</h4>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12" id="chart_section_wf">
                            <canvas id="myChartWf" class="chart_instudio" style="width:100%;"></canvas>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('pagescript')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    loadOfstedChart();
    loadWorkForceChart();
});


function loadOfstedChart() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] = "{{route('get_ofsted_chart', Session()->get('tenant_info')['subdomain'])}}";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        "_token": "{{ csrf_token() }}",
        "ofsted_show_group": $('#ofsted_show_group').val(),
        "ofsted_show_value": $('#ofsted_show_value').val(),
    });
    params['beforeSendCallbackFunction'] = function(response) {
        $('#chart_section').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px;  opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        $('#chart_section').html(`<canvas id="myChart" style="width:100%"></canvas>`);
        console.log(response.result.ofsted_xvalues);
        var xval = response.result.ofsted_xvalues;
        var yval = response.result.ofsted_yvalues;

        const xValues = xval.split(',');
        const yValues = yval.split(',');

        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,1.0)",
                    borderColor: "rgba(0,0,255,0.1)",
                    data: yValues
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                legend: {
                    display: false,

                },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: response.result.ylabel
                        },
                        ticks: {
                            // Include a dollar sign in the ticks
                            callback: function(value, index, ticks) {
                                return response.result.yprefix + value + response.result
                                .ysuffix;
                            }
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: response.result.xlabel
                        }
                    }],
                }
            }
        });
    }
    params['errorCallBackFunction'] = function(httpObj) {
        // $('#chart_section').html('Unable to load chart.');
        alert('Unable to load ofsted chart.');
    }

    doAjax(params);

}

$('#ofsted_show_group').on('change', function() {
    loadOfstedChart();
});
$('#ofsted_show_value').on('change', function() {
    loadOfstedChart();
});

function loadWorkForceChart() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-workforce-count'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    // params['data'] = JSON.stringify({
    //     "_token": "{{ csrf_token() }}",
    // });
    params['beforeSendCallbackFunction'] = function(response) {
        $('#chart_section_wf').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px;  opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        $('#chart_section_wf').html(
            `<canvas id="myChartWf" class="chart_instudio" style="width:100%"></canvas>`);
        // console.log(response.result.ofsted_xvalues);
        var total_teachers = response.result.listing.total_teachers;
        var total_non_qualifed_teachers = response.result.listing.total_non_qualifed_teachers;
        var total_qualified_teachers = response.result.listing.total_qualifed_teachers;
        var total_teacher_assistants = response.result.listing.total_teacher_assistants;
        var total_employee = response.result.listing.total_employee;



        var ctx = document.getElementById('myChartWf').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ["Qualified Teachers","Other Teachers", "Teacher Assistants", "Non-Teaching Staff"],
                datasets: [{
                    label: "Work Force",
                    data: [total_qualified_teachers,total_non_qualifed_teachers, total_teacher_assistants, total_employee],
                    backgroundColor: ['#64dad0', '#53b4ac', '#74e5ff', '#fbbf10'],
                    hoverOffset: 5
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                "legend": {
                    "display": true,
                    "position": "right",
                    "align": "start"
                }
            },
        });

    }
    params['errorCallBackFunction'] = function(httpObj) {
        // $('#chart_section').html('Unable to load chart.');
        alert('Unable to load work force chart.');
    }

    doAjax(params);

}
</script>
@endsection