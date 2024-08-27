@extends('layouts.ajax')
@section('pagecss')
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('ta_saveacademicyear',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="start_year">start Year</label>
            <input type="number" class="form-control" id="start_year" name ="start_year" required min="1901"  max="2100">
        </div>
        <div class="form-group mb-1">
            <label for="end_year">End Year</label>
            <input type="number" class="form-control" id="end_year" name ="end_year" required  min="1901"  max="2100">
        </div>
        <div class="form-group mb-1">
            <label for="academic_year">Academic Year</label>
            <input type="text" class="form-control" id="academic_year" name ="academic_year" required readonly>
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit" onclick="setAcademicYear();">Save Academic Year</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script type="text/javascript">
    function setAcademicYear(){
        var start_year=$('#start_year').val();
        var end_year=$('#end_year').val();
        if(start_year!='' && end_year!=''){
            $('#academic_year').val(start_year+'-'+end_year);
        }else{
            $('#academic_year').val('');
        }
    }
    $('#start_year').on('change',function(){
        setAcademicYear();
    });
    $('#end_year').on('change',function(){
        setAcademicYear();
    });
</script>
@endsection
