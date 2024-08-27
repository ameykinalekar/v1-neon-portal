<!--- Sidemenu -->
<?php 
$planFeatures=array();
if($tenantInfo['features_available'] != ''){
    $planFeatures=json_decode($tenantInfo['features_available']);
}
// print_r($planFeatures);
?>
@if($userInfo['role']=='S' && $userInfo['user_type']=='TU')
<ul class="side-nav">
    <!-- <li class="side-nav-title side-nav-item py-1">Proposed Navigation</li> -->
    <li class="side-nav-item">
        <a href="{{route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/home394.png')}}">
            <span > Home</span>
        </a>
    </li>
    @if(!empty($planFeatures) && $planFeatures->ai_copilot>0)
    <li class="side-nav-item  "> <a data-bs-toggle="collapse" href="#ai_pilot" aria-controls="ai_pilot"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(), array('get_ai_help')))
            aria-expanded=true @else aria-expanded=false @endif>
            <img width="22" height="22" src="{{asset('img/system/icons/ai_pilot.png')}}">
            <span >AI Co-Pilot</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('get_ai_help'))) show @endif" id="ai_pilot">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('get_ai_help'))) class="active" @endif><a href="{{route('get_ai_help', Session()->get('tenant_info')['subdomain'])}}">AI help 24x7</a></li>
                <li><a href="#">AI Search Library</a></li>
            </ul>
        </div>
    </li>
    @endif
    <li class="side-nav-item">
        <a href="{{route('tus_studentdashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/analytics.png')}}">
            <span > Dashboard</span>
        </a>
    </li>
    @if(!empty($planFeatures) && $planFeatures->analytics_studio>0)
    <li class="side-nav-item">
        <a href="{{route('tus_analyticstudio', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/home.png')}}">
            <span > Analytics Studio</span>
        </a>
    </li>
    @endif
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#my_course" aria-controls="my_course"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(), array('tus_mycourseplan','tus_lessons','tus_skillmap','tus_starget','tus_coursestatus'.'tus_calendar')))
            aria-expanded=true @else aria-expanded=false @endif >

            <img width="22" height="22" src="{{asset('img/system/icons/my_course.png')}}">
            <span >My Courses</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tus_mycourses','tus_mycourseplan','tus_skillmap','tus_starget','tus_coursestatus','tus_calendar'))) show @endif" id="my_course">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('tus_mycourses','tus_mycourseplan'))) class="active" @endif><a href="{{route('tus_mycourses', Session()->get('tenant_info')['subdomain'])}}">Subjects /Topics</a></li>
                <li @if(in_array(Route::current()->getName(), array('tus_starget'))) class="active" @endif><a href="{{route('tus_starget', Session()->get('tenant_info')['subdomain'])}}">Target</a></li>
                <li @if(in_array(Route::current()->getName(), array('tus_skillmap'))) class="active" @endif><a href="{{route('tus_skillmap', Session()->get('tenant_info')['subdomain'])}}">AI Adaptive Learning Plan</a></li>
                <li @if(in_array(Route::current()->getName(), array('tus_coursestatus'))) class="active" @endif><a href="{{route('tus_coursestatus',Session()->get('tenant_info')['subdomain'])}}">Course Completion Status</a></li>
                <li @if(in_array(Route::current()->getName(), array('tus_calendar'))) class="active" @endif><a href="{{route('tus_calendar',Session()->get('tenant_info')['subdomain'])}}">Calendar</a></li>
            </ul>
        </div>
    </li>
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#academics" aria-controls="academics"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(), array('tus_quizes','tus_assesments','tus_quiz','tus_assesment','tus_homework')))
            aria-expanded=true @else aria-expanded=false @endif >

            <img width="22" height="22" src="{{asset('img/system/icons/academic.png')}}">
            <span >Academics</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tus_quizes','tus_assesments','tus_quiz','tus_assesment','tus_homework'))) show @endif" id="academics">
            <ul class="side-nav-second-level">
                <!-- <li><a href="#">Adminssion Test</a></li> -->

                <li @if(in_array(Route::current()->getName(), array('tus_quizes','tus_quiz'))) class="active" @endif>
                    <a href="{{route('tus_quizes', Session()->get('tenant_info')['subdomain'])}}">Automated Quiz</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tus_assesments','tus_assesment'))) class="active"
                    @endif>
                    <a href="{{route('tus_assesments', Session()->get('tenant_info')['subdomain'])}}">Automated Assessment</a>
                </li>
                <li><a href="#">Marking Scheme Analysis</a></li>
                <li @if(in_array(Route::current()->getName(), array('tus_homework'))) class="active"
                @endif><a href="{{route('tus_homework', Session()->get('tenant_info')['subdomain'])}}">Home Work</a></li>

            </ul>
        </div>
    </li>
    @if(!empty($planFeatures) && $planFeatures->attendance>0)
    <li class="side-nav-item">
        <a href="{{route('tus_attendances', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/user4.png')}}">
            <span > Attendance</span>
        </a>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->test_gen>0)
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#aitestgen" aria-controls="aitestgen"
            class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/reader.png')}}">
            <span >AI Test Generator</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="aitestgen">
            <ul class="side-nav-second-level">
                <li><a href="#">Allocated Test</a></li>
                <li><a href="{{route('tus_testgen', Session()->get('tenant_info')['subdomain'])}}">Self Practices</a></li>
                <li><a href="#">Self Allocated Test</a></li>
                <li><a href="{{route('tus_achievement', Session()->get('tenant_info')['subdomain'])}}">Achievement Tracker</a></li>
            </ul>
        </div>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->library>0)
    <li class="side-nav-item">
        <a href="{{route('tus_mylibrary', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">
            <img width="22" height="22" src="{{asset('img/system/icons/Library.png')}}">
            <span >Library</span>
        </a>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->ntp>0)
    <li class="side-nav-item">
        <a href="{{route('tus_ntpsupport', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">
            <img width="22" height="22" src="{{asset('img/system/icons/ntp.png')}}">
            <span >NTP Support</span>
        </a>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->study_group>0)
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#mygroups" aria-controls="mygroups"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(), array('tus_studygroups')))
            aria-expanded=true @else aria-expanded=false @endif >

            <img width="22" height="22" src="{{asset('img/system/icons/study.png')}}">
            <span >My Groups</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tus_studygroups'))) show @endif" id="mygroups">
            <ul class="side-nav-second-level">
                <li><a href="{{route('tus_studygroups', Session()->get('tenant_info')['subdomain'])}}">School Clubs</a></li>
                <li><a href="{{route('tus_studygroups', Session()->get('tenant_info')['subdomain'])}}">Personal Groups</a></li>
            </ul>
        </div>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->global_chat>0)
    <li class="side-nav-item">
        <a href="#" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/global_chat.png')}}">
            <span > Global Chat</span>
        </a>
    </li>
    @endif

    <!-- <li class="side-nav-item">
        <a href="#" class="side-nav-link">
            <img width="22" height="22" src="{{asset('img/system/icons/npay.png')}}">
            <span >N-Pay</span>
        </a>
    </li> -->
    <!-- <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#npay" aria-controls="npay"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/npay.png')}}">
            <span >NPay</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="npay">
            <ul class="side-nav-second-level">
                <li><a href="#">Wallet</a></li>
                <li><a href="#">e-Shop</a></li>
                <li><a href="#">Fee Subscription</a></li>
                <li><a href="#">Order History</a></li>
            </ul>
        </div>
    </li>
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#inbox" aria-controls="inbox"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/Mail.png')}}">
            <span > Inbox</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="inbox">
            <ul class="side-nav-second-level">
                <li><a href="#">To Parent</a></li>
                <li><a href="#">To Student</a></li>
                <li><a href="#">Automated Alerts</a></li>
            </ul>
        </div>
    </li> -->
    @if(!empty($planFeatures) && $planFeatures->teacher_review>0)
    <li class="side-nav-item"> <a d href="{{route('tus_teacherrating', Session()->get('tenant_info')['subdomain'])}}"  class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/star.png')}}">
            <span >Teacher Review</span>
        </a>
       <!-- <div class="collapse" id="teacher_review">
            <ul class="side-nav-second-level">
                <li><a href="#">Rate The Teacher/Content</a></li>
                <li><a href="#"></a></li>
            </ul>
        </div>-->
    </li>
    @endif
    <!-- Commented as moved in dashboard section -->
     <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#exam" @if(in_array(Route::current()->getName(),
            array('tus_quiz_marks','tus_assessment_marks','tus_reviewed_answers'))) aria-expanded=true @else
            aria-expanded=false @endif aria-controls="exam"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/Exam.png')}}">
            <span >Exam Results</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tus_quiz_marks','tus_assessment_marks','tus_reviewed_answers'))) show @endif"
            id="exam">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('tus_quiz_marks'))) class="active" @endif>
                    <a href="{{route('tus_quiz_marks', Session()->get('tenant_info')['subdomain'])}}">Quiz Marks</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tus_assessment_marks'))) class="active" @endif>
                    <a href="{{route('tus_assessment_marks', Session()->get('tenant_info')['subdomain'])}}">Assessment
                        Marks</a>
                </li>
            </ul>
        </div>

    </li>
    @if(!empty($planFeatures) && $planFeatures->inbox>0)
    <li class="side-nav-item">
        <a href="{{route('tus_inbox',Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/notification.png')}}">
            <span > Inbox</span>
        </a>
    </li>
    @endif
    <!-- <li class="side-nav-title side-nav-item py-1">Existing Navigation</li>
    <li class="side-nav-item">
        <a href="{{route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/home.png')}}">
            <span > Home </span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{route('tus_mycourses', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/my_course.png')}}">
            <span >My Courses</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#academic" aria-expanded="false" aria-controls="academic"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(),
            array('tut_mycourses','tus_assesments','tus_quizes','tus_quiz','tus_assesment'))) aria-expanded=true @else
            aria-expanded=false @endif >

            <img width="22" height="22" src="{{asset('img/system/icons/academic.png')}}">
            <span >Academic</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tus_quizes','tus_assesments','tus_quiz','tus_assesment'))) show @endif"
            id="academic" style="">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('tus_quizes','tus_quiz'))) class="active" @endif>
                    <a href="{{route('tus_quizes', Session()->get('tenant_info')['subdomain'])}}">Quiz</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tus_assesments','tus_assesment'))) class="active"
                    @endif>
                    <a href="{{route('tus_assesments', Session()->get('tenant_info')['subdomain'])}}">Assessments</a>
                </li>

            </ul>
        </div>

    </li>
    <li class="side-nav-item">
        <a href="{{route('tus_attendances', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/my_course.png')}}">
            <span >Attendances</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{route('tus_mylibrary', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/Library.png')}}">
            <span >Library</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{route('tus_studygroups', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/study.png')}}">
            <span >Study Groups</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="#" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/Global Chat.png')}}">
            <span >Global Chat</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#exam" @if(in_array(Route::current()->getName(),
            array('tus_quiz_marks','tus_assessment_marks','tus_reviewed_answers'))) aria-expanded=true @else
            aria-expanded=false @endif aria-controls="exam"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/Exam.png')}}">
            <span >Exam</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tus_quiz_marks','tus_assessment_marks','tus_reviewed_answers'))) show @endif"
            id="exam">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('tus_quiz_marks'))) class="active" @endif>
                    <a href="{{route('tus_quiz_marks', Session()->get('tenant_info')['subdomain'])}}">Quiz Marks</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tus_assessment_marks'))) class="active" @endif>
                    <a href="{{route('tus_assessment_marks', Session()->get('tenant_info')['subdomain'])}}">Assessment
                        Marks</a>
                </li>
            </ul>
        </div>

    </li>
    <li class="side-nav-item">
        <a href="#" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Analytics Studio</span>
        </a>
    </li> -->
    
