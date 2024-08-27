@extends('layouts.default')
@section('title', 'Analytic Studio')
@section('pagecss')
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
}

.title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.grid-container {
    display: grid;
    grid-template-columns: repeat(3, minmax(150px, 1fr));
    gap: 20px;
}

.card {
    /* width: 306px;
        height: 260px; */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    /* UI Properties */

    background: #FFFFFF 0% 0% no-repeat padding-box;
    border: 1px solid #DBDBDB;
    border-radius: 10px;
    opacity: 1;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .title {
        font-size: 20px;
        margin-bottom: 15px;
    }

    .card1 {
        height: 120px;
    }
}

.card1 {
    width: 306px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    /* UI Properties */

    background: #FFFFFF 0% 0% no-repeat padding-box;
    border: 1px solid #DBDBDB;
    border-radius: 10px;
    opacity: 1;
}

.alert {
    padding: 3px 15px;
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
            <h4> Analytic Studio</h4>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h5 style="">Pupil Gender Chart</h5>
                    <div class="card-body">
                        <div class="col-md-12" id="chart_section_pupil">
                            <canvas id="myChartPupil" class="chart_instudio" style="width:100%;"></canvas>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-4">

                <div class="card">
                    <h5 style="">Teachers</h5>
                    <div class="card-body">
                        <div class="col-md-12" id="chart_section_noteacher">
                            <canvas id="myChartNoTeacher" class="chart_instudio" style="width:100%;"></canvas>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h5 style="">Work Force</h5>
                    <div class="card-body">
                        <div class="col-md-12" id="chart_section_wf">
                            <canvas id="myChartWf" class="chart_instudio" style="width:100%;"></canvas>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-4">

                <div class="card">
                    <h5 style="">Pupil Attendance</h5>
                    <div class="card-body">
                        <div class="col-md-12" id="chart_section_attn">
                            <canvas id="myChartAttendance" class="chart_instudio" style="width:100%;"></canvas>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h5 style="">SEN Support Pupil Gender Chart</h5>
                    <div class="card-body">
                        <div class="col-md-12" id="chart_section_sen_pupil">
                            <canvas id="myChartSenPupil" class="chart_instudio" style="width:100%;"></canvas>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h5 style="">Free Meal Pupil Gender Chart</h5>
                    <div class="card-body">
                        <div class="col-md-12" id="chart_section_freemeal_pupil">
                            <canvas id="myChartFreeMealPupil" class="chart_instudio" style="width:100%;"></canvas>
                        </div>

                    </div>

                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <h5 style="">Ofsted Finance</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8" id="chart_section">
                                <canvas id="myChart" style="width:100%;"></canvas>
                            </div>
                            <div class="col-md-4  mt-0">
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
                                                    <option value="occupation_total">Occupation Expenditure</option>
                                                    <option value="supplies_and_services_total">Supplies & Services
                                                        Expenditure</option>
                                                    <option value="special_facilities_total">Special Facilites
                                                        Expenditure</option>
                                                    <option value="cost_of_finance_total">Cost of finance</option>
                                                    <option value="community_expenditure_total">Community</option>
                                                    <option value="total_income">Total Income</option>
                                                    <option value="grant_funding_total">Grant Funding</option>
                                                    <option value="self_generated_funding_total">Self-generated</option>
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
            <div class="col-md-4">
                <div class="card">
                    <h5 style="">Pupil Transport Chart</h5>
                    <div class="card-body">
                        <div class="col-md-12" id="chart_section_transport">
                            <canvas id="myChartTransport" class="chart_instudio" style="width:100%;"></canvas>
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
<script>
$(document).ready(function() {
    loadOfstedChart();
    loadAttendanceChart();
    loadAcademicPupilChart();
    loadAcademicSenPupilChart();
    loadAcademicFreeMealPupilChart();
    loadNoTeacherChart();
    loadWorkForceChart();
    loadTransportChart();
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
                    fill: true,
                    lineTension: 0,
                    backgroundColor: "rgb(107, 232, 221,0.5)",
                    borderColor: "rgb(107, 232, 221,0.6)",
                    data: yValues
                }]
            },
            options: {
                responsive: true,
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

function loadAttendanceChart() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] = "{{route('get_tut_attendance_chart', Session()->get('tenant_info')['subdomain'])}}";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        "_token": "{{ csrf_token() }}",
        // "ofsted_show_group": $('#ofsted_show_group').val(),
        // "ofsted_show_value": $('#ofsted_show_value').val(),
    });
    params['beforeSendCallbackFunction'] = function(response) {
        $('#chart_section_attn').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px;  opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        $('#chart_section_attn').html(
            `<canvas id="myChartAttendance" class="chart_instudio" style="width:100%"></canvas>`);
        // console.log(response.result.ofsted_xvalues);
        var xval = response.result.date_xvalues;
        var yval_present = response.result.present_yvalues;
        var yval_enroll = response.result.enroll_yvalues;
        var yval_absent = response.result.absent_yvalues;

        const xValues = xval.split(',');
        const yValuesPresent = yval_present.split(',');
        const yValuesEnroll = yval_enroll.split(',');
        const yValuesAabsent = yval_absent.split(',');

        var ctx = document.getElementById('myChartAttendance').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: xValues,

                datasets: [{
                        label: 'Enrolled',
                        data: yValuesEnroll,
                        backgroundColor: '#fbbf10',

                    },
                    {
                        label: 'Present',
                        data: yValuesPresent,
                        backgroundColor: '#54b5ac',

                    },
                    {
                        label: 'Absent',
                        data: yValuesAabsent,
                        backgroundColor: '#DBDBDB',
                    }

                ],

            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                scales: {
                    x: {
                        //stacked: true,
                    },
                    y: {
                        //stacked: true
                    },

                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: "No. of Students"
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: "Attendance Date"
                        }
                    }],
                },
            }
        });
    }
    params['errorCallBackFunction'] = function(httpObj) {
        // $('#chart_section').html('Unable to load chart.');
        alert('Unable to load attendance chart.');
    }

    doAjax(params);

}

