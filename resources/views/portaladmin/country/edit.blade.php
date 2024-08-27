@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('pa_updatecountry')}}">
    @csrf
    <input type="hidden" name="country_id" value="{{$details['country_id']??''}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="name">Country Name</label>
            <input type="text" class="form-control" id="name" name ="name" value="{{$details['name']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="code">Code</label>
            <input type="text" class="form-control" id="code" name = "code" value="{{$details['code']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="currency_code">Currency</label>
            {{ Form::select('currency_code',$currencies, $details['currency_code']??'', array('class' => 'form-control select2_el','required','id' => 'currency_code','placeholder' => 'Select Currency')) }}
        </div>
        <div class="form-group mb-1">
            <label for="record_status">Status</label>
            {{ Form::select('status',$status, $details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Update Country</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        initailizeSelect2();
    });
    // Initialize select2
    function initailizeSelect2() {

        $(".select2_el").select2({dropdownParent: $("#right-modal")});
    }
</script>
@endsection
