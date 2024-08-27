@extends('layouts.default')
@section('title', 'Dashboard')
@section('pagecss')
<style>
.dash-inr {
    margin: -45px 15px;
}

.alert.alert-info {
    display: none !important
}

.dash-box {
    padding: 15px 30px;
    border-radius: 7px;
    margin-bottom: 26px;
    background: #fff;
    /* color:#dbdbdb; */
}

.grn {
    /* background: #30a24b; */
    border-left: 10px solid #5BC2B9;
}

.red {
    /* background: #f3425f; */
    border-left: 10px solid #E87E69;
}

.blue {
    /* background: #763ee7; */
    border-left: 10px solid #4C94DB;
}

.purple {
    /* background: #1878f3; */
    border-left: 10px solid #5C4D8F;
}

.yellow {
    /* background: orange; */
    border-left: 10px solid #FCC244;
}

.dash-box img {
    width: 80px
}

.dash-cont h4 {
    /* color: #fff; */
    padding-top: 15px
}

.numb h5 {
    /* color: #fff; */
    font-size: 28px;
}

.view-more a img {
    width: 22px;
    padding-top: 19px;
}

.numb {
    text-align: right;
}

.view-more {
    text-align: right;
}
.dash-cont h4{
margin:-15px 0;
}
</style>
@endsection
@section('content')

<div class="px-2">
    <div class="row">
        <div class="col-md-12" style="width: 100%;
                                     height: 50px;background:white;border-radius: 5px;left: 398px;">
            <h4 style="">
                Overview</h4>
        </div>
    </div>

    <div class="row" style="margin-top: 5px;align:center">
        <div class="col-md-4" style="width:309px;height: 126px;padding-top: 15px;
  padding-left: 2px;">
            <div class="dash-box grn">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h4>Overall Attendance</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <img src="{{ asset('img/system/attendance.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:309px;height: 126px;padding-top: 15px;
  padding-left: 2px;">
            <div class="dash-box yellow">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h4>My Training Plan</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <img src="{{ asset('img/system/check-list.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:309px;height: 126px;padding-top: 15px;
  padding-left: 2px;">
            <div class="dash-box red">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h4>Leader Board</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <img src="{{ asset('img/system/competition.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:310px;height: 126px;padding-top: 25px;
  padding-left: 0px;">
            <div class="dash-box blue">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h4>UK Grade Boundaries</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <img src="{{ asset('img/system/best.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;" style="width: 60px;height: 60px;padding-left:10px;">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:300px;height: 126px;padding-top: 25px;
  padding-left: 0px;">
            <div class="dash-box purple">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h4>Behavioral Score</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <img src="{{ asset('img/system/evaluation.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;">
                        </div>

                    </div>
                </div>
            </div>
        </div>



    </div>

    <div class="row " style="margin-top: 5px;">

    </div>

</div>

@endsection

@section('pagescript')


@endsection
