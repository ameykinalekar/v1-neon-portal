@extends('layouts.default')
@section('title', 'Subscribe Plan')
@section('pagecss')
<style>
    .scroll {
    width: 100%; height: 150px;
    overflow: overlay;
}    
</style>
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> Subscribe Plan -
                    {{$school_details['first_name']??''}}
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body admin_content">
            <div class="table-responsive">
                <table id="basic-datatable" class="table table-striped  nowrap" width="100%">
                    <thead>
                        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                            <th width="40%">Plan Name</th>
                            <th>Price</th>
                            <th>Validity <small>in days</small></th>
                            <th>Features</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($plans)>0)
                        @foreach($plans as $plan)
                        @php
                        $price=$plan['base_price'];
                        $tax=$plan['tax_percentage'];
                        $taxamt=round((($price*$tax)/100),2);
                        $final_price=$price + $taxamt;
                        $features_available=json_decode($plan['features_available']);
                        //dd($features_available);
                        @endphp
                        <tr>
                            <td>{{$plan['plan_name']}}</td>
                            <td>{{$final_price}}</td>
                            <td>{{$plan['validity_indays']}}</td>
                            <td><div class="scroll">
                                <small>
                                    <ul>
                                        
                                        @foreach($module_list as $key=>$module)
                        <li>
                                @if($module['name']!='')
                                <label for="{{$key}}">{{$module['name']}}</label>
                                @endif
                                @foreach($module['sub_modules'] as $subkey=>$submodule)
                                
                                <li>
                                            <label for="{{$key}}">{{$submodule}}</label> :
                                            {{$features_available->$subkey>0?'Yes':'No'}}
                                        </li>
                                @endforeach
                                            </li>
                        @endforeach
                                    </ul>
                                </small></div>
                            </td>
                            <td><button class="btn btn-outline-primary btn-rounded"
                                    onclick="rightModal('{{route('pa_schoolplansubscribe',[$user_id,\Helpers::encryptId($plan['subscription_plan_id'])])}}', 'Subscribe Plan')">Subscribe</button>
                            </td>
                        </tr>
                        @endforeach
                        @endif

                    </tbody>
                </table>

            </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')

@endsection
