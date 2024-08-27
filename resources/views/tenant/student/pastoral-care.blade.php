@extends('layouts.default')
@section('title', 'Pastoral Care')
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
        border-bottom: 2px solid white;
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
    .dashed-2 {
        border: none;
        height: 1px;
        background: #000;
        background: repeating-linear-gradient(90deg,#000,#000 6px,transparent 6px,transparent 12px);
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
                   Pastoral Care
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="card"><div class="col-md-12">
            <div class="table-responsive">
            <table class="">
            <thead>
            <tr>
                <th>Incident</th>
                <th>Date</th>
                <th>Time</th>
                <th>Reason</th>
                <th>Allocated person</th>
                <th>Actions taken</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>View</td>
            </tr>
            <tr>
                <td colspan="6"><hr class="dashed-2"></td>
            </tr>
            <tr>
                <td colspan="6">Teacher comment</td>
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
