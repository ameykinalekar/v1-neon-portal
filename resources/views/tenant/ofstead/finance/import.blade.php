@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_saveimportofinance',Session()->get('tenant_info')['subdomain'])}}" enctype='multipart/form-data'>
    @csrf

    <div class="form-row">

        <div class="form-group mb-1">
            {!! Form::label('Choose CSV File to Import') !!} <strong class="error">*</strong>
            {{ Form::file('import_file', ['required','class' => 'form-control validate-file','data-msg-accept'=>"File must be CSV",'accept'=>".csv"]) }}
        </div>
        <div class="form-group">
            <a target="_blank" href="{{ config('app.api_asset_url').'/sampledata/ofstead-finance-import-sample-format.csv' }}">Click to view/download sample format</a>
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
</script>
@endsection