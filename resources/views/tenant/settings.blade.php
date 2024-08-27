@extends('layouts.default')
@section('title', 'System Settings')
@section('pagecss')
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
<style>
    .page-title {
        display: flex;
        align-items: center;
        /* justify-content: space-between; */
        flex-wrap: wrap;
    }
</style>
@endsection
@section('content')
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class=""> <i class="mdi mdi-settings title_icon"></i>System Settings</h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end page title -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <!-- <h4 class="header-title">Update Settings</h4> -->
                <form method="POST" class="col-12 systemLogoAjaxForm" action="" id="system_settings"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="setting_id"
                        value="{{ $response['result']['settings']['setting_id']??'' }}">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="system_title">System Title</label>
                                <input type="text" id="system_title" name="system_title" class="form-control"
                                    value="{{ $response['result']['settings']['system_title']??'' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="system_email">System Email</label>
                                <input type="email" id="system_email" name="system_email" class="form-control"
                                    value="{{ $response['result']['settings']['system_email']??'' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control"
                                    onkeypress="return isPhone(event);"
                                    value="{{ $response['result']['settings']['phone']??'' }}"  title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}" ><small><i>10 digit telephone number with no dashes or dots.</i></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="address">Address</label>
                                <input type="text" id="address" name="address" class="form-control"
                                    value="{{ $response['result']['settings']['address']??'' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="footer_text">Footer Text</label>
                                <input type="text" id="footer_text" name="footer_text" class="form-control"
                                    value="{{ $response['result']['settings']['footer_text']??'' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="footer_link">Footer Link</label>
                                <input type="url" id="footer_link" name="footer_link" class="form-control"
                                    value="{{ $response['result']['settings']['footer_link']??'' }}">
                            </div>
                        </div>
                        @php
                            $mail_settings=$response['result']['settings']['mail_settings']??'';

                            $smtp_host='';
                            $smtp_port='';
                            $smtp_username='';
                            $smtp_password='';
                            $smtp_security='';
                            if($mail_settings!=''){
                                $mail_settings=\Helpers::decryptId($mail_settings);
                                $mail_settings=json_decode($mail_settings);
                                //dd($mail_settings);
                                $smtp_host=$mail_settings->smtp_host??'';
                                $smtp_port=$mail_settings->smtp_port??'';
                                $smtp_username=$mail_settings->smtp_username??'';
                                $smtp_password=$mail_settings->smtp_password??'';
                                $smtp_security=$mail_settings->smtp_security??'';
                            }
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="smtp_host">SMTP Host</label>
                                <input type="text" id="smtp_host" name="smtp_host" class="form-control"
                                    value="{{$smtp_host}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="smtp_port">SMTP Port</label>
                                <input type="number" step="any" id="smtp_port" name="smtp_port" class="form-control"
                                    value="{{$smtp_port}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="smtp_username">SMTP Username</label>
                                <input type="text" id="smtp_username" name="smtp_username" class="form-control"
                                    value="{{$smtp_username}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="smtp_password">SMTP Password</label>
                                <input type="text" id="smtp_password" name="smtp_password" class="form-control"
                                    value="{{$smtp_password}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="smtp_security">SMTP Security</label>
                                <select id="smtp_security" name="smtp_security" class="form-control">
                                    <option value="tls" <?php if ($smtp_security == 'tls') {echo 'selected';}?>>TLS</option>
                                    <option value="ssl" <?php if ($smtp_security == 'ssl') {echo 'selected';}?>>SSL</option>
                                    <option value="starttls" <?php if ($smtp_security == 'starttls') {echo 'selected';}?>>STARTTLS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="theme_color">Header Color</label>
                                <input type="color" id="theme_color" name="theme_color" class="form-control"
                                    value="{{$response['result']['tenantInfo']['theme_color']}}">
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="form-group  mb-3">
                                <label class="form-label" for="logo">Regular Logo</label>
                                <div class="js--image-preview"
                                    style="background-image: url(<?php echo config('app.api_asset_url') . $response['result']['tenantInfo']['logo'] ?? ''; ?>); background-color: #F5F5F5;">
                                </div>
                                <input type="file" class="form-control" name="logo" id="logo"
                                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                                <small>Please upload file size 300 X 300 (Pixels)</small>

                                <img id="logo_show_image" width='100%' src="">
                                <input id="imagedata_logo" type="hidden" class="form-control input-border-bottom"
                                    name="imagedata_logo" value="">

                            </div>
                        </div>
                        @php
                        $favicon='';
                        if($response['result']['settings'] !=null){
                        $favicon=config('app.api_asset_url').$response['result']['settings']['favicon'];
                        }
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group  mb-3">
                                <label class="form-label" for="favicon">Favicon</label>
                                <div class="js--image-preview"
                                    style="background-image: url(<?php echo $favicon; ?>); background-color: #F5F5F5;">
                                </div>
                                <input type="file" class="form-control" name="favicon" id="favicon"
                                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                                <small>Please upload file size 50 X 50 (Pixels)</small>
                                <img id="favicon_show_image" width='100%' src="">
                                <input id="imagedata_favicon" type="hidden" class="form-control input-border-bottom"
                                    name="imagedata_favicon" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group  mb-3">
                                <label class="form-label" for="background_image">Background Image</label>
                                <div class="js--image-preview"
                                    style="background-image: url(<?php echo config('app.api_asset_url') . $response['result']['tenantInfo']['background_image'] ?? ''; ?>); background-color: #F5F5F5;">
                                </div>
                                <input type="file" class="form-control" name="background_image" id="background_image"
                                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                                <small>Please upload file size 1024 X 768 (Pixels)</small>

                                <img id="bg_show_image" width='100%' src="">
                                <input id="imagedata_bg" type="hidden" class="form-control input-border-bottom"
                                    name="imagedata_bg" value="">
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-secondary col-xl-4 col-lg-6 col-md-12 col-sm-12"
                            onclick="cropme();">Update Settings</button>
                    </div>
                </form>
            </div> <!-- end card body-->
        </div>
    </div>
