@extends('layouts.default')
@section('title', 'Report Card')
@section('pagecss')
<style>
    .info-container {
        display: flex;
        flex-wrap: wrap;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }
    .attendance-info, .behavior-info {
        padding: 20px;
        box-sizing: border-box;
    }
    .attendance-info {
        flex: 2;
        border-right: 1px solid #ddd;
    }
    .behavior-info {
        flex: 1;
    }
    .info-container h3 {
        margin-top: 0;
        font-size: 16px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 8px;
    }
    .info-container p {
        margin: 5px 0;
    }
    .chart-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 150px;
    }
    .chart-container img {
        max-width: 100%;
        height: auto;
    }
    @media (max-width: 768px) {
        .attendance-info, .behavior-info {
            flex: 100%;
            border-right: none;
            border-bottom: 1px solid #ddd;
        }
        .behavior-info {
            border-bottom: none;
        }
    }
    .table>:not(caption)>*>* {
        padding: 0px;
    }
    .alert{
        padding : 3px 15px;
    }
    header, .summary, .behavior, .behavior-subject {
        margin-bottom: 20px;
    }

    h5 {
        font-size: 1em;
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #ddd;
        text-align: left;

    }
    thead th {
        text-align: left;
    }
    td {
        background-color: white;
    }

    @media screen and (max-width: 768px) {
        table, th, td {
            display: block;
            width: 100%;
        }

        th, td {
            text-align: right;
        }
        tr {
            background-color: white;
        }
        th::before, td::before {
            float: left;
            text-align: left;
            content: attr(data-label);
        }

        th:last-child, td:last-child {
            border-bottom: 1px solid #ddd;
        }
    }

</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
                  <h4>Report Card</h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-xl-12"><div class="row">
            <div class="col-xl-4"></div> <div class="col-xl-4">
                <div class="card-body">
                    <div class="form-group">
                        <label for="reportCardType" style="font: normal normal 600 18px/24px Open Sans;">Select Month</label>
                        <select class="form-control" id="reportCardType">
                            <option value="" disabled="" selected="">Select Month</option>
                            <option value="january">January</option>
                            <option value="february">February</option>
                            <option value="march">March</option>
                            <!-- Add more months as needed -->
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="report-table">
        <thead>
        <tr>
            <th>Subject</th>
            <th>Target</th>
            <th>Current</th>
            <th>Projected</th>
            <th>Effort and Engagement in Lesson</th>
            <th>Homework and Independent Study</th>
            <th>Academic Interventions to Improve Progress</th>
            <th>Interventions for Behavior/Attitudes to Learning</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-label="Subject" rowspan="2">Physics</td>
            <td data-label="Target">6</td>
            <td data-label="Current">3</td>
            <td data-label="Projected">5</td>
            <td data-label="Effort and Engagement in Lesson">Below expectations</td>
            <td data-label="Homework and Independent Study">Below expectations</td>
            <td data-label="Academic Interventions to Improve Progress">Make sure all coursework is produced to the highest standard possible, in line with your targets.</td>
            <td data-label="Interventions for Behavior/Attitudes to Learning">Be respectful: concentrate fully on tasks at hand and produce work to the best of your ability.</td>
        </tr>
        <tr>
            <td colspan="6"><span>Group Teacher(s)</td>
            <td><strong>Teacher Name</strong></td>
        </tr>
        <!-- Add more rows as needed -->
        </tbody>
    </table>
<br/>
    <br/>
    <br/>
    <table class="report-table" style="margin-top: 30px;">
            <tr>
                <th colspan="2">Session Attendance Information</th>
                <th>Behavior Information</th>
            </tr>
        <tr>
            <td>
            <p><strong>Percentage attendance:</strong> 89.39%</p>
            <p><strong>Attendance:</strong> 118</p>
            <p><strong>Authorised absences:</strong> 12</p>
            <p><strong>Unauthorised absences:</strong> 2</p>
            <p><strong>Possible sessions:</strong> 132</p>

            </td>
            <td>
                <div class="chart-container">
                    <img src="path_to_pie_chart_image.png" alt="Attendance Pie Chart">
                </div>
            </td>
            <td>
                <p><strong>Positive points:</strong> 67</p>
                <p><strong>Negative points:</strong> 0</p>
            </td>
        </tr>
    </table>

    @endsection
    @section('pagescript')
    <script>

        initDataTable('datatable');


    </script>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>


    @endsection
