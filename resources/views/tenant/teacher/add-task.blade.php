@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<style type="text/css">
#dvSpecificUsers {
    display: none;
}
#dvSpecificExam {
    display: none;
}
</style>
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('tut_savetask',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="task">Task Name</label>
            <input type="text" class="form-control" id="task" name="task" required>
        </div>
        <div class="form-group mb-1">
            <label for="task">Task Description</label>
            <textarea type="text" class="form-control" id="description" name="description" ></textarea>
        </div>
        <div class="form-group mb-1">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" required>
        </div>
        <div class="form-group mb-1">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" required>
        </div>

        <div class="form-group mb-1">
            <label for="task_type">Task Type</label>
            <select class="form-control select2_el" id="task_type" name="task_type" required>
                <option value="">Select message audience</option>
                @foreach($task_type as $k=>$v)
                <option value="{{$k}}">{{$v}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1" id="dvSpecificExam">
            <label for="mexam">Select Examination</label>
            <select class="form-control select2_el" id="mexam" name="mexam[]" multiple="multiple">

            </select>
        </div>
        <div class="form-group mb-1">
            <label for="created_for">Audience</label>
            <select class="form-control select2_el" id="created_for" name="created_for" required>
                <option value="">Select message audience</option>
                @foreach($created_for as $k=>$v)
                <option value="{{$k}}">{{$v}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1" id="dvSpecificUsers">
            <label for="musers">Select Users</label>
            <select class="form-control select2_el" id="musers" name="users[]" multiple="multiple">

            </select>
        </div>
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit">Save Task</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{asset('plugins/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('plugins/ckeditor/plugins/ckfinder/ckfinder.js')}}"></script>
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    loadEditor();
    initailizeSelect2();
});
// Initialize select2
function initailizeSelect2() {
    $(".select2_el").select2({
        dropdownParent: $("#right-modal")
    });
}
function loadEditor() {
    $('.ckeditor').each(function() {
        // alert("aaa");
        id = $(this).attr('id');

        if (!CKEDITOR.instances[id]){
            // alert("aaa"+id);
            CKEDITOR.replace(id);
        }
        //delete CKEDITOR.instances[id];
    });
}

$("#start_date").change(function() {
  //when i select a new date in from field
  $("#end_date").attr({
    "min": $(this).val()
  });
  //i set the min attribute for the to field as the from one
});
$('#end_date').on('blur',function(){
  //let me check if the value is greater than the from one
  var from = new Date($('#start_date').val());
  var to = new Date($('#end_date').val());
  if(to<from){
      console.log('invalid value in to field');
      $('#end_date').val('');
  }
});

$('#task_type').on('change', function() {
    if ($(this).val() == 'Q' || $(this).val() == 'A') {
        $('#mexam').html('');
        $('#mexam').attr('required', true);
        $('#dvSpecificExam').show();
        var token = "{{Session::get('usertoken')}}";
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/all-creator-examinations'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            examination_type: $(this).val(),
            homework:0,
        });

        params['beforeSendCallbackFunction'] = function(response) {
            var option = '<option value="">Loading.....</option>';
            $('#mexam').html(option);
            $('#mexam').attr("disabled", "disabled");
        }
        params['successCallbackFunction'] = function(response) {
            var option = '';

            response.result.listing.forEach(function(item) {
                var etype='';
                if(item.examination_type=='Q'){
                    etype='Quiz-';
                }
                if(item.examination_type=='A'){
                    etype='Assessment-';
                }
                option = option + '<option value="' + item.examination_id + '">' +
                etype+item.name + ' ('+ item.subject.subject_name +'-'+item.subject.yeargroup.name + '-'+item.subject.academicyear.academic_year+')' +
                    '</option>';
            });
            // console.log('==='+option);
            $('#mexam').html(option);
        }
        params['errorCallBackFunction'] = function(httpObj) {
            $('#mexam').html('');
        }
        params['completeCallbackFunction'] = function(response) {

                $('#mexam').attr("disabled", false);

        }

            doAjax(params);

    }else if ($(this).val() == 'H') {
        $('#mexam').html('');
        $('#mexam').attr('required', true);
        $('#dvSpecificExam').show();
        var token = "{{Session::get('usertoken')}}";
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/all-creator-examinations'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            examination_type: $(this).val(),
            homework:1,
        });

        params['beforeSendCallbackFunction'] = function(response) {
            var option = '<option value="">Loading.....</option>';
            $('#mexam').html(option);
            $('#mexam').attr("disabled", "disabled");
        }
        params['successCallbackFunction'] = function(response) {
            var option = '';

            response.result.listing.forEach(function(item) {
                var etype='';
                if(item.examination_type=='Q'){
                    etype='Quiz-';
                }
                if(item.examination_type=='A'){
                    etype='Assessment-';
                }
                option = option + '<option value="' + item.examination_id + '">' +
                etype+item.name + ' ('+ item.subject.subject_name +'-'+item.subject.yeargroup.name + '-'+item.subject.academicyear.academic_year+')' +
                    '</option>';
            });
            // console.log('==='+option);
            $('#mexam').html(option);
        }
        params['errorCallBackFunction'] = function(httpObj) {
            $('#mexam').html('');
        }
        params['completeCallbackFunction'] = function(response) {

                $('#mexam').attr("disabled", false);

        }

            doAjax(params);




    } else {
        $('#mexam').attr('required', false);
        $('#dvSpecificExam').hide();
    }
});
$('#created_for').on('change', function() {
    if ($(this).val() == 'Specifc Students') {
        $('#musers').html('');
        $('#musers').attr('required', true);
        $('#dvSpecificUsers').show();
        var token = "{{Session::get('usertoken')}}";
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/all-active-students'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };


        params['beforeSendCallbackFunction'] = function(response) {
            var option = '<option value="">Loading.....</option>';
            $('#musers').html(option);
            $('#musers').attr("disabled", "disabled");
        }
        params['successCallbackFunction'] = function(response) {
            var option = '<option value="">Select Users</option>';
            response.result.listing.forEach(function(item) {
                option = option + '<option value="' + item.user_id + '">' +
                    item.first_name + ' ' + item.last_name + ' (' + item.code + ')' +
                    '</option>';
            });
            // console.log('==='+option);
            $('#musers').html(option);
        }
        params['errorCallBackFunction'] = function(httpObj) {
            $('#musers').html('<option value="">Select User</option>');
        }
        params['completeCallbackFunction'] = function(response) {

                $('#musers').attr("disabled", false);

        }

            doAjax(params);



    } else {
        $('#musers').attr('required', false);
        $('#dvSpecificUsers').hide();
    }
});
</script>
@endsection
