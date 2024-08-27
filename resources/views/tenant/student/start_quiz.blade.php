@extends('layouts.default')
@section('title', 'Start Quiz')
@section('pagecss')

<style type="text/css">
.student_quiz_optiondiv {
    background: #FFFFFF;
    border: 1px solid #DBDBDB;
    margin-bottom: 15px;
}

.student_quiz_optiondiv:hover,
.student_quiz_optiondiv.active {
    background: #5BC2B9;
}

.student_quiz_optiondiv.option_a.active .student_quiz_option_no,
.student_quiz_optiondiv.option_a.active .student_quiz_option {
    color: #fff;
}

.student_quiz_optiondiv.option_b.active .student_quiz_option_no,
.student_quiz_optiondiv.option_b.active .student_quiz_option {
    color: #fff;
}

.student_quiz_optiondiv.option_c.active .student_quiz_option_no,
.student_quiz_optiondiv.option_c.active .student_quiz_option {
    color: #fff;
}

.student_quiz_optiondiv.option_d.active .student_quiz_option_no,
.student_quiz_optiondiv.option_d.active .student_quiz_option {
    color: #fff;
}

.student_quiz_optiondiv:hover .student_quiz_option_no,
.student_quiz_optiondiv:hover .student_quiz_option {
    color: #fff;
}

.student_quiz_option_no {
    /* font: normal normal normal 16px/25px Open Sans; */
    letter-spacing: 0px;
    color: #434343;
    border-right: 1px solid #DBDBDB;
    padding: 14px 21px;
}

.student_quiz_option {
    padding: 14px 50px;
    /* font: normal normal normal 16px/25px Open Sans; */
    letter-spacing: 0px;
    color: #434343;
}

.student_quiz_jump_question-btn {
    background: #FFFFFF 0% 0% no-repeat padding-box;
    border: 1px solid #DBDBDB;
    /* font: normal normal bold 16px/25px Open Sans; */
    letter-spacing: 0px;
    color: #434343;
    padding: 9px 26px;
}

.student_quiz_jump_question-btn:hover {
    background: #5BC2B9;
    color: #fff;
}

.ocean_color.form-check-input:checked {
    background-color: rgb(91 194 185);
    border-color: rgb(91 194 185);
}

.time_progress__main {
    position: relative;
}

.time_progress__wrap {
    position: absolute;
    top: -37px;
    left: 50%;
    width: 104px;
    height: 104px;
    transform: translate(-50%, -22%);
}

.time_progress__clock {
    position: absolute;
    bottom: -50px;
    left: 50%;
    transform: translate(-50%, -24%);
}

svg.time_progress__svg {
    display: inline-flex;
    vertical-align: bottom;
    width: 104px;
    height: 104px;
    margin-left: auto;
    margin-right: auto;
}

svg.time_progress__svg .meter {
    stroke: #5BC2B9;
}

svg.time_progress__svg circle {
    stroke: #f8ecec;
    stroke-width: 5px;
    stroke-dasharray: 0;
    fill: none;
}

.meter {
    stroke-width: 5px;
    /* stroke: #fff; */
    fill: #fff;
    transition: stroke-dashoffset 1s cubic-bezier(0.43, 0.41, 0.22, 0.91);
    transform-origin: center center;
    transform: rotate(-90deg) scaleX(-1);
}

text {
    fill: #444444;
    font-weight: bold;
}

.answer_square_count {
    /* font: normal normal normal 12px/17px Open Sans; */
    letter-spacing: 0px;
    color: #434343;
    padding: 11px 15px;
    border: 1px solid #DBDBDB;
    border-radius: 5px;
    width: 41px;
    margin-bottom: 10px;
}

.answer_square_count.answered {
    color: #FFF;
    background-color: #5BC2B9;
}

.answer_square_count.non_answered {
    border: 1px solid #5BC2B9;
}

.answer_square_count.non_visited {}