</div>

@endsection
@section('pagescript')
<script src="{{ asset('rcrop/dist/rcrop.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#logo").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParamLogo = $.extend({}, doCrop_params_default);
                cropParamLogo['file'] = this.files[0];
                cropParamLogo['imageId'] = "logo_show_image";
                cropParamLogo['dataImageId'] = "imagedata_logo";
                cropParamLogo['requiredImageWidth'] = 300;
                cropParamLogo['requiredImageHeight'] = 300;
                cropParamLogo['previewImageWidth'] = 100;
                cropParamLogo['previewImageHeight'] = 100;

                doCrop(cropParamLogo);
            }
        }
    });
    $("#favicon").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParamFavicon = $.extend({}, doCrop_params_default);
                cropParamFavicon['file'] = this.files[0];
                cropParamFavicon['imageId'] = "favicon_show_image";
                cropParamFavicon['dataImageId'] = "imagedata_favicon";
                cropParamFavicon['requiredImageWidth'] = 50;
                cropParamFavicon['requiredImageHeight'] = 50;
                cropParamFavicon['previewImageWidth'] = 100;
                cropParamFavicon['previewImageHeight'] = 100;

                doCrop(cropParamFavicon);
            }
        }
    });
    $("#background_image").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParam = $.extend({}, doCrop_params_default);
                cropParam['file'] = this.files[0];
                cropParam['imageId'] = "bg_show_image";
                cropParam['dataImageId'] = "imagedata_bg";
                cropParam['requiredImageWidth'] = 1024;
                cropParam['requiredImageHeight'] = 768;
                cropParam['previewImageWidth'] = 100;
                cropParam['previewImageHeight'] = 75;

                doCrop(cropParam);
            }
        }
    });
});


function cropme() {
    var srcOriginal;
    if (document.getElementById('logo').files.length >0){
        srcOriginal = $('#logo_show_image').rcrop('getDataURL',300,300);
        $('#imagedata_logo').val(srcOriginal);
    }
    if (document.getElementById('favicon').files.length >0){
        srcOriginal = $('#favicon_show_image').rcrop('getDataURL',50,50);
        $('#imagedata_favicon').val(srcOriginal);
    }
    if (document.getElementById('background_image').files.length >0){
        srcOriginal = $('#bg_show_image').rcrop('getDataURL',1024,768);
        $('#imagedata_bg').val(srcOriginal);
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
