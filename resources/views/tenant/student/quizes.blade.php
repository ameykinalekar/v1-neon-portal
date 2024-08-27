@extends('layouts.default')
@section('title', 'Quizes')
@section('pagecss')

<style>
.card.main_top_overview_card {
    padding: 1px 20px !important;
}

.card-body {
    padding: 0px;
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

.lp-button.button.button-purchase-course {
    color: #fff;
    background-color: #ff9933;
    border-color: #ff9933;
    display: flow;
    padding: 6px 12px;
    margin-bottom: 0px;
    font-size: 14px;
    font-weight: normal;
    /*
    float: right;
    */
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid;
    border-radius: 4px;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
    -ms-transition: all 0.5s;
    -o-transition: all 0.5s;
    transition: all 0.5s;
}

.quizdisclaimer {
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
                   <h4> <i ></i> Quizes</h4>
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
            @foreach($listing['data'] as $record)
            @php
            //dd($record['subject']['yeargroup']['name']);
            @endphp
            <div class="col-md-6 col-lg-2 my-2 px-2 grid">
                <div class="card" style="width: 100%; border-radius: 4px !important;">
                    @if($record['subject']['subject_image']!='')
                    <img src="{{config('app.api_asset_url') . $record['subject']['subject_image']}}" width="100%"
                        class="card-img-top" alt="..."
                        style="border-top-left-radius: 4px;border-top-right-radius: 4px;">
                    @else
                    <img src="{{config('app.api_asset_url') . $no_image}}" width="100%" class="card-img-top" alt="..."
                        style="border-top-left-radius: 4px;border-top-right-radius: 4px;">
                    @endif

                    <div class="card-body">
                        <h6 class="card-title">
                            {{$record['name']}}<br><small>{{$record['subject']['subject_name']  }}<br><small> ({{$record['subject']['yeargroup']['name']}} : {{$record['subject']['academicyear']['academic_year']}})</small></small>
                        </h6>
                        <div class="lp-course-buttons">
                         @if($record['is_submitted'])
                         <a href="javascript:void(0);" data-examid="{{ \Helpers::encryptId($record['examination_id']) }}" data-url="<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-disclaimer-data'; ?>/{{ \Helpers::encryptId($record['examination_id']) }}" class=" lp-button button button-purchase-course">
                                Attempted
                            </a>
                         @else
                            <a href="javascript:void(0);" data-examid="{{ \Helpers::encryptId($record['examination_id']) }}" data-url="<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-disclaimer-data'; ?>/{{ \Helpers::encryptId($record['examination_id']) }}" class="start_quiz1 lp-button button button-purchase-course">
                                Open Test
                            </a>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="table-responsive" id="tableGrid">
        <table id="datatable" class="table table-striped  nowrap table list" width="100%">
            <thead>
                <tr style="background-color: rgba(90, 194, 185, 1); color: #ffffff;">
                    <th>Image</th>
                    <th>Subject</th>
                    <th>Year Group</th>
                    <th>Academic Year</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listing['data'] as $record)

                <tr>
                    <td>
                        @if($record['subject']['subject_image']!='')
                        <img height="50px" width="50px"
                            src="{{config('app.api_asset_url') . $record['subject']['subject_image']}}">
                        @else
                        <img height="50px" width="50px" src="{{config('app.api_asset_url') . $no_image}}">
                        @endif

                    </td>
                    <td>{{$record['subject']['subject_name']}}</td>
                    <td>{{$record['subject']['yeargroup']['name']}}</td>
                    <td>{{$record['subject']['academicyear']['academic_year']}}</td>
                    <td>{{$record['name']}}</td>
                    <td>
                    @if($record['is_submitted'])
                         <a href="javascript:void(0);" data-examid="{{ \Helpers::encryptId($record['examination_id']) }}" data-url="<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-disclaimer-data'; ?>/{{ \Helpers::encryptId($record['examination_id']) }}" class=" lp-button button button-purchase-course">
                                Attempted
                            </a>
                         @else
                            <a href="javascript:void(0);" data-examid="{{ \Helpers::encryptId($record['examination_id']) }}" data-url="<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-disclaimer-data'; ?>/{{ \Helpers::encryptId($record['examination_id']) }}" class="start_quiz1 lp-button button button-purchase-course">
                                Open Test
                            </a>
                        @endif
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
$('.start_quiz1').click(function() {
    url = $(this).data('url');
    examid = $(this).data('examid');
    disclaimerExamModal(url,examid);

    // //$(this).removeAttr('href');

    // $('.quizdisclaimer').css('display', 'block');

    // $('.start_quiz_btn').attr('href', thisquizurl);

    // console.log('thisquizurl= ' + thisquizurl);

});


$('.agree_disclaimer').click(function() {


    if ($(this).is(':checked')) {

        $('.start_quiz_btn').attr('href', thisquizurl);

    } else {

        $('.start_quiz_btn').attr('href', '');
    }

});

$('#tableGrid').css('display', 'none');

function listView() {
    $('.grid').css('display', 'none');
    $('.list').css('display', 'inline-table');
    $('#tableGrid').css('display', 'block');

}

function gridView() {
    $('.list').css('display', 'none');
    $('.grid').css('display', 'block');
    $('#tableGrid').css('display', 'none');

}
initDataTable('datatable');
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
