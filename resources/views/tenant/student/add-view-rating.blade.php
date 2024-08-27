@extends('layouts.ajax')
@section('pagecss')
<link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection
@section('content')
<form method="POST" class="d-block ajaxForm"
    action="{{route('tus_save_teacherrating',Session()->get('tenant_info')['subdomain'])}}">
    @csrf
    <input type="hidden" name="creator_id" id="creator_id" value="{{$teacher_info['user_id']??''}}">
    <input type="hidden" name="lesson_id" id="lesson_id" value="{{$lesson_info['lesson_id']??''}}">
    <input type="hidden" name="subject_id" id="subject_id" value="{{$lesson_info['subject_id']??''}}">
    <input type="hidden" name="academic_year_id" id="academic_year_id" value="{{$lesson_info['subject']['academic_year_id']??''}}">
    <input type="hidden" name="year_group_id" id="year_group_id" value="{{$lesson_info['subject']['year_group_id']??''}}">
    <div class="form-row">
        <div class="ratingModal">
            
            <div class="ratingTeacher" style="text-align:center">
                <h5 style="text-align:center">Rate The Teacher</h5>
                <hr />
                <div>
                    <span>Teacher Name - {{$teacher_info['first_name']??''}} {{$teacher_info['last_name']??''}}</span>
                    <br />
                    <div id="rate_creator"  style="text-align:center;width:100%"></div><input type="hidden" id="creator_rating" name="creator_rating" value="{{$rating['creator_rating']??''}}">
                    <br>
                    <textarea name="creator_remarks" id="creator_remarks" class="form-control editable" placeholder="You can type here..." rows=4>{{$rating['creator_remarks']??''}}</textarea>
                    <!-- <div class="editable-container" style="text-align:center">

                        <div class="editable" contenteditable="true">
                            You can type here...
                        </div>
                        <div class="button-container">
                            <button class="btn" onclick="">Share</button>
                        </div>
                    </div> -->
                </div>
            </div>

            <br />

            <div class="ratingContent" style="text-align:center">
                <h5>Rate The Content</h5>
                <hr />
                <div>
                    <span style="align-items: center;">{{$lesson_info['lesson_name']??''}}</span>
                    <div id="rate_content"  style="text-align:center;width:100%"></div><input type="hidden" id="content_rating" name="content_rating" value="{{$rating['content_rating']??''}}">
                    <br>
                    <textarea name="content_remarks" id="content_remarks" class="form-control editable" placeholder="You can type here..." rows=4>{{$rating['content_remarks']??''}}</textarea>
                    <!-- <div class="editable-container" style="text-align:center">
                        <textarea name="" id="" class="editable" placeholder="You can type here..."></textarea>
                        <div class="editable" contenteditable="true">

                        </div>
                        <div class="button-container">
                            <button class="btn" onclick="">Share</button>
                        </div>
                    </div> -->

                </div>
            </div>
        </div>
        
        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" id="submitBtn" type="submit">Share</button>
        </div>
       
    </div>
</form>
@endsection
@section('pagescript')
<script src="{{ asset('/admin/js/raty/js/jquery.raty.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    $('#rate_content').raty({
        path: "{{asset('admin/img')}}",
        half: false,
        width: '100%',
        target: '#content_rating',
        targetKeep: true,
        targetType: 'number',
        size: 44,
        @if($rating['content_rating']??null != null)
        start: {{$rating['content_rating']}},
        @endif
        starOff: 'star-off-big.png',
        starOn: 'star-on-big.png',
        starHalf: 'star-half-big.png'
    });
    $('#rate_creator').raty({
        path: "{{asset('admin/img')}}",
        half: false,
        width: '100%',
        target: '#creator_rating',
        targetKeep: true,
        targetType: 'number',
        size: 44,
        @if($rating['creator_rating']??null != null)
        start: {{$rating['creator_rating']}},
        @endif
        starOff: 'star-off-big.png',
        starOn: 'star-on-big.png',
        starHalf: 'star-half-big.png'
    });
});
</script>
@endsection
