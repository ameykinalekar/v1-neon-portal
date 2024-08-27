@extends('layouts.default')
@section('title', 'All School Subscriptions')
@section('pagecss')

@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All School Subscriptions - {{$school_details['first_name']??''}}
                </h4>
                <a  class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    href="{{route('pa_schoolsubscribeplan',$user_id)}}"> <i class="mdi mdi-plus"></i> Add
                    Subscription</a>
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
                            <th width="40%">Subscription Name</th>
                            <th>Enrollment Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(count($plans)>0)
                        @foreach($plans as $plan)
                        <tr>
                        <td>{{$plan['plan_name']}}</td>
                        <td>{{$plan['start_date']}}</td>
                        <td>{{$plan['end_date']}}</td>
                        <td>
                            <div class="status status-{{strtolower($plan['status'])}}">
                                    {{$plan['status']}}
                                </div>
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
