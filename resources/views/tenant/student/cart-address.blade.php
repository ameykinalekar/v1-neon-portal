@extends('layouts.default')
@section('title', 'Cart')
@section('pagecss')
<style>
    .alert{
        padding : 3px 15px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header h2 {
        margin: 0;
        font-size: 18px;
    }

    .add-address {
        color: #5BC2B9;
        text-decoration: none;
        font-weight: normal;
    }

    .add-address:hover {
        text-decoration: underline;
    }

    .address-card {
        border: 2px solid #5BC2B9;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .address-header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .address-header input[type="radio"] {
        margin-right: 10px;
    }

    .address-header label {
        font-size: 16px;
    }

    .address-body {
        margin-bottom: 20px;
    }

    .address-body p {
        margin: 5px 0;
        font-size: 14px;
        color: #555;
    }

    .address-actions {
        display: flex;
        gap: 10px;
    }

    .address-actions button {
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .edit-btn {
        background-color: white;
        border-radius: 1px;
        border: 1px solid #5BC2B9;
    }

    .card {
        padding:20px;
        border-radius: 0px !important;
    }

    .remove-btn {
        background-color: white;
        border-radius: 1px;
        border: 1px solid #5BC2B9;
    }


    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .header h2 {
            font-size: 16px;
        }

        .address-card {
            padding: 10px;
        }

        .address-header label {
            font-size: 14px;
        }

        .address-body p {
            font-size: 12px;
        }

        .address-actions button {
            padding: 8px 16px;
            font-size: 12px;
        }
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
                <div class="header">
                    <h2>Select Address</h2>
                    <a href="#" class="add-address">Add New Address</a>
                </div>
                <div class="address-card">
                    <div class="address-header">
                        <input type="radio" id="address1" name="address" checked>
                        <label for="address1"><strong>Olivia Green</strong></label>
                    </div>
                    <div class="address-body">
                        <p>Building J49, Ottery, Hedge-end Oxford BH1 1AA</p>
                        <p>Mobile: 978765678</p>
                    </div>
                    <div class="address-actions">
                        <button class="edit-btn" >Edit</button>
                        <button class="remove-btn">Remove</button>
                    </div>
                </div>

</div>
@endsection
@section('pagescript')
<script>
    initDataTable('datatable');
</script>
@endsection
