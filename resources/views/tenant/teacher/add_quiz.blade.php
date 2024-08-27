@extends('layouts.ajax')
@section('pagecss')
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('tut_savequiz',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="name">Quiz Name</label>
            <input type="text" class="form-control" id="name" name ="name" required>
        </div>
        <div class="form-group mb-1">
            <label for="name">Is Homework?</label>
            <select class="form-control" id="homework" name ="homework" required>
                <option value="">Select your choice</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Create Quiz</button>
        </div>
    </div>
</form>
@endsection
