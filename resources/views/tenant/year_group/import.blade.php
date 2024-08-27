@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_saveimportyeargroup',Session()->get('tenant_info')['subdomain'])}}" enctype='multipart/form-data'>
    @csrf

    <div class="form-row">
        <div class="form-group mb-1">
            <label for="academic_year_id">Academic Year</label>
            <select name="academic_year_id" id="academic_year_id" class="form-control select2" required>
            </select>
        </div>

        <div class="form-group mb-1">
            {!! Form::label('Choose XLS File to Import') !!} <strong class="error">*</strong>
            {{ Form::file('import_file', ['required','class' => 'form-control validate-file','data-msg-accept'=>"File must be XLS or XLSX",'accept'=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"]) }}
        </div>
        <div class="form-group">
            <a target="_blank" href="{{ config('app.api_asset_url').'/sampledata/yeargroup-import-sample-format.xlsx' }}">Click to view/download sample format</a>
        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit">Import</button>
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
$('.validate-file').on('change', function() {
    console.log('no. of files' + this.files.length);
    var size;
    var allFilesValid = 0;
    for (let i = 0; i < this.files.length; i++) {
        size = (this.files[i].size / 1024 / 1024).toFixed(2);
        if (size > 2) {
            allFilesValid++;
        }

        console.log('File ' + i + ' Size:' + size);

    }
    if (allFilesValid > 0) {
        alert("Each File must be with in the size of 2 MB");
        $(this).val('');
        // $('#is_form_valid').val('1');
    } else {
        // $('#is_form_valid').val('0');
    }
});

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
            
                if(item.status=='Active'){
                    option=option+'<option value="' + item.academic_year_id + '">' +
                        item.academic_year + '</option>';
                }
        });
        $('#academic_year_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#academic_year_id').html('<option value="">Select Academic Year</option>');
    }

    doAjax(params);

}

</script>
@endsection