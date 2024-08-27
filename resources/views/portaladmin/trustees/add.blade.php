@extends('layouts.ajax')
@section('pagecss')
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm" action="{{route('pa_savetrustee')}}">
    @csrf
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="trustee_name">Trustee Name</label>
            <input type="text" class="form-control" id="trustee_name" name="trustee_name" required>
        </div>

        <div class="form-group mb-1">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div id="emailValidationError" class="invalid-feedback"></div>
        </div>

        <div class="form-group mb-1">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="form-group mb-1">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
        </div>

        <div class="form-group mb-1">
            <label for="phone">Address</label>
            <textarea class="form-control" id="address" name="address" rows="5"></textarea>
        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit" id="btnSubmitCT" disabled>Create Trustee</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script type="text/javascript">
$(document).ready(function(){
    $('#email').on("focusout", function() {
        var email = $("#email").val();
        $('#emailValidationError').hide();
        if (email != '' && typeof email != 'undefined') {
            var params = $.extend({}, doAjax_params_default);
            params['url'] = "<?php echo config('app.api_base_url') . '/email/exist'; ?>";
            params['requestType'] = "POST";
            params['dataType'] = "json";
            params['contentType'] = "application/json; charset=utf-8";
            params['data'] = JSON.stringify({ email: email });
            params['successCallbackFunction'] = function(response) {
                $('#emailValidationError').html('');
                $('#btnSubmitCT').attr('disabled', false);
                $('#email').removeClass('is-invalid');
            }
            params['errorCallBackFunction'] = function(httpObj) {
                $('#emailValidationError').show();
                $('#emailValidationError').html(httpObj.responseJSON.error.message);
                $('#email').addClass('is-invalid');
            }
            params['completeCallbackFunction']=function(data) {
                $("#email").attr('disabled', false);
            }


            doAjax(params);

            // $.ajax({
            //     type: 'POST',
            //     url: "<?php echo config('app.api_base_url') . '/email/exist'; ?>",
            //     data: {
            //         'email': email,
            //     },
            //     dataType: "json",
            //     cors: true ,
            //     headers: {
            //         'Access-Control-Allow-Origin': '*',
            //     },
            //     beforeSend: function() {
            //         $("#email").attr('disabled', true);
            //     },
            //     success: function(response) {
            //         $('#emailValidationError').html('');
            //         $('#btnSubmitCT').attr('disabled', false);
            //     },
            //     error: function(httpObj, textStatus) {
            //         $('#emailValidationError').show();
            //         $('#emailValidationError').html(httpObj.responseJSON.error.message);
            //     },
            //     complete: function(data) {
            //         $("#email").attr('disabled', false);
            //     }
            // });
        }
    });
});
</script>
@endsection
