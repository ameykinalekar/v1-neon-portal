@extends('layouts.default')
@section('title', 'My Schools')
@section('pagecss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> My Schools
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <!-- <div class="card-header">
                <div class="float-end">
                    <form>
                        @csrf
                        <div class="input-group input-group-sm" style="width: 330px;">
                            <input type="text" name="search_text" value="{{$search_text}}"
                                class="form-control pull-right" placeholder="Search text...">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    Search

                                </button>
                                <a class="btn btn-light"
                                    href="{{route('t_dashboard')}}">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div> -->
            <div class="card-body admin_content">
                <table id="basic-datatable" class="table table-striped  nowrap" width="100%">
                    <thead>
                        <tr style="background-color:rgba(90, 194, 185, 1); color: #ffffff;">
                            <th width="10%">Photo</th>
                            <th width="20%">School Name</th>
                            <th width="20%">Email</th>
                            <th width="20%">Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($response['result']['listing']['data'])>0)
                        @foreach($response['result']['listing']['data'] as $record)
                        <tr>
                        <td>
                            @if($record['user_logo']!='')
                                <span>
                                <a class="fancy-box-a" data-fancybox="demo" data-caption="Profile Image"  href="{{config('app.api_asset_url') . $record['user_logo']}}"><img src="{{config('app.api_asset_url') . $record['user_logo']}}" height="auto" width="35px" /></a>
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
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="text-center">No data found.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @if(isset($current_page))
                <div class="row">
                    <div class="col-md-6">Page : {{$current_page}} of {{$numOfpages}}</div>
                    <div class="col-md-6">
                        <nav class="float-end">
                            <ul class="pagination">
                                @if(($has_next_page == true) && ($has_previous_page == false))
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('t_dashboard').'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('t_dashboard',).'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('t_dashboard').'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('t_dashboard').'/?search_text='.$search_text.'&page='.$next_page}}"><span
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
