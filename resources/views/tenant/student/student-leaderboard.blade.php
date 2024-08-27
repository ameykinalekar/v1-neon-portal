@extends('layouts.default')
@section('title', 'Leaderboard')
@section('pagecss')
<style>

    .badge {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-size: cover;
    }

    .badge-chemistry {
        background-color: #00bcd4; /* Replace with the actual badge image or color */
    }

    .badge-biology {
        background-color: #ffc107; /* Replace with the actual badge image or color */
    }

    .badge-mathematics {
        background-color: #f44336; /* Replace with the actual badge image or color */
    }
    .alert{
        padding : 3px 15px;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
                  <h4>Leaderboard</h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<br/>
    <div class="float-end ">
        <a style="color:#6c757d" href="javascript:void(0);" onclick="rightModal('http://localhost:8001/heritage/student/attendances/filter?fp=eDJEeGpDZDlhSEp3UXVFcm1NTDgyeStTcXhNVlhkTFFiV0NTQnB4ZHArbG5yRmlvOUs0Smh1a04vanM0R2NGTU5ybHhORGxRMXVWMnNhaXQvQk1OU1M1WUtIT3BXQU85V1Exa1lpalBoYnA2UHlIZ0pyWU16c2Z5eCtmQ2FOYktGY1FVQ1J4dkxLaldBd0UxWmZpanBVa3RCbkptaXVKaHRyaXNCTFZEdjA2MDFsdGhMaVpvZkMwbmlKLzJmVm1NcGxCd2ZGWVB6dTl2UmZPRlVhMUZHQ210MzlBbXpIWXlCT000a0dHKzJMdz0=', 'Filter')">Filter <i class="fa fa-filter" aria-hidden="true"></i></a>
    </div>

    <br/>
<div class="row">
<div class="col-xl-12">
    <div class="row">
        <div class="col-xl-1">
        </div>
    <div class="col-md-8">
    <div class="table-responsive">
        <table id="datatable" class="table table-striped  nowrap" width="100%">
            <thead>
            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                <th>No</th>
                <th>Badge</th>
                <th>Subject</th>
                <th>Score</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td><span class="badge badge-chemistry"></span></td>
                <td>Chemistry</td>
                <td>100%</td>
            </tr>
            <tr>
                <td>23</td>
                <td><span class="badge badge-biology"></span></td>
                <td>Biology</td>
                <td>85%</td>
            </tr>
            <tr>
                <td>32</td>
                <td><span class="badge badge-mathematics"></span></td>
                <td>Mathematics</td>
                <td>70%</td>
            </tr>
            <!--<tr>
                <td colspan="4" class="text-center">No data found.</td>
            </tr>-->


            </tbody>
        </table>
    </div>
        <div class="col-xl-1">
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>


    @endsection
