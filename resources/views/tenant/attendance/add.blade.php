@extends('layouts.default')
@section('title', 'Record Attendance')
@section('pagecss')

@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> Record Attendance

                </h4>
                <span id="btnContainer" style="float:inline-end;">
                        <a href="#" class="btn btn-sm btn-default" title=""><i class="fa fa-backward"></i> Back</a>
                    </span>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" class="d-block ajaxForm"
                action="{{route('tut_attendance_save',Session()->get('tenant_info')['subdomain'])}}">
                @csrf
                <div class="card-body admin_content">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="form-group required">
                                <label for="attendance_date" class="form-label">Attendance Date</label>
                                <input type="date" name="attendance_date" id="attendance_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="form-group required">
                                <label for="year_group_id" class="form-label">Year Group</label>
                                <select name="year_group_id" id="year_group_id" class="form-control select2_el"
                                    required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="form-group required">
                                <label for="subject_id" class="form-label">Subject</label>
                                <select name="subject_id" id="subject_id" class="form-control select2_el" required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="form-group required">
                                <label for="lesson_id" class="form-label">Lesson</label>
                                <select name="lesson_id" id="lesson_id" class="form-control select2_el" required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div id="div_subject_students">

                    </div>
                    <div class="form-group mt-2 col-md-12">
                        <button class="btn btn-block btn-primary" id="submitBtn" type="submit" disabled>Save
                            Attendance</button>
                    </div>
                </div>
            </form>
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
$("#attendance_date").on('change',function(){
    var subject_id=$('#subject_id').val();
    loadStudentsOfSubject(subject_id);
});

function loadStudentsOfSubject(subject_id) {
    var token = "{{Session::get('usertoken')}}";
    var basePhotoUrl = "<?php echo config('app.api_asset_url'); ?>";
    var no_image = "<?php echo $no_image; ?>";
    var attendance_date=$('#attendance_date').val();
    // alert(token);
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-students-by-subjectid'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        subject_id: subject_id,
        enroll_date: attendance_date
    });
    params['beforeSendCallbackFunction'] = function(response) {
        $('#div_subject_students').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px; padding: 50% 0px; opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        var option = `<table id="basic-datatable" class="table table-striped  nowrap" width="100%">
                    <thead>
                        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                            <th width="8%">Photo</th>
                            <th width="20%">Student Name</th>
                            <th width="10%" class="text-center">Attendance</th>
                            <th width="16%">Comments</th>
                        </tr>
                    </thead>
                    <tbody>`;
        response.result.listing.forEach(function(item) {
            option = option + `<tr>`;
            if (item.user_logo != '') {
                option = option + `<td><img src="` + basePhotoUrl + item.user_logo +
                    `" alt="user image" height="auto" width="45px"></td>`;
            } else {
                option = option + `<td><img src="` + basePhotoUrl + no_image +
                    `" alt="user image" height="auto" width="45px"></td>`;
            }
            option = option + `<td><input type="hidden"
                                class="form-control" name="user_id[]"
                                value="` + item.user_id + `">` + item.first_name + ` ` + item.last_name + `</td>`;
            option = option + `<td class="text-center"><input type="checkbox"
                                class="form-control-checkbox"
                                name="attendance_` + item.user_id + `"></td>`;
            option = option + `<td><input type="text"
                                class="form-control"
                                name="comment_` + item.user_id + `"></td>`;
            option = option + `</tr>`;
            //option = option + `<option value="` + item.year_group_id + `">` + item.name + ` (` + item.academic_year + `)` + `</option>`;
        });
        $('#div_subject_students').html(option);
        $('#submitBtn').attr('disabled',false);
    }
    params['errorCallBackFunction'] = function(httpObj) {}
    params['completeCallbackFunction'] = function(response) {

    }


    doAjax(params);
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

    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Year Group</option>';
        response.result.yeargroup_list.forEach(function(item) {
            option = option + '<option value="' + item.year_group_id + '">' +
                item.name + ' (' + item.academic_year + ')' + '</option>';
        });
        $('#year_group_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#year_group_id').html('<option value="">Select Year Group</option>');
    }
    params['completeCallbackFunction'] = function(response) {

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
        var option = '<option value="">Select Subject</option>';
        response.result.subject_list.forEach(function(item) {
            option = option + '<option value="' + item.subject_id + '">' +
                item.subject_name + '</option>';
        });
        $('#subject_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#subject_id').html('<option value="">Select Subject</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#subject_id').attr("disabled", false);

    }
    if ($(this).val() != '') {
        doAjax(params);
    }
});

$("#subject_id").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    // alert(token);
    loadStudentsOfSubject($(this).val());
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
        var option = '<option value="">Select Lesson</option>';
        response.result.listing.forEach(function(item) {
            option = option + '<option value="' + item.lesson_id + '">' +
                item.lesson_name + '</option>';
        });
        $('#lesson_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#lesson_id').html('<option value="">Select Lesson</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#lesson_id').attr("disabled", false);
    }
    doAjax(params);
});
</script>
@endsection
