@extends('layouts.default')
@section('title', 'Library')
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
                    <h4><i></i> Library</h4>
                    <span id="btnContainer">
                        <button class="btn active" onclick="gridView()"><i class="fa fa-th-large"></i> Grid</button>
                        <button class="btn" onclick="listView()"><i class="fa fa-bars"></i> List</button>
                    </span>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
    <div class="row">
            @foreach($subject_list as $record)
            <div class="col-md-6 col-lg-2 my-2 px-2 grid">
                <div class="card" style="width: 110%; border-radius: 4px !important;">
                    @if($record['subject_image']!='')
                    <img src="{{config('app.api_asset_url') . $record['subject_image']}}" width="100%"
                        class="card-img-top" alt="..."
                        style="border-top-left-radius: 4px;border-top-right-radius: 4px;">
                    @else
                    <img src="{{config('app.api_asset_url') . $no_image}}" width="100%" class="card-img-top" alt="..."
                        style="border-top-left-radius: 4px;border-top-right-radius: 4px;">
                    @endif

                    <div class="card-body">
                        <h6 class="card-title" style="font-weight: normal;color:#434343">
                            {{$record['subject_name'] . ' - '.$shortboards[$record['board_id']]}}<br>
                            <small>{{$record['yeargroup'].' - '.$record['academic_year']}}</small>
                        </h6>
                        <a href="{{route('tut_mylibrarycontent',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['subject_id'])])}}"
                            class="btn w-100"
                            style="background: #5BC2B9;font-weight: normal;color:#fff;border-radius: 5px;">
                            Open
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="table-responsive list" style="width:100%">
            <table id="datatable" class="table table-striped  nowrap table " width="100%">
                <thead>
                    <tr style="background-color: rgba(90, 194, 185, 1); color: #ffffff;">
                        <th>Image</th>
                        <th>Subject</th>
                        <th>Year Group</th>
                        <th>Academic year</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subject_list as $record)

                    <tr>
                        <td>
                            @if($record['subject_image']!='')
                            <img height="50px" width="50px"
                                src="{{config('app.api_asset_url') . $record['subject_image']}}">
                            @else
                            <img height="50px" width="50px" src="{{config('app.api_asset_url') . $no_image}}">
                            @endif

                        </td>
                        <td>{{$record['subject_name']. ' - '.$shortboards[$record['board_id']]}}</td>
                        <td>{{$record['yeargroup']}}</td>
                        <td>{{$record['academic_year']}}</td>
                        <td>
                            <a href="{{route('tut_mylibrarycontent',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['subject_id'])])}}"
                                class="btn w-100"
                                style="background: #5BC2B9;font-weight: normal;color:#fff;border-radius: 5px;">
                                Open
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
function listView() {
    $('.grid').css('display', 'none');
    $('.list').css('display', 'inline-table');
}

function gridView() {
    $('.list').css('display', 'none');
    $('.grid').css('display', 'block');
}
</script>
<script>
initDataTable('datatable');
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