</ul>
@elseif($userInfo['role']=='T' && $userInfo['user_type']=='TU')
<ul class="side-nav">
    <!-- <li class="side-nav-title side-nav-item py-1">Proposed Navigation</li> -->
    <li class="side-nav-item">
        <a href="{{route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/home394.png')}}">
            <span > Home</span>
        </a>
    </li>
    @if(!empty($planFeatures) && $planFeatures->ai_copilot>0)
    <li class="side-nav-item  "> <a data-bs-toggle="collapse" href="#ai_pilot" aria-controls="ai_pilot"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(), array('get_ai_help')))
            aria-expanded=true @else aria-expanded=false @endif>
            <img width="22" height="22" src="{{asset('img/system/icons/ai_pilot.png')}}">
            <span >AI Co-Pilot</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('get_ai_help'))) show @endif" id="ai_pilot">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('get_ai_help'))) class="active" @endif><a href="{{route('get_ai_help', Session()->get('tenant_info')['subdomain'])}}">AI help 24x7</a></li>
                <li><a href="#">AI Search Library</a></li>
            </ul>
        </div>
    </li>
    @endif
    <li class="side-nav-item">
        <a href="{{route('tut_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/analytics.png')}}">
            <span > Dashboard</span>
        </a>
    </li>
    @if(!empty($planFeatures) && $planFeatures->attendance>0)
    <li class="side-nav-item">
        <a href="{{route('tut_attendances', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/user4.png')}}">
            <span > Record Attendance</span>
        </a>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->analytics_studio>0)
    <li class="side-nav-item">
        <a href="#" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/home.png')}}">
            <span > Analytics Studio</span>
        </a>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->teacher_review>0)
    <li class="side-nav-item" @if(in_array(Route::current()->getName(), array('tut_myrating'))) class="active" @endif>
        <a href="{{route('tut_myrating', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/star.png')}}">
            <span > Teacher Review</span>
        </a>
        
    </li>
    @endif
    <!-- <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#teacher_review" aria-controls="teacher_review"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/star.png')}}">
            <span >Teacher Review</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="teacher_review">
            <ul class="side-nav-second-level">
                <li><a href="#">My Rating</a></li>
                <li><a href="#">Content Rating</a></li>
            </ul>
        </div>
    </li> -->
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#my_course" aria-controls="my_course"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(), array('tut_mycourses','tut_lessons','tut_alllessons','tut_teacherassistant','tut_skillmap','tut_starget','tut_adaptivelearn','tut_coursestatus')))
            aria-expanded=true @else aria-expanded=false @endif >

            <img width="20" height="20" src="{{asset('img/system/icons/my_course.png')}}">
            <span >My Classes</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tut_mycourses','tut_lessons','tut_alllessons','tut_teacherassistant','tut_skillmap','tut_starget','tut_adaptivelearn','tut_coursestatus'))) show @endif" id="my_course">
            <ul class="side-nav-second-level">
                @if(!empty($planFeatures) && $planFeatures->subject>0)
                <li @if(in_array(Route::current()->getName(), array('tut_mycourses','tut_lessons'))) class="active" @endif><a href="{{route('tut_mycourses', Session()->get('tenant_info')['subdomain'])}}">Subjects /Topics</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->lesson_plan>0)
                <li @if(in_array(Route::current()->getName(), array('tut_alllessons'))) class="active" @endif><a href="{{route('tut_alllessons', Session()->get('tenant_info')['subdomain'])}}">Lesson Plan</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->skillmap>0)
                <li @if(in_array(Route::current()->getName(), array('tut_skillmap'))) class="active" @endif><a href="{{route('tut_skillmap', Session()->get('tenant_info')['subdomain'])}}">Skill Map</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->target>0)
                <li @if(in_array(Route::current()->getName(), array('tut_starget'))) class="active" @endif><a href="{{route('tut_starget', Session()->get('tenant_info')['subdomain'])}}">Student Target Setting</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->ai_adaptive_learning_plan>0)
                <li @if(in_array(Route::current()->getName(), array('tut_adaptivelearn'))) class="active" @endif><a href="{{ route('tut_adaptivelearn', Session()->get('tenant_info')['subdomain'])}}">AI Adaptive Learning Plan</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->course_completion_status>0)
                <li @if(in_array(Route::current()->getName(), array('tut_coursestatus'))) class="active" @endif><a href="{{ route('tut_coursestatus', Session()->get('tenant_info')['subdomain'])}}">Course Completion Status</a></li>
                @endif

                <li @if(in_array(Route::current()->getName(), array('tut_teacherassistant'))) class="active" @endif><a href="{{route('tut_teacherassistant', Session()->get('tenant_info')['subdomain'])}}">Teaching Assistants</a></li>
            </ul>
        </div>
    </li>
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#academics" aria-controls="academics"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(), array('tut_quizes','tut_assesments','tut_editquiz','tut_quiz_submitted','tut_quiz_review','tut_assessment_submitted','tut_assessment_review','tut_editassesment','tut_calendar','tut_homework')))
            aria-expanded=true @else aria-expanded=false @endif >

            <img width="22" height="22" src="{{asset('img/system/icons/academic.png')}}">
            <span >Academics</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tut_quizes','tut_editquiz','tut_quiz_submitted','tut_quiz_review','tut_assesments','tut_assessment_submitted','tut_assessment_review','tut_editassesment','tut_calendar','tut_homework'))) show @endif" id="academics">
            <ul class="side-nav-second-level">
                @if(!empty($planFeatures) && $planFeatures->admission_test>0)
                <!-- <li><a href="#">Adminssion Test</a></li> -->
                 @endif
                @if(!empty($planFeatures) && $planFeatures->ai_automated_quiz>0)
                <li @if(in_array(Route::current()->getName(), array('tut_quizes','tut_editquiz'))) class="active"
                @endif><a href="{{route('tut_quizes', Session()->get('tenant_info')['subdomain'])}}">Automated Quiz</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->ai_automated_assessment>0)
                <li @if(in_array(Route::current()->getName(), array('tut_assesments','tut_editassesment'))) class="active" @endif><a href="{{route('tut_assesments', Session()->get('tenant_info')['subdomain'])}}">Automated Assesment</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->marking_scheme_analysis>0)
                <li><a href="#">Marking Scheme Analysis</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->calendar>0)
                <li @if(in_array(Route::current()->getName(), array('tut_calendar'))) class="active" @endif><a href="{{route('tut_calendar', Session()->get('tenant_info')['subdomain'])}}">Task Calendar</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->homework>0)
                <li @if(in_array(Route::current()->getName(), array('tut_homework'))) class="active" @endif><a href="{{route('tut_homework', Session()->get('tenant_info')['subdomain'])}}">Home Work</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->report_card>0)
                <li><a href="#">Report Cards</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->ntp>0)
                <li><a href="#">NTP Support</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->ai_automated_quiz>0)
                <li @if(in_array(Route::current()->getName(), array('tut_quiz_submitted','tut_quiz_review')))
                    class="active" @endif>
                    <a href="{{route('tut_quiz_submitted', Session()->get('tenant_info')['subdomain'])}}">Review Quiz
                        Submissions</a>
                </li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->ai_automated_assessment>0)
                <li @if(in_array(Route::current()->getName(), array('tut_assessment_submitted','tut_assessment_review')))
                    class="active" @endif>
                    <a href="{{route('tut_assessment_submitted', Session()->get('tenant_info')['subdomain'])}}">Review
                        Assessment Submissions</a>
                </li>
                @endif
            </ul>
        </div>
    </li>
    <li class="side-nav-item  "> <a data-bs-toggle="collapse" href="#users" @if(in_array(Route::current()->getName(),
            array('tut_students'))) aria-expanded=true @else aria-expanded=false @endif aria-controls="users"
            class="side-nav-link py-1">
            <!-- <i class="User"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Users</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tut_students'))) show @endif" id="users">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('tut_students'))) class="active" @endif>
                    <a href="{{route('tut_students', Session()->get('tenant_info')['subdomain'])}}">Students</a>
                </li>


            </ul>
        </div>
    </li>
    @if(!empty($planFeatures) && $planFeatures->test_gen>0)
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#aitestgen" aria-controls="aitestgen"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/reader.png')}}">
            <span >AI Test Generator</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="aitestgen">
            <ul class="side-nav-second-level">
                <li><a href="#">Self Test</a></li>
                <li><a href="#">Achievement Tracker</a></li>
                <li><a href="#">All Tests</a></li>
            </ul>
        </div>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->ofstead>0)
    <li class="side-nav-item">
        <a href="#" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/stats-chart.png')}}">
            <span > Ofsted Tracker</span>
        </a>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->library>0)
    <li class="side-nav-item">
        <a href="{{route('tut_mylibrary', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/Library.png')}}">
            <span >Library</span>
        </a>
    </li>
    @endif
    <!-- <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#library" aria-controls="library"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/Library.png')}}">
            <span >Library</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="library">
            <ul class="side-nav-second-level">
                <li><a href="#">Recorded Videos</a></li>
                <li><a href="#">Assessments</a></li>
                <li><a href="#">PPT</a></li>
                <li><a href="#">Teacher's Notes</a></li>
                <li><a href="#">Others</a></li>
                <li><a href="#">Links</a></li>
            </ul>
        </div>
    </li> -->
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#exam"  @if(in_array(Route::current()->getName(),
            array('tut_quiz_reviews','tut_assessment_reviews','tut_reviewed_answers'))) aria-expanded=true @else aria-expanded=false @endif  aria-controls="exam" class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/Exam.png')}}">
            <span >Exam Result</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tut_quiz_reviews','tut_assessment_reviews','tut_reviewed_answers'))) show @endif" id="exam">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('tut_quiz_reviews'))) class="active" @endif>
                    <a href="{{route('tut_quiz_reviews', Session()->get('tenant_info')['subdomain'])}}">Quiz Marks</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tut_assessment_reviews'))) class="active" @endif>
                    <a href="{{route('tut_assessment_reviews', Session()->get('tenant_info')['subdomain'])}}">Assessment Marks</a>
                </li>
            </ul>
        </div>

    </li>
    @if(!empty($planFeatures) && $planFeatures->study_group>0)
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#mygroups" aria-controls="mygroups"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/study.png')}}">
            <span >My Groups</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="mygroups">
            <ul class="side-nav-second-level">
                <li><a href="#">School Clubs</a></li>
                <li><a href="#">Teacher Groups</a></li>
                <li><a href="#">Personal Groups</a></li>
            </ul>
        </div>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->global_chat>0)
    <li class="side-nav-item">
        <a href="#" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/global_chat.png')}}">
            <span > Global Chat</span>
        </a>
    </li>
    @endif
    <!-- <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#npay" aria-controls="npay"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/npay.png')}}">
            <span >NPay</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="npay">
            <ul class="side-nav-second-level">
                <li><a href="#">e-Shop</a></li>
                <li><a href="#">Order History</a></li>
            </ul>
        </div>
    </li>-->
    @if(!empty($planFeatures) && $planFeatures->inbox>0)
    <li class="side-nav-item">
        <a href="{{route('tut_inbox',Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/Mail.png')}}">
            <span > Inbox</span>
        </a>
    </li>
    @endif

    <!--<li class="side-nav-title side-nav-item py-1">Existing Navigation</li> -->
    <!-- <li class="side-nav-item">
        <a href="{{route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/home.png')}}">
            <span > Home</span>
        </a>
    </li> -->
    <!-- <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#academic" aria-controls="academic" class="side-nav-link py-1"
            @if(in_array(Route::current()->getName(), array('tut_mycourses','tut_lessons','tut_quizes','tut_editquiz')))
            aria-expanded=true @else aria-expanded=false @endif >

            <img width="22" height="22" src="{{asset('img/system/icons/academic.png')}}">
            <span >Academic</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tut_mycourses','tut_lessons','tut_quizes','tut_editquiz','tut_quiz_submitted','tut_quiz_review','tut_assesments','tut_assessment_submitted'))) show @endif"
            id="academic">
            <ul class="side-nav-second-level">

                <li @if(in_array(Route::current()->getName(), array('tut_mycourses','tut_lessons'))) class="active" @endif>
                    <a href="{{route('tut_mycourses', Session()->get('tenant_info')['subdomain'])}}">Subject</a>
                </li>

                <li @if(in_array(Route::current()->getName(), array('tut_quizes','tut_editquiz'))) class="active"
                    @endif>
                    <a href="{{route('tut_quizes', Session()->get('tenant_info')['subdomain'])}}">Add Quiz</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tut_assesments'))) class="active" @endif>
                    <a href="{{route('tut_assesments', Session()->get('tenant_info')['subdomain'])}}">Add Assessment</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tut_quiz_submitted','tut_quiz_review')))
                    class="active" @endif>
                    <a href="{{route('tut_quiz_submitted', Session()->get('tenant_info')['subdomain'])}}">View Quiz
                        Submissions</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tut_assessment_submitted','tut_assessment_review')))
                    class="active" @endif>
                    <a href="{{route('tut_assessment_submitted', Session()->get('tenant_info')['subdomain'])}}">View
                        Assessment Submissions</a>
                </li>
            </ul>
        </div>
    </li> -->
    <!-- <li class="side-nav-item  "> <a data-bs-toggle="collapse" href="#users" @if(in_array(Route::current()->getName(),
            array('tut_students'))) aria-expanded=true @else aria-expanded=false @endif aria-controls="users"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Users</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tut_students'))) show @endif" id="users">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('tut_students'))) class="active" @endif>
                    <a href="{{route('tut_students', Session()->get('tenant_info')['subdomain'])}}">Students</a>
                </li>


            </ul>
        </div>
    </li> -->
    <!-- <li class="side-nav-item">
        <a href="{{route('tut_attendances', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/my_course.png')}}">
            <span >Record Attendance</span>
        </a>
    </li> -->
    <!-- <li class="side-nav-item">
        <a href="{{route('tut_mylibrary', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/Library.png')}}">
            <span >Library</span>
        </a>
    </li> -->
    <!-- <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#exam"  @if(in_array(Route::current()->getName(),
            array('tut_quiz_reviews','tut_assessment_reviews'))) aria-expanded=true @else aria-expanded=false @endif  aria-controls="exam" class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/Exam.png')}}">
            <span >Exam</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('tut_quiz_reviews','tut_assessment_reviews'))) show @endif" id="exam">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('tut_quiz_reviews'))) class="active" @endif>
                    <a href="{{route('tut_quiz_reviews', Session()->get('tenant_info')['subdomain'])}}">Quiz Marks</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('tut_assessment_reviews'))) class="active" @endif>
                    <a href="{{route('tut_assessment_reviews', Session()->get('tenant_info')['subdomain'])}}">Assessment Marks</a>
                </li>
            </ul>
        </div>

    </li> -->
    <!-- <li class="side-nav-item">
        <a href="#" class="side-nav-link">

            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Analytics Studio</span>
        </a>
    </li> -->
