@extends('layouts.default')
@section('title', 'NTP Support')
@section('pagecss')
<style>

    .card {
        margin-top: 20px;
        border: 1px solid #dee2e6;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        background-color: #fff;
        font-size: 1.25rem;
        font-weight: bold;
    }
    .form-group label {
        font-weight: bold;
    }
    .form-control {
        font-size: 1rem;
    }
    .table thead th {
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        text-align: center;
        vertical-align: middle;
    }
    .table th {
        font: normal normal 600 15px/23px Poppins;
        text-align: center;
        vertical-align: middle;
    }
    .table tbody tr {
        background-color: white;
    }
    .color-monday { background-color: #ff80ab !important; color: #fff;height: 50px;border-radius: 5px 0px 0px 0px; }
    .color-tuesday { background-color:  #FFA000 !important;; color: #fff; height: 50px;border-radius: 5px 0px 0px 0px;}
    .color-wednesday { background-color:  #4C94DB !important;; color: #fff; height: 50px;border-radius: 5px 0px 0px 0px;}
    .color-thursday { background-color: #8BE869 !important;; color: #fff; height: 50px;border-radius: 5px 0px 0px 0px;}
    .color-friday { background-color: #5ACEFF !important;; color: #fff; height: 50px;border-radius: 5px 0px 0px 0px;}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block" style="text-align: left;
font: normal normal bold 18px/27px Poppins;
letter-spacing: 0.09px;
color: #434343;">
                    NTP Support
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
<div class="col-md-8 mx-auto">
    <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="selectWeek">Select Week</label>
            <select class="form-control" id="selectWeek">
                <option value="" disabled selected>Select Month</option>
                <option value="week1">January</option>
                <option value="week2">February</option>
                <option value="week3">March</option>
                <option value="week3">April</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="selectWeek">Select Week</label>
            <select class="form-control" id="selectWeek">
                <option value="" disabled selected>Select Week</option>
                <option value="week1">Week 1</option>
                <option value="week2">Week 2</option>
                <option value="week3">Week 3</option>
                <option value="week3">Week 4</option>
            </select>
        </div>
    </div></div>
    <br><div class="row">  <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="background-color:#5BC2B9;color:white" >Time</th>
                    <th class="color-monday">Monday</th>
                    <th class="color-tuesday">Tuesday</th>
                    <th class="color-wednesday">Wednesday</th>
                    <th class="color-thursday">Thursday</th>
                    <th class="color-friday">Friday</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>10:00 - 11:00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>11:00 - 12:00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>12:00 - 13:00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                </tbody>
            </table>
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
