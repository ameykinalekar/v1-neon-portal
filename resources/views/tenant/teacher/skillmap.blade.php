@extends('layouts.default')
@section('title', 'Skill Map')
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
tr th, tr td {
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
                    Skill Map
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <form id="frmSkillmap" method="post">
                    @csrf
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="year_group_id">Year Group</label>
                                <select name="year_group_id" id="year_group_id" class="form-control select2" required>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="year_group_id">Subject</label>
                                <select name="subject_id" id="subject_id" class="form-control select2" required>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="lesson_name">Lesson</label>
                                <select name="lesson_id" id="lesson_id" class="form-control select2_el" required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>

                        </div>

                    </div>
                </form>
            </div>
            @if(count($listing)>0)
            <div class="card-body admin_content">
                <div class="table-responsive">
                    <table id="tableid" class="table table-bordered wrap" width="100%">
                        <thead>
                        <tr style="background-color: rgba(90, 194, 185, 1); color: #ffffff;">
                            <th width="10%">Topic</th>
                            <th width="70%">Skill Map</th>
                            <th width="5%">TC</th>
                            <th width="5%">MS</th>
                            <th width="5%">PS</th>
                            <th width="5%">AT</th>
                        </tr>
                        </thead>

                        @foreach($listing as $record)<?php //print_r(count($record['sub_topics']));?>
                        <tr>
                            <td class="rotate-me" style="text-align:center;font-weight:700;vertical-align: middle" rowspan="{{count($record['sub_topics'])+1}}">{{$record['topic']??''}}
                            </td>
                        </tr>
                        @foreach($record['sub_topics'] as $subrecord)
                        <tr>
                            <td>{{$subrecord['sub_topic']}}</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                        </tr>
                        @endforeach
                        @endforeach
                    </table>
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
            $('#subject_id').val(existingValue).trigger('change');
            $('#subject_id').attr("disabled", false);
        } else {
            $('#subject_id').attr("disabled", false);
        }

    }
    if ($(this).val() != '') {
        doAjax(params);
    }
});

$("#subject_id").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-subjectid-lessons'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        subject_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#lesson_id').html(option);
        $('#lesson_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select your choice</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.lesson_id + '">' +
                item.lesson_name + '</option>';
        });
        $('#lesson_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#lesson_id').html('<option value="">Select your choice</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        var existingValue = "{{$lesson_id ?? ''}}";
        if (existingValue != '') {
            $('#lesson_id').val(existingValue);
            $('#lesson_id').attr("disabled", false);
        } else {
            $('#lesson_id').attr("disabled", false);
        }
    }
    doAjax(params);
});
$('#lesson_id').on('change', function() {
    document.getElementById('frmSkillmap').submit();
});
</script>
@endsection
