@extends('layouts.ajax')
@section('pagecss')
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('ta_savedepartment',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="department_name">Department Name</label>
            <input type="text" class="form-control" id="department_name" name ="department_name" required>
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Save Department</button>
        </div>
    </div>
</form>
@endsection
