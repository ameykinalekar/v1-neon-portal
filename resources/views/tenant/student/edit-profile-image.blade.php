@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
<link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css"
    />
<style type="text/css">
    .fancybox__container {
        z-index: 10000 !important;
    }
</style>
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('tus_updatestudentpi',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="student_id" id="student_id" value="{{$student_details['user_id']??''}}">
    <div class="form-row">

        <div class="form-group mb-1">
            <label for="profile_image">Profile Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="profile_image" name="profile_image"
                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                <h6 id="showSize"></h6>
                @if($student_details['user_logo']!='')
                <span>
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"
                        href="{{config('app.api_asset_url') . $student_details['user_logo']}}"><img
                            style="padding-top: 13px;"
                            src="{{config('app.api_asset_url') . $student_details['user_logo']}}" height="auto"
                            width="70px" /></a>
                </span>
                @endif
                <img id="imgshowactualpic" width='100%'>
                <input id="imagedata_profile_image" type="hidden" name="imagedata_profile_image" value="">
                <input id="req_width_profile_image" type="hidden" name="req_width_profile_image" value="300">
                <input id="req_height_profile_image" type="hidden" name="req_height_profile_image" value="300">
            </div>

        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" onclick="cropme();">Update
                 Profile Image</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script src="{{ asset('rcrop/dist/rcrop.min.js') }}"></script>
<script src="{{ asset('rcrop/dist/rcrop-onload.min.js') }}"></script>


<script type="text/javascript">
$(document).ready(function() {
    $(document).ready(function() {
        var width = document.getElementById("req_width_profile_image").value;
        var height = document.getElementById("req_height_profile_image").value;
        document.getElementById("showSize").innerHTML = "Please upload file size " + width + "X" + height + "(Pixels)";
    });
    initailizeSelect2();



    $("#profile_image").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "imgshowactualpic";
                cropParams['dataImageId'] = "imagedata_profile_image";
                cropParams['requiredImageWidth'] = $('#req_width_profile_image').val();
                cropParams['requiredImageHeight'] = $('#req_height_profile_image').val();

                doCrop(cropParams);
            }
        }
    });
});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({
        dropdownParent: $("#right-modal")
    });
}

function cropme() {
    if (document.getElementById('imgshowactualpic').src == '') {} else {
        var requiredWidth = $('#req_width_profile_image').val();
        var requiredHeight = $('#req_height_profile_image').val();
        var srcOriginal = $('#imgshowactualpic').rcrop('getDataURL', requiredWidth,requiredHeight);
        $('#imagedata_profile_image').val(srcOriginal);
        $('#profile_image').val('');
    }
    console.log(srcOriginal);
    //document.forms[0].submit();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
