@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_updateyeargroup',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="year_group_id" id="year_group_id" value="{{$year_group_details['year_group_id']??''}}">
    <input type="hidden" name="batchtypes" id="batchtypes" value="{{json_encode($year_group_batch_types)}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="academic_year_id">Academic Year</label>
            <select name="academic_year_id" id="academic_year_id" class="form-control select2" required>
                <option value="">Select Academic Year</option>
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{$year_group_details['name']??''}}"
                required>
        </div>
        <div class="form-group mb-1">
            <label for="batch_type">Batch type</label><br>
            @foreach($year_group_batch_types as $record)
            @if($year_group_details['one_one']=='1:1' && $record['name']=='one:one')
            <input type="checkbox" name="chk_{{\Helpers::encryptId($record['name'])}}"
                id="chk_{{\Helpers::encryptId($record['name'])}}" checked> {{$record['name']}}<br />
            @elseif($year_group_details['group']=='group' && $record['name']=='group')
            <input type="checkbox" name="chk_{{\Helpers::encryptId($record['name'])}}"
                id="chk_{{\Helpers::encryptId($record['name'])}}" checked> {{$record['name']}}<br />
            @else
            <input type="checkbox" name="chk_{{\Helpers::encryptId($record['name'])}}"
                id="chk_{{\Helpers::encryptId($record['name'])}}"> {{$record['name']}}<br />
            @endif
            @endforeach
        </div>

        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $year_group_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Update Year Group</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();
    onPageLoad();

});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({
        dropdownParent: $("#right-modal")
    });
}

function onPageLoad() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-academic-years'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Academic Year</option>';
        response.result.academic_year_list.forEach(function(item) {
            if(item.status=='Active' || item.academic_year_id=="{{ $year_group_details['academic_year_id'] ?? '' }}"){
                if (academic_year_id == item.academic_year_id) {
                    option = option + '<option value="' + item.academic_year_id + '" selected>' +
                        item.academic_year + '</option>';
                } else {
                    option = option + '<option value="' + item.academic_year_id + '">' +
                        item.academic_year + '</option>';
                }
            }
        });
        $('#academic_year_id').html(option);
        $('#academic_year_id').val("{{ $year_group_details['academic_year_id'] ?? '' }}").trigger('change');
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#academic_year_id').html('<option value="">Select Academic Year</option>');
    }

    doAjax(params);

}
</script>
@endsection