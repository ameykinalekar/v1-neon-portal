@extends('layouts.default')
@section('title', 'Targets')
@section('pagecss')

<style>
.card.main_top_overview_card {
    padding: 1px 20px !important;
}

.card-body {
    padding: 5px;
}

/* #btnContainer {
    text-align: right;
    margin-bottom: 15px;
    background: white;
} */
#btnContainer {
    float: inline-end;

}

.thead-dark {
    background: #5BC2B9;
}

.thead-dark th {
    color: white;
    font-weight: 600;
    font-size: 12px;
}

.list {
    display: none;
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
                    <h4><i></i> Targets</h4>

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
                    <table id="basic-datatable" class="table table-striped  nowrap table" width="100%">
                        <thead>
                            <tr style="background-color: rgba(90, 194, 185, 1); color: #ffffff;">
                                <th>Yr Group</th>
                                <th>Subject</th>
                                <th>Target</th>
                                <th>Target Date</th>
                                <th>Set Date</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($listing as $record)
                                <tr class="text-center">
                                    <td>{{$record['yeargroup']}}</td>
                                    <td>{{$record['subject_name']}}</td>
                                    <td>{{$record['target']}}%</td>
                                    <td>{{date('d-m-Y',strtotime($record['target_date']))}}</td>
                                    <td>{{date('d-m-Y',strtotime($record['set_date']))}}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('pagescript')
    <script>

// initDataTable('basic-datatable');


</script>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    @endsection