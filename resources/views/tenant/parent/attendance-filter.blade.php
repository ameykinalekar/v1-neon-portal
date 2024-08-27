@extends('layouts.ajax')
@section('pagecss')
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('p_attendancelist',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="search_student">Student</label>
            <select name="search_student" id="search_student" class="form-control"><option value="">Select Student</option></select>
        </div>
        <div class="form-group mb-1">
            <label for="search_academic_year_id">Academic Year</label>
            <select name="search_academic_year_id" id="search_academic_year_id" class="form-control">
            <option value="">All Academic Year</option>
        </select>
        </div>
        <div class="form-group mb-1">
            <label for="search_year_group_id">Year Group</label>
            <select name="search_year_group_id" id="search_year_group_id" class="form-control">
            <option value="">All Year Group</option>
        </select>
        </div>
        <div class="form-group mb-1">
            <label for="search_subject_id">Subject</label>
            <select name="search_subject_id" id="search_subject_id" class="form-control">
            <option value="">All Subject</option>
        </select>
        </div>
        <div class="form-group mb-1">
            <label for="search_lesson_id">Lesson</label>
            <select name="search_lesson_id" id="search_lesson_id" class="form-control">
            <option value="">All Lessons</option>
        </select>
        </div>
        <div class="form-group mb-1">
            <label for="search_date_from">Date From</label>
            <input type="date" class="form-control" id="search_date_from" name="search_date_from" value="{{$search_date_from??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="search_date_to">Date To</label>
            <input type="date" class="form-control" id="search_date_to" name="search_date_to" value="{{$search_date_to??''}}">
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Filter</button>
            <a class="btn btn-block btn-default" href="{{route('p_attendancelist', Session()->get('tenant_info')['subdomain'])}}">Reset</a>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();
    onPageLoad();
});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2();
}

function onPageLoad() {
    var token = "{{Session::get('usertoken')}}";

    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/parent/students'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Student</option>';
        response.result.item_list.forEach(function(item) {
            if(item.status=='Active'){
                    option=option+'<option value="' + item.user_id + '">' +
                        item.first_name+' '+item.last_name + '</option>';
                }
        });
        $('#search_student').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#search_student').html('<option value="">Select Student</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        @if($search_student!=null)
        $('#search_student').val("{{$search_student}}").trigger('change');
        @endif
    }
    doAjax(params);

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
        var option = '<option value="">All Academic Year</option>';
        response.result.academic_year_list.forEach(function(item) {
            //if(item.status=='Active'){
                    option=option+'<option value="' + item.academic_year_id + '">' +
                        item.academic_year + '</option>';
               // }
        });
        $('#search_academic_year_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#search_academic_year_id').html('<option value="">All Academic Year</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        @if($search_academic_year_id!=null)
        $('#search_academic_year_id').val("{{$search_academic_year_id}}").trigger('change');
        @endif
    }
    doAjax(params);

}

$("#search_academic_year_id").on('change',function(){
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-academicyearid-yeargroups'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        academic_year_id: $(this).val()
            });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#search_year_group_id').html(option);
        $('#search_year_group_id').attr("disabled","disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Year Group</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + '</option>';
        });
        $('#search_year_group_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#search_year_group_id').html('<option value="">All Year Group</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#search_year_group_id').attr("disabled",false);
        @if($search_year_group_id!=null)
        $('#search_year_group_id').val("{{$search_year_group_id}}").trigger('change');
        @endif
    }
    doAjax(params);
});

$("#search_year_group_id").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
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
        $('#search_subject_id').html(option);
        $('#search_subject_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">All Subject</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name + '</option>';
        });
        $('#search_subject_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#search_subject_id').html('<option value="">All Subject</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#search_subject_id').attr("disabled", false);
        @if($search_subject_id!=null)
        $('#search_subject_id').val("{{$search_subject_id}}").trigger('change');
        @endif
    }
    doAjax(params);
});

$("#search_subject_id").on('change', function() {
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
        $('#search_lesson_id').html(option);
        $('#search_lesson_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Lesson</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.lesson_id + '">' +
                item.lesson_name + '</option>';
        });
        $('#search_lesson_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#search_lesson_id').html('<option value="">Select Lesson</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#search_lesson_id').attr("disabled", false);
        @if($search_lesson_id!=null)
        $('#search_lesson_id').val("{{$search_lesson_id}}").trigger('change');
        @endif
    }
    doAjax(params);
});

</script>
@endsection