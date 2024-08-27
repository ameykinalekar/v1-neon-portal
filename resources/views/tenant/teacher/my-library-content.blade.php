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

.accordion-item {
    margin-bottom: 8px;
}

.accordion-item:not(:first-of-type) {
    border-top-width: thin;
    border-top: 1;
}

.rotate-180 {
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
                   <h4> <i ></i> Library</h4>
                    <span id="btnContainer">
                        <a href="{{route('tut_mylibrary',Session()->get('tenant_info')['subdomain'])}}"
                            class="btn btn-sm btn-default" title="My Courses"><i class="fa fa-backward"></i> Go Back</a>

                    </span>
                </div>
                <div class="row py-2">

                    <div class="col-md-6"><label for="">Subject</label> :
                        {{$subject_info['subject_name']}} ({{$subject_info['yeargroup']['name']}} -
                        {{$subject_info['academicyear']['academic_year']}})</div>

                    <div class="col-md-6"><label for="">Board</label> :
                        {{$boards[$subject_info['board_id']]}}</div>

                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title">Lessons</h4><?php //dd($library_content_types);?>
                <div class="accordion" id="accordionExample">
                    @foreach($listing as $k=>$record)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{$k}}">
                            <button class="accordion-button @if($k>0) collapsed @endif" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse{{$k}}" @if($k==0)
                                aria-expanded="true" @endif aria-controls="collapse{{$k}}">
                                {{$record['lesson_number']}} - {{$record['lesson_name']}}
                            </button>
                        </h2>
                        <div id="collapse{{$k}}" class="accordion-collapse collapse @if($k==0) show @endif"
                            aria-labelledby="heading{{$k}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-3 py-2">
                                        <a
                                            href="{{route('tut_libcontentbytype',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id']),'N'])}}"><i
                                                class="fa fa-file-text"></i> Teacher Notes
                                            ({{$record['total_notes']}})</a>
                                    </div>
                                    <div class="col-md-3 py-2">
                                        <a
                                            href="{{route('tut_libcontentbytype',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id']),'M'])}}"><i
                                                class="fa fa-code-fork rotate-180"></i> Mindmaps
                                            ({{$record['total_mindmaps']}})</a>
                                    </div>
                                    <div class="col-md-3 py-2">
                                        <a
                                            href="{{route('tut_libcontentbytype',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id']),'V'])}}"><i
                                                class="fa fa-video"></i> Videos ({{$record['total_videos']}})</a>
                                    </div>
                                    <div class="col-md-3 py-2">
                                        <a
                                            href="{{route('tut_libcontentbytype',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id']),'P'])}}"><i
                                                class="fa fa-file"></i> PPTs ({{$record['total_ppts']}})</a>
                                    </div>
                                    <div class="col-md-3 py-2">
                                        <a
                                            href="{{route('tut_libcontentbytype',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id']),'U'])}}"><i
                                                class="fa fa-link"></i> URL ({{$record['total_urls']}})</a>
                                    </div>
                                    <div class="col-md-3 py-2">
                                        <a
                                            href="{{route('tut_libcontentbytype',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id']),'A'])}}"><i
                                                class="fa fa-file"></i> Assessments
                                            ({{$record['total_assessments']}})</a>
                                    </div>
                                    <div class="col-md-3 py-2">
                                        <a
                                            href="{{route('tut_libcontentbytype',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id']),'AS'])}}"><i
                                                class="fa fa-file"></i> Assessment Solutions
                                            ({{$record['total_assessment_solutions']}})</a>
                                    </div>
                                    <div class="col-12">
                                        <a href="javascript:void(0);"
                                            onclick="rightModal('{{route('tut_addlibrarycontent',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($record['lesson_id'])])}}', 'Add new content to {{$record['lesson_number']}} - {{$record['lesson_name']}}')"
                                            class="btn btn-sm btn-default float-end"
                                            title="Add new content to this lesson"><i class="fa fa-plus"></i> Add new
                                            content</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div>
</div>
@endsection
@section('pagescript')
<script>
initDataTable('basic-datatable');
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
