student-reportcard.blade.php@extends('layouts.default')
@section('title', 'Report card')
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
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="reportCardType">Academic Year</label>
                    <select class="form-control" id="reportCardType">
                        <option value="" disabled selected>Select Year</option>

                    </select>
                </div>
            </div>
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
