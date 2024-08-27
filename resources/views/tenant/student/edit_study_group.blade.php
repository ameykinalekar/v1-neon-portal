@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
<style type="text/css">
    .fancybox__container {
        z-index: 10000 !important;
    }
</style>
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('tus_updatestudygroup',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="study_group_id" id="study_group_id" value="{{$details['study_group_id']??''}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="name">Study Group Name</label>
            <input type="text" class="form-control" id="name" name="name" required value="{{$details['name']}}">
        </div>

        <div class="form-group mb-1">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5">{{$details['description']}}</textarea>
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mb-1">
            <label for="group_image">Group Image</label>
            <div class="custom-file-upload">
                <input type="file" class="form-control" id="group_image" name="group_image"
                    accept="image/x-png,image/jpeg,image/png;capture=camera">
                @if($details['group_image']!='')
                <span>
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Group Image"
                        href="{{config('app.api_asset_url') . $details['group_image']}}"><img
                            style="padding-top: 13px;"
                            src="{{config('app.api_asset_url') . $details['group_image']}}" height="auto"
                            width="70px" /></a>
                </span>
                @endif
                <img id="imgshow_group_image" width='100%'>
                <input id="imagedata_group_image" type="hidden" name="imagedata_group_image" value="">
                <input id="req_width_group_image" type="hidden" name="req_width_group_image" value="200">
                <input id="req_height_group_image" type="hidden" name="req_height_group_image" value="200">
            </div>

        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit" onclick="cropme();">Save
                Group</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script src="{{ asset('rcrop/dist/rcrop.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();

    $("#group_image").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "imgshow_group_image";
                cropParams['dataImageId'] = "imagedata_group_image";
                cropParams['requiredImageWidth'] = $('#req_width_group_image').val();
                cropParams['requiredImageHeight'] = $('#req_height_group_image').val();

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
    if (document.getElementById('imgshow_group_image').src == '') {} else {
        var requiredWidth = $('#req_width_group_image').val();
        var requiredHeiht = $('#req_height_group_image').val();
        var srcOriginal = $('#imgshow_group_image').rcrop('getDataURL', requiredWidth, requiredHeiht);
        $('#imagedata_group_image').val(srcOriginal);
        $('#group_image').val('');
    }
    console.log(srcOriginal);
    //document.forms[0].submit();
}
</script>
@endsection