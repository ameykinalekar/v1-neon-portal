@extends('layouts.default')
@section('title', 'Behavioral')
@section('pagecss')
<style>
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
                  <h4>Detailed Progress</h4>
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
<div class="container">

    <div class="header">
        <table class="table table-bordered">
            <tr>
                <th></th>
                <th>End of week scores</th>
                <th>This Week</th>
            </tr>
            <tr><td>
                    <table>
                        <tr>
                            <th>Year group</th>
                            <th>Student</th>
                        </tr>
                        <tr>
                            <td>AS Level</td>

                            <td>Nikita</td>
                        </tr>

                    </table>
                </td>
                <td>
                    <table>
                        <tr>

                            <th>17 Decem</th>
                            <th>24 Decem</th>
                            <th>31 Decem</th>
                        </tr>
                        <tr>
                            <td>91</td>
                            <td>91</td>
                            <td>91</td>
                        </tr>
                    </table>
                <td>
                    <table>
                        <tr>
                            <th>Target</th>
                            <th>Current</th>
                        </tr>
                        <tr>
                            <td>92</td>
                            <td>91</td>
                        </tr>
                    </table>
                </td>
        </table>
        </td>
        </tr>
        </table>

    </div>

    <div class="summary">
        <h5 style="color:#338A82;font-weight:normal">Summary of all events in the academic year</h5>
        <table>
            <tr>
                <th>Event</th>
                <th>Count</th>
                <th>Managed Detentions</th>
            </tr>
            <tr>
                <td>Responsible Outstanding commitment and efforts in interventions session</td>
                <td>2</td>
                <td>n/a</td>
            </tr>
            <tr>
                <td>R1 - Meeting good expectations (Attendance, Effort, Engagement)</td>
                <td>16</td>
                <td>n/a</td>
            </tr>
            <tr>
                <td>Meetings with Parent</td>
                <td>10</td>
                <td>n/a</td>
            </tr>
            <tr>
                <td>Discussion with student</td>
                <td>3</td>
                <td>n/a</td>
            </tr>
            <tr>
                <td>Phone call home</td>
                <td>4</td>
                <td>n/a</td>
            </tr>
            <tr>
                <td>Failure to complete homework</td>
                <td>10</td>
                <td>n/a</td>
            </tr>
        </table>
    </div>

    <div class="behavior">
        <h5 style="color:#338A82;font-weight:normal">Behaviour by Group</h5>
        <table>
            <tr>
                <th>Group</th>
                <th>Student Score</th>
                <th>Managed Detentions</th>
            </tr>
            <tr>
                <td>Biology</td>
                <td>2</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Physics</td>
                <td>16</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Chemistry</td>
                <td>10</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Mathematics</td>
                <td>3</td>
                <td>0</td>
            </tr>
            <tr>
                <td>English</td>
                <td>4</td>
                <td>0</td>
            </tr>
        </table>
    </div>

    <div class="behavior-subject">
        <h5 style="color:#338A82;font-weight:normal">Behaviour by Subject</h5>
        <p>The following will include events listed in the 'Behaviour by group' table with the addition of events not specifically attached to a group</p>
        <table>
            <tr>
                <th>Group</th>
                <th>Student Score</th>
                <th>Managed Detentions</th>
            </tr>
            <tr>
                <td>Biology</td>
                <td>120</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Physics</td>
                <td>16</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Chemistry</td>
                <td>10</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Mathematics</td>
                <td>3</td>
                <td>0</td>
            </tr>
            <tr>
                <td>English</td>
                <td>4</td>
                <td>0</td>
            </tr>
        </table>
    </div>
</div>

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
