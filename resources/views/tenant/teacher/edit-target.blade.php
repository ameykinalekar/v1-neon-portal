@extends('layouts.default')
@section('pagecss')
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i class="mdi mdi-account-circle title_icon"></i> Edit Student Target
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body admin_content">
                
                <form method="POST" class="d-block ajaxForm" action="{{route('tut_starget_update',Session()->get('tenant_info')['subdomain'])}}" id="frmPage">
                    @csrf
                    <input type="hidden" name="target_id" value="{{$details['target_id'] ?? null}}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label for="year_group_id" class="form-label">Year Group</label>
                                <select name="year_group_id" id="year_group_id" class="form-control select2_el"
                                    required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group required">
                                <label for="user_id" class="form-label">Student</label>
                                <select name="user_id" id="user_id" class="form-control select2_el"
                                    required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label for="set_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="set_date" name="set_date" value="{{$details['set_date'] ?? null}}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label for="set_date" class="form-label">Status</label>
                                {{ Form::select('status',$status, $details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
                            </div>
                        </div>
                        <div class="form-group mt-2 col-md-12" id="divStudents"></div>
                        <div class="form-group mt-2 col-md-12">
                            <button class="btn btn-block btn-primary" type="submit" id="btnSubmitCS" disabled
                                >Update Targets</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('pagescript')
<script src="{{ asset('rcrop/dist/rcrop.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    onPageLoad();
    initailizeSelect2();
});

// Initialize select2
function initailizeSelect2() {
    $(".select2_el").select2();
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
        var existingValue = "{{$details['year_group_id'] ?? ''}}";
        if (existingValue != '') {
            $('#year_group_id').val(existingValue).trigger('change');
            $('#year_group_id').attr("disabled", false);
        } else {
            $('#year_group_id').attr("disabled", false);
        }

    }


    doAjax(params);
    //set & disable year group based on existing

}

$("#year_group_id").on('change', function() {

    var token = "{{Session::get('usertoken')}}";
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/students-by-yrgpid'; ?>";
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
        $('#user_id').html(option);
        $('#user_id').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="">Select Student</option>';
        response.result.list.forEach(function(item) {
            option = option + '<option value="' + item.user_id + '">' +
                item.first_name+' '+item.last_name + '</option>';
        });
        $('#user_id').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#user_id').html('<option value="">Select Student</option>');
    }
    params['completeCallbackFunction'] = function(response) {
        var existingValue = "{{$details['user_id'] ?? ''}}";
        if (existingValue != '') {
            $('#user_id').val(existingValue).trigger('change');
            $('#user_id').attr("disabled", false);
        } else {
            $('#user_id').attr("disabled", false);
        }

    }
    if ($(this).val() != '') {
        doAjax(params);
    }
});
$("#user_id").on('change', function() {
    var token = "{{Session::get('usertoken')}}";
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-user-subjects'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";
    params['headers'] = {
        Authorization: 'Bearer ' + token
    };
    params['data'] = JSON.stringify({
        user_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        
        $('#divStudents').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px; padding: 50% 0px; opacity: .6;">
        </div>`);
    }
    params['successCallbackFunction'] = function(response) {
        
        var option = `<div class="table-responsive"><table id="datatable" class="table table-striped  nowrap" width="100%">
                        <thead>
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th>#</th>
                                <th>Subject</th>
                                <th>Target <small>(in %)</small></th>
                                <th>Target Date</th>
                            </tr>
                        </thead>
                        <tbody></div>`;
        var i=0;
        var existingTarget;
        var existingTargetDate;
    
        var edata='{{json_encode($details['details'])}}';
        edata=JSON.parse(edata.replace(/&quot;/g,'"'));
        
        response.result.details.forEach(function(item) {
            i++;
            existingTarget='';
            existingTargetDate='';
            edata.forEach(function(ed){
                if(item.subject_id==ed.subject_id){
                    existingTarget=ed.target;
                    existingTargetDate=ed.target_date;
                }
            });

            option = option + `<tr>`;
            option = option + `<td>`+i+`</td>`;
            option = option + `<td>`+item.subject_name+`<input type="hidden" name="subject_id[]" value="`+item.subject_id+`"></td>`;
            option = option + `<td><input class="form-control" type="number" step="any" min="1" name="target[]" value="`+existingTarget+`"></td>`;
            option = option + `<td><input class="form-control" type="date"  name="target_date[]" value="`+existingTargetDate+`"></td>`;
            option = option + `</tr>`;
            
           
        });
        if(i>0){
            $('#btnSubmitCS').attr("disabled", false);
        }
        option=option+`</tbody></table>`;
        $('#divStudents').html(option);
    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#divStudents').html('Issue finding subjects. Please try after sometime.');
    }
    params['completeCallbackFunction'] = function(response) {


    }
    if ($(this).val() != '') {
        doAjax(params);
    }
});

</script>
@endsection