.list-inline-item.answer_square_count:not(:last-child) {
    margin-right: 15px;
}

/* =======================quiz Clock */
.main-clock-progress-container {
    width: 100px;
    height: 100px;
    position: relative;
}

.center {
    display: flex;
    justify-content: center;
    align-items: center;
}

.circle-container {
    width: 100%;
    height: 100%;
    background-color: white;
    background-color: #dddddd;
    border-radius: 50%;
    position: relative;
    z-index: 1;
    overflow: hidden;
}

.semicircle {
    width: 50%;
    height: 100%;
    background-color: white;
    position: absolute;
    top: 0;
    left: 0;
    transform-origin: right center;
}

.semicircle:nth-child(1) {
    background-color: red;
    background-color: #088b8b;
    z-index: 2;
}

.semicircle:nth-child(2) {
    background-color: blue;
    background-color: #088b8b;
    z-index: 3;
}

.semicircle:nth-child(3) {
    background-color: white;
    background-color: #ddd;
    z-index: 4;
    /* display: none; */
}

.outermost--circle {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    height: 90%;
    background-color: rgb(0, 0, 0);
    background-color: #fff;
    border-radius: 50%;
    z-index: 5;
    /* display: none; */
}

.timer-container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    height: 20%;
    z-index: 6;
}

.timer {}

.timer div {
    font-size: 14px;
    font-weight: 500;
    width: auto;
    height: ;
    display: flex;
    justify-content: center;
    align-items: center;
}

.timer .colon {
    background-color: transparent;
    width: 3px;
    margin-left: 0;
    margin-right: 0;
}

/* =======================quiz Clock */

.form_inside_wrap {
    padding: 24px 40px;
    margin-top: 40px;
}

@media (max-width:768px) {
    .form_inside_wrap {
        padding: 24px 0px;
    }
}

.leftside-menu {
    display: none;
}

