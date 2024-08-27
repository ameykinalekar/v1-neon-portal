@extends('layouts.default')
@section('title', 'Order History')
@section('pagecss')
<style>
    .alert{
        padding : 3px 15px;
    }

    .order-summary {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    .order-summary h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
    }

    .order-details {
        border-top: 1px solid #ddd;
        padding-top: 20px;
    }

    .order-info p,
    .product-info p,
    .pricing-info p,
    .address-info p {
        margin: 0 0 10px;
        line-height: 1.6;
    }

    .order-info,
    .product-info,
    .pricing-info,
    .address-info {
        margin-bottom: 20px;
    }

    .product-item {
        display: flex;
        align-items: center;
        border-top: 1px solid #ddd;
        padding-top: 20px;
        margin-top: 20px;
    }

    .product-item img {
        width: 103px;
        height: 113px;
        border-radius: 8px;
        margin-right: 20px;
    }

    .product-details {
       flex: 1;
    }
    .product-quantity {
        flex: 1 1 30%;
        font-weight: normal;
        font-size: 18px;
    }
    .product-price {
        flex: 1 1 30%;
        font-weight: bold;
        font-size: 18px;
    }

    .address-info {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .billing-address,
    .contact-details,
    .shipping-address {
        flex: 1 1 30%;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .product-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .product-item img {
            margin-bottom: 10px;
        }

        .address-info {
            flex-direction: column;
        }

        .order-summary {
            background-color: #fff;
            padding: 0px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
         table.tablebody, table.tablebody tbody,  table.tablebody td, table.tablebody tr {
            display: block;
        }
        th, td {
            width: 100%;
        }
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

<div class="order-summary">
    <h6>View More</h6>
    <div class="order-details">
        <div class="order-info">
            <center>
            <table>
                <thead>
                <tr>
                    <th><strong>Order #:</strong></th>
                    <th><strong>Order Date:</strong></th>
                    <th><strong>Status:</strong></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>#17390</td>
                    <td>1-Jan-2024</td>
                    <td>Delivered</td>
                </tr>
                </tbody>
            </table>
            </center>
        </div>
        <table class="tablebody">
            <thead>
            <tr>
                <th align="left"></th>
                <th colspan="2" align="left">Product</th>
                <th align="left">Quantity</th>
                <th align="left">Price</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td>
                    <img src="image.png" alt="T-shirt" width="200px;">
                </td>
                <td>
                    <p><strong> T-shirt</strong></p>
                    <p><strong>Size:</strong> M</p>
                    <p><strong>Colour:</strong> Red</p>
                    <p><strong>Type:</strong> Cotton</p>
                </td>
                <td>
                    <p style="text-align: center">1</p>
                </td>
                <td>
                    <p style="text-align: center">£40.00</p>
                </td>
             </tr>
            <tr>
                <td></td>
                <td colspan="4"><hr></td>

            </tr>
            <tr>
                <td></td>
                <td></td>
                <td colspan="2">
                    <p><strong>Subtotal:</strong></p>
                    <p><strong>Shipping charges:</strong> </p>
                    <p><strong>Payment Method:</strong></p>
                    <p><strong>Total:</strong></p>
                </td>
                <td style="text-align: center">
                    <p>£40.00</p>
                    <p> Free</p>
                    <p>Direct Debit</p>
                    <p> £40.00</p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="4"><hr></td>

            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <p><strong>Billing address:</strong></p>
                    <p>Andy Martin<br>Flat 4, Regent House,<br>1 - 6 Pratt Mews,<br>London NW1 0AD</p>
                </td>
               <td>
                    <p><strong>Contact Details:</strong></p>
                    <p>6767678766<br>andymartin@hotmail.com</p>
               </td>
               <td>
                    <p><strong>Shipping address:</strong></p>
                    <p>Andy Martin<br>Flat 4, Regent House,<br>1 - 6 Pratt Mews,<br>London NW1 0AD</p>
               </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
@section('pagescript')
<script>
    initDataTable('datatable');
</script>
@endsection
