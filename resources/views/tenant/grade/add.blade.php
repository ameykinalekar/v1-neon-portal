@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('ta_savegrade',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="board_id">Board</label>
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
            <input type="text" class="form-control" id="grade" name ="grade" pattern="^[a-zA-Z]\+?\*?$">
        </div>
        <div class="form-group mb-1">
            <label for="min_value">Minimum %age</label>
            <input type="number" class="form-control" id="min_value" name ="min_value" step="any" required  min="0"  max="100">
        </div>
        <div class="form-group mb-1">
            <label for="max_value">Maximum %age</label>
            <input type="number" class="form-control" id="max_value" name ="max_value" step="any" required  min="0"  max="100">
        </div>
        
        <div class="form-group mb-1">
            <label for="effective_date">Effective From</label>
            <input type="date" class="form-control" id="effective_date" name ="effective_date" required >
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Save Grade</button>
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
        $(".select2_el").select2({
            dropdownParent: $("#right-modal")
        });
    }

    $('#min_value').on('blur',function(){
        $('#max_value').attr('min',$('#min_value').val());
    });
    $('#max_value').on('blur',function(){
        $('#min_value').attr('max',$('#max_value').val());
    });

</script>
@endsection
