@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('pa_updatetrustee')}}">
    @csrf
    <input type="hidden" name="user_id" value="{{$trustee_details['user_id']??''}}">
    <div class="form-row">
    <div class="form-group mb-1">
      <label for="name">Trustee Name</label>
      <input type="text" value="{{$trustee_details['first_name']??''}}" class="form-control" id="trustee_name" name ="trustee_name" required>
      <small class="form-text text-muted">Provide Trustee Name</small>
    </div>

    <!-- <div class="form-group mb-1">
      <label for="email">Email</label>
      <input type="email" value="{{$trustee_details['email']??''}}" class="form-control" id="email" name = "email" required>
      <small id="" class="form-text text-muted">Provide Admin Email</small>
    </div> -->

    <div class="form-group mb-1">
      <label for="phone">Phone Number</label>
      <input type="text" value="{{$trustee_details['phone']??''}}" class="form-control" id="phone" name ="phone" required  onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
    </div>

    <div class="form-group mb-1">
      <label for="address">Address</label>
      <textarea class="form-control" id="address" name ="address" rows="5" required>{{$trustee_details['address']??''}}</textarea>
      <small class="form-text text-muted">Provide Admin Address</small>
    </div>

    <div class="form-group mb-1">
            <label for="status">Status</label>
            {{ Form::select('status',$status, $trustee_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
            <small class="form-text text-muted">Provide trustee status</small>
        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Update Trustee</button>
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
