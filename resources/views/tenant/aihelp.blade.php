@extends('layouts.default')
@section('title', 'Homework')
@section('pagecss')
<style>
    #btnContainer {
    float: inline-end;
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <div class="page-title">
                    <h4><i ></i> AI Help 24x7</h4>
                    
                </div>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body admin_content">
                <iframe src="{{$ai_url}}" frameborder="0" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
@endsection
