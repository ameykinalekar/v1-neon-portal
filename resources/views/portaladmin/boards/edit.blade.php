@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('pa_updateboard')}}">
    @csrf
    <input type="hidden" name="board_id" value="{{$board_details['board_id']??''}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="board_name">Board Name</label>
            <input type="text" class="form-control" id="board_name" name ="board_name" value="{{$board_details['board_name']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Short Name</label>
            <input type="text" class="form-control" id="short_name" name = "short_name" value="{{$board_details['short_name']??''}}" required>
        </div>
        <div class="form-group mb-1">
            <label for="country_id">Country</label>
            <select class="form-control select2_el" id="country_id" name="country_id" required>
                <option value="">Select Country</option>
                @foreach($countries as $record)

                <option value="{{$record['country_id']}}">{{$record['name']}}</option>

                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Description</label>
            <textarea class="form-control" id="description" name = "description"  maxlength="200">{{$board_details['description']??''}}</textarea>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $board_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Update Board</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        initailizeSelect2();

        $('#country_id').val("{{ $board_details['country_id'] ?? '' }}").trigger('change');

    });
    // Initialize select2
    function initailizeSelect2() {

        $(".select2_el").select2({dropdownParent: $("#right-modal")});
    }
</script>
@endsection
