@extends('layouts.default')
@section('pagecss')
<style>
    .checkbox{
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        padding:10px 20px 0px 25px;
    }
    .modules { grid-area: modules; }
    .academics { grid-area: academics; }
    .course { grid-area: course; }
    .dashboard { grid-area: dashboard; }
    .user { grid-area: user; }

    .grid-container {
        display: grid;
        grid-template-areas:
            'modules academics course'
            'modules dashboard user' ;
        gap: 10px;
        padding: 10px;
    }
    /* Responsive Design */
    @media (max-width: 768px) {
        .grid-container {
            display: grid;
            grid-template-areas:
            'modules'
            'academics'
            'course'
            'dashboard'
            'user' ;
            gap: 10px;
            padding: 10px;
        }
    }
</style>
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title">Update Subscription Plan</h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end page title -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" class="d-block ajaxForm" action="{{route('pa_updatesubscriptionplan')}}">
                    @csrf
                    <input type="hidden" name="subscription_plan_id"
                        value="{{$plan_details['subscription_plan_id']??''}}">
                    <input type="hidden" name="module_list" value="{{ json_encode($module_list)}}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="plan_name">Plan Name</label>
                                <input type="text" class="form-control" id="plan_name" name="plan_name" required
                                    value="{{$plan_details['plan_name']??''}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="base_price">Base Price</label>
                                <input type="number" step="any" class="form-control" id="base_price" name="base_price" min="1"
                                    required value="{{$plan_details['base_price']??''}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="tax_percentage">Tax <small>(in %)</small></label>
                                <input type="number" step="any" class="form-control" id="tax_percentage" min="1"
                                    name="tax_percentage" required value="{{$plan_details['tax_percentage']??''}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="validity_indays">Validity <small>(in days)</small></label>
                                <input type="number" step="any" class="form-control" id="validity_indays" min="1"
                                    name="validity_indays" required value="{{$plan_details['validity_indays']??''}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="users_allowed">No. of Users Allowed</label>
                                <input type="number" step="any" min="1" class="form-control" id="users_allowed"
                                    name="users_allowed" required value="{{$plan_details['users_allowed']??''}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="short_name">Status</label>
                                {{ Form::select('status',$status, $plan_details['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="short_name">Description</label>
                                <textarea class="form-control" id="description" name="description"
                                    maxlength="200">{{$plan_details['description']??''}}</textarea>
                            </div>
                        </div>
                        <h5 class="mb-2">Modules</h5>
                        @php
                            $features_available=$plan_details['features_available']??'';
                            if($features_available!=''){
                                $features_available=json_decode($features_available);
                                $features_available = (array) $features_available;
                            }else{
                                $features_available=array();
                            }
                            //dd($features_available);
                        @endphp
                        <div  class="grid-container">
                        @foreach($module_list as $key=>$module)
                        <div class="col-md-12 m-2 {{$key}}" style="border:2px solid {{$module['color']}}">
                            <div class="form-group mb-1">
                                @if($module['name']!='')
                                <label for="{{$key}}" style="padding:0px 0px 0px 15px">{{$module['name']}}</label>
                                @endif

                                @foreach($module['sub_modules'] as $subkey=>$submodule)

                                <div class="row {{$key}}">
                                    <div class="col-md-12 checkbox">
                                        <label for="{{$subkey}}" style="font-weight: normal">{{$submodule}}</label>
                                        <input type="checkbox" id="{{$subkey}}" name="{{$subkey}}" @if(in_array($subkey, $features_available) && $features_available[$subkey]=='1') checked @endif value="1">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            </div>

                        @endforeach
                        </div>
                        <div class="form-group mt-2 col-md-12">
                            <button class="btn btn-block btn-primary" type="submit">Update Subscription Plan</button>
                        </div>
                    </div>
                </form>
            </div> <!-- end card body-->
        </div>
    </div>
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

    $(".select2_el").select2({
        dropdownParent: $("#right-modal")
    });
}
</script>
@endsection
