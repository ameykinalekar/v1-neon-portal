@extends('layouts.ajax')
@section('pagecss')
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" id="frmTfs">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="trustee_name">Trustee Name</label>
            <input type="text" class="form-control" id="trustee_name" name="trustee_name" required>
        </div>

        <div class="form-group mb-1">
            <label for="trustee_email">Email</label>
            <input type="email" class="form-control" id="trustee_email" name="trustee_email" required>
            <div id="trustee_emailValidationError" class="invalid-feedback"></div>
        </div>

        <div class="form-group mb-1">
            <label for="trustee_password">Password</label>
            <input type="password" class="form-control" id="trustee_password" name="trustee_password" required>
        </div>

        <div class="form-group mb-1">
            <label for="trustee_phone">Phone Number</label>
            <input type="text" class="form-control" id="trustee_phone" name="trustee_phone" onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>

        <div class="form-group mb-1">
            <label for="trustee_address ">Address</label>
            <textarea class="form-control" id="trustee_address" name="trustee_address" rows="5"></textarea>
        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit" id="btnSubmitCT" disabled>Create Trustee</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script type="text/javascript">
$(document).ready(function() {
    $('#trustee_email').on("focusout", function() {
        var email = $("#trustee_email").val();
        // alert(email);
        $('#trustee_emailValidationError').hide();
        if (email != '' && typeof email != 'undefined') {
            var params = $.extend({}, doAjax_params_default);
            params['url'] = "<?php echo config('app.api_base_url') . '/email/exist'; ?>";
            params['requestType'] = "POST";
            params['dataType'] = "json";
            params['contentType'] = "application/json; charset=utf-8";
            params['data'] = JSON.stringify({
                email: email
            });
            params['successCallbackFunction'] = function(response) {
                $('#trustee_emailValidationError').html('');
                $('#btnSubmitCT').attr('disabled', false);
                $('#trustee_email').removeClass('is-invalid');
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#trustee_emailValidationError').show();
                $('#trustee_emailValidationError').html(httpObj.responseJSON.error.message);
                $('#trustee_email').addClass('is-invalid');
            }
            params['completeCallbackFunction'] = function(data) {
                $("#trustee_email").attr('disabled', false);
            }


            doAjax(params);


        }
    });

    $("#frmTfs").on("submit", function(e) {
        e.preventDefault();
        // alert('form submit clicked');
        var token = "{{Session::get('usertoken')}}";
        var params = $.extend({}, doAjax_params_default);
        params['url'] = "<?php echo config('app.api_base_url') . '/create-trustee'; ?>";
        params['requestType'] = "POST";
        params['dataType'] = "json";
        params['contentType'] = "application/json; charset=utf-8";
        params['headers'] = {
            Authorization: 'Bearer ' + token
        };
        params['data'] = JSON.stringify({
            trustee_name: $('#trustee_name').val(),
            email: $('#trustee_email').val(),
            password: $('#trustee_password').val(),
            phone: $('#trustee_phone').val(),
            address: $('#trustee_address').val()
        });
        params['beforeSendCallbackFunction'] = function(response) {
            $('#btnSubmitCT').attr('disabled', true);
        }
        params['successCallbackFunction'] = function(response) {
            alert(response.result.message);
            resetForm();
        }
        params['errorCallBackFunction'] = function(httpObj) {
            alert(httpObj.responseJSON.error.message);
        }
        params['completeCallbackFunction'] = function(data) {
            // $('#btnSubmitCT').attr('disabled', false);

        }


        doAjax(params);

    });
});
function resetForm(){
    $('#trustee_name').val('');
    $('#trustee_email').val('');
    $('#trustee_password').val('');
    $('#trustee_phone').val('');
    $('#trustee_address').val('');
}
</script>
@endsection