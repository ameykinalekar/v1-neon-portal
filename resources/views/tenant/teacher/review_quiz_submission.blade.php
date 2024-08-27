@extends('layouts.default')
@section('title', 'Edit Quiz')
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
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<form method="post" action="{{route('tut_quiz_review_save',Session()->get('tenant_info')['subdomain'])}}" enctype="multipart/form-data">
    <input type="hidden" name="user_result_id" value="{{$user_result_id}}">
    @csrf
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body py-1">
                    <div class="row">
                        <div class="col-md-4"><label for="">Quiz</label> :
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
                    </div>

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <div class="row">
        <div class="col-12">

            @if($details['page_count']>0)
            @for($page=1;$page<=$details['page_count'];$page++)
            <div class="card">
                <div class="card-header" style="height:50px;background-color: #f2f2f2; text-align:left">
                    <span>Page {{$page}}</span>
                </div>
                <div class="card-body">
                    @if(isset($details['user_result']['examination']['examquestions']) &&
                    count($details['user_result']['examination']['examquestions'])>0)
                    @for($i=0;$i < count($details['user_result']['examination']['examquestions']);$i++)
                        @if($details['user_result']['examination']['examquestions'][$i]['page_id']==$page)
                        @php
                        $questionInfo=json_decode($details['user_result']['examination']['examquestions'][$i]['question_info']);

                        $inputs_key=array_search($details['user_result']['examination']['examquestions'][$i]['examination_question_id'],array_column($details['user_result']['inputs'], 'examination_question_id'));
                        $correct_option_key='';
                        if(isset($questionInfo->options) && is_array($questionInfo->options)){
                            $correct_option_key=array_search('1',array_column($questionInfo->options, 'is_correct' ));
                        }
                        $disable_correction='disabled';
                        if($questionInfo->question_type=='text'){
                            $disable_correction='';
                        }
                        @endphp <div class="row mb-5">
                            <div class="col-md-12"><strong style="width:95%">{!!$questionInfo->question!!} </strong><input type="hidden" name="examination_question_id[]" value="{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}">
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
                                    <textarea class="form-control" disabled cols="30"
                                        rows="5">{{$details['user_result']['inputs'][$inputs_key]['answer']??''}}</textarea>
                                </p>
                                @endif

                                @if($questionInfo->require_file_upload)

                                <p>
                                    Attachments:
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
                                <label class="mx-2">Is Correct? :</label>
                                @php $isCorrectAnswer=0; @endphp
                                @if($details['user_result']['inputs'][$inputs_key]['answer'] !='' && $details['user_result']['inputs'][$inputs_key]['answer']==$correct_option_key)
                                @php $isCorrectAnswer=1; @endphp
                                <input type="radio" class="form-control-radio is_correct_radio" data-eqid="{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" {{$disable_correction}}
                                    name="is_correct_{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                    checked value="1" required>
                                Yes <input type="radio" class="form-control-radio is_correct_radio" data-eqid="{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" {{$disable_correction}}
                                    name="is_correct_{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                    value="0" required>
                                No
                                @else
                                @if($details['user_result']['inputs'][$inputs_key]['answer_status']=='Not Attempted')
                                <input type="radio" class="form-control-radio is_correct_radio" data-eqid="{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" {{$disable_correction}}
                                    name="is_correct_{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                    value="1">
                                Yes <input type="radio" class="form-control-radio is_correct_radio" data-eqid="{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" {{$disable_correction}}
                                    name="is_correct_{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                     value="0">
                                No
                                @else
                                <input type="radio" class="form-control-radio is_correct_radio" data-eqid="{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" {{$disable_correction}}
                                    name="is_correct_{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                    value="1" required>
                                Yes <input type="radio" class="form-control-radio is_correct_radio" data-eqid="{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" {{$disable_correction}}
                                    name="is_correct_{{$details['user_result']['examination']['examquestions'][$i]['question_id']}}"
                                     value="0" required>
                                No
                                @endif
                                @endif
                                <input type="hidden" id="hdIsCorrect_{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" name="is_correct[]" value="{{$isCorrectAnswer}}">
                                <br>
                                @if($questionInfo->question_type=='text')
                                <label class="mx-2">Marks Obtained:
                                </label>
                                <input type="number" step="any" class="form-control" id="ansmarks_obtained_{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" name="ansmarks_obtained[]" value="{{($details['user_result']['inputs'][$inputs_key]['answer'] !='' && $details['user_result']['inputs'][$inputs_key]['answer']==$correct_option_key)?$details['user_result']['examination']['examquestions'][$i]['point']:'0'}}" max="{{$details['user_result']['examination']['examquestions'][$i]['point']}}" {{($details['user_result']['inputs'][$inputs_key]['answer'] !='' && $details['user_result']['inputs'][$inputs_key]['answer']==$correct_option_key)?'':'readonly'}} >
                                @else
                                <label class="mx-2">Marks Obtained:
                                </label>{{($details['user_result']['inputs'][$inputs_key]['answer']==$correct_option_key)?$details['user_result']['examination']['examquestions'][$i]['point']:'0'}}
                                <input type="hidden" id="ansmarks_obtained_{{$details['user_result']['examination']['examquestions'][$i]['examination_question_id']}}" name="ansmarks_obtained[]" value="{{($details['user_result']['inputs'][$inputs_key]['answer']==$correct_option_key)?$details['user_result']['examination']['examquestions'][$i]['point']:'0'}}">
                                @endif
                                <br>
                                <label class="mx-2">Status:
                                </label>{{$details['user_result']['inputs'][$inputs_key]['answer_status']}}
                            </div>
                            <div class="col-md-12">
                                <label>Reviewer remarks:</label><textarea name="remarks[]" class="form-control" cols="30" rows="3"></textarea>
                            </div>
                            <br><br>
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
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mt-2 mb-4 col-md-12 text-center">
                <button class="btn btn-block btn-primary" type="submit" id="btnSubmit">Submit Review</button>
            </div>
        </div>
    </div>
</form>

@endsection
@section('pagescript')
<script type="text/javascript">
    $('.is_correct_radio').on('click',function(){
        var value = $(this).val();
        var eqid = $(this).data('eqid');
        var fullmarks=$('#ansmarks_obtained_' + eqid).attr('max');
        $('#hdIsCorrect_' + eqid).val(value);
        if (value > 0) {

            $('#ansmarks_obtained_' + eqid).attr('readonly', false);
            $('#ansmarks_obtained_' + eqid).val(fullmarks);
        } else {
            $('#ansmarks_obtained_' + eqid).val('0');
            $('#ansmarks_obtained_' + eqid).attr('readonly', true);
        }

    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
