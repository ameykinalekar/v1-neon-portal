@extends('layouts.default')
@section('title', 'Signals')
@section('pagecss')
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
    }
    .title {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }
    .grid-container {
        display: grid;
        grid-template-columns: repeat(3, minmax(150px, 1fr));
        gap: 20px;
    }
    .card {
        width: 306px;
        height: 260px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        /* UI Properties */

        background: #FFFFFF 0% 0% no-repeat padding-box;
        border: 1px solid #DBDBDB;
        border-radius: 10px;
        opacity: 1;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    @media (max-width: 768px) {
        .title {
            font-size: 20px;
            margin-bottom: 15px;
        }
        .card1 {
            height: 120px;
        }
    }
    .card1 {
        width: 306px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        /* UI Properties */

        background: #FFFFFF 0% 0% no-repeat padding-box;
        border: 1px solid #DBDBDB;
        border-radius: 10px;
        opacity: 1;
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
        <div class="alert alert-light">
            <h4> Signals </h4>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
</div>
<!--<div class="container">
    <div class="grid-container">
        <div class="card1"></div>
        <div class="card1"></div>
        <div class="card1"></div>
        <div class="card1"></div>
        <div class="card1"></div>
        <div class="card1"></div>
    </div>
</div>-->
<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="row">
            <div class="col-md-4">

                <div class="card" style="width: 18rem;">
                    <div class="card-body">

                    </div>
                </div>
            </div>

            <div class="col-md-4">

                <div class="card" style="width: 18rem;">
                    <div class="card-body">

                    </div>
                </div>
            </div>
            <div class="col-md-4">

                <div class="card" style="width: 18rem;">
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 mx-auto">
        <div class="row">
            <div class="col-md-4">

                <div class="card" style="width: 18rem;">
                    <div class="card-body">

                    </div>
                </div>
            </div>

            <div class="col-md-4">

                <div class="card" style="width: 18rem;">
                    <div class="card-body">

                    </div>
                </div>
            </div>
            <div class="col-md-4">

                <div class="card" style="width: 18rem;">
                    <div class="card-body">

                    </div>
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
