@extends('layouts.ajax')
@section('pagecss')

@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('tus_saveinviteestudygroup',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" class="form-control" id="study_group_id" name="study_group_id" value="{{$study_group_id}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="name">Invitee Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group mb-1">
            <label for="description">Invitee Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>


        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" >Send invitation</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')

@endsection
