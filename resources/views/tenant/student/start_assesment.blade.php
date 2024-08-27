@extends('layouts.default')
@section('title', 'Start Assessment')
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

ol li {
    list-style: block;
    font-weight: 600;
    font-size: 15px;
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<?php
$examTotalTime = $listing['examination']['total_time'] ?? 0;
$examTotalTimeArr = explode(':', $examTotalTime);
$examHr = $examTotalTimeArr[0] ?? 0;
$examMin = $examTotalTimeArr[1] ?? 0;
$examSec = $examTotalTimeArr[2] ?? 0;
$totExamMins = ($examHr * 60) + $examMin + ($examSec / 60);
?>
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <div class="page-title">
                    <i ></i> {{$listing['examination']['name']}}
                    <span class="float-end">Time out- <span id="time"></span></span>
                </div>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="times_up_msg"></div>
            <div class="card-body admin_content"> <?php //dd($listing);?>
                <form method="POST" class="d-block ajaxForm" id="form1" enctype="multipart/form-data"
                    action="{{route('tus_saveassessment',Session()->get('tenant_info')['subdomain'])}}">
                    @csrf
                    <input type="hidden" name="examination_id" value="{{$listing['examination']['examination_id']}}">
                    @php $total_marks = 0; @endphp
                    <div class="row">
                        <div id="answer_fields" class="optsec">

                            @if(isset($listing['examination_questions']) && is_array($listing['examination_questions']))
                            <ol>
                                @for($i=0; $i<count($listing['examination_questions']); $i++)
                                    @php
                                    $total_marks=$total_marks+(float)$listing['examination_questions'][$i]['point']??'0';
                                    $question_info=json_decode($listing['examination_questions'][$i]['question_info']);
                                    @endphp
                                    @if($i>0)
                                    <hr>
                                    @endif

                                    <li>
                                        <div class="float-start">{!!$question_info->question??''!!} </div>
                                        @if($listing['examination_questions'][$i]['linked_question']==0)
                                        <div class="float-end">Marks: {{$listing['examination_questions'][$i]['point']}}
                                        </div>
                                        @endif

                                    </li>
                                    @if($listing['examination_questions'][$i]['linked_question']==0)
                                    <div style="clear:both">
                                        <input type="hidden" name="questid[]" value="{{$listing['examination_questions'][$i]['examination_question_id']}}">
                                        <input type="hidden" name="quest_starttime[]" id="quest_starttime_{{$listing['examination_questions'][$i]['examination_question_id']}}">

                                        <input type="hidden" name="quest_endtime[]" id="quest_endtime_{{$listing['examination_questions'][$i]['examination_question_id']}}">

                                        <textarea name="answer[]" id="answer{{$i}}" cols="30" rows="3"
                                            class="form-control ckeditor question" required></textarea>
                                            @if($question_info->require_file_upload)
                                            <br>
                                            <p>
                                                Upload Attachment: <input type="file" class="validate-file" name="file_{{$listing['examination_questions'][$i]['examination_question_id']}}[]" multiple><br>
                                                <small>To select multiple files, hold CTRL key and choose files of your choice.<br>Each selected file should be under 2MB. </small>
                                            </p>
                                            @endif

                                    </div>
                                    @endif
                                    @if($listing['examination_questions'][$i]['linked_question']>0)
                                        @php
                                            $subquestions=$listing['examination_questions'][$i]['subquestions']??array();

                                        @endphp

                                        @if(count($subquestions)>0)
                                        <ol>
                                            @for($j=0; $j<count($subquestions); $j++)
                                            @php
                                            //dd($subquestions[$j]);
                                            $total_marks=$total_marks+(float)$subquestions[$j]['point']??'0';
                                            $subquestion_info=json_decode($subquestions[$j]['question_info']);
                                            @endphp
                                            @if($j>0)
                                            <hr>
                                            @endif

                                            <li>
                                                <div class="float-start">{!!$subquestion_info->question??''!!} </div>
                                                <div class="float-end">Marks: {{$subquestions[$j]['point']}}
                                                </div>

                                            </li>
                                            <div style="clear:both">
                                                <input type="hidden" name="questid[]" value="{{$subquestions[$j]['examination_question_id']}}">
                                                <input type="hidden" name="quest_starttime[]" id="quest_starttime_{{$subquestions[$j]['examination_question_id']}}">

                                                <input type="hidden" name="quest_endtime[]" id="quest_endtime_{{$subquestions[$j]['examination_question_id']}}">

                                                <textarea name="answer[]" id="answer{{$i}}" cols="30" rows="3"
                                                    class="form-control ckeditor question" required></textarea>
                                                    @if($subquestion_info->require_file_upload)
                                                    <br>
                                                    <p>
                                                        Upload Attachment: <input type="file" class="validate-file" name="file_{{$subquestions[$j]['examination_question_id']}}[]" multiple><br>
                                                        <small>To select multiple files, hold CTRL key and choose files of your choice.<br>Each selected file should be under 2MB. </small>
                                                    </p>
                                                    @endif
                                            </div>
                                            @endfor
                                            </ol>
                                        @endif

                                    @endif

                                    @endfor
                            </ol>
                            @else

                            @endif
                        </div>



                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-2 col-md-12">
                                <input type="hidden" name="total_quiztime"
                                    value="{{$listing['examination']['total_time'] ?? 0}}">
                                <input type="hidden" name="taken_quiztime" id="taken_quiztime" value="">
                                <input type="hidden" name="examination_id" id="examination_id"
                                    value="{{$listing['examination']['examination_id']}}">
                                <input type="hidden" name="is_form_valid" id="is_form_valid" value="0">
                                <input type="hidden" name="total_marks" id="total_marks" value="{{$total_marks}}">
                                <input type="hidden" name="quiz_begintime" value="<?php echo date('Y-m-d H:i:s'); ?>">
                                <br><br><input type="submit" class="btn btn-primary submitquiz" value="Submit Assessment">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('pagescript')
<script src="{{asset('plugins/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('plugins/ckeditor/plugins/ckfinder/ckfinder.js')}}"></script>
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>

<script type="text/javascript" asyc>
var quiz_time = "{{$totExamMins}}";
var timeLeft = quiz_time * 60;

function timeout() {
    var minute = Math.floor(timeLeft / 60);
    var second = timeLeft % 60;
    var sec = checktime(second);
    var min = checktime(minute);
    console.log('left:: '+eval(timeLeft));
    if (eval(timeLeft) == 0) {
        clearTimeout(tm);
        console.log(eval(min) + ':' + eval(sec));
        if (eval(min) == 0 && eval(sec) == 0){
            document.getElementById('time').innerHTML = min + ':' + sec;
            $('#taken_quiztime').val(min + ':' + sec);
            console.log('***'+eval(min) + ':' + eval(sec));
            $('.times_up_msg').addClass('alert alert-danger');
            $('.times_up_msg').html('Times Up! Your Assesment will Going to Submit');
            $('.submitquiz').click();
            //document.getElementById('form1').submit();
        }
    } else {
        document.getElementById('time').innerHTML = min + ':' + sec;
        $('#taken_quiztime').val(min + ':' + sec);

        timeLeft--;
        var tm = setTimeout(function() {
            timeout()
        }, 1000);
    }
}

function checktime(msg) {
    if (msg < 10) {
        msg = '0' + msg;
    }
    return msg;
}
</script>
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

$('.validate-file').on('change', function() {
    console.log('no. of files'+this.files.length);
    var size;
    var allFilesValid=0;
    for(let i=0;i<this.files.length;i++){
        size = (this.files[i].size / 1024 / 1024).toFixed(2);
        if (size > 2){
            allFilesValid++;
        }

           console.log('File '+i+' Size:'+size);

    }
    if(allFilesValid>0){
        alert("Each File must be with in the size of 2 MB");
        $('#is_form_valid').val('1');
    }else{
        $('#is_form_valid').val('0');
    }
});
// $('.submitquiz').click(function() {

//     let is_form_valid=$('#is_form_valid').val();
//     if(is_form_valid==0){
//         $('#frmQuiz').submit();
//     }
// });
</script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
