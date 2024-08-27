@extends('layouts.default')
@section('title', 'Report Card')
@section('pagecss')
<style>
    td  {
        font : normal normal normal 13px/20px Poppins;;
    }
     th {
        font: normal normal 600 14px/20px Poppins;
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
        font-weight: normal;
    }
    .form-control {
        font-size: 1rem;
    }
    .table thead th {
        border-bottom: 2px solid #dee2e6;
    }
    .table tbody tr {
        background-color: white;
    }
    .legend {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .legend span {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 5px;
    }
    .legend .working-well-below { background-color: #FFF5F3 ; }
    .legend .working-towards { background-color: #FCFFCD ; }
    .legend .working-at { background-color: #C7FFFA ; }
    .legend .working-above { background-color: #B7DBFF; }
    .export-btn {
        text-align: center;
        margin-top: 20px;
    }
    .print-btn {
        background: transparent url('printer.png') 0% 0% no-repeat padding-box;
        opacity: 1;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <div class="page-title">
                    <h4>Report Card</h4>

                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<h6 style="float:right"><img src="{{ asset('img/system/printer.png') }}" style="width:30px;height: 30px"/>Print</h6>
<div class="row">
    <div class="col-xl-12"><div class="row">
            <div class="col-xl-4"></div> <div class="col-xl-4">
        <div class="card-body">
            <div class="form-group">
                <label for="reportCardType" style="font: normal normal 600 18px/24px Open Sans;">Select Month</label>
                <select class="form-control" id="reportCardType">
                    <option value="" disabled selected>Select Month</option>
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
    <div class="row">
    <div class="col-xl-12">
            <table class="table table-bordered">
                <thead>
                <tr style="/* UI Properties */

                        background: #F2F2F2 0% 0% no-repeat padding-box;
                        border: 1px solid #DBDBDB;
                        opacity: 1;">
                    <th>Subject</th>
                    <th>Target</th>
                    <th>Progress towards target grade</th>
                    <th>Breakdown</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="working-well-below"></td>
                    <td></td>
                </tr>

                </tbody>
            </table>
            <div class="legend">
                <div><span class="working-well-below"></span> Working Well Below Target Grade</div>
                <div><span class="working-towards"></span> Working Towards Target Grade</div>
                <div><span class="working-at"></span> Working at Target Grade</div>
                <div><span class="working-above"></span> Working Above Target Grade</div>
            </div>
            <table class="table table-bordered mt-4">
                <thead>
                <tr>
                    <th>Subject</th>
                    <th>16 Dec 2022</th>
                    <th>31 Mar 2023</th>
                    <th>Projected</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>---</td>
                    <td>---</td>
                    <td>---</td>
                    <td>---</td>
                </tr>

                </tbody>
            </table>
            <div class="export-btn">
                <button style="background: #FFFFFF 0% 0% no-repeat padding-box;
border: 1px solid #DBDBDB;
opacity: 1;">Export Report in PDF</button>
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
