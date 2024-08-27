@extends('layouts.default')
@section('title', 'All Countries')
@section('pagecss')

@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Countries
                </h4>
                <!-- <button type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    onclick="rightModal('{{route('pa_addboard')}}', 'Add Country')"> <i class="mdi mdi-plus"></i> Add
                    Country</button> -->
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
                            <th width="40%">Name</th>
                            <th width="12%">Code</th>
                            <th>Currency</th>
                            <th width="10%">Status</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($response['result']['listing'])>0)
                        @foreach($response['result']['listing'] as $record)
                        <tr>
                            <td>{{$record['name']}}</td>
                            <td>{{$record['code']}}</td>
                            <td>{{$record['currency_code']}}</td>
                            <td>
                                <div class="status status-{{strtolower($record['status'])}}">
                                    {{$record['status']}}
                                </div>
                            </td>
                            <td>
                                <a href="javascript:void(0);" title="Edit Country" onclick="rightModal('{{route('pa_editcountry',\Helpers::encryptId($record['country_id']))}}', 'Edit Country')"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5" class="text-center">No data found.</td>
                        </tr>
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
