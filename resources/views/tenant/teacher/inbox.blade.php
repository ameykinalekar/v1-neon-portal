@extends('layouts.default')
@section('title', 'In Box')
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
                    <h4><i class="mdi mdi-mail title_icon"></i> In Box</h4>
                    
                </div>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body admin_content">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped  nowrap" width="100%">
                        <thead>
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Message</th>

                            </tr>
                        </thead>
                        <tbody>
                        @if(count($listing)>0)
                        @foreach($listing as $record)
                        @php
                            $modalTitle=$record['subject'].'<br><small>'.date('d-m-y',strtotime($record['created_at'])).'</small>';
                        @endphp
                        <tr>
                            <td @if($record['is_read']<1) style="font-weight:600;" @endif>{{$record['subject']}}</td>
                            <td @if($record['is_read']<1) style="font-weight:600;" @endif>{{date('d-m-y',strtotime($record['created_at']))}}</td>
                            <td @if($record['is_read']<1) style="font-weight:600;" @endif>
                               <a href="javascript:void(0);" title="View Message"
                               onclick="questionModal('{{route('view_message',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['message_id'])])}}', '{{$modalTitle}}')"> {!! \Helpers::excerpt(strip_tags($record['message']),50) !!}</a>
                            </td>
                            
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="3" class="text-center">No data found.</td>
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
<script>
initDataTable('datatable');



</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
