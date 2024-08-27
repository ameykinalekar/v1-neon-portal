@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('p_validatechild',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <div class="form-row">
        
        <div class="form-group mb-1">
            <label for="email">Student Email</label>
            <input type="email" class="form-control" id="email" name="email" required readonly value="{{$student_details['email']}}">
        </div>
        <div class="form-group mb-1">
            <label for="code">Student Code</label>
            <input type="text" class="form-control" id="code" name="code" required readonly value="{{$student_details['code']}}">
        </div>
        <div class="form-group mb-1">
            <label for="otp">OTP</label>
            <input type="number" class="form-control" id="otp" name="otp" required>
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit">Verify Child</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script src="{{ asset('rcrop/dist/rcrop.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();

});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({
        dropdownParent: $("#right-modal")
    });
}

</script>
@endsection