function loadAcademicPupilChart() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-pupil-count'; ?>";
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
        $('#chart_section_pupil').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px;  opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        $('#chart_section_pupil').html(
            `<canvas id="myChartPupil" class="chart_instudio" style="width:100%"></canvas>`);
        // console.log(response.result.ofsted_xvalues);
        var total_male = response.result.listing.total_male_pupil;
        var total_female = response.result.listing.total_female_pupil;
        var total_other = response.result.listing.total_other_pupil;



        var ctx = document.getElementById('myChartPupil').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Male", "Female", "Other"],
                datasets: [{
                    label: "Pupil count",
                    data: [total_male, total_female, total_other],
                    backgroundColor: ['#53b4ac', '#64dad0', '#fbbf10'],
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
        alert('Unable to load pupil chart.');
    }

    doAjax(params);

}

function loadAcademicSenPupilChart() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-pupil-count'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        "have_sensupport": "Y",
    });
    params['beforeSendCallbackFunction'] = function(response) {
        $('#chart_section_sen_pupil').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px;  opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        $('#chart_section_sen_pupil').html(
            `<canvas id="myChartSenPupil" class="chart_instudio" style="width:100%"></canvas>`);
        // console.log(response.result.ofsted_xvalues);
        var total_male = response.result.listing.total_male_pupil;
        var total_female = response.result.listing.total_female_pupil;
        var total_other = response.result.listing.total_other_pupil;



        var ctx = document.getElementById('myChartSenPupil').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Male", "Female", "Other"],
                datasets: [{
                    label: "Pupil count",
                    data: [total_male, total_female, total_other],
                    backgroundColor: ['#53b4ac', '#64dad0', '#fbbf10'],
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
        alert('Unable to load sen pupil chart.');
    }

    doAjax(params);

}

function loadAcademicFreeMealPupilChart() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-pupil-count'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        "free_meal": "Y",
    });
    params['beforeSendCallbackFunction'] = function(response) {
        $('#chart_section_freemeal_pupil').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px;  opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        $('#chart_section_freemeal_pupil').html(
            `<canvas id="myChartFreeMealPupil" class="chart_instudio" style="width:100%"></canvas>`);
        // console.log(response.result.ofsted_xvalues);
        var total_male = response.result.listing.total_male_pupil;
        var total_female = response.result.listing.total_female_pupil;
        var total_other = response.result.listing.total_other_pupil;



        var ctx = document.getElementById('myChartFreeMealPupil').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Male", "Female", "Other"],
                datasets: [{
                    label: "Pupil count",
                    data: [total_male, total_female, total_other],
                    backgroundColor: ['#53b4ac', '#64dad0', '#fbbf10'],
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
        alert('Unable to load sen pupil chart.');
    }

    doAjax(params);

}

function loadNoTeacherChart() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/datewise-active-count'; ?>";
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
        $('#chart_section_noteacher').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px;  opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        $('#chart_section_noteacher').html(
            `<canvas id="myChartNoTeacher" class="chart_instudio" style="width:100%"></canvas>`);
        console.log(response.result.listing.entries());

        const xValues = [];
        const yValues = [];
        const entries = response.result.listing;

        for (let i = 0; i < entries.length; i++) {
            xValues.push(entries[i]['createdon']);
            yValues.push(entries[i]['teacher_count']);
        }

        var ctx = document.getElementById('myChartNoTeacher').getContext('2d');
        var myChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    fill: true,
                    lineTension: 0,
                    backgroundColor: "rgb(107, 232, 221,0.5)",
                    borderColor: "rgb(107, 232, 221,0.6)",
                    data: yValues
                }]
            },
            options: {
                responsive: true,
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
                            labelString: "No. of Teachers"
                        },
                        ticks: {

                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: false,
                            labelString: "Date"
                        }
                    }],
                }
            }
        });

    }
    params['errorCallBackFunction'] = function(httpObj) {
        // $('#chart_section').html('Unable to load chart.');
        alert('Unable to load teacher chart.');
    }

    doAjax(params);

}

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

function loadTransportChart() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-student-transport-count'; ?>";
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
        $('#chart_section_transport').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px;  opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        $('#chart_section_transport').html(
            `<canvas id="myChartTransport" class="chart_instudio" style="width:100%"></canvas>`);
        // console.log(response.result.ofsted_xvalues);

        var total_pupil = response.result.listing.total_pupil;
        var percent_take_commute = response.result.listing.percent_take_commute;
        var percent_take_transport = response.result.listing.percent_take_transport;
        var percent_unknown_transport = response.result.listing.percent_unknown;

        var ctx = document.getElementById('myChartTransport').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ["% Take Commute", "% Take Transport", "Unknown"],
                datasets: [{
                    label: "Transport",
                    data: [percent_take_commute, percent_take_transport, percent_unknown_transport],
                    backgroundColor: ['#53b4ac', '#74e5ff', '#fbbf10'],
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
        alert('Unable to load transport chart.');
    }

    doAjax(params);

}
</script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>



@endsection