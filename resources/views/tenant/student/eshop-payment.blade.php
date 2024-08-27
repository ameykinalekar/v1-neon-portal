@extends('layouts.default')
@section('title', 'NTP Support')
@section('pagecss')
<style>
    .card{
        padding: 20px;
        background-color: #dfdcdc;
        border-radius: 0px !important;
    }
    .subscription-container {
        display: flex;
        flex-wrap: wrap;
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .subscription-details, .payment-form {
        flex: 1 1 300px;
        padding: 20px;
    }

    .subscription-details {
        border-right: 1px solid #ddd;
    }

    .subscription-details h3 {
        margin-bottom: 10px;
        font-size: 20px;
    }

    .subscription-details p {
        margin: 5px 0;
        font-size: 16px;
        color: #333;
    }

    .payment-form h3 {
        margin-bottom: 10px;
        font-size: 20px;
    }

    .payment-form input[type="text"],
    .payment-form .checkbox-group label {
        width: 100%;
        margin-bottom: 10px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .checkbox-group label {
        display: block;
        font-size: 14px;
        color: #555;
    }

    .payment-form .card-details {
        display: flex;
        justify-content: space-between;
    }

    .payment-form .expiry-date input {
        width: 48%;
    }

    .payment-form .cvv input {
        width: 100%;
    }

    .btn-subscribe {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        background-color: #27ae60;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .subscription-terms {
        margin-top: 20px;
        font-size: 12px;
        color: #555;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .subscription-container {
            flex-direction: column;
        }

        .subscription-details {
            border-right: none;
            border-bottom: 1px solid #ddd;
        }
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
            <h4>E-Shop</h4>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
</div>
<div class="subscription-container">
    <div class="subscription-details">
        <div class="card">
        <h3>Subscribe to Year 10 - 1 Subjects</h3>
        <p><strong>Student Name:</strong> Nikita</p>
        <p><strong>Year Group:</strong> Year 10</p>
        <p><strong>Subjects:</strong> Chemistry</p>
        <p><strong>Course Duration Plan:</strong> Regular monthly (Debit/Credit Card) - 4 lessons</p>
        <p><strong>Lesson Type:</strong> One</p>
        <p><strong>Total Price:</strong> £100</p>
        </div>
    </div>
    <div class="payment-form">
        <h3>Card Information</h3>
        <form>
            <input type="text" placeholder="1234 1234 1234 1234" required>
            <input type="text" placeholder="Name on card" required>
            <div class="checkbox-group">
                <label><input type="checkbox"> Securely save my information for 1-click checkout</label>
                <label><input type="checkbox"> I agree to Neon Education Ltd Terms and conditions</label>
                <label><input type="checkbox"> Cancellation Policy: 1 month notice in writing.</label>
            </div>
            <div class="card-details">
                <div class="expiry-date">
                    <input type="text" placeholder="MM" maxlength="2" required>
                    <input type="text" placeholder="YYYY" maxlength="4" required>
                </div>
                <div class="cvv">
                    <input type="text" placeholder="CVV" maxlength="3" required>
                </div>
            </div>
            <button type="submit" class="btn w-100" style="background: #5BC2B9;font-weight: normal;color:#fff;border-radius: 5px;">Subscribe</button>
        </form>
        <p class="subscription-terms">
            By confirming your subscription, you allow Neon Education Limited to charge your card for this payment and future payments in accordance with their terms.
        </p>
    </div>
</div>
@endsection
@section('pagescript')
<script>

    initDataTable('datatable');


</script>




@endsection
