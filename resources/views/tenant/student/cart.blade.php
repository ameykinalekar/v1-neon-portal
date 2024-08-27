@extends('layouts.default')
@section('title', 'Cart')
@section('pagecss')
<style>
    .alert{
        padding : 3px 15px;
    }

    .item {
        display: flex;
        align-items: flex-start;
        border-bottom: 1px solid #ddd;
        padding: 20px 0;
        position: relative;
    }

    .item:last-child {
        border-bottom: none;
    }

    .item img {
        width: 100px;
        height: 100px;
        border-radius: 8px;
        margin-right: 20px;
    }

    .item-details {
        flex: 1;
    }

    .item-details h3 {
        margin: 0 0 10px;
        font-size: 18px;
    }

    .item-details p {
        margin: 5px 0;
    }

    .price {
        color: #28a745;
        font-size: 16px;
        font-weight: bold;
    }

    .original-price {
        text-decoration: line-through;
        color: #888;
    }

    .discount {
        color: #dc3545;
    }

    button {
        background-color: #28a745;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    button:hover {
        background-color: #218838;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        color: #888;
        cursor: pointer;
    }

    .close-btn:hover {
        color: #000;
    }

    @media (max-width: 768px) {
        .item {
            flex-direction: column;
            align-items: center;
        }

        .item img {
            margin-bottom: 10px;
        }

        .close-btn {
            top: -10px;
            right: -10px;
        }


</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
            <h4>Cart</h4>

        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
</div>
<div class="card" style="border-radius: 0px !important;padding:20px;">
    <!-- Item 1 -->
    <div class="item">
        <img src="" alt="Chemistry">
        <div class="item-details">
            <h3>Chemistry</h3>
            <p>Chemistry course is ideal for learners who want to study chemistry or a wide variety of related subjects at university or to follow a career in science.</p>
            <p><strong>10 months Duration</strong></p>
            <p class="price">£90 <span class="original-price">£100</span> <span class="discount">(10% OFF)</span></p>
            <button class="btn w-100" style="background: #5BC2B9;font-weight: normal;color:#fff;border-radius: 5px;">Buy Now</button>
        </div>
        <span class="close-btn">&times;</span>
    </div>
<br/>
    <!-- Item 2 -->
    <div class="item">
        <img src="" alt="Chemistry">
        <div class="item-details">
            <h3>Chemistry</h3>
            <p>Chemistry course is ideal for learners who want to study chemistry or a wide variety of related subjects at university or to follow a career in science.</p>
            <p><strong>10 months Duration</strong></p>
            <p class="price">£90 <span class="original-price">£100</span> <span class="discount">(10% OFF)</span></p>
            <button class="btn w-100" style="background: #5BC2B9;font-weight: normal;color:#fff;border-radius: 5px;">Buy Now</button>
        </div>
        <span class="close-btn">&times;</span>
    </div>

</div>
@endsection
@section('pagescript')
<script>
    initDataTable('datatable');
</script>
@endsection
