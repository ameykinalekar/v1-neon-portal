@extends('layouts.ajax')
@section('pagecss')
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('ta_updatelibrarycontent',Session()->get('tenant_info')['subdomain'])}}"
    enctype='multipart/form-data' id="frmContent">
    @csrf
    <input type="hidden" name="library_id" id="library_id" value="{{ $libdetails['library_id']}}">
    <input type="hidden" name="subject_id" id="subject_id" value="{{ \Helpers::encryptId($details['subject_id'])}}">
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="academic_year">Academic Year</label>
            <input type="text" class="form-control" id="academic_year" name="academic_year" required readonly
                value="{{$details['subject']['academicyear']['academic_year']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="year_group">Year Group</label>
            <input type="text" class="form-control" id="year_group" name="year_group" required readonly
                value="{{$details['subject']['yeargroup']['name']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="subject_name">Subject</label>
            <input type="text" class="form-control" id="subject_name" name="subject_name" required readonly
                value="{{$details['subject']['subject_name']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="board">Board</label>
            <input type="text" class="form-control" id="board" name="board" required readonly
                value="{{$boards[$details['subject']['board_id']??'']}}">
        </div>
        <div class="form-group mb-1">
            <label for="lesson_name">Lesson Name</label>
            <input type="text" class="form-control" id="lesson_name" name="lesson_name" required readonly
                value="{{$details['lesson_name']??''}}">
            <input type="hidden" class="form-control" id="lesson_id" name="lesson_id" required readonly
                value="{{$details['lesson_id']??''}}">
        </div>
        <div class="form-group mb-1">
            <label for="title">Content Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{$libdetails['title']}}">
        </div>
        <div class="form-group mb-1">
            <label for="name">Content Type</label>
            <select class="form-control" id="content_type" name="content_type" required>
                <option value="">select content type</option>
                @foreach($lib_content_types as $k=>$v)
                <option value="{{$k}}" @if($libdetails['content_type']==$k) selected @endif>{{$v}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="content_file">Content File</label>
            @php $supported_image = array('gif','jpg','jpeg','png'); @endphp
            @php
            if($libdetails['content_type']!='U'){
            $arrFile=explode('.',$libdetails['content_file']);
            $fileExtension=$arrFile[1];
            }
            @endphp
            @if($libdetails['content_type']=='U')
            <a class="fancy-box-a" data-fancybox="demo" data-caption="Content {{$libdetails['title']}}"
                href="{{$libdetails['content_url']}}"><i class="fa fa-eye"></i> View Content</a>
            @elseif($libdetails['content_type']=='V')
            <a class="fancy-box-a" data-fancybox="demo" data-caption="Content {{$libdetails['title']}}"
                href="{{config('app.api_asset_url') . '/'.$libdetails['content_file']}}"><i class="fa fa-eye"></i> View
                Content</a>
            @else
            @if(in_array($fileExtension,$supported_image))
            <a class="fancy-box-a" data-fancybox="demo" data-caption="Content {{$libdetails['title']}}"
                href="{{config('app.api_asset_url') . '/'.$libdetails['content_file']}}"><i class="fa fa-eye"></i> View
                Content</a>
            @else

            <a href="javascript:void(0);"
                onclick="fullModal('{{route('view_lib_file',[Session()->get('tenant_info')['subdomain'],\Helpers::encryptId($libdetails['library_id'])])}}', 'Library File')"
                class="btn btn-sm btn-default" title="View content file"><i class="fa fa-eye"></i> View
                Content</a>
            @endif
            @endif
            <input type="file" class="form-control validate-file" id="content_file" name="content_file">
            <input type="hidden" name="old_content_file" id="old_content_file" value="{{$libdetails['content_file']}}">
        </div>
        <div class="form-group mb-1">
            <label for="content_url">Content URL</label>
            <input type="url" class="form-control" id="content_url" name="content_url"
                value="{{$libdetails['content_url']}}">
        </div>
        <div class="form-group mb-1">
            <label for="short_name">Status</label>
            {{ Form::select('status',$status, $libdetails['status']??'', array('class' => 'form-control select2_el','required','id' => 'record_status','placeholder' => 'Select Status')) }}
        </div>
        <div class="form-group mt-2 col-md-12">
            <input type="hidden" name="is_form_valid" id="is_form_valid" value="0">
            <button class="btn btn-block btn-primary" type="submit" id="btnSubmit" disabled>Update Content</button>
        </div>
    </div>
</form>
@endsection
@section('pagescript')
<script type="text/javascript">
$(document).ready(function() {
    // alert($('#content_type').val());
    if ($('#content_type').val() == 'U') {
        $('#content_url').attr('required', true);
        $('#content_file').attr('required', false);
    } else {
        $('#content_url').attr('required', false);
        if ($('#old_content_file').val() == '') {
            $('#content_file').attr('required', true);
        }
        if ($('#content_type').val() == 'V') {
            $('#content_file').attr('accept', 'video/*');
        } else {
            $('#content_file').attr('accept', 'image/*,audio/*,.xls,.xlsx,.doc,.docx,.pdf,.txt,.ppt,.pptx');
        }
    }
});
$('.validate-file').on('change', function() {
    console.log('no. of files' + this.files.length);
    var size;
    var allFilesValid = 0;
    for (let i = 0; i < this.files.length; i++) {
        size = (this.files[i].size / 1024 / 1024).toFixed(2);
        if (size > 2) {
            allFilesValid++;
        }

        console.log('File ' + i + ' Size:' + size);

    }
    if (allFilesValid > 0) {
        alert("Each File must be with in the size of 2 MB");
        $(this).val('');
        $('#is_form_valid').val('1');
    } else {
        $('#is_form_valid').val('0');
    }
});
$('#content_type').on('change', function() {
    if ($(this).val() == 'U') {
        $('#content_url').attr('required', true);
        $('#content_file').attr('required', false);
    } else {
        $('#content_url').attr('required', false);
        if ($('#old_content_file').val() == '') {
            $('#content_file').attr('required', true);
        }
        if ($(this).val() == 'V') {
            $('#content_file').attr('accept', 'video/*');
        } else {
            $('#content_file').attr('accept', 'image/*,audio/*,.xls,.xlsx,.doc,.docx,.pdf,.txt,.ppt,.pptx');
        }
    }
});
$('#content_type').on('blur', function() {
    validateForm();
});
$('#content_file').on('blur', function() {
    validateForm();
});
$('#content_url').on('blur', function() {
    validateForm();
});
$('#title').on('blur', function() {
    validateForm();
});
$('#record_status').on('blur', function() {
    validateForm();
});
$('#btnSubmit').on('click', function() {
    $('#btnSubmit').attr('disabled', true);
    $('#frmContent').submit();
});

function validateForm() {
    $('#btnSubmit').attr('disabled', false);
    if ($('#old_content_file').val() == '') {
            $('#content_file').attr('required', true);
        }
    if ($('#is_form_valid').val() == 0) {
        if ($('#content_type').val() == 'U' && $('#content_url').val() != '') {
            $('#btnSubmit').attr('disabled', false);
        } else {
            $('#content_url').val('');
            if ($('#content_file').val() != '') {
                $('#btnSubmit').attr('disabled', false);
            }
        }
    }
}
</script>
@endsection