@extends('layouts.default')
@section('title', 'Order History')
@section('pagecss')
<style>
    .alert{
        padding : 3px 15px;
    }
    .linkStyle{
        text-decoration: underline;
        font: normal normal 600 14px/21px Poppins;
        letter-spacing: 0px;
        color: #5BC2B9;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
            <h4>Order History</h4>

        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
</div>
<div class="float-end ">
    Filter <a style="color:#6c757d" href="javascript:void(0);"
              onclick="rightModal('')"><i
            class="fa fa-filter" aria-hidden="true"></i></a>
</div>
<br/>
<br/>
<div class="table-responsive">
    <table id="datatable" class="table table-striped  nowrap" width="100%">
        <thead>
        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
            <th>Order</th>
            <th>Date</th>
            <th>Product</th>
            <th>Status</th>
            <th>Total</th>
            <th>Action</th>

        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>1</td>
            <td><a href="#" class="linkStyle">Product</a></td>
            <td></td>
            <td></td>
            <td><a href="#" class="linkStyle">View More</a></td>
            <!--<tr>
                <td colspan="6" class="text-center">No data found.</td>
            </tr>-->

        </tbody>
    </table>
</div>
@endsection
@section('pagescript')
<script>
    initDataTable('datatable');
</script>
@endsection
