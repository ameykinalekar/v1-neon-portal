@extends('layouts.default')
@section('title', 'Teacher Review')
@section('pagecss')
<style>
.rating {
    display: flex;
    direction: row-reverse;
    justify-content: center;
}

.rating input {
    display: none;
}

.rating label {
    cursor: pointer;
    width: 50px;
    background-size: contain;
    opacity: 1;
}

.rating input:checked~label,
.rating label:hover,
.rating label:hover~label {
    opacity: 0.5;
}

.editable-container {
    border: 1px solid #ccc;
    padding: 10px;
    height: 119px;
    overflow-y: auto;
    position: relative;
}

.editable {
    width: 100%;
    height: 100%;
    outline: none;
}

.button-container {
    position: absolute;
    bottom: 10px;
    right: 10px;
}

.btn {
    background: #5BC2B9 0% 0% no-repeat padding-box;
    border-radius: 5px;
    opacity: 1;
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
                    <h4><i></i> Teacher Review</h4>

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
                    <table id="datatable" class="table table-striped  nowrap table list" width="100%">
                        <thead>
                            <tr style="background-color: rgba(90, 194, 185, 1); color: #ffffff;">
                                <th>Lesson Name</th>
                                <th>Teacher Name</th>
                                <th>Rate the Teacher & Content</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($listing)>0)
                            @foreach($listing as $record)
                            <tr>
                                <td>{{$record['lesson']['lesson_name']??''}}</td>
                                <td>{{$record['creator']['first_name']??''}} {{$record['creator']['last_name']??''}}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary"
                                        onclick="questionModal('{{route('tus_addview_teacherrating',[Session()->get('tenant_info')['subdomain'],$record['created_by'],$record['lesson_id']])}}', 'Rating')">
                                        View
                                    </button>

                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    @endsection
    @section('pagescript')
    <script>
        initDataTable('datatable');
    </script>

    @endsection
