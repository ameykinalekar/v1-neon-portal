@extends('layouts.ajax')
@section('pagecss')
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('pa_saveboard')}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="board_name">Board Name</label>
            <input type="text" class="form-control" id="board_name" name ="board_name" required>
            <small class="form-text text-muted">Provide board name</small>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Short Name</label>
            <input type="text" class="form-control" id="short_name" name = "short_name" required>
            <small class="form-text text-muted">Provide board short name</small>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Description</label>
            <textarea class="form-control" id="description" name = "description"  maxlength="200"></textarea>
            <small class="form-text text-muted">Provide board description</small>
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Save Board</button>
        </div>
    </div>
</form>
@endsection