.option-label {
    font-weight: normal;
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')

<div class="row">
    <div class="col-md-12"
        style="background: #FFFFFF 0% 0% no-repeat padding-box;border-radius: 5px; padding: 24px 40px;">
        <h2 style="font-size:18px;letter-spacing: 0px;color: #434343;">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">

                    <g id="Group_91" data-name="Group 91" transform="translate(-553 -212)">

                        <rect id="Rectangle_11" data-name="Rectangle 11" width="24" height="24"
                            transform="translate(553 212)" fill="none" />

                        <g id="school" transform="translate(538.603 166.973)">

                            <path id="Path_320" data-name="Path 320"
                                d="M102.943,288.38a.694.694,0,0,1-.344-.092l-6.078-3.474a.347.347,0,0,0-.521.3v3.264a.694.694,0,0,0,.357.608l6.249,3.472a.694.694,0,0,0,.674,0l6.249-3.472a.694.694,0,0,0,.357-.608v-3.264a.347.347,0,0,0-.521-.3l-6.078,3.474A.694.694,0,0,1,102.943,288.38Z"
                                transform="translate(-76.546 -226.493)" fill="#434343" />

                            <path id="Path_321" data-name="Path 321"
                                d="M36.808,54.185v0a.694.694,0,0,0-.347-.533l-9.72-5.554a.694.694,0,0,0-.689,0l-9.72,5.554a.694.694,0,0,0,0,1.205l9.72,5.554a.694.694,0,0,0,.689,0l8.552-4.887a.087.087,0,0,1,.13.076v6.272a.708.708,0,0,0,.661.713.694.694,0,0,0,.728-.693V54.25A.641.641,0,0,0,36.808,54.185Z"
                                transform="translate(0)" fill="#434343" />

                        </g>

                    </g>

                </svg>
            </span> Quiz - {{$listing['examination']['name']??''}}
        </h2>
    </div>
    <div class="col-md-12 time_progress__main">
        <div class="time_progress__wrap">
            <div class="main-clock-progress-container center">
                <div class="circle-container center">
                    <div class="semicircle"></div>
                    <div class="semicircle"></div>
                    <div class="semicircle"></div>
                    <div class="outermost--circle"></div>
                </div>
                <div class="timer-container center">
                    <div class="timer center"></div>
                </div>
            </div>
        </div>
        <a href="#" class="time_progress__clock">

            <svg xmlns="http://www.w3.org/2000/svg" width="18.839" height="17.395" viewBox="0 0 18.839 17.395">

                <g id="alarm" transform="translate(-48.07 -64)">

                    <path id="Path_359" data-name="Path 359"
                        d="M52.85,66.1a.97.97,0,0,0-.05-1.425A2.839,2.839,0,0,0,50.966,64l-.148,0H50.8a2.975,2.975,0,0,0-2.726,3.031,2.461,2.461,0,0,0,.649,1.681.934.934,0,0,0,.666.353c.012,0,.032,0,.091,0A.863.863,0,0,0,50.1,68.8ZM64.184,64l-.148,0h-.024a2.839,2.839,0,0,0-1.836.679.97.97,0,0,0-.05,1.424l2.753,2.7a.863.863,0,0,0,.625.267.694.694,0,0,0,.091,0,.934.934,0,0,0,.665-.353,2.458,2.458,0,0,0,.649-1.681A2.975,2.975,0,0,0,64.184,64Z"
                        transform="translate(0)" fill="#434343" />

                    <path id="Path_360" data-name="Path 360"
                        d="M88.043,96a7.968,7.968,0,0,0-6.124,13.072l1.025,1.025a7.956,7.956,0,0,0,10.2,0l1.025-1.025A7.968,7.968,0,0,0,88.043,96Zm.725,7.973a.725.725,0,0,1-.725.725H84.419a.725.725,0,1,1,0-1.45h2.9V98.9a.725.725,0,1,1,1.45,0Z"
                        transform="translate(-30.55 -30.55)" fill="#434343" />

                </g>

            </svg>

        </a>
    </div>
</div>
<?php
if (isset($listing['examination_questions']) && count($listing['examination_questions']) > 0) {

    $q_cnt = 1;
    $total_marks = 0;

    ?>
<form method="post" action="{{route('tus_savequiz',Session()->get('tenant_info')['subdomain'])}}"
    enctype="multipart/form-data" id="frmQuiz">
    @csrf
    <div class="col-md-12 form_inside_wrap" style="">
        <div class="row">
            <div class="col-md-7">
                <div class="times_up_msg"></div>
                <div class="student_quiz_owl">
                    
                    @foreach ($listing['examination_questions'] as $ql)
                    @php
                    //dd($ql['point']);
                    $questionInfo=json_decode($ql['question_info']);
                    $total_marks=$total_marks+(float)$ql['point']??'0';
                    @endphp
                    <div class="question_area" id="question_area<?php echo $q_cnt; ?>"
                        data-value="<?php echo $questionInfo->question_type; ?>"
                        data-id="<?php echo $ql['examination_question_id']; ?>"
                        data-qtype="<?php echo $questionInfo->question_type; ?>"
                        style="display: <?php echo $q_cnt == 1 ? 'block' : 'none'; ?>;">

                        <input type="hidden" name="quest_starttime[]"
                            id="quest_starttime_{{$ql['examination_question_id']}}">

                        <input type="hidden" name="quest_endtime[]"
                            id="quest_endtime_{{$ql['examination_question_id']}}">
                        @if($questionInfo->question_type=='radio')
                        <p style="font-weight: 600;letter-spacing: 0px; color: #434343;">
                            <?php echo $q_cnt++; ?>. Choose the correct answer</p>
                        <p style="font: normal normal normal 16px/25px;letter-spacing: 0px; color: #434343;">
                            {!!$questionInfo->question!!}</p>
                        <p>
                            @foreach($questionInfo->options as $k=>$option)
                            <input type="radio" class="form-control-radio ansin_{{$ql['examination_question_id']}}"
                                name="ans_{{$ql['examination_question_id']}}"
                                id="ans_{{$ql['examination_question_id'].$k}}" value="{{$k}}"
                                onclick="answered('{{$ql['examination_question_id']}}','{{$questionInfo->question_type}}');">
                            <label for="ans_{{$ql['examination_question_id'].$k}}"
                                class="option-label">{{$option->option_value}}</label><br>
                            @endforeach
                        </p>
                        @endif
                        @if($questionInfo->question_type=='checkbox')
                        <p style="font-weight: 600;letter-spacing: 0px; color: #434343;">
                            <?php echo $q_cnt++; ?>. Choose the correct answer</p>
                        <p style="font: normal normal normal 16px/25px;letter-spacing: 0px; color: #434343;">
                            {!!$questionInfo->question!!}</p>
                        <p>
                            @foreach($questionInfo->options as $k=>$option)
                            <input type="checkbox"
                                class="form-control-checkbox ansin_{{$ql['examination_question_id']}}"
                                id="ans_{{$ql['examination_question_id'].$k}}"
                                name="ans_{{$ql['examination_question_id']}}" value="{{$k}}"
                                onclick="answered('{{$ql['examination_question_id']}}','{{$questionInfo->question_type}}');">
                            <label for="ans_{{$ql['examination_question_id'].$k}}"
                                class="option-label">{{$option->option_value}}</label><br>
                            @endforeach
                        </p>
                        @endif
                        @if($questionInfo->question_type=='select')
                        <p style="font-weight: 600;letter-spacing: 0px; color: #434343;">
                            <?php echo $q_cnt++; ?>. Choose the correct answer</p>
                        <p style="font: normal normal normal 16px/25px;letter-spacing: 0px; color: #434343;">
                            {!!$questionInfo->question!!}</p>
                        <p>
                            <select class="form-control ansin_{{$ql['examination_question_id']}}"
                                name="ans_{{$ql['examination_question_id']}}"
                                onchange="answered('{{$ql['examination_question_id']}}','{{$questionInfo->question_type}}');">
                                <option></option>
                                @foreach($questionInfo->options as $k=>$option)
                                <option value="{{$k}}">{{$option->option_value}}</option>
                                @endforeach
                            </select>
                        </p>
                        @endif
                        @if($questionInfo->question_type=='text')
                        <p style="font-weight: 600;letter-spacing: 0px; color: #434343;">
                            <?php echo $q_cnt++; ?>. Type your answer</p>
                        <p style="font: normal normal normal 16px/25px;letter-spacing: 0px; color: #434343;">
                            {!!$questionInfo->question!!}</p>
                        <p>
                            <textarea class="form-control ansin_{{$ql['examination_question_id']}}" cols="30" rows="10"
                                name="ans_{{$ql['examination_question_id']}}"
                                onchange="answered('{{$ql['examination_question_id']}}','{{$questionInfo->question_type}}');"></textarea>
                        </p>
                        @endif

                        @if($questionInfo->require_file_upload)
                        <p>
                            Upload Attachment: <input type="file" class="validate-file" name="file_{{$ql['examination_question_id']}}[]" multiple><br>
                            <small>To select multiple files, hold CTRL key and choose files of your choice.<br>Each selected file should be under 2MB. </small>
                        </p>
                        @endif


                    </div>
                    <input type="hidden" name="questid[]" value="<?php echo $ql['examination_question_id']; ?>">

                    @endforeach
                    <span class="total_quiz" data-value="<?php echo --$q_cnt; ?>"></span>
                </div>
                <div class="row">
                    <div class="col-6">
                        <button class="student_quiz_jump_question-btn quest_prev" type="button" role="button"
                            disabled="disabled">
                            Previous
                        </button>
                    </div>
                    <div class="col-6 text-end ">
                        <button class="student_quiz_jump_question-btn quest_next" type="button" role="button" @if(count($listing['examination_questions'])==1) disabled @endif>
                            Next
                        </button>
                    </div>
                </div>

                <input type="hidden" name="total_quiztime" value="{{$listing['examination']['total_time'] ?? 0}}">
                <input type="hidden" name="taken_quiztime" id="taken_quiztime" value="">
                <input type="hidden" name="is_form_valid" id="is_form_valid" value="0">
                <input type="hidden" name="examination_id" id="examination_id"
                    value="{{$listing['examination']['examination_id']}}">
                <input type="hidden" name="total_marks" id="total_marks" value="{{$total_marks}}">
                <input type="hidden" name="quiz_begintime" value="<?php echo date('Y-m-d H:i:s'); ?>">
                <br><br>
                <div class="text-center"><input type="button" class="btn btn-primary submitquiz @if(count($listing['examination_questions'])>1) hide @endif" value="Submit Quiz"></div>
            </div>
            <div class="col-md-5">
                <div class="row" style="background-color: #FFFFFF;">
                    <div class="col-md-12" style="padding: 21px 28px;">
                        <p style="font: normal normal normal 14px/19px;letter-spacing: 0px;color: #434343;">
                            Question Palette :
                        </p>
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <div class="form-check">
                                    <label class="form-check-label know_question_status_ans" for="exampleRadios2"
                                        style="font: normal normal normal 12px/17px;letter-spacing: 0px;color: #434343;">
                                        Answered
                                    </label>
                                </div>
                            </li>
                            <li class="list-inline-item">
                                <div class="form-check">
                                    <label class="form-check-label know_question_status_non_ans" for="exampleRadios2"
                                        style="font: normal normal normal 12px/17px;letter-spacing: 0px;color: #434343;">
                                        Not Answered
                                    </label>
                                </div>
                            </li>
                            <li class="list-inline-item">
                                <div class="form-check">
                                    <label class="form-check-label know_question_status_non_vstd" for="exampleRadios2"
                                        style="font: normal normal normal 12px/17px;letter-spacing: 0px;color: #434343;">
                                        Not Visited
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-12" style="padding: 21px 28px;">
                        <ul class="list-inline">
                            @foreach ($listing['examination_questions'] as $kqx=>$qxl)
                            <li class="list-inline-item answer_square_count <?php echo $kqx == 0 ? 'non_answered' : ''; ?>"
                                id="answer_count{{$qxl['examination_question_id']}}" style="cursor: pointer;">
                                <?php echo ($kqx + 1); ?>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
}?>

@endsection
@section('pagescript')
<?php
$examTotalTime = $listing['examination']['total_time'] ?? 0;
$examTotalTimeArr = explode(':', $examTotalTime);
$examHr = $examTotalTimeArr[0] ?? 0;
$examMin = $examTotalTimeArr[1] ?? 0;
$examSec = $examTotalTimeArr[2] ?? 0;
$totExamMins = ($examHr * 60) + $examMin + ($examSec / 60);
?>
<script>
var min_alloc = "{{$totExamMins}}";
const semicircle = document.querySelectorAll('.semicircle');
const timer = document.querySelector('.timer');

const hr = 0;
const min = min_alloc; //60;
const sec = 0;

const hours = hr * 3600000;
const minutes = min * 60000;
const seconds = sec * 1000;
const setTime = hours + minutes + seconds;
const starTime = Date.now();
const futureTime = starTime + setTime;

const timerLoop = setInterval(countDownTimer);
countDownTimer();

function countDownTimer() {
    const currentTime = Date.now();
    const remainingTime = futureTime - currentTime;
    const angle = (remainingTime / setTime) * 360;

    if (angle > 180) {
        semicircle[2].style.display = 'none';
        semicircle[0].style.transform = 'rotate(180deg';
        semicircle[1].style.transform = `rotate(${angle}deg)`;

    } else {
        semicircle[2].style.display = 'block';
        semicircle[0].style.transform = `rotate(${angle}deg)`;
        semicircle[1].style.transform = `rotate(${angle}deg)`;
    }

    const hrs = Math.floor((remainingTime / (1000 * 60 * 60)) % 24).toLocaleString(
        'en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });
    const mins = Math.floor((remainingTime / (1000 * 60)) % 60).toLocaleString(
        'en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });
    const secs = Math.floor((remainingTime / (1000)) % 60).toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });

    timer.innerHTML = `
            <div>${hrs}</div>
            <div class="colon">:</div>
                <div>${mins}</div>
                    <div class="colon">:</div>
                        <div>${secs}</div>
            `;

    if (remainingTime <= 6000) {
        semicircle[0].style.backgroundColor = 'red';
        semicircle[1].style.backgroundColor = 'red';
    }

    if (remainingTime == 0) {
        clearInterval(timerLoop);
        semicircle[0].style.display = 'none';
        semicircle[1].style.display = 'none';
        semicircle[2].style.display = 'none';

        timer.innerHTML = `
            <div>00</div>
            <div class="colon">:</div>
                <div>00</div>
                    <div class="colon">:</div>
                        <div>00</div>
            `;
    }

    console.log(mins + ':' + secs);

    $('#taken_quiztime').val(mins + ':' + secs);

    if (mins == 0 && secs == 0) {
        clearTimeout(timerLoop);
        //alert('Times Up! Your Quiz will Going to Submit');
        $('.times_up_msg').addClass('alert alert-danger');
        $('.times_up_msg').html('Times Up! Your Quiz will Going to Submit');
        $('.submitquiz').show();
        $('.submitquiz').click();
    }
}

$(document).ready(function() {
    $('.question_area').each(function() {
        if ($(this).attr('style') == 'display: block;') {
            var thisid = $(this).attr('id').replace('question_area', '');
            var quesid = $('#question_area' + thisid).data('id');
            var date = new Date();
            var year = date.getFullYear();
            var month = ("00" + (date.getMonth() + 1)).slice(-2);
            var day = ("00" + date.getDate()).slice(-2);
            var hours = ("00" + date.getHours()).slice(-2);
            var minutes = ("00" + date.getMinutes()).slice(-2);
            var seconds = ("00" + date.getSeconds()).slice(-2);
            currentdate = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;

            $('#quest_starttime_' + quesid).val(currentdate);
        }
    });
});

$('.student_quiz_optiondiv').click(function() {

    var this_parentid = $(this).parent().attr('id').replace('question_area', '');

    // alert(this_parentid);

    if ($(this).parent().attr('data-value') == 'radio' || $(this).parent().attr('data-value') == 'select') {

        $(this).parent().find('.student_quiz_optiondiv').each(function() {

            $(this).removeClass('active');

            $(this).children().val(0);

        });
    }

    $(this).toggleClass('active');

    if ($(this).hasClass('active')) {

        $(this).children().val(1);
    } else {

        $(this).children().val(0);
    }

    var total_active = $(this).parent().find('.active').length;

    if (total_active == 0) {

        $('#answer_count' + this_parentid).removeClass('answered');

        $('#answer_count' + this_parentid).addClass('non_answered');
    } else {

        $('#answer_count' + this_parentid).removeClass('non_answered');

        $('#answer_count' + this_parentid).addClass('answered');
    }
});

$('.quest_next').click(function() {

    $('.question_area').each(function() {

        if ($(this).attr('style') == 'display: block;') {

            var thisid = $(this).attr('id').replace('question_area', '');

            // $(this).find('.student_quiz_optiondiv').each(function() {
            //     //console.log('id= '+$(this).attr('id'));
            //     console.log('id= ' + (parseInt(thisid) + 1));
            //     if ($(this).hasClass('active'))
            //         $('#answer_count' + thisid).addClass('answered');
            //     else
            //         $('#answer_count' + (parseInt(thisid) + 1)).addClass('non_answered');
            // });

            $('#question_area' + thisid).css('display', 'none');

            $('.quest_prev').removeAttr('disabled');
            var prev_quesid = $('#question_area' + thisid).data('id');
            var nxtthis = (++thisid);

            $('#question_area' + nxtthis).css('display', 'block');
            var quesid = $('#question_area' + nxtthis).data('id');
            var qtype = $('#question_area' + nxtthis).data('qtype');
            answered(quesid, qtype);

            var date = new Date();
            var year = date.getFullYear();
            var month = ("00" + (date.getMonth() + 1)).slice(-2);
            var day = ("00" + date.getDate()).slice(-2);
            var hours = ("00" + date.getHours()).slice(-2);
            var minutes = ("00" + date.getMinutes()).slice(-2);
            var seconds = ("00" + date.getSeconds()).slice(-2);
            currentdate = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;

            $('#quest_starttime_' + quesid).val(currentdate);

            $('#quest_endtime_' + prev_quesid).val(currentdate);

            if (thisid == $('.total_quiz').attr('data-value')){
                $('.quest_next').attr('disabled', 'disabled');
                // $('.submitquiz').removeAttr('disabled');
                $('.submitquiz').show();
            }else{
                // $('.submitquiz').attr('disabled', 'disabled');
                $('.submitquiz').hide();
            }
            return false;

        }

    });

});

$('.quest_prev').click(function() {
    $('.question_area').each(function() {
        if ($(this).attr('style') == 'display: block;') {
            var thisid = $(this).attr('id').replace('question_area', '');
            $('#question_area' + thisid).css('display', 'none');
            $('.quest_next').removeAttr('disabled');
            if (thisid == 2)
                $('.quest_prev').attr('disabled', 'disabled');

            $('#question_area' + (--thisid)).css('display', 'block');

            if (thisid == $('.total_quiz').attr('data-value')){
                // $('.submitquiz').removeAttr('disabled');
                $('.submitquiz').show();
            }else{
                // $('.submitquiz').attr('disabled', 'disabled');
                $('.submitquiz').hide();
            }
            return false;

        }

    });

});

function answered(question_id, typc) {
    if (typc == 'text' || typc == 'select') {
        if ($('.ansin_' + question_id).val() != '') {
            $('#answer_count' + question_id).addClass('answered');
            $('#answer_count' + question_id).removeClass('non_answered');
        } else {
            $('#answer_count' + question_id).addClass('non_answered');
            $('#answer_count' + question_id).removeClass('answered');
        }
    } else if (typc == 'radio' || typc == 'checkbox') {
        ctlName = 'ans_' + question_id;
        if ($('input[name=' + ctlName + ']:checked').length > 0) {
            $('#answer_count' + question_id).addClass('answered');
            $('#answer_count' + question_id).removeClass('non_answered');
        } else {
            $('#answer_count' + question_id).addClass('non_answered');
            $('#answer_count' + question_id).removeClass('answered');
        }
    } else {

        $('#answer_count' + question_id).addClass('answered');
        $('#answer_count' + question_id).removeClass('non_answered');
    }
}
$('.submitquiz').click(function() {
    $('.question_area').each(function() {
        if ($(this).attr('style') == 'display: block;') {
            var thisid = $(this).attr('id').replace('question_area', '');
            var quesid = $('#question_area' + thisid).data('id');
            var date = new Date();
            var year = date.getFullYear();
            var month = ("00" + (date.getMonth() + 1)).slice(-2);
            var day = ("00" + date.getDate()).slice(-2);
            var hours = ("00" + date.getHours()).slice(-2);
            var minutes = ("00" + date.getMinutes()).slice(-2);
            var seconds = ("00" + date.getSeconds()).slice(-2);
            currentdate = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;

            $('#quest_endtime_' + quesid).val(currentdate);
        }
    });
    let is_form_valid=$('#is_form_valid').val();
    if(is_form_valid==0){
        $('#frmQuiz').submit();
    }
});
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
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection