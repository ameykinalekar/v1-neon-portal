@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('ta_updategrade',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="grade_id" value="{{$details['grade_id']??''}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="board_id">Board</label>
            <input type="hidden" id="hdboard_id" value="{{$details['board_id']}}">
            <select name="board_id" id="board_id" class="form-control select2_el" required>
                <option value="">Select Board</option>
                @foreach($boards as $record)
                @if($record['status']==GlobalVars::ACTIVE_STATUS)
                <option value="{{$record['board_id']}}">{{$record['short_name']}}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="grade">Grade</label>
            <input type="text" class="form-control" id="grade" name ="grade" pattern="^[a-zA-Z]\+?\*?$"   value="{{$details['grade']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="min_value">Minimum %age</label>
            <input type="number" class="form-control" id="min_value" name ="min_value" step="any" required  min="0"  max="100"   value="{{$details['min_value']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="max_value">Maximum %age</label>
            <input type="number" class="form-control" id="max_value" name ="max_value" step="any" required  min="0"  max="100"   value="{{$details['max_value']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="effective_date">Effective From</label>
            <input type="date" class="form-control" id="effective_date" name ="effective_date" required   value="{{$details['effective_date']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Update Grade</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        initailizeSelect2();
        var existingBoadIdValue = $('#hdboard_id').val();
        if (existingBoadIdValue != '') {
            $('#board_id').val(existingBoadIdValue).trigger('change');

        }
    });

    // Initialize select2
    function initailizeSelect2() {
        $(".select2_el").select2({
            dropdownParent: $("#right-modal")
        });
    }
  
    $(document).ready(function(){
        $('#max_value').attr('min',$('#min_value').val());
        $('#min_value').attr('max',$('#max_value').val());
    });

    $('#min_value').on('blur',function(){
        $('#max_value').attr('min',$('#min_value').val());
    });
    $('#max_value').on('blur',function(){
        $('#min_value').attr('max',$('#max_value').val());
    });

</script>
@endsection
