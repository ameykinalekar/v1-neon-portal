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
.accordion-header {
    margin: 0;
}
.accordion-item{
    margin-bottom:8px;
}
.accordion-item:not(:first-of-type) {
    border-top-width: thin;
    border-top:1;
}
.rotate-180{
    transform: rotate(180deg);
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
                    <h4><i ></i> Library</h4>
                    <span id="btnContainer">
                        <a href="{{route('tut_mylibrarycontent',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($lesson_info['subject']['subject_id'])])}}"
                            class="btn btn-sm btn-default" title="My Courses"><i class="fa fa-backward"></i> Go Back</a>

                    </span>
                </div>
                <div class="row py-2">

                    <div class="col-md-4"><label for="">Subject</label> :
                        {{$lesson_info['subject']['subject_name']}} ({{$lesson_info['subject']['yeargroup']['name']}} -
                        {{$lesson_info['subject']['academicyear']['academic_year']}})</div>

                    <div class="col-md-4"><label for="">Board</label> :
                        {{$boards[$lesson_info['subject']['board_id']]}}</div>
                    <div class="col-md-4"><label for="">Lesson</label> :
                        {{$lesson_info['lesson_name']}}</div>

                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title" style="line-height: unset;">{{$lib_content_types[$content_type]}} Contents <a href="javascript:void(0);"
                                            onclick="rightModal('{{route('tut_addlibrarycontent',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($lesson_info['lesson_id']),'type='.$content_type])}}', 'Add new content to {{$lesson_info['lesson_number']}} - {{$lesson_info['lesson_name']}}')"
                                            class="btn btn-sm btn-default float-end"
                                            title="Add new content to this lesson"><i class="fa fa-plus"></i> Add new
                                            content</a></h4>
                                            <div class="table-responsive">
                <table id="basic-datatable" class="table table-striped nowrap" width="100%">
                    <thead>
                        <tr style="background-color: rgba(90, 194, 185, 1); color: #ffffff;">
                            <th>Title</th>
                            <th>Content</th>
                            <th width="15%">Created On</th>
                            <th width="15%">Updated On</th>
                            <th width="10%">Status</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php $supported_image = array('gif','jpg','jpeg','png'); $userInfo = Session::get('user'); @endphp

                    @foreach($listing as $k=>$record)
                    @php
                    if($record['content_type']!='U'){
                        $arrFile=explode('.',$record['content_file']);
                        $fileExtension=$arrFile[1];
                    }
                    @endphp
                        <tr>
                            <td>{{$record['title']}}</td>
                            <td>
                                @if($record['content_type']=='U')
                                <a class="fancy-box-a" data-fancybox="demo" data-caption="Content {{$record['title']}}"  href="{{$record['content_url']}}"><i class="fa fa-eye"></i> View Content</a>
                                @elseif($record['content_type']=='V')
                                <a class="fancy-box-a" data-fancybox="demo" data-caption="Content {{$record['title']}}"  href="{{config('app.api_asset_url') . '/'.$record['content_file']}}"><i class="fa fa-eye"></i> View Content</a>
                                @else
                                    @if(in_array($fileExtension,$supported_image))
                                        <a class="fancy-box-a btn btn-sm btn-default" data-fancybox="demo" data-caption="Content {{$record['title']}}"  href="{{config('app.api_asset_url') . '/'.$record['content_file']}}"><i class="fa fa-eye"></i> View Content</a>
                                    @else

                                        <a href="javascript:void(0);"
                                            onclick="fullModal('{{route('view_lib_file',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['library_id'])])}}', 'Library File')"
                                            class="btn btn-sm btn-default"
                                            title="Add new content to this lesson"><i class="fa fa-eye"></i> View Content</a>
                                    @endif
                                @endif

                            </td>
                            <td>{{date('d-M-Y',strtotime($record['created_at']))}}</td>
                            <td>{{date('d-M-Y',strtotime($record['updated_at']))}}</td>
                            <td><div class="status status-{{strtolower($record['status'])}}">
                                    {{$record['status']}}
                                </div></td>
                            <td>
                                @if($userInfo['user_id']==$record['created_by'])
                                <a href="javascript:void(0);" onclick="rightModal('{{route('tut_editlibrarycontent',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['library_id'])])}}', 'Edit content')" class="btn btn-sm btn-default" title="Edit content to this lesson"><i class="fa fa-edit"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div>
</div>
@endsection
@section('pagescript')
<script>
initDataTable('basic-datatable');
$(document).ready(function(){

// $.fancybox.defaults.protect = true;
});
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
