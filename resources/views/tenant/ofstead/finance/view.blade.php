@extends('layouts.ajax')
@section('pagecss')

@endsection
@section('content')
<div class="row">
    
    <div class="col-12">
        <table id="basic-datatable" class="table table-striped  nowrap" width="100%">
            <thead>
                <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                    <th>Sub Indicator</th>
                    <th>Value</th>
                </tr>
            </thead>
            @if(count($listing)>0)
            @foreach($listing as $record)
            <tr>
                <td>{{$record['sub_indicator']['name']}}</td>
                <td>{{$record['value']}}</td>
            </tr>
            @endforeach
            @endif
        </table>
    </div>
</div>

@endsection
@section('pagescript')

@endsection