@extends('layouts.default')
@section('title', 'Course Completion Status')
@section('pagecss')
<style>
#btnContainer {
    float: inline-end;
}
th, td {
    padding: 0px;
    align-items: center;
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
                    <h4><i></i> Course Completion Status</h4>
                </div>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body admin_content">
                <div class="table-responsive" >
                    @if(count($listing)>0)
                    <table width="100%" style="display:flex">
                        @foreach($listing as $record)
                        <tr>
                            <td>
                                <table width="100%" class="table-borderless" style="table-layout:fixed;caption-side: bottom;border-collapse: separate;border-spacing: 5px;">
                                    <tr style="">
                                        <td width="50px">
                                            {{$record['subject_name']}}<br><small>{{count($record['lessons'])}} Lessons</small>
                                        </td>
                                        @foreach($record['lessons'] as $lrec)
                                        @php
                                            $fill=0;
                                            if($lrec['quizcnt']>0 || $lrec['assesmentcnt']>0 || $lrec['attendancecnt']>0){
                                                $fill=1;
                                            }
                                        @endphp
                                        @if($fill>0)
                                        <td width="2%" height="56px" style="background-color:#4C94DB;"></td>
                                        @else
                                        <td width="2%" height="56px" style="background-color:#F0F0F0;"></td>
                                        @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td width="50px">
                                            Status
                                        </td>
                                        @foreach($record['lessons'] as $lrec)
                                        @php
                                            $fill=0;
                                            if($lrec['quizcnt']>0 || $lrec['assesmentcnt']>0 || $lrec['attendancecnt']>0){
                                                $fill=1;
                                            }
                                        @endphp
                                        @if($fill>0)
                                        <td width="2%" height="18px" style="background-color:#5BC2B9;"></td>
                                        @else
                                        <td width="2%" height="18px" style="background-color:#F0F0F0;"></td>
                                        @endif

                                        @endforeach
                                        <hr/>

                                    </tr>
                     </table>
                            </td>


                        </tr>

                        @endforeach
                    </table>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
initDataTable('basic-datatable');
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
