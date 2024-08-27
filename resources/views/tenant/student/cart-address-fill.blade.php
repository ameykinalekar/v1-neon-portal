@extends('layouts.default')
@section('title', 'Cart')
@section('pagecss')
<style>
    .alert{
        padding : 3px 15px;
    }

    h2 {
        text-align: left;
        margin-bottom: 20px;
        color: #333;
    }

    .section-title {
        margin-bottom: 10px;
        font-weight: bold;
        color: #666;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    .form-group input[type="text"] {
        display: block;
        width: calc(100% - 22px);
    }

    .form-group input[type="text"]:focus {
        border-color: #66afe9;
        outline: none;
        box-shadow: 0 0 5px rgba(102, 175, 233, 0.6);
    }

    .form-group input:nth-child(2) {
        margin-left: 10px;
    }

    @media (min-width: 600px) {
        .form-group input {
            width: calc(100% - 20px);
        }
        .form-group input:nth-child(2) {
            width: calc(49% - 20px);
            margin-left: 2%;
        }
        .form-group input:first-child {
            width: calc(49% - 20px);
        }
    }

    .submit-btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 5px;
        background-color: #20c997;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }

    .submit-btn:hover {
        background-color: #17a78e;
    }

    .card{
        padding:20px;
        border-radius: 0px !important;
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
<div class="card">
        <form>
            <h4>Add New Address</h4>

            <div class="section-title">Contact Details</div>
            <div class="form-group">
                <input type="text" placeholder="Name" required>
            </div>
            <div class="form-group">
                <input type="text" placeholder="Mobile Number" required>
            </div>

            <div class="section-title">Address</div>
            <div class="form-group">
                <input type="text" placeholder="Pincode" required>
            </div>
            <div class="form-group">
                <input type="text" placeholder="Address" required>
            </div>
            <div class="form-group">
                <input type="text" placeholder="Locality" required>
            </div>
            <div class="form-group">
                <span><input type="text" placeholder="City" required></span>

                <span><input type="text" placeholder="State" required></span>

            </div>


            <button type="submit" class="btn w-100" style="background: #5BC2B9;font-weight: normal;color:#fff;border-radius: 5px;">Add Address</button>
        </form>

</div>
@endsection
@section('pagescript')
<script>
    initDataTable('datatable');
</script>
@endsection
