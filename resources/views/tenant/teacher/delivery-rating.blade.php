@extends('layouts.default')
@section('title', 'Teacher Delivery rating')
@section('pagecss')
<style>
body {
    background-color: #f9f9f9;
}

.card {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card-header {
    font-size: 1.25rem;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-body .row {
    display: flex;
    align-items: center;
}

.card-body .row img {
    width: 50px;
    height: 50px;
}

.card-body h5 {
    margin-bottom: 0;
}

.card-body p {
    margin-bottom: 5px;
}
#btnContainer {
        float: inline-end;

    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
            <h4>Delivery Rating 
            <span id="btnContainer">
                <a href="{{route('tut_myrating',Session()->get('tenant_info')['subdomain'])}}" class="btn btn-sm btn-default" title=""><i class="fa fa-backward"></i> Back</a>
            </span>
            </h4>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
<div class="card-body">
    <div class="card">
        @if(count($listing)>0)
        @foreach($listing as $k=>$record)
        @php
        $creator_rating=$record['creator_rating']??0;
        $creator_rating_outof=$record['creator_rating_outof']??5;

        $creator_rating_p=($creator_rating/$creator_rating_outof)*100;
        $creator_rating_p=round($creator_rating_p,0);

        $content_rating=$record['content_rating']??0;
        $content_rating_outof=$record['content_rating_outof']??5;

        $content_rating_p=($content_rating/$content_rating_outof)*100;
        $content_rating_p=round($content_rating_p,0);
        @endphp
        @if($k>0)
        <hr />
        @endif
        <div class="row mb-3">
            <div class="col-2">

                @if($record['student']['user_logo']!='')
                <span style="padding:10px">
                    <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"
                        href="{{config('app.api_asset_url') . $record['student']['user_logo']}}"><img
                            src="{{config('app.api_asset_url') . $record['student']['user_logo']}}"
                            class="rounded-circle" /></a>
                </span>
                @else
                <span style="padding:10px">
                    <img src="{{config('app.api_asset_url') . $no_image}}" class="rounded-circle" />
                </span>
                @endif
            </div>
            <div class="col-10">
                <h5>{{$record['student']['first_name']}} {{$record['student']['last_name']}}</h5>
                <p>{{$creator_rating_p}}%</p>
                <p>{{$record['creator_remarks']}}</p>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>



@endsection
@section('pagescript')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection