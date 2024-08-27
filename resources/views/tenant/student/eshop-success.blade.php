@extends('layouts.default')
@section('title', 'NTP Support')
@section('pagecss')
<style>
    .alert{
        padding : 3px 15px;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
            <h4>E-Shop</h4>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
</div>
<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
    <div class="card" style="border-radius: 0px;">
<center>
    <img src="<?php echo e(asset('img/system/icons/eshopsuccess.png')); ?>" style="align-items: center" width="300px" height="400px;">
    <h4>You have Subscribed to Chemistry Course Successfully! </h4>
</center>
        <br/>
        <br/>
        <br/><br/>

    </div>
</div>
</div>

    @endsection
    @section('pagescript')
    <script>

        initDataTable('datatable');


    </script>




    @endsection
