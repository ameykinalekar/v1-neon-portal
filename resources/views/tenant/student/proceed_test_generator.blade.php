@extends('layouts.default')
@section('title', 'Test Generator')
@section('pagecss')
<style type="text/css">
.rotate-me {
    -ms-writing-mode: tb-rl;
    -webkit-writing-mode: vertical-rl;
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    white-space: nowrap;

}

.thead-dark th {
    color: white;
    font-weight: 600;
    font-size: 12px;
}

.table-bordered>:not(caption)>*>* {
    padding: 0px;
}

tr th,
tr td {
    font-family: Poppins;
    font-weight: 400;
    font-size: 11px;
}
</style>
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title d-inline-block">
                    Test Generator
                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <form id="frmTG" action="{{route('tus_testgen_save',Session()->get('tenant_info')['subdomain'])}}" method="post" onsubmit="return validateForm();">
                    @csrf
                    <input type="text" name="year_group_id" id="" value="{{$year_group_id ?? ''}}">
                    <input type="text" name="subject_id" id="" value="{{$subject_id ?? ''}}">
                    <input type="text" name="lesson_ids" id="" value="{{$year_group_id ?? ''}}">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="exam_name">Test Name</label>
                                <input type="text" name="exam_name" id="exam_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="exam_date">Test Date</label>
                                <input type="date" name="exam_date" id="exam_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="no_questions">No. of Questions</label>
                                <input type="number" min="1" step="any" name="no_questions" id="no_questions"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="total_time_mins">Allotted Time <small>(in Mins)</small></label>
                                <input type="number" min="1" step="any" name="total_time_mins" id="total_time_mins"
                                    class="form-control" required onkeyup="calTotalMarks();">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="marks_each_question">Marks per question</label>
                                <input type="number" min="1" step="any" name="marks_each_question"
                                    id="marks_each_question" class="form-control" required onkeyup="calTotalMarks();">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1" style="text-align:left">
                                <label for="total_marks">Total Marks</label>
                                <input type="number" min="1" step="any" name="total_marks" id="total_marks"
                                    class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-1 mt-2" style="text-align:left">
                                <label>Choose question dificulty level</label>
                                @if(count($question_levels)>0)
                                <div class="row">
                                    @foreach($question_levels as $qlevel)
                                    <div class="col-md-4">
                                        <div class="form-group mb-1" style="text-align:left">
                                            <label
                                                for="level_{{$qlevel['level_key']}}">{{$qlevel['level_text']}}</label>
                                            <select name="level_{{$qlevel['level_key']}}"
                                                id="level_{{$qlevel['level_key']}}" class="form-control select2_el dlevels"
                                                required>
                                                <option value="0">0%</option>
                                                <option value="10">10%</option>
                                                <option value="20">20%</option>
                                                <option value="30">30%</option>
                                                <option value="40">40%</option>
                                                <option value="50">50%</option>
                                                <option value="60">60%</option>
                                                <option value="70">70%</option>
                                                <option value="80">80%</option>
                                                <option value="90">90%</option>
                                                <option value="100">100%</option>
                                            </select>
                                        </div>

                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div class="form-group mt-2 col-md-12">
                                    <button class="btn btn-block btn-primary" type="submit" id="btnSubmit">Generate Test</button>
                                </div>
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
<script src="{{ asset('admin/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    initailizeSelect2();
});
// Initialize select2
function initailizeSelect2() {
    $(".select2_el").select2({

    });
}

function calTotalMarks(){
    let no_questions=$('#no_questions').val();
    let marks_each_question=$('#marks_each_question').val();
    let total_marks=0;

    if(no_questions!=null && marks_each_question!=null){
        total_marks=eval(no_questions)*eval(marks_each_question);
    }

    $('#total_marks').val(total_marks);
}
function checkAllDLevels(){
    var level_val=0;
    $('.dlevels').each(function(i, obj){
        level_val=eval(level_val)+eval(obj.value);
    });
    // alert(level_val);
    if(eval(level_val)==100){
        return true;
    }else{
        return false;
    }
}

function validateForm(){
    const levelCheck=checkAllDLevels();
    console.log(levelCheck);
    if(!levelCheck){
        alert("Summation of all difficulty level must be 100%.");
        return false;
    }
    return true;
}

</script>

@endsection