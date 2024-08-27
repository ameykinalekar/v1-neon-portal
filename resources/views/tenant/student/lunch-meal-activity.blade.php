@extends('layouts.default')
@section('title', 'Lunch Meal Activity')
@section('pagecss')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    thead {
        background-color: #2ec4b6;
        color: white;
    }
    th, td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
        vertical-align: top;
        position: relative;
    }
    .badge {
        background-color: #f66;
        color: white;
        padding: 5px 10px;
        border-radius: 50%;
        font-size: 12px;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .checkbox {
        position: absolute;
        top: 10px;
        left: 10px;
    }
    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tbody tr:nth-child(odd) {
        background-color: #e2e2e2;
    }
    @media screen and (max-width: 600px) {

    }
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
    .alert{
        padding : 3px 15px;
    }
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
                    Lunchtime meal activity
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
            <label for="selectWeek">Select Year</label>
            <select class="form-control" id="selectWeek">
                <option value="" disabled selected>Select Year</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
            </select>
        </div>
    </div></div>
</div>
    <br>
    <div class="row" style="padding-left: 20px;">
        <div class="card"><div class="col-md-12">
            <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td data-label="Monday" style="padding-top: 50px;width: 150px;text-align: left;">
                    <span class="checkbox">✔</span>
                    <span class="badge">1</span>
                    <span class="align:left">Chicken curry & Rice<br>Apple<br>Dryfruits</span>
                </td>
                <td data-label="Tuesday" style="padding-top: 50px;width: 150px;text-align: left;">
                    <span class="checkbox">✔</span>
                    <span class="badge">2</span>
                    <span class="align:left">Homemade Sausage<br>Roll & Smash<br>Apple</span>
                </td>
                <td data-label="Wednesday" style="padding-top: 50px;width: 150px;text-align: left;">
                    <span class="badge">3</span>
                    Sweet Chili<br>Salmon Wrap<br>Apple<br>Dryfruits
                </td>
                <td data-label="Thursday" style="padding-top: 50px;width: 150px;text-align: left;">
                    <span class="badge">4</span>
                    Ham<br>Cheese Pizza<br>Banana<br>Dryfruits
                </td>
                <td data-label="Friday" style="padding-top: 50px;width: 150px;text-align: left;">
                    <span class="checkbox">✔</span>
                    <span class="badge">5</span>
                    Breaded Fish<br>Cheese Pizza<br>Banana<br>Dryfruits
                </td>
            </tr>
            </tbody>
        </table>
            </div>
            </div>
        </div>
    </div>
</div>


    @endsection
    @section('pagescript')
    <script>

        initDataTable('datatable');


    </script>


    @endsection
