@extends('layouts.default')
@section('title', 'Dashboard')
@section('pagecss')
<style>


.alert.alert-info {
    display: none !important
}

.dash-box {
    padding: 15px 15px;
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
.orange{
    border-left: 10px solid #FFA000;
}
.pink {
    /* background: orange; */
    border-left: 10px solid #F78BFF;
}
.navyblue{
    /* background: orange; */
    border-left: 10px solid #0091FF ;
}
.green{
    /* background: orange; */
    border-left: 10px solid #8BE869 ;
}
.sky{
    /* background: orange; */
    border-left: 10px solid #5ACEFF ;
}
.dash-box img {
    width: 80px
}

.dash-cont h5 {
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
.dash-cont h5{
margin:-15px 0;
}
</style>
@endsection
@section('content')

<div class="px-2">
    <div class="row">
        <div class="col-md-12" style="width: 100%;
                                     height: 40px;background:white;border-radius: 5px;left: 398px;">
            <h4 style="vertical-align:middle">
                Overview</h4>
        </div>
    </div>

    <div class="row" style="margin-top: 15px;align:center">
        <div class="col-md-4" style="width:309px;height: 126px;
  ">
            <div class="dash-box grn">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>Overall Attendance</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">

                            <a href="{{route('tus_attendances',Session()->get('tenant_info')['subdomain'])}}"><img src="{{ asset('img/system/attendance.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;"></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:309px;height: 126px;
  ">
            <div class="dash-box pink">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>Signals</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <a href="{{route('tus_studentsignals',Session()->get('tenant_info')['subdomain'])}}"><img src="{{ asset('img/system/radio-waves.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;"></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:309px;height: 126px;
  ">
            <div class="dash-box orange">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>Skill Map</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <a href="{{route('tus_skillmap',Session()->get('tenant_info')['subdomain'])}}"><img src="{{ asset('img/system/creative-thinking.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;"></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:310px;height: 126px;
  ">
            <div class="dash-box yellow">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>Report Card</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <a href="{{route('tus_studentreportcard',Session()->get('tenant_info')['subdomain'])}}"><img src="{{ asset('img/system/check-list.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;" style="width: 60px;height: 60px;padding-left:10px;"></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:310px;height: 126px;
  ">
            <div class="dash-box red">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>Leaderboard</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <a href="{{route('tus_leaderboard',Session()->get('tenant_info')['subdomain'])}}"><img src="{{ asset('img/system/competition.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;"></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:310px;height: 126px;
  ">
            <div class="dash-box blue">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>UK Grade Boundaries</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <img src="{{ asset('img/system/best.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:310px;height: 126px;
  ">
            <div class="dash-box purple">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>Behavioral</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <a href="{{route('tus_behavioral',Session()->get('tenant_info')['subdomain'])}}"> <img src="{{ asset('img/system/evaluation.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;"></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" style="width:310px;height: 126px;
  ">
            <div class="dash-box sky">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>Pastoral</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <a href="{{route('tus_pastoralcare',Session()->get('tenant_info')['subdomain'])}}"><img src="{{ asset('img/system/darts.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;"></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" style="width:310px;height: 126px;
  ">
            <div class="dash-box green">
                <div class="row">
                    <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                        <div class="dash-cont">
                            <h5>Rewards</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-ico text-center">
                            <a href="{{route('tus_reward',Session()->get('tenant_info')['subdomain'])}}"> <img src="{{ asset('img/system/medal.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;"></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="width:310px;height: 126px;
  ">
                <div class="dash-box navyblue">
                    <div class="row">
                        <div class="col-md-6" style="border-right:1px solid #dbdbdb">
                            <div class="dash-cont">
                                <h5>Exam Result</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dash-ico text-center">
                                <a href="{{route('tus_reward',Session()->get('tenant_info')['subdomain'])}}"><img src="{{asset('img/system/notepad.png') }}" alt="" style="width: 60px;height: 60px;padding-left:10px;"></a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>




    </div>

    <div class="row " style="margin-top: 15px;">

    </div>

</div>

@endsection

@section('pagescript')


@endsection
