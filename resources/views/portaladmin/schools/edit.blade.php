@extends('layouts.default')
@section('pagecss')

<link href="{{asset('rcrop/dist/rcrop.min.css')}}" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
<style type="text/css">
.fancybox__container {
    z-index: 10000 !important;
}

</style>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> Edit School
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body admin_content">
                <form method="POST" class="d-block" action="{{route('pa_updateschool')}}" id="frmPage">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$school_details['user_id']??''}}">
                    <input type="hidden" name="old_trustee_id" id="old_trustee_id"
                        value="{{$school_details['trustee_id']??''}}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="school_name">School Name</label>
                                <input type="text" value="{{$school_details['first_name']??''}}" class="form-control"
                                    id="school_name" name="school_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="short_name">Short Number</label>
                                <input type="text" value="{{$school_details['short_name']??''}}" class="form-control"
                                    id="short_name" name="short_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control validate-phone" id="phone" name="phone" onkeypress="return isPhone(event);" value="{{$school_details['phone']??''}}" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"><small><i>10 digit telephone number with no dashes or dots.</i></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="country_id">Country</label>
                                <select class="form-control select2_el" id="country_id" name="country_id" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $record)
                                    <option value="{{$record['country_id']}}">{{$record['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address"
                                    rows="5">{{$school_details['address']??''}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="trustee">Trustee</label><br>
                                <div class="col-md-10 float-start">
                                <select name="trustee_id" id="trustee_id" class="form-control  select2_el"
                                    data-toggle="select2">
                                    <option value="">Select School Trustee</option>
                                </select>
                                </div>
                                <div class="col-md-2 float-end ">
                                    <a title="refresh trustee list" href="javascript:void(0);" onclick="onPageLoad();"><i class="fa fa-refresh mx-2 my-2"></i></a>
                                    <a title="Add trustee" href="javascript:void(0);" onclick="rightModal('{{route('pa_addtrusteefs')}}', 'Add Trustee')"><i class="fa fa-plus my-2 mx-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="customer_name">Customer Name</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{$school_details['customer_name']??''}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="company_address">Company Address</label>
                                <textarea class="form-control" id="company_address" name="company_address" rows="2">{{$school_details['company_address']??''}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="logo">Add Logo</label>
                                <input type="file" class="form-control validate-file" name="logo" id="logo" accept="image/x-png,image/jpeg,image/png;capture=camera">
                                @if($school_details['logo']!='')
                                <span>
                                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Background Image"
                                        href="{{config('app.api_asset_url') . $school_details['logo']}}"><img
                                            style="padding-top: 13px;"
                                            src="{{config('app.api_asset_url') . $school_details['logo']}}"
                                            height="auto" width="70px" /></a>
                                </span>
                                @endif
                                <img id="logo_show_image" width='100%' src=''>
                                <input id="imagedata_logo" type="hidden" class="form-control input-border-bottom"
                                    name="imagedata_logo" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="logo">Add Background Image</label>
                                <input type="file" class="form-control validate-file" name="background_image" id="background_image" accept="image/x-png,image/jpeg,image/png;capture=camera">
                                @if($school_details['background_image']!='')
                                <span>
                                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Background Image"
                                        href="{{config('app.api_asset_url') . $school_details['background_image']}}"><img
                                            style="padding-top: 13px;"
                                            src="{{config('app.api_asset_url') . $school_details['background_image']}}"
                                            height="auto" width="70px" /></a>
                                </span>
                                @endif
                                <img id="bg_show_image" width='100%' src=''>
                                <input id="imagedata_bg" type="hidden" class="form-control input-border-bottom"
                                    name="imagedata_bg" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="short_name">Status</label>
                                {{ Form::select('status',$status, $school_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
                            </div>
                        </div>
                        @php
                            $contact_persons=$school_details['contact_persons']??'';
                            if($contact_persons!=''){
                                $contact_persons=json_decode($contact_persons);
                            }else{
                                $contact_persons=array();
                            }

                            $technical_poc=$school_details['technical_poc']??'';
                            if($technical_poc!=''){
                                $technical_poc=json_decode($technical_poc);
                            }else{
                                $technical_poc=array();
                            }
                            //dd($technical_poc);
                            $customer_service_contact=$school_details['customer_service_contact']??'';
                            if($customer_service_contact!=''){
                                $customer_service_contact=json_decode($customer_service_contact);
                            }else{
                                $customer_service_contact=array();
                            }

                            $billing_contact=$school_details['billing_contact']??'';
                            if($billing_contact!=''){
                                $billing_contact=json_decode($billing_contact);
                            }else{
                                $billing_contact=array();
                            }

                        @endphp
                        <h5>Contact Person Information</h5>
                        <div id="cp_fields" class="optsec">
                            @if(!empty($contact_persons))
                            @foreach($contact_persons as $k=>$option)
                            <div class="form-group removeclasscp{{$k}}">
                                <div class="row mb-2">
                                    <div class="col-md-2">
                                        <select class="form-control select2_el" name="cp_salutation[]">
                                            <option value="">Select Salutation</option>
                                            @foreach($salutations as $record)
                                            <option value="{{$record['salutation']}}" @php if(isset($option->salutation) && $record['salutation']==$option->salutation){ @endphp selected @php } @endphp>{{$record['salutation']}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="cp_name[]" value="{{$option->name??''}}" placeholder="Name">

                                    </div>
                                    <div class="col-md-3">
                                        <input type="email" class="form-control" name="cp_email[]" value="{{$option->email??''}}" placeholder="Email">

                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control validate-phone" name="cp_phone[]"
                                            onkeypress="return isPhone(event);" value="{{$option->phone??''}}" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}">

                                    </div>

                                    <div class="col-md-2">
                                        <button class="btn btn-sm btn-success" type="button" onclick="addOptionRowCP();"> <i class="fa fa-plus"></i> </button>
                                        @if($k>0)
                                        <button class="btn btn-sm btn-danger" type="button" onclick="removeOptionRowCP('{{$k}}');"><i class="fa fa-minus"></i></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="row mb-2">
                                <div class="col-md-2">
                                    <select class="form-control select2_el" name="cp_salutation[]">
                                        <option value="">Select Salutation</option>
                                        @foreach($salutations as $record)
                                        <option value="{{$record['salutation']}}">{{$record['salutation']}}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="cp_name[]" placeholder="Name">

                                </div>
                                <div class="col-md-3">
                                    <input type="email" class="form-control" name="cp_email[]" placeholder="Email">

                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control validate-phone" name="cp_phone[]"
                                        onkeypress="return isPhone(event);" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}">

                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-success" type="button" onclick="addOptionRowCP();"> <i
                                            class="fa fa-plus"></i> </button>
                                </div>
                            </div>
                            @endif
                        </div>
                        <h5>Technical POC Information</h5>
                        <div id="tpoc_fields" class="optsec">
                            @if(!empty($technical_poc))
                            @foreach($technical_poc as $k=>$option)
                            <div class="form-group removeclasstpoc{{$k}}">
                                <div class="row mb-2">
                                    <div class="col-md-2">
                                        <select class="form-control select2_el" name="tpoc_salutation[]">
                                            <option value="">Select Salutation</option>
                                            @foreach($salutations as $record)
                                            <option value="{{$record['salutation']}}" @php if(isset($option->salutation) && $record['salutation']==$option->salutation){ @endphp selected @php } @endphp>{{$record['salutation']}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="tpoc_name[]" value="{{$option->name??''}}" placeholder="Name">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="email" class="form-control" name="tpoc_email[]" value="{{$option->email??''}}" placeholder="Email">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control validate-phone" name="tpoc_phone[]" placeholder="Phone No."
                                            onkeypress="return isPhone(event);" value="{{$option->phone??''}}" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}">
                                    </div>

                                    <div class="col-md-2">
                                        <button class="btn btn-sm btn-success" type="button" onclick="addOptionRowTPOC();"> <i class="fa fa-plus"></i> </button>
                                        @if($k>0)
                                    <button class="btn btn-sm btn-danger" type="button" onclick="removeOptionRowTPOC('{{$k}}');"><i class="fa fa-minus"></i></button>
                                    @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="row mb-2">
                                <div class="col-md-2">
                                    <select class="form-control select2_el" name="tpoc_salutation[]">
                                        <option value="">Select Salutation</option>
                                        @foreach($salutations as $record)
                                        <option value="{{$record['salutation']}}">{{$record['salutation']}}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="tpoc_name[]" placeholder="Name">
                                </div>
                                <div class="col-md-3">
                                    <input type="email" class="form-control" name="tpoc_email[]" placeholder="Email">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control validate-phone" name="tpoc_phone[]" placeholder="Phone No."
                                        onkeypress="return isPhone(event);" title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}">
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-success" type="button" onclick="addOptionRowTPOC();"> <i class="fa fa-plus"></i> </button>

                                </div>
                            </div>
                            @endif
                        </div>
                        <h5>Customer Service Contact Information</h5>
                        <div id="csc_fields" class="optsec">
                            @if(!empty($customer_service_contact))
                            @foreach($customer_service_contact as $k=>$option)
                            <div class="form-group removeclasscsc{{$k}}">
                                <div class="row mb-2">
                                    <div class="col-md-2">
                                        <select class="form-control select2_el" name="csc_salutation[]">
                                            <option value="">Select Salutation</option>
                                            @foreach($salutations as $record)
                                            <option value="{{$record['salutation']}}" @php if(isset($option->salutation) && $record['salutation']==$option->salutation){ @endphp selected @php } @endphp>{{$record['salutation']}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="csc_name[]" value="{{$option->name??''}}" placeholder="Name">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="email" class="form-control" name="csc_email[]" value="{{$option->email??''}}" placeholder="Email">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control validate-phone" name="csc_phone[]"
                                            onkeypress="return isPhone(event);" value="{{$option->phone??''}}" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}">
                                    </div>

                                    <div class="col-md-2">
                                        <button class="btn btn-sm btn-success" type="button" onclick="addOptionRowCSC();"> <i class="fa fa-plus"></i> </button>
                                        @if($k>0)
                                        <button class="btn btn-sm btn-danger" type="button" onclick="removeOptionRowCSC('{{$k}}');"><i class="fa fa-minus"></i></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="row mb-2">
                                <div class="col-md-2">
                                    <select class="form-control select2_el" name="csc_salutation[]">
                                        <option value="">Select Salutation</option>
                                        @foreach($salutations as $record)
                                        <option value="{{$record['salutation']}}">{{$record['salutation']}}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="csc_name[]" placeholder="Name">
                                </div>
                                <div class="col-md-3">
                                    <input type="email" class="form-control" name="csc_email[]" placeholder="Email">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control validate-phone" name="csc_phone[]"
                                        onkeypress="return isPhone(event);" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}">
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-success" type="button" onclick="addOptionRowCSC();"> <i class="fa fa-plus"></i> </button>
                                </div>
                            </div>
                            @endif
                        </div>
                        <h5>Billing Contact Information</h5>
                        <div id="bc_fields" class="optsec">
                            @if(!empty($billing_contact))
                            @foreach($billing_contact as $k=>$option)
                            <div class="form-group removeclassbc{{$k}}">
                                <div class="row mb-2">
                                    <div class="col-md-2">
                                        <select class="form-control select2_el" name="bc_salutation[]">
                                            <option value="">Select Salutation</option>
                                            @foreach($salutations as $record)
                                            <option value="{{$record['salutation']}}" @php if(isset($option->salutation) && $record['salutation']==$option->salutation){ @endphp selected @php } @endphp>{{$record['salutation']}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="bc_name[]" value="{{$option->name??''}}" placeholder="Name">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="email" class="form-control" name="bc_email[]" value="{{$option->email??''}}" placeholder="Email">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control validate-phone" name="bc_phone[]"
                                            onkeypress="return isPhone(event);" value="{{$option->phone??''}}" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}">
                                    </div>

                                    <div class="col-md-2">
                                        <button class="btn btn-sm btn-success" type="button" onclick="addOptionRowBC();"> <i class="fa fa-plus"></i> </button>
                                        @if($k>0)
                                        <button class="btn btn-sm btn-danger" type="button" onclick="removeOptionRowBC('{{$k}}');"><i class="fa fa-minus"></i></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="row mb-2">
                                <div class="col-md-2">
                                    <select class="form-control select2_el" name="bc_salutation[]">
                                        <option value="">Select Salutation</option>
                                        @foreach($salutations as $record)
                                        <option value="{{$record['salutation']}}">{{$record['salutation']}}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="bc_name[]" placeholder="Name">
                                </div>
                                <div class="col-md-3">
                                    <input type="email" class="form-control" name="bc_email[]" placeholder="Email">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control validate-phone" name="bc_phone[]"
                                        onkeypress="return isPhone(event);" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}">
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-success" type="button" onclick="addOptionRowBC();"> <i class="fa fa-plus"></i> </button>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="form-group mt-2 col-md-12">
                            <button class="btn btn-block btn-primary" type="submit" id="btnSubmitCS"
                                onclick="cropme();">Update School</button>
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
    initailizeSelect2();
    onPageLoad();

    $("#logo").change(function() {
        if (this.files && this.files[0]) {
            if (this.files[0].type.match('image.*')) {
                //call doCrop
                var cropParams = $.extend({}, doCrop_params_default);
                cropParams['file'] = this.files[0];
                cropParams['imageId'] = "logo_show_image";
                cropParams['dataImageId'] = "imagedata_logo";
                cropParams['requiredImageWidth'] = 300;
                cropParams['requiredImageHeight'] = 300;
                cropParams['previewImageWidth'] = 100;
                cropParams['previewImageHeight'] = 100;

                doCrop(cropParams);
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

    $('#country_id').val("{{ $school_details['country_id'] ?? '' }}").trigger('change');
});

// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({

    });
}

function cropme() {
    if (document.getElementById('logo').files.length >0){
        var requiredWidth = 300;
        var requiredHeight = 300;
        var srcOriginal = $('#logo_show_image').rcrop('getDataURL', requiredWidth,requiredHeight);
        $('#imagedata_logo').val(srcOriginal);
    }
    if (document.getElementById('background_image').files.length >0){
        var requiredWidth = 1024;
        var requiredHeight = 768;
        var srcOriginal = $('#bg_show_image').rcrop('getDataURL', requiredWidth,requiredHeight);
        $('#imagedata_bg').val(srcOriginal);
    }
}

$('.validate-file').on('change', function() {
    console.log('no. of files'+this.files.length);
    var size;
    var allFilesValid=0;
    for(let i=0;i<this.files.length;i++){
        size = (this.files[i].size / 1024 / 1024).toFixed(2);
        if (size > 2){
            allFilesValid++;
        }

           console.log('File '+i+' Size:'+size);

    }
    if(allFilesValid>0){
        alert("Each File must be with in the size of 2 MB");
        $(this).val('');
        $('#is_form_valid').val('1');
    }else{
        $('#is_form_valid').val('0');
    }
});

function onPageLoad() {
    var token = "{{Session::get('usertoken')}}";
    var old_trustee_id = $("#old_trustee_id").val();
    // alert(token);
    $.ajax({
        url: "<?php echo config('app.api_base_url') . '/dropdown/trustees'; ?>",
        crossDomain: true,
        type: 'POST',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        headers: {
            Authorization: 'Bearer ' + token
        },
        success: function(response) {
            console.log(response.result.trustees);
            var option = '';
            $('#trustee_id').html('<option value="">Select School Trustee</option>');
            response.result.trustees.forEach(function(item) {
                if (old_trustee_id == item.id) {
                    $('#trustee_id').append(`<option value="` + item.id + `" selected>` + item
                        .trustee_name + `</option>`);
                } else {
                    $('#trustee_id').append(`<option value="` + item.id + `">` + item
                        .trustee_name + `</option>`);
                }
            });
        },
        error: function(response) {
            $('#trustee_id').html('<option value="">Select School Trustee</option>');
        },
    });
}

$('#btnSubmitCS').on('click', function() {
    var validPhoneInput=0;
    // var pattern=/[0-9]{10}/;
    const pattern = /^\+?[0-9]{10,12}$/;
    // let pattern== new RegExp('^\+?[0-9]{10,12}$');
    //validate-phone
    $(".validate-phone").each(function() {
        //check regex
        if($(this).val()!=''){
            if (!pattern.test($(this).val())) {
                // alert("not a match");
                $(this).addClass('is-invalid');
                validPhoneInput++;
            }else{
                $(this).removeClass('is-invalid');
            }
        }else{
            $(this).removeClass('is-invalid');
        }
    });
    if(validPhoneInput==0){
        $(this).attr('disabled', true);
        $('#frmPage').submit();
    }else{
        return false;
    }
});


var optionRowCP = '{{count($contact_persons)-1??0}}';
function addOptionRowCP() {

    optionRowCP++;
    var objTo = document.getElementById('cp_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclasscp"+optionRowCP);
    var rdiv = 'removeclasscp'+optionRowCP;

    divtest.innerHTML ='<div class="row mb-2"><div class="col-md-2"><select class="form-control select2_el" name="cp_salutation[]"><option value="">Select Salutation</option>@foreach($salutations as $record)<option value="{{$record["salutation"]}}">{{$record["salutation"]}}</option>@endforeach</select></div><div class="col-md-3"><input type="text" class="form-control" name="cp_name[]" placeholder="Name"></div><div class="col-md-3"><input type="email" class="form-control" name="cp_email[]" placeholder="Email"></div><div class="col-md-2"><input type="text" class="form-control validate-phone" name="cp_phone[]" onkeypress="return isPhone(event);" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"></div><div class="col-md-2"><button class="btn btn-sm btn-success" type="button" onclick="addOptionRowCP();"> <i class="fa fa-plus"></i> </button> <button class="btn btn-sm btn-danger" type="button" onclick="removeOptionRowCP('+ optionRowCP +');"><i class="fa fa-minus"></i></button></div></div>';

    objTo.appendChild(divtest);
    initailizeSelect2();
}
function removeOptionRowCP(rid) {
    $('.removeclasscp'+rid).remove();
}

var optionRowTPOC = '{{count($technical_poc)-1??0}}';
function addOptionRowTPOC() {

    optionRowTPOC++;
    var objTo = document.getElementById('tpoc_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclasstpoc"+optionRowTPOC);
    var rdiv = 'removeclasstpoc'+optionRowTPOC;

    divtest.innerHTML ='<div class="row mb-2"><div class="col-md-2"><select class="form-control select2_el" name="tpoc_salutation[]"><option value="">Select Salutation</option>@foreach($salutations as $record)<option value="{{$record["salutation"]}}">{{$record["salutation"]}}</option>@endforeach</select></div><div class="col-md-3"><input type="text" class="form-control" name="tpoc_name[]" placeholder="Name"></div><div class="col-md-3"><input type="email" class="form-control" name="tpoc_email[]" placeholder="Email"></div><div class="col-md-2"><input type="text" class="form-control validate-phone" name="tpoc_phone[]" onkeypress="return isPhone(event);" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"></div><div class="col-md-2"><button class="btn btn-sm btn-success" type="button" onclick="addOptionRowTPOC();"> <i class="fa fa-plus"></i> </button> <button class="btn btn-sm btn-danger" type="button" onclick="removeOptionRowTPOC('+ optionRowTPOC +');"><i class="fa fa-minus"></i></button></div></div>';

    objTo.appendChild(divtest);
    initailizeSelect2();
}
function removeOptionRowTPOC(rid) {
    $('.removeclasstpoc'+rid).remove();
}

var optionRowCSC = '{{count($customer_service_contact)-1??0}}';
function addOptionRowCSC() {

    optionRowCSC++;
    var objTo = document.getElementById('csc_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclasscsc"+optionRowCSC);
    var rdiv = 'removeclasscsc'+optionRowCSC;

    divtest.innerHTML ='<div class="row mb-2"><div class="col-md-2"><select class="form-control select2_el" name="csc_salutation[]"><option value="">Select Salutation</option>@foreach($salutations as $record)<option value="{{$record["salutation"]}}">{{$record["salutation"]}}</option>@endforeach</select></div><div class="col-md-3"><input type="text" class="form-control" name="csc_name[]" placeholder="Name"></div><div class="col-md-3"><input type="email" class="form-control" name="csc_email[]" placeholder="Email"></div><div class="col-md-2"><input type="text" class="form-control validate-phone" name="csc_phone[]" onkeypress="return isPhone(event);" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"></div><div class="col-md-2"><button class="btn btn-sm btn-success" type="button" onclick="addOptionRowCSC();"> <i class="fa fa-plus"></i> </button> <button class="btn btn-sm btn-danger" type="button" onclick="removeOptionRowCSC('+ optionRowCSC +');"><i class="fa fa-minus"></i></button></div></div>';

    objTo.appendChild(divtest);
    initailizeSelect2();
}
function removeOptionRowCSC(rid) {
    $('.removeclasscsc'+rid).remove();
}

var optionRowBC = '{{count($billing_contact)-1??0}}';
function addOptionRowBC() {

    optionRowBC++;
    var objTo = document.getElementById('bc_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclassbc"+optionRowBC);
    var rdiv = 'removeclassbc'+optionRowBC;

    divtest.innerHTML ='<div class="row mb-2"><div class="col-md-2"><select class="form-control select2_el" name="bc_salutation[]"><option value="">Select Salutation</option>@foreach($salutations as $record)<option value="{{$record["salutation"]}}">{{$record["salutation"]}}</option>@endforeach</select></div><div class="col-md-3"><input type="text" class="form-control" name="bc_name[]" placeholder="Name"></div><div class="col-md-3"><input type="email" class="form-control" name="bc_email[]" placeholder="Email"></div><div class="col-md-2"><input type="text" class="form-control validate-phone" name="bc_phone[]" onkeypress="return isPhone(event);" placeholder="Phone No." title="Please use a 10 digit telephone number with no dashes or dots" pattern="\+?[0-9]{10,12}"></div><div class="col-md-2"><button class="btn btn-sm btn-success" type="button" onclick="addOptionRowBC();"> <i class="fa fa-plus"></i> </button> <button class="btn btn-sm btn-danger" type="button" onclick="removeOptionRowBC('+ optionRowBC +');"><i class="fa fa-minus"></i></button></div></div>';

    objTo.appendChild(divtest);
    initailizeSelect2();
}
function removeOptionRowBC(rid) {
    $('.removeclassbc'+rid).remove();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