</ul>
@elseif($userInfo['user_type']=='TA' && $userInfo['role']=='A')
<ul class="side-nav">
    <!-- <li class="side-nav-title side-nav-item py-1">Navigation</li> -->
    <li class="side-nav-item">
        <a href="{{route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <!--<i class="dripicons-meter"></i>-->
            <img width="22" height="22" src="{{asset('img/system/icons/home.png')}}">
            <span > Home </span>
        </a>

    </li>

    <li class="side-nav-item  ">
        <a data-bs-toggle="collapse" href="#academic" @if(in_array(Route::current()->getName(),
            array('ta_gradelist','ta_academicyearlist','ta_yeargrouplist','ta_subjectlist','ta_lessonlist','ta_topiclist'))) aria-expanded=true @else
            aria-expanded=false @endif aria-controls="academic"
            class="side-nav-link py-1">
            <!-- <i class="academic"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/academic.png')}}">
            <span >Academic</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('ta_gradelist','ta_academicyearlist','ta_yeargrouplist','ta_subjectlist','ta_lessonlist','ta_topiclist'))) show @endif"
            id="academic">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('ta_gradelist'))) class="active" @endif>
                    <a href="{{route('ta_gradelist', Session()->get('tenant_info')['subdomain'])}}">Grades</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('ta_academicyearlist'))) class="active" @endif>
                    <a href="{{route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])}}">Academic
                        Year</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('ta_yeargrouplist'))) class="active" @endif>
                    <a href="{{route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])}}">Year Group</a>
                </li>
                @if(!empty($planFeatures) && $planFeatures->subject>0)
                <li @if(in_array(Route::current()->getName(), array('ta_subjectlist'))) class="active" @endif>
                    <a href="{{route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])}}">Subject</a>
                </li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->lesson_plan>0)
                <li @if(in_array(Route::current()->getName(), array('ta_lessonlist'))) class="active" @endif>
                    <a href="{{route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])}}">Lesson</a>
                </li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->skillmap>0)
                <li @if(in_array(Route::current()->getName(), array('ta_topiclist'))) class="active" @endif>
                    <a href="{{route('ta_topiclist', Session()->get('tenant_info')['subdomain'])}}">Topics</a>
                </li>
                @endif
                <!-- <li>
                    <a href="#">Check Quiz</a>
                </li>
                <li>
                    <a href="#">Check Assessment</a>
                </li> -->
                <!-- <li>
                    <a href="#">Subscription Plan</a>
                </li> -->
            </ul>
        </div>
    </li>

    <li class="side-nav-item  "> <a data-bs-toggle="collapse" href="#users"  @if(in_array(Route::current()->getName(),
            array('ta_studentlist','ta_teacherlist','ta_teacherassistantlist','ta_departmentlist','ta_employeelist','ta_parentlist'))) aria-expanded=true @else
            aria-expanded=false @endif aria-controls="users"
            class="side-nav-link py-1">
            <!-- <i class="User"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Users</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('ta_studentlist','ta_teacherlist','ta_teacherassistantlist','ta_departmentlist','ta_employeelist','ta_parentlist'))) show @endif" id="users">
            <ul class="side-nav-second-level">
                @if(!empty($planFeatures) && $planFeatures->student>0)
                <li @if(in_array(Route::current()->getName(), array('ta_studentlist'))) class="active" @endif>
                    <a href="{{route('ta_studentlist', Session()->get('tenant_info')['subdomain'])}}">Students</a>
                </li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->teacher>0)
                <li @if(in_array(Route::current()->getName(), array('ta_teacherlist'))) class="active" @endif>
                    <a href="{{route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])}}">Teachers</a>
                </li>
                @endif
                <li @if(in_array(Route::current()->getName(), array('ta_teacherassistantlist'))) class="active" @endif>
                    <a href="{{route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])}}">Teacher
                        Assistants</a>
                </li>
                @if(!empty($planFeatures) && $planFeatures->parent>0)
                <li @if(in_array(Route::current()->getName(), array('ta_parentlist'))) class="active" @endif>
                    <a href="{{route('ta_parentlist', Session()->get('tenant_info')['subdomain'])}}">Parents</a>
                </li>
                @endif
                <li @if(in_array(Route::current()->getName(), array('ta_departmentlist'))) class="active" @endif>
                    <a href="{{route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])}}">Departments</a>
                </li>
                @if(!empty($planFeatures) && $planFeatures->employee>0)
                <li @if(in_array(Route::current()->getName(), array('ta_employeelist'))) class="active" @endif>
                    <a href="{{route('ta_employeelist', Session()->get('tenant_info')['subdomain'])}}">Employees</a>
                </li>
                @endif
            </ul>
        </div>
    </li>
    @if(!empty($planFeatures) && $planFeatures->library>0)
    <li class="side-nav-item  "> <a href="{{route('ta_library', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">
            <!-- <i class="Library"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/Library.png')}}">
            <span >Library</span>
        </a>
    </li>
    @endif
    @if(!empty($planFeatures) && $planFeatures->ofstead>0)
    <li class="side-nav-item  "> <a data-bs-toggle="collapse" href="#importofstead"   @if(in_array(Route::current()->getName(),
            array('ta_ofinancelist'))) aria-expanded=true @else
            aria-expanded=false @endif  aria-controls="importofstead"
            class="side-nav-link py-1">
            <!-- <i class="Exam"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/Exam.png')}}">
            <span >Import Ofsteds</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('ta_ofinancelist'))) show @endif" id="importofstead">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('ta_ofinancelist'))) class="active" @endif>
                    <a href="{{route('ta_ofinancelist', Session()->get('tenant_info')['subdomain'])}}">Finance Ostead</a>
                </li>
            </ul>
        </div>
    </li>
    @endif
    <li class="side-nav-item  "> <a data-bs-toggle="collapse" href="#exam"   @if(in_array(Route::current()->getName(),
            array('ta_resultlist'))) aria-expanded=true @else
            aria-expanded=false @endif  aria-controls="exam"
            class="side-nav-link py-1">
            <!-- <i class="Exam"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/Exam.png')}}">
            <span >Exam</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('ta_resultlist'))) show @endif" id="exam">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('ta_resultlist'))) class="active" @endif>
                    <a href="{{route('ta_resultlist', Session()->get('tenant_info')['subdomain'])}}">Marks</a>
                </li>
            </ul>
        </div>
    </li>
    @if(!empty($planFeatures) && $planFeatures->inbox>0)
    <li class="side-nav-item">
        <a href="{{route('ta_inbox',Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/Mail.png')}}">
            <span > Inbox</span>
        </a>
    </li>
    @endif
    <!-- <li class="side-nav-item  "> <a href="#" class="side-nav-link">
            <img width="22" height="22" src="{{asset('img/system/icons/Parentpay.png')}}">
            <span >Parent Pay</span>
        </a>
    </li>
    <li class="side-nav-item  "> <a href="#" class="side-nav-link">
            <img width="22" height="22" src="{{asset('img/system/icons/Thirdpartypay.png')}}">
            <span >Marketing</span>
        </a>
    </li> -->
    @if(!empty($planFeatures) && $planFeatures->analytics_studio>0)
    <li class="side-nav-item  @if(in_array(Route::current()->getName(), array('ta_analyticstudio'))) "active" @endif "> <a href="{{route('ta_analyticstudio',Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">
            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Analytics Studio</span>
        </a>
    </li>
    @endif
