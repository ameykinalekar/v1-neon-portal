@extends('layouts.login')
@section('title', 'Where you want to go!')
@section('pagecss')
<link href="{{asset('css/init/login.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<div class="super_logo">

    <img src="{{asset('img/system/logo/logo-dark.png')}}" alt="" height="45px">
</div>

<div class="auth-fluid">

    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex">
            <div class="card-body">
                <!-- Logo -->
                <div class="text-center text-lg-left mb-3">
<!--                    <span><img src="{{asset('img/system/logo/logo-dark.png')}}" alt="" height="80" class="landing-logo"></span>
-->                    <!--</a>-->
                   <!-- <h4 class="mt-0">Redirect To</h4>-->

                </div>
                <!-- form -->
                <form action="" method="post" id="loginForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="country_id">Country</label>
                        <select class="form-control select2_el" id="country_id" name="country_id" >
                            <option value="">All Countries</option>
                            @foreach($countries as $record)
                            <option value="{{$record['country_id']}}">{{$record['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="subdomain">Login</label>
                        <select name="subdomain" id="subdomain" class="form-control select2_el" required>
                            <option value="pa">Platform Admin / Trustee</option>
                            <optgroup label="Registered Schools">
                            @foreach($tenants as $record)
                            <option value="{{$record['subdomain']}}">{{ $record['first_name'].' '.$record['middle_name'].' '.$record['last_name']}}</option>
                            @endforeach
                            </optgroup>
                        </select>
                    </div>


                    <div class="form-group mb-3 mb-0 text-center">
                        <button class="btn btn-block" style="width:100%" type="submit"><i class="mdi mdi-login"></i>
                            Proceed </button>
                    </div>
                </form>

                <!-- end form-->
            </div> <!-- end .card-body -->
        </div> <!-- end .align-items-center.d-flex.h-100-->
    </div>

    <!-- end auth-fluid-form-box-->

    <!-- Auth fluid right content -->
    <!-- end Auth fluid right content -->
</div>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();

});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2();
}

$("#country_id").on('change', function() {
    var params = $.extend({}, doAjax_params_default);
    params['url'] =
        "<?php echo config('app.api_base_url') . '/dropdown/portal-tenants'; ?>";
    params['requestType'] = "POST";
    params['dataType'] = "json";
    params['contentType'] = "application/json; charset=utf-8";

    params['data'] = JSON.stringify({
        country_id: $(this).val()
    });

    params['beforeSendCallbackFunction'] = function(response) {
        var option = '<option value="">Loading.....</option>';
        $('#subdomain').html(option);
        $('#subdomain').attr("disabled", "disabled");
    }
    params['successCallbackFunction'] = function(response) {
        var option = '<option value="pa">Platform Admin / Trustee</option><optgroup label="Registered Schools">';
        response.result.tenants.forEach(function(item) {
            item_name=item.first_name;
            if(item.middle_name!=null){
                item_name=item_name+ ' ' + item.middle_name;
            }
            if(item.last_name!=null){
                item_name=item_name+ ' ' + item.last_name;
            }
            option = option + '<option value="' + item.subdomain + '">' +
            item_name + '</option>';
        });
        option = option +  '</<optgroup>';
        $('#subdomain').html(option);

    }
    params['errorCallBackFunction'] = function(httpObj) {
        $('#subdomain').html('<option value="pa">Platform Admin / Trustee</option>');
    }
    params['completeCallbackFunction'] = function(response) {

        $('#subdomain').attr("disabled", false);
    }
    doAjax(params);
});
</script>
@endsection
