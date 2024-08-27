@extends('layouts.ajax')
@section('title', 'Subscribe Plan')
@section('pagecss')

@endsection
@section('content')
@php
    $price=$plan_details['base_price'];
    $tax=$plan_details['tax_percentage'];
    $taxamt=round((($price*$tax)/100),2);
    $final_price=$price + $taxamt;
@endphp
<form method="POST" class="d-block ajaxForm" action="{{route('pa_schoolsaveplansubscribe')}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="board_name">School Name</label>
            <input type="text" class="form-control" readonly value="{{$school_details['first_name']??''}}">
            <input type="hidden" class="form-control" name="user_id" readonly value="{{$school_details['user_id']??''}}">
            <input type="hidden" class="form-control" name="tenant_id" readonly value="{{$school_details['tenant_id']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Selected Subscription Plan</label>
            <input type="text" class="form-control" readonly value="{{$plan_details['plan_name']??''}}">
            <input type="hidden" class="form-control" name="subscription_plan_id" readonly value="{{$plan_details['subscription_plan_id']??''}}">

        </div>
        <div class="form-group mb-1">
            <label for="short_name">Amount</label>
            <input type="text" class="form-control" readonly value="{{$final_price}}">
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Subscribe</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')

@endsection