</ul>
@elseif($userInfo['user_type']=='P' && $userInfo['role']=='P')
<ul class="side-nav">
    <!-- <li class="side-nav-title side-nav-item py-1">Navigation</li> -->
    <li class="side-nav-item">
        <a href="{{route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/home394.png')}}">
            <span > Home</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="#" class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/analytics.png')}}">
            <span> Dashboard</span>
        </a>
    </li>
    <li class="side-nav-item" @if(in_array(Route::current()->getName(), array('p_children'))) class="active" @endif>
        <a href="{{route('p_children',Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <!--<i class="dripicons-meter"></i>-->
            <img width="22" height="22" src="{{asset('img/system/icons/child.png')}}">
            <span > Child Management </span>
        </a>
    </li>
    @if(!empty($planFeatures) && $planFeatures->attendance>0)
    <li class="side-nav-item">
        <a href="{{route('p_attendancelist', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link">
            <!-- <i class="Library"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/user4.png')}}">
            <span >Attendances</span>
        </a>
    </li>
    @endif
    @if(!empty($planFeatures) && ($planFeatures->subject>0 || $planFeatures->lesson_plan>0 ||$planFeatures->ai_adaptive_learning_plan>0 || $planFeatures->course_completion_status>0))
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#courses" aria-controls="courses"
            class="side-nav-link py-1">
            <img width="20" height="20" src="{{asset('img/system/icons/my_course.png')}}">
            <span >Courses</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="courses">
            <ul class="side-nav-second-level">
                @if(!empty($planFeatures) && ($planFeatures->subject>0 || $planFeatures->lesson_plan>0 ))
                <li><a href="#">Subjects /Topics</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->ai_adaptive_learning_plan>0)
                <li><a href="#">AI Adaptive Learning Plan</a></li>
                @endif
                @if(!empty($planFeatures) && $planFeatures->course_completion_status>0)
                <li><a href="#">Course Completion Status</a></li>
                @endif
            </ul>
        </div>
    </li>
    @endif
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#academics" aria-controls="academics"
            class="side-nav-link py-1" @if(in_array(Route::current()->getName(),
            array('p_resultqlist','p_resultalist','p_reviewed_answers'))) aria-expanded=true @else
            aria-expanded=false @endif>

            <img width="22" height="22" src="{{asset('img/system/icons/academic.png')}}">
            <span >Academics</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('p_resultqlist','p_resultalist','p_reviewed_answers'))) show @endif" id="academics">
            <ul class="side-nav-second-level">
                <li><a href="#">Adminssion Test</a></li>

                <li @if(in_array(Route::current()->getName(), array('p_resultqlist'))) class="active" @endif>
                    <a href="{{route('p_resultqlist', Session()->get('tenant_info')['subdomain'])}}">Quiz Result</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('p_resultalist'))) class="active" @endif>
                    <a href="{{route('p_resultalist', Session()->get('tenant_info')['subdomain'])}}">Assessment Result</a>
                </li>
                <li><a href="#">Home Work</a></li>
                <li><a href="#">Test Generator Result</a></li>
            </ul>
        </div>
    </li>
    @if(!empty($planFeatures) && $planFeatures->ofstead>0)
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#survey" aria-controls="survey"
            class="side-nav-link py-1">
            <img width="22" height="22" src="{{asset('img/system/icons/reader.png')}}">
            <span >Survey</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="survey">
            <ul class="side-nav-second-level">
                <li><a href="#">Forms OFSTED</a></li>
                <li><a href="#">Others</a></li>
            </ul>
        </div>
    </li>
    @endif
    <!-- <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#npay" aria-controls="npay"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/npay.png')}}">
            <span >NPay</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="npay">
            <ul class="side-nav-second-level">
                <li><a href="#">Wallet</a></li>
                <li><a href="#">e-Shop</a></li>
                <li><a href="#">Fee Subscription</a></li>
            </ul>
        </div>
    </li>
    <li class="side-nav-item"> <a data-bs-toggle="collapse" href="#inbox" aria-controls="inbox"
            class="side-nav-link py-1">

            <img width="22" height="22" src="{{asset('img/system/icons/Mail.png')}}">
            <span > Inbox</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="inbox">
            <ul class="side-nav-second-level">
                <li><a href="#">To Parent</a></li>
                <li><a href="#">Automated Alerts</a></li>
            </ul>
        </div>
    </li> -->
</ul>
@else
<ul class="side-nav">
    <!-- <li class="side-nav-title side-nav-item py-1">Navigation</li> -->
    <li class="side-nav-item">
        <a href="{{route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="side-nav-link py-1">
            <!--<i class="dripicons-meter"></i>-->
            <img width="22" height="22" src="{{asset('img/system/icons/home.png')}}">
            <span > Home </span>
        </a>
    </li>

</ul>
@endif
<!-- End Sidebar -->
