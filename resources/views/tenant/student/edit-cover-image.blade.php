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
    action="{{route('tus_updatestudentci',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="student_id" id="student_id" value="{{$student_details['user_id']??''}}">
    <div class="form-row">

        <div class="form-group mb-1">
            <label for="cover_picture">Cover Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="cover_picture" name="cover_picture"
                    accept="image/x-png,image/jpeg,image/png;capture=camera" required><h6 id="showSize"></h6>

                @if($student_details['cover_picture']!='')
                <span>
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Cover Image"
                        href="{{config('app.api_asset_url') . $student_details['cover_picture']}}"><img
                            style="padding-top: 13px;"
                            src="{{config('app.api_asset_url') . $student_details['cover_picture']}}" height="auto"
                            width="70px" /></a>
                </span>
                @endif
                <img id="imgshowactualpic" width='100%'>
                <input id="imagedata_cover_picture" type="hidden" name="imagedata_cover_picture" value="">
                <input id="req_width_cover_picture" type="hidden" name="req_width_cover_picture" value="1678">
                <input id="req_height_cover_picture" type="hidden" name="req_height_cover_picture" value="1068">
            </div>

        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" onclick="cropme();">Update
                 Cover Image</button>
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
    initailizeSelect2();
    var width = document.getElementById("req_width_cover_picture").value;
    var height = document.getElementById("req_height_cover_picture").value;
    document.getElementById("showSize").innerHTML = "Please upload file size "+width +"X" +height +"(Pixels)" ;


    $("#cover_picture").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "imgshowactualpic";
                cropParams['dataImageId'] = "imagedata_cover_picture";
                cropParams['requiredImageWidth'] = $('#req_width_cover_picture').val();
                cropParams['requiredImageHeight'] = $('#req_height_cover_picture').val();

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
        var requiredWidth = $('#req_width_cover_picture').val();
        var requiredHeiht = $('#req_height_cover_picture').val();
        var srcOriginal = $('#imgshowactualpic').rcrop('getDataURL', requiredWidth,requiredHeiht);
        $('#imagedata_cover_picture').val(srcOriginal);
        // $('#cover_picture').val('');
    }
    console.log(srcOriginal);
    //document.forms[0].submit();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
