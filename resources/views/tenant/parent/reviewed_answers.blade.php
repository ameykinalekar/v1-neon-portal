@php
    $typeOfExam='';
    $paginationRouteName='';
    $extraTextAreaClass='';
    $showPageHeader=0;
    $examination_type=$details['user_result']['examination']['examination_type']??'';
    if($examination_type=='Q'){
        $typeOfExam='Quiz';
        $paginationRouteName='tus_quiz_marks';
        $showPageHeader=1;
    }
    if($examination_type=='A'){
        $typeOfExam='Assessment';
        $paginationRouteName='tus_assessment_marks';
        $extraTextAreaClass='ckeditor';
    }
    $pagetitle=($typeOfExam!=''?$typeOfExam.' ':'').'Marks';
@endphp
@extends('layouts.default')
@section('title', 'Answer Sheet')
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
.incorrect{
    font-weight:600;
    color:red;
}
.correct{
    font-weight:600;
    color:green;
}

</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <div class="row">
                    <div class="col-md-4"><label for="">{{$typeOfExam}} Name</label> :
                        {{$details['user_result']['examination']['name']}}</div>
                    <div class="col-md-4"><label for="">Student Code</label> :
                        {{$details['user_result']['consumer']['code']}}</div>
                    <div class="col-md-4"><label for="">Student</label> :
                        {{$details['user_result']['consumer']['first_name']}}
                        {{$details['user_result']['consumer']['last_name']}} </div>
                    <div class="col-md-4"><label for="">Taken On</label> :
                        {{date('d-m-Y',strtotime($details['user_result']['created_at']))}}</div>
                    <div class="col-md-4"><label for="">Time Taken</label> :
                        {{$details['user_result']['time_taken_inmins']}}
                        {{($details['user_result']['time_taken_inmins']>1)? 'minutes' : 'minute'}}</div>
                    <div class="col-md-4"><label for="">Allotted Time</label> :
                        {{$details['user_result']['total_time_in_mins']}}
                        {{($details['user_result']['total_time_in_mins']>1)? 'minutes' : 'minute'}}</div>
                    <div class="col-md-4"><label for="">Total Marks</label> :
                        {{$details['user_result']['total_marks']}}
                    </div>
                    <div class="col-md-4"><label for="">Marks Obtained</label> :
                        {{$details['user_result']['marks_obtained']}}</div>
                    <div class="col-md-4"><label for="">Percentage</label> :
                        {{round(($details['user_result']['marks_obtained']/$details['user_result']['total_marks'])*100,2)}}</div>
                </div>
                <span id="btnContainer">
                <a href="#" class="btn btn-sm btn-default" title=""><i class="fa fa-backward"></i> Back</a>

                </span>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">

        @if($details['page_count']>0)
        @for($page=1;$page<=$details['page_count'];$page++) <div class="card">
            @if($showPageHeader>0)
            <div class="card-header" style="height:50px;background-color: #f2f2f2; text-align:left">
                <span>Page {{$page}}</span>
            </div>
            @endif
            <div class="card-body" id="answer_fields">
                @if(isset($details['user_result']['examination']['examquestions']) &&
                count($details['user_result']['examination']['examquestions'])>0)
                @for($i=0;$i < count($details['user_result']['examination']['examquestions']);$i++)

                    @if($details['user_result']['examination']['examquestions'][$i]['page_id']==$page)

                    @php
                        $questionInfo=json_decode($details['user_result']['examination']['examquestions'][$i]['question_info']);

                        $inputs_key=array_search($details['user_result']['examination']['examquestions'][$i]['examination_question_id'],array_column($details['user_result']['inputs'],'examination_question_id'));

                        if(isset($questionInfo->options) && is_array($questionInfo->options)){
                            $correct_option_key=array_search('1',array_column($questionInfo->options, 'is_correct' ));
                        }

                        $disable_correction='disabled';

                        if($questionInfo->question_type=='text'){
                            $disable_correction='';
                        }
                    @endphp
                    <div class="row mb-5">
                        @if($details['user_result']['examination']['examquestions'][$i]['linked_question']>0)
                        <div class="col-md-12"><strong style="width:95%">{!!$questionInfo->question!!} </strong>
                        </div>
                        @if(isset($details['user_result']['examination']['examquestions'][$i]['subquestions']) &&
                    count($details['user_result']['examination']['examquestions'][$i]['subquestions'])>0)
                        @for($j=0;$j < count($details['user_result']['examination']['examquestions'][$i]['subquestions']);$j++)

                            @php
                                $subquestions=$details['user_result']['examination']['examquestions'][$i]['subquestions'];
                                //dd($subquestions);

                                $subquestionInfo=json_decode($subquestions[$j]['question_info']);

                                $inputs_key=array_search($subquestions[$j]['examination_question_id'],array_column($details['user_result']['inputs'], 'examination_question_id'));
                                $correct_option_key='' ;

                                if(isset($subquestionInfo->options) && is_array($subquestionInfo->options)){
                                    $correct_option_key=array_search('1',array_column($subquestionInfo->options, 'is_correct' ));
                                }

                                $disable_correction='disabled';
                                if($subquestionInfo->question_type=='text'){
                                    $disable_correction='';
                                }

                            @endphp
                            <div class="col-md-12"><strong style="width:95%">{!!$subquestionInfo->question!!} </strong>
                        </div>
                        <div class="col-md-8">

                            @if($subquestionInfo->question_type=='radio')
                            <p>
                                @foreach($subquestionInfo->options as $k=>$option)
                                @if($details['user_result']['inputs'][$inputs_key]['answer']==$k &&
                                $details['user_result']['inputs'][$inputs_key]['answer']!=null)
                                <input type="radio" class="form-control-radio" checked
                                    name="{{$subquestions[$j]['question_id']}}"
                                    disabled>
                                {{$option->option_value}}<br>
                                @else
                                <input type="radio" class="form-control-radio"
                                    name="{{$subquestions[$j]['question_id']}}"
                                    disabled>
                                {{$option->option_value}}<br>
                                @endif
                                @endforeach
                            </p>
                            @endif
                            @if($subquestionInfo->question_type=='checkbox')
                            <p>
                                @foreach($subquestionInfo->options as $k=>$option)
                                @if($details['user_result']['inputs'][$inputs_key]['answer']==$k &&
                                $details['user_result']['inputs'][$inputs_key]['answer']!=null)
                                <input type="checkbox" class="form-control-checkbox"
                                    name="{{$subquestions[$j]['question_id']}}"
                                    disabled checked>
                                {{$option->option_value}}<br>
                                @else
                                <input type="checkbox" class="form-control-checkbox"
                                    name="{{$subquestions[$j]['question_id']}}"
                                    disabled>
                                {{$option->option_value}}<br>
                                @endif
                                @endforeach
                            </p>
                            @endif
                            @if($subquestionInfo->question_type=='select')
                            <p>
                                <select class="form-control" style="width:50%;" disabled>
                                    <option></option>
                                    @foreach($subquestionInfo->options as $k=>$option)
                                    @if($details['user_result']['inputs'][$inputs_key]['answer']==$k &&
                                    $details['user_result']['inputs'][$inputs_key]['answer']!=null)
                                    <option selected>{{$option->option_value}}</option>
                                    @else
                                    <option>{{$option->option_value}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </p>
                            @endif
                            @if($subquestionInfo->question_type=='text')
                            <p>
                                <textarea class="form-control {{$extraTextAreaClass}}" id="answer{{$i}}" disabled cols="30"
                                    rows="5">{{$details['user_result']['inputs'][$inputs_key]['answer']??''}}</textarea>
                            </p>
                            @endif

                            @if($subquestionInfo->require_file_upload)
                            <p>
                                Attachment:
                                @if($details['user_result']['inputs'][$inputs_key]['attachment_file'] !='')
                                    @php
                                    $attachments=json_decode($details['user_result']['inputs'][$inputs_key]['attachment_file']);

                                    @endphp
                                    @if($attachments==null)
                                    <ul>
                                       <li> <a
                                        href="{{config('app.api_asset_url') . $details['user_result']['inputs'][$inputs_key]['attachment_file']}}"
                                        target="_blank"><i class="fa fa-download"></i> Attachment 1</a> </li>
                                    </ul>
                                    @else
                                    <ul>
                                        @foreach($attachments as $atch=>$attachment)
                                        <li><a
                                        href="{{config('app.api_asset_url') . $attachment}}"
                                        target="_blank"><i class="fa fa-download"></i> Attachment {{($atch+1)}}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif

                                    @endif
                            </p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="mx-2">Marks Allotted:
                            </label>{{$subquestions[$j]['point']}}

                            <br>

                            <label class="mx-2">Marks Obtained:
                            </label>{{$details['user_result']['inputs'][$inputs_key]['marks_given']}}


                            <br>
                            <label class="mx-2">Answer Status:
                            </label><span class="{{strtolower($details['user_result']['inputs'][$inputs_key]['answer_status'])}}">{{$details['user_result']['inputs'][$inputs_key]['answer_status']}}</span>
                        </div>
                        <div class="col-md-12">
                            <label>Reviewer remarks:</label><p>{{($details['user_result']['inputs'][$inputs_key]['reviewer_comments']??'')==''?'Not Available':$details['user_result']['inputs'][$inputs_key]['reviewer_comments']}}</p>
                        </div>
                        <br><br>
                        @endfor

                        @endif
                        @else
                        <div class="col-md-12"><strong style="width:95%">{!!$questionInfo->question!!} </strong>
                        </div>
                        <div class="col-md-8">

                            @if($questionInfo->question_type=='radio')
                            <p>
                                @foreach($questionInfo->options as $k=>$option)
                                @if($details['user_result']['inputs'][$inputs_key]['answer']==$k &&
                                $details['user_result']['inputs'][$inputs_key]['answer']!=null)
                                <input type="radio" class="form-control-radio" checked
                                    name="{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                    disabled>
                                {{$option->option_value}}<br>
                                @else
                                <input type="radio" class="form-control-radio"
                                    name="{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                    disabled>
                                {{$option->option_value}}<br>
                                @endif
                                @endforeach
                            </p>
                            @endif
                            @if($questionInfo->question_type=='checkbox')
                            <p>
                                @foreach($questionInfo->options as $k=>$option)
                                @if($details['user_result']['inputs'][$inputs_key]['answer']==$k &&
                                $details['user_result']['inputs'][$inputs_key]['answer']!=null)
                                <input type="checkbox" class="form-control-checkbox"
                                    name="{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                    disabled checked>
                                {{$option->option_value}}<br>
                                @else
                                <input type="checkbox" class="form-control-checkbox"
                                    name="{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                    disabled>
                                {{$option->option_value}}<br>
                                @endif
                                @endforeach
                            </p>
                            @endif
                            @if($questionInfo->question_type=='select')
                            <p>
                                <select class="form-control" style="width:50%;" disabled>
                                    <option></option>
                                    @foreach($questionInfo->options as $k=>$option)
                                    @if($details['user_result']['inputs'][$inputs_key]['answer']==$k &&
                                    $details['user_result']['inputs'][$inputs_key]['answer']!=null)
                                    <option selected>{{$option->option_value}}</option>
                                    @else
                                    <option>{{$option->option_value}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </p>
                            @endif
                            @if($questionInfo->question_type=='text')
                            <p>
                                <textarea class="form-control {{$extraTextAreaClass}}" id="answer{{$i}}" disabled cols="30"
                                    rows="5">{{$details['user_result']['inputs'][$inputs_key]['answer']??''}}</textarea>
                            </p>
                            @endif

                            @if($questionInfo->require_file_upload)
                            <p>
                                Attachment:
                                @if($details['user_result']['inputs'][$inputs_key]['attachment_file'] !='')
                                    @php
                                    $attachments=json_decode($details['user_result']['inputs'][$inputs_key]['attachment_file']);

                                    @endphp
                                    @if($attachments==null)
                                    <ul>
                                       <li> <a
                                        href="{{config('app.api_asset_url') . $details['user_result']['inputs'][$inputs_key]['attachment_file']}}"
                                        target="_blank"><i class="fa fa-download"></i> Attachment 1</a> </li>
                                    </ul>
                                    @else
                                    <ul>
                                        @foreach($attachments as $atch=>$attachment)
                                        <li><a
                                        href="{{config('app.api_asset_url') . $attachment}}"
                                        target="_blank"><i class="fa fa-download"></i> Attachment {{($atch+1)}}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif

                                    @endif
                            </p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="mx-2">Marks Allotted:
                            </label>{{$details['user_result']['examination']['examquestions'][$i]['point']}}

                            <br>

                            <label class="mx-2">Marks Obtained:
                            </label>{{$details['user_result']['inputs'][$inputs_key]['marks_given']}}


                            <br>
                            <label class="mx-2">Answer Status:
                            </label><span class="{{strtolower($details['user_result']['inputs'][$inputs_key]['answer_status'])}}">{{$details['user_result']['inputs'][$inputs_key]['answer_status']}}</span>
                        </div>
                        <div class="col-md-12">
                            <label>Reviewer remarks:</label><p>{{($details['user_result']['inputs'][$inputs_key]['reviewer_comments']??'')==''?'Not Available':$details['user_result']['inputs'][$inputs_key]['reviewer_comments']}}</p>
                        </div>
                        <br><br>
                        @endif
                    </div>
                    @endif
                    @endfor
                    @endif

            </div>
    </div>
    @endfor
    @endif

</div>

</div>


@endsection
@section('pagescript')
<script src="{{asset('plugins/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('plugins/ckeditor/plugins/ckfinder/ckfinder.js')}}"></script>
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();

    loadEditor();

    $.each($("#answer_fields img"), function() {
        var imgsrc = $(this).attr("src");
        var el = $(this);
        // create wrapper container
        var updatedContent =
            "<a class='fancy-box-a' data-fancybox='demo' data-caption='Content Image'  href='" +
            imgsrc + "'>" + el.parent().html() + "</a>";
        el.parent().html(updatedContent);
    });

    timeout();

});
// Initialize select2
function initailizeSelect2() {

    $(".select2_el").select2({

    });
}

function loadEditor() {
    $('.ckeditor').each(function() {

        //alert("aaa");
        id = $(this).attr('id');
        if (!CKEDITOR.instances[id])
            CKEDITOR.replace(id);
        //delete CKEDITOR.instances[id];
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
