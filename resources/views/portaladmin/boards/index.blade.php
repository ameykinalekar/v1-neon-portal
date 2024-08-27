@extends('layouts.default')
@section('title', 'All Boards')
@section('pagecss')

@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    <i ></i> All Boards
                </h4>
                <button type="button" class="btn btn-outline-primary btn-rounded align-middle mt-1 float-end"
                    onclick="rightModal('{{route('pa_addboard')}}', 'Add Board')"> <i class="mdi mdi-plus"></i> Add
                    Board</button>
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
                        <input type="text" name="search_text" value="{{$search_text}}" class="form-control pull-right"
                            placeholder="Search text...">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-primary">
                                Search
                                <!-- <i class="fa fa-search"></i> -->
                            </button>
                            <a class="btn btn-light" href="{{route('pa_boardlist')}}">Reset</a>
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
                            <th >Name</th>
                            <th >Short Name</th>
                            <th>Description</th>
                            <th >Country</th>
                            <th >Status</th>
                            <th >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($response['result']['boards']['data'])>0)
                        @foreach($response['result']['boards']['data'] as $record)
                        <tr>
                            <td>{{$record['board_name']}}</td>
                            <td>{{$record['short_name']}}</td>
                            <td>{{$record['description']}}</td>
                            <td>{{$record['country']['name']??''}}</td>
                            <td><div class="status status-{{strtolower($record['status'])}}">
                                    {{$record['status']}}
                                </div></td>
                            <td><a href="javascript:void(0);" title="Edit Board" onclick="rightModal('{{route('pa_editboard',\Helpers::encryptId($record['board_id']))}}', 'Edit Board')"><i class="fa fa-pencil"></i></a></td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5" class="text-center">No data found.</td>
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
                                        href="{{route('pa_boardlist').'/?search_text='.$search_text.'&page='.$next_page}}"><span
                                            aria-hidden="true">&raquo;</span></a></li>
                                @elseif(($has_next_page == false) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('pa_boardlist').'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                @elseif(($has_next_page == true) && ($has_previous_page == true))
                                <li class="page-item"><a class="page-link" title="Previous Page"
                                        href="{{route('pa_boardlist').'/?search_text='.$search_text.'&page='.$prev_page}}"><span
                                            aria-hidden="true">&laquo;</span></a></li>
                                <li class="page-item"><a class="page-link" title="Next Page"
                                        href="{{route('pa_boardlist').'/?search_text='.$search_text.'&page='.$next_page}}"><span
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

@endsection
