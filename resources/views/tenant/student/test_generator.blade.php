@extends('layouts.default')
@section('title', 'Test Generator')
@section('pagecss')
<style type="text/css">
.rotate-me {
    -ms-writing-mode: tb-rl;
    -webkit-writing-mode: vertical-rl;
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    white-space: nowrap;

}

.thead-dark th {
    color: white;
    font-weight: 600;
    font-size: 12px;
}

.table-bordered>:not(caption)>*>* {
    padding: 0px;
}

tr th,
tr td {
    font-family: Poppins;
    font-weight: 400;
    font-size: 11px;
}
</style>
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    Test Generator
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <form id="frmTG" method="post">
                    @csrf
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="year_group_id">Year Group</label>
                                <select name="year_group_id" id="year_group_id" class="form-control select2_el" required>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="subject_id">Subject</label>
                                <select name="subject_id" id="subject_id" class="form-control select2_el" required>
                                </select>
                            </div>
                        </div>


                    </div>
                </form>
            </div>
            @if(count($listing)>0)
            <div class="card-body admin_content">
                <div class="table-responsive">
                    <form action="{{route('tus_testgen_proceed',Session()->get('tenant_info')['subdomain'])}}" method="post">
                        @csrf
                        <input type="hidden" name="hd_year_group_id" id="" value="{{$year_group_id ?? ''}}">
                        <input type="hidden" name="hd_subject_id" id="" value="{{$subject_id ?? ''}}">
                        <table class="table table-bordered wrap" width="100%">
                            <thead>
                                <tr style="">
                                    <th>select</th>
                                    <th>Lesson Number</th>
                                    <th width="60%">Lesson Name</th>
                                    <th>No. of Questions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($listing as $record)
                                <tr>
                                    <td style="text-align:center;">
                                        <input type="hidden" name="hd_lesson_id[]" id="" value="{{ $record['lesson_id']}}">
                                        <input type="checkbox" name="lesson_id[]" id="" value="{{ $record['lesson_id']}}" @if($record['question_count']=='0') disabled @endif>
                                    </td>
                                    <td style="text-align:center;">{{ $record['lesson_number']}}</td>
                                    <td>{{ $record['lesson_name']}}</td>
                                    <td style="text-align:center;">{{ $record['question_count']}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div class="form-group mt-2 col-md-12">
                                    <button class="btn btn-block btn-primary" type="submit" id="btnSubmit">Done</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif
        </div>
    </div>
</div>
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

    });
}

function onPageLoad() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-yeargroups'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#year_group_id').html(option);
        $('#year_group_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select your choice</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + ' (' + item.academic_year + ')' + '</option>';
        });
        $('#year_group_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#year_group_id').html('<option value="">Select your choice</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        var existingValue = "{{$year_group_id ?? ''}}";
        if (existingValue != '') {
            $('#year_group_id').val(existingValue).trigger('change');
            $('#year_group_id').attr("disabled", false);
        } else {
            $('#year_group_id').attr("disabled", false);
        }

    }


    doAjax(params);

}


$("#year_group_id").on('change', function() {

    var token = "{{Session::get('usertoken')}}";
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-yeargroup-subjects'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        year_group_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#subject_id').html(option);
        $('#subject_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select your choice</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name + ' (' + response.result.boards[item.board_id] + ')' +
                '</option>';
        });
        $('#subject_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#subject_id').html('<option value="">Select your choice</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        var existingValue = "{{$subject_id ?? ''}}";
        if (existingValue != '') {
            $('#subject_id').val(existingValue);
            $('#subject_id').attr("disabled", false);
        } else {
            $('#subject_id').attr("disabled", false);
        }

    }
    if ($(this).val() != '') {
        doAjax(params);
    }
});


$('#subject_id').on('change', function() {
    document.getElementById('frmTG').submit();
});
</script>

@endsection