@php
    $typeOfExam='';
    $paginationRouteName='';
    if($examination_type=='Q'){
        $typeOfExam='Quiz';
        $paginationRouteName='tut_quiz_submitted';
    }
    if($examination_type=='A'){
        $typeOfExam='Assessment';
        $paginationRouteName='tut_assessment_submitted';
    }
    $pagetitle='Review '. ($typeOfExam!=''?$typeOfExam.' ':'').'Submissions';
@endphp
@extends('layouts.default')
@section('title', $pagetitle)
@section('pagecss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">

                    <i ></i> {{$pagetitle}}
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="float-end">
                    <form>
                        @csrf
                        <div class="input-group input-group-sm" style="width: 330px;">
                            <input type="text" name="search_text" value="{{$search_text}}"
                                class="form-control pull-right" placeholder="Search text...">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    Search
                                    <!-- <i class="fa fa-search"></i> -->
                                </button>
                                <a class="btn btn-light"
                                    href="{{route($paginationRouteName,Session()->get('tenant_info')['subdomain'])}}">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body admin_content">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped  nowrap" width="100%">
                        <thead>
                            <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                                <th width="20%">{{$typeOfExam!=''?$typeOfExam.' ':''}}Name</th>
                                <th width="20%">Student name</th>

                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $showData=0;  @endphp
                            @if(count($response['result']['listing']['data'])>0)
                            @foreach($response['result']['listing']['data'] as $record)
                            @if($record['examination'] !=null) @php $showData=1;  @endphp
                            <tr>
                                <td>{{$record['examination']['name']}}</td>
                                <td>{{$record['consumer']['first_name']}} {{$record['consumer']['last_name']}}</td>
                                <td>
                                @if($examination_type=='Q')
                                    <a href="{{route('tut_quiz_review',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['user_result_id'])])}}"><i class="fa fa-eye"></i></a>
                                @endif
                                @if($examination_type=='A')
                                    <a href="{{route('tut_assessment_review',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['user_result_id'])])}}"><i class="fa fa-eye"></i></a>
                                @endif
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            @else
                            <tr>
                                <td colspan="9" class="text-center">No data found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if(isset($current_page) && $showData>0)
                <div class="row">
                    <div class="col-md-6">Page : {{$current_page}} of {{$numOfpages}}</div>
                    <div class="col-md-6">
                        <nav class="float-end">
                            <ul class="pagination">
                                @if(($has_next_page == true) && ($has_previous_page == false))
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route($paginationRouteName,Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route($paginationRouteName,Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route($paginationRouteName,Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route($paginationRouteName,Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>

                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
