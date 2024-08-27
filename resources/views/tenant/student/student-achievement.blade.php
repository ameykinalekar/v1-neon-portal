@extends('layouts.default')
@section('title', 'Achievement')
@section('pagecss')
<style>
    .alert{
        padding : 3px 15px;
    }
    .orange {
        background: transparent url('../img/system/icons/status-orange.svg') 0% 0% no-repeat padding-box;    }
    .green {
        background: transparent url('img/system/icons/status-green.svg') 0% 0% no-repeat padding-box;    }
    .red {
        background: transparent url('img/system/icons/status-red.svg') 0% 0% no-repeat padding-box;    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
                  <h4>Achievement Tracker</h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="table-responsive">
    <table id="datatable" class="table table-striped  nowrap" width="100%">
        <thead>
        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
            <th>Test Name</th>
            <th>Subject</th>
            <th>Test Date</th>
            <th>Total Marks</th>
            <th>Marks Achieved</th>
            <th>Percent</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
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
