@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<style type="text/css">
#dvSpecificUsers {
    display: none;
}
</style>
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_sendmsg',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" required>
        </div>
        <div class="form-group mb-1">
            <label for="imessage">Message</label>
            <textarea class="form-control ckeditor" id="imessage" name="message" required></textarea>
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
            <button class="btn btn-block btn-primary" type="submit">Send Message</button>
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

$('#created_for').on('change', function() {
    if ($(this).val() == 'Specifc Teachers') {
        $('#musers').html('');
        $('#musers').attr('required', true);
        $('#dvSpecificUsers').show();
        var token = "{{Session::get('usertoken')}}";
        var params = $.extend({}, doAjax_params_default);
        params['url'] =
            "<?php echo config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/all-active-teachers'; ?>";
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
                    item.first_name + ' ' + item.last_name  +
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
    }else if ($(this).val() == 'Specifc Students') {
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
