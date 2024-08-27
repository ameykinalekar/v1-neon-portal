@extends('layouts.default')
@section('title', 'Teacher Assistants')
@section('pagecss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> Teacher Assistants
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
                                    href="{{route('tut_students',Session()->get('tenant_info')['subdomain'])}}">Reset</a>
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
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Year Group</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <!-- <th width="8%">Action</th> -->
                            </tr>
                    </thead>
                    <tbody>
                        @if(count($response['result']['details']['data'])>0)
                        @foreach($response['result']['details']['data'] as $record)
                        @php
                            $yrgrp=array();
                            $subjects=array();
                            if($record['year_group_names']??''!=''){
                                $yrgrp=explode(',',$record['year_group_names']);
                            }
                            if($record['subject_names']??''!=''){
                                $subjects=explode(',',$record['subject_names']);
                            }
                        @endphp
                        <tr>
                                <td>
                                    @if($record['user_logo']!='')
                                    <span>
                                        <a class="fancy-box-a" data-fancybox="demo"
                                            data-caption="{{$record['first_name'].' '.$record['last_name']}} - Profile Image"
                                            href="{{config('app.api_asset_url') . $record['user_logo']}}"><img
                                                src="{{config('app.api_asset_url') . $record['user_logo']}}" height="auto"
                                                width="35px" /></a>
                                    </span>
                                    @else
                                    <span>
                                        <img src="{{config('app.api_asset_url') . $no_image}}" height="auto" width="35px" />
                                    </span>
                                    @endif
                                </td>
                                <td>{{$record['first_name'].' '.$record['last_name']}}

                                </td>
                                <td>{{$record['email']}}</td>
                                <td>{{$record['phone']}}</td>
                                <td>
                                @if(count($yrgrp)>0)
                                <small>

                                @foreach($yrgrp as $yr)
                                <div>{{$yr}}</div>
                                @endforeach
                                </small>
                                @endif
                            </td>
                            <td>
                                @if(count($subjects)>0)
                                <small>
                                @foreach($subjects as $sub)
                                <div>{{$sub}}</div>
                                @endforeach
                                </small>
                                @endif
                            </td>

                                <td><div class="status status-{{strtolower($record['status'])}}">
                                        {{$record['status']}}
                                    </div></td>
                                <!-- <td>
                                    <a href="javascript:void(0);" title="Edit Teacher Assistant"
                                        onclick="rightModal('{{route('ta_editteacherassistant',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['user_id'])])}}', 'Edit Teacher Assistant')"><i
                                            class="fa fa-pencil"></i></a>
                                </td> -->
                            </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="9" class="text-center">No data found.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                </div>
                @if(isset($current_page))
                <div class="row">
                    <div class="col-md-6">Page : {{$current_page}} of {{$numOfpages}}</div>
                    <div class="col-md-6">
                        <nav class="float-end">
                            <ul class="pagination">
                                @if(($has_next_page == true) && ($has_previous_page == false))
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('tut_teacherassistant',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tut_teacherassistant',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('tut_teacherassistant',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('tut_teacherassistant',Session()->get('tenant_info')['subdomain']).'/?search_text='.$search_text.'&page='.$next_page}}"><span
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
