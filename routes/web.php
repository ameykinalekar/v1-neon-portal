<?php
use App\Helpers\CommonHelper;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PortalAdminController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TrusteeController;
use Illuminate\Support\Facades\Route;
use Vish4395\LaravelFileViewer\LaravelFileViewerFacade;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::any('/preview', [HomeController::class, 'file_preview'])->name('preview');

// Add this to a route or controller to check the alias
Route::get('/check-alias', function () {
    // return class_exists('LaravelFileViewer') ? 'Alias is registered' : 'Alias is not registered';

// Test if the class can be instantiated without errors
    $facade = new LaravelFileViewerFacade();
    dd($facade);
});

Route::get('/', [HomeController::class, 'index'])->name('front_index');
Route::get('/flush', [HomeController::class, 'doFlush'])->name('front_flush');
Route::get('/bye', [HomeController::class, 'doBye'])->name('front_bye');
Route::post('/', [HomeController::class, 'doRedirect'])->name('do_redirect');
Route::get('/img/{path}', 'ImageController@show')->where('path', '.*');
Route::get('/login', [LoginController::class, 'login'])->name('front_login');
Route::post('/dologin', [LoginController::class, 'dologin'])->name('do_login');
Route::get('/logout', [LoginController::class, 'logout'])->name('front_logout');
Route::post('/forgot-password', [LoginController::class, 'doForgotPassword'])->name('do_forgot_password');
Route::get('/reset-password/{token}', [LoginController::class, 'resetPassword'])->name('reset_newpassword');
Route::post('/update-password', [LoginController::class, 'doResetPassword'])->name('update_newpassword');

Route::get('/invitation/thank-you', [HomeController::class, 'invitationThanks'])->name('front_invitation_thanks');
Route::any('/invitation/{token}', [HomeController::class, 'invitation'])->name('front_invitation');

Route::group(['prefix' => '/trustee', 'middleware' => ['web']], function () {
    Route::get('/', [TrusteeController::class, 'schools'])->name('t_dashboard');
    Route::any('/my-account', [TrusteeController::class, 'myAccount'])->name('t_myaccount');
});
//portal admin routes
Route::group(['prefix' => '/', 'middleware' => ['web']], function () {

    // Your portal routes go here
    Route::get('/portal-admin', [PortalAdminController::class, 'index'])->name('pa_dashboard');
    Route::any('/my-account', [PortalAdminController::class, 'myAccount'])->name('pa_myaccount');
    Route::get('/settings', [PortalAdminController::class, 'settings'])->name('pa_settings');
    Route::post('/settings', [PortalAdminController::class, 'doSettings'])->name('pa_do_setting');

    //Board routes
    Route::any('/boards', [PortalAdminController::class, 'getBoardListing'])->name('pa_boardlist');
    Route::get('/create-board', [PortalAdminController::class, 'addBoard'])->name('pa_addboard');
    Route::post('/create-board', [PortalAdminController::class, 'saveBoard'])->name('pa_saveboard');
    Route::get('/edit-board/{board_id}', [PortalAdminController::class, 'editBoard'])->name('pa_editboard');
    Route::post('/update-board', [PortalAdminController::class, 'updateBoard'])->name('pa_updateboard');

    //Country routes
    Route::any('/countries', [PortalAdminController::class, 'getCountryListing'])->name('pa_countrylist');
    // Route::get('/create-board', [PortalAdminController::class, 'addBoard'])->name('pa_addboard');
    // Route::post('/create-board', [PortalAdminController::class, 'saveBoard'])->name('pa_saveboard');
    Route::get('/edit-country/{country_id}', [PortalAdminController::class, 'editCountry'])->name('pa_editcountry');
    Route::post('/update-country', [PortalAdminController::class, 'updateCountry'])->name('pa_updatecountry');

    //Trustee route
    Route::any('/trustees', [PortalAdminController::class, 'getTrusteeListing'])->name('pa_trusteelist');
    Route::get('/create-trustee', [PortalAdminController::class, 'addTrustee'])->name('pa_addtrustee');
    Route::post('/create-trustee', [PortalAdminController::class, 'saveTrustee'])->name('pa_savetrustee');
    Route::get('/create-trustee-fs', [PortalAdminController::class, 'addTrusteeFromSchool'])->name('pa_addtrusteefs');
    Route::get('/edit-trustee/{user_id}', [PortalAdminController::class, 'editTrustee'])->name('pa_edittrustee');
    Route::post('/update-trustee', [PortalAdminController::class, 'updateTrustee'])->name('pa_updatetrustee');
    //School route
    Route::any('/schools', [PortalAdminController::class, 'getSchoolListing'])->name('pa_schoollist');
    Route::get('/create-school', [PortalAdminController::class, 'addSchool'])->name('pa_addschool');
    Route::post('/create-school', [PortalAdminController::class, 'saveSchool'])->name('pa_saveschool');
    Route::get('/edit-school/{user_id}', [PortalAdminController::class, 'editSchool'])->name('pa_editschool');
    Route::post('/update-school', [PortalAdminController::class, 'updateSchool'])->name('pa_updateschool');
    Route::get('/school/subscriptions/{user_id}', [PortalAdminController::class, 'schoolSubscriptions'])->name('pa_schoolsubscriptions');
    Route::get('/school/subscribe-plan/{user_id}', [PortalAdminController::class, 'subscribePlan'])->name('pa_schoolsubscribeplan');
    Route::get('/school/subscribe/{user_id}/{plan_id}', [PortalAdminController::class, 'planSubscribe'])->name('pa_schoolplansubscribe');
    Route::post('/school/subscribe-plan', [PortalAdminController::class, 'doPlanSubscribe'])->name('pa_schoolsaveplansubscribe');

    //Subscription plan route
    Route::any('/subscription-plans', [PortalAdminController::class, 'getSubscriptionListing'])->name('pa_subscriptionplanlist');
    Route::get('/create-subscription-plan', [PortalAdminController::class, 'addSubscriptionPlan'])->name('pa_addsubscriptionplan');
    Route::post('/create-subscription-plan', [PortalAdminController::class, 'saveSubscriptionPlan'])->name('pa_savesubscriptionplan');
    Route::get('/edit-subscription-plan/{subscription_plan_id}', [PortalAdminController::class, 'editSubscriptionPlan'])->name('pa_editsubscriptionplan');
    Route::post('/update-subscription-plan', [PortalAdminController::class, 'updateSubscriptionPlan'])->name('pa_updatesubscriptionplan');
});

//Tenant Frontend
Route::group(['prefix' => '/{subdomain}', 'middleware' => ['tenant']], function () {
    // Your tenant-specific routes go here
    Route::get('/login', [LoginController::class, 'loginTenant'])->name('tenant_login');
    Route::post('/dologin', [LoginController::class, 'doLoginTenant'])->name('do_login_tenant');
    Route::post('/forgot-password', [LoginController::class, 'doForgotPasswordTenant'])->name('do_forgot_password_tenant');
    Route::get('/reset-password/{token}', [LoginController::class, 'resetPasswordTenant'])->name('reset_newpassword_tenant');
    Route::post('/update-password', [LoginController::class, 'doResetPasswordTenant'])->name('update_newpassword_tenant');

    Route::get('/', [TenantController::class, 'index'])->name('tenant_dashboard');

    Route::any('/ofsted-chart', [TenantController::class, 'getChartOfsted'])->name('get_ofsted_chart');
    Route::any('/teacher/attendance-chart', [TenantController::class, 'getChartTeacherAttendance'])->name('get_tut_attendance_chart');
    
    Route::any('/ai-help', [TenantController::class, 'aiHelp'])->name('get_ai_help');

    Route::any('/my-account', [TenantController::class, 'myAccount'])->name('myaccount');
    Route::get('/settings', [TenantController::class, 'settings'])->name('settings');
    Route::post('/settings', [TenantController::class, 'doSettings'])->name('do_setting');

    //TA - Academic year routes
    Route::any('/academic-years', [TenantController::class, 'getAcademicYearListing'])->name('ta_academicyearlist');
    Route::get('/create-academic-year', [TenantController::class, 'addAcademicYear'])->name('ta_addacademicyear');
    Route::post('/create-academic-year', [TenantController::class, 'saveAcademicYear'])->name('ta_saveacademicyear');
    Route::get('/edit-academic-year/{academic_year_id}', [TenantController::class, 'editAcademicYear'])->name('ta_editacademicyear');
    Route::post('/update-academic-year', [TenantController::class, 'updateAcademicYear'])->name('ta_updateacademicyear');

    //TA - Grade routes
    Route::any('/grades', [TenantController::class, 'getGradeListing'])->name('ta_gradelist');
    Route::get('/create-grade', [TenantController::class, 'addGrade'])->name('ta_addgrade');
    Route::post('/create-grade', [TenantController::class, 'saveGrade'])->name('ta_savegrade');
    Route::get('/edit-grade/{grade_id}', [TenantController::class, 'editGrade'])->name('ta_editgrade');
    Route::post('/update-grade', [TenantController::class, 'updateGrade'])->name('ta_updategrade');
    Route::get('/import-grade', [TenantController::class, 'importGrade'])->name('ta_importgrade');
    Route::post('/import-grade', [TenantController::class, 'saveImportGrade'])->name('ta_saveimportgrade');

    //TA - Year group routes
    Route::any('/year-groups', [TenantController::class, 'getYearGroupListing'])->name('ta_yeargrouplist');
    Route::get('/create-year-group', [TenantController::class, 'addYearGroup'])->name('ta_addyeargroup');
    Route::post('/create-year-group', [TenantController::class, 'saveYearGroup'])->name('ta_saveyeargroup');
    Route::get('/edit-year-group/{year_group_id}', [TenantController::class, 'editYearGroup'])->name('ta_edityeargroup');
    Route::post('/update-year-group', [TenantController::class, 'updateYearGroup'])->name('ta_updateyeargroup');
    Route::get('/import-year-group', [TenantController::class, 'importYearGroup'])->name('ta_importyeargroup');
    Route::post('/import-year-group', [TenantController::class, 'saveImportYearGroup'])->name('ta_saveimportyeargroup');

    //TA - Subject routes
    Route::any('/subjects', [TenantController::class, 'getSubjectListing'])->name('ta_subjectlist');
    Route::get('/create-subject', [TenantController::class, 'addSubject'])->name('ta_addsubject');
    Route::post('/create-subject', [TenantController::class, 'saveSubject'])->name('ta_savesubject');
    Route::get('/edit-subject/{subject_id}', [TenantController::class, 'editSubject'])->name('ta_editsubject');
    Route::post('/update-subject', [TenantController::class, 'updateSubject'])->name('ta_updatesubject');
    Route::get('/import-subject', [TenantController::class, 'importSubject'])->name('ta_importsubject');
    Route::post('/import-subject', [TenantController::class, 'saveImportSubject'])->name('ta_saveimportsubject');

    //TA - Lesson routes
    Route::any('/lessons', [TenantController::class, 'getLessonListing'])->name('ta_lessonlist');
    Route::get('/create-lesson', [TenantController::class, 'addLesson'])->name('ta_addlesson');
    Route::post('/create-lesson', [TenantController::class, 'saveLesson'])->name('ta_savelesson');
    Route::get('/edit-lesson/{lesson_id}', [TenantController::class, 'editLesson'])->name('ta_editlesson');
    Route::post('/update-lesson', [TenantController::class, 'updateLesson'])->name('ta_updatelesson');
    Route::get('/import-lesson', [TenantController::class, 'importLesson'])->name('ta_importlesson');
    Route::post('/import-lesson', [TenantController::class, 'saveImportLesson'])->name('ta_saveimportlesson');

    //TA - Topic routes
    Route::any('/topics', [TenantController::class, 'getTopicListing'])->name('ta_topiclist');
    Route::get('/create-topic', [TenantController::class, 'addTopic'])->name('ta_addtopic');
    Route::post('/create-topic', [TenantController::class, 'saveTopic'])->name('ta_savetopic');
    Route::get('/edit-topic/{topic_id}', [TenantController::class, 'editTopic'])->name('ta_edittopic');
    Route::post('/update-topic', [TenantController::class, 'updateTopic'])->name('ta_updatetopic');
    Route::get('/import-topic', [TenantController::class, 'importTopic'])->name('ta_importtopic');
    Route::post('/import-topic', [TenantController::class, 'saveImportTopic'])->name('ta_saveimporttopic');

    //TA - students routes
    Route::any('/students', [TenantController::class, 'getStudentListing'])->name('ta_studentlist');
    Route::get('/create-student', [TenantController::class, 'addStudent'])->name('ta_addstudent');
    Route::post('/create-student', [TenantController::class, 'saveStudent'])->name('ta_savestudent');
    Route::get('/edit-student/{student_id}', [TenantController::class, 'editStudent'])->name('ta_editstudent');
    Route::post('/update-student', [TenantController::class, 'updateStudent'])->name('ta_updatestudent');
    Route::get('/students/import', [TenantController::class, 'importStudent'])->name('ta_importstudent');
    Route::post('/students/import', [TenantController::class, 'saveImportStudent'])->name('ta_saveimportstudent');

    //TA - teachers routes
    Route::any('/teachers', [TenantController::class, 'getTeacherListing'])->name('ta_teacherlist');
    Route::get('/create-teacher', [TenantController::class, 'addTeacher'])->name('ta_addteacher');
    Route::post('/create-teacher', [TenantController::class, 'saveTeacher'])->name('ta_saveteacher');
    Route::get('/edit-teacher/{teacher_id}', [TenantController::class, 'editTeacher'])->name('ta_editteacher');
    Route::post('/update-teacher', [TenantController::class, 'updateTeacher'])->name('ta_updateteacher');
    Route::get('/teachers/import', [TenantController::class, 'importTeacher'])->name('ta_importteacher');
    Route::post('/teachers/import', [TenantController::class, 'saveImportTeacher'])->name('ta_saveimportteacher');

    //TA - teacher assistants routes
    Route::any('/teacher-assistants', [TenantController::class, 'getTeacherAssistantListing'])->name('ta_teacherassistantlist');
    Route::get('/create-teacher-assistant', [TenantController::class, 'addTeacherAssistant'])->name('ta_addteacherassistant');
    Route::post('/create-teacher-assistant', [TenantController::class, 'saveTeacherAssistant'])->name('ta_saveteacherassistant');
    Route::get('/edit-teacher-assistant/{teacher_assistant_id}', [TenantController::class, 'editTeacherAssistant'])->name('ta_editteacherassistant');
    Route::post('/update-teacher-assistant', [TenantController::class, 'updateTeacherAssistant'])->name('ta_updateteacherassistant');

    //TA - Department routes
    Route::any('/departments', [TenantController::class, 'getDepartmentListing'])->name('ta_departmentlist');
    Route::get('/create-department', [TenantController::class, 'addDepartment'])->name('ta_adddepartment');
    Route::post('/create-deparment', [TenantController::class, 'saveDepartment'])->name('ta_savedepartment');
    Route::get('/edit-department/{department_id}', [TenantController::class, 'editDepartment'])->name('ta_editdepartment');
    Route::post('/update-department', [TenantController::class, 'updateDepartment'])->name('ta_updatedepartment');
    Route::get('/import-department', [TenantController::class, 'importDepartment'])->name('ta_importdepartment');
    Route::post('/import-department', [TenantController::class, 'saveImportDepartment'])->name('ta_saveimportdepartment');

    //TA - Employee routes
    Route::any('/employees', [TenantController::class, 'getEmployeeListing'])->name('ta_employeelist');
    Route::get('/create-employee', [TenantController::class, 'addEmployee'])->name('ta_addemployee');
    Route::post('/create-employee', [TenantController::class, 'saveEmployee'])->name('ta_saveemployee');
    Route::get('/edit-employee/{employee_id}', [TenantController::class, 'editEmployee'])->name('ta_editemployee');
    Route::post('/update-employee', [TenantController::class, 'updateEmployee'])->name('ta_updateemployee');

    //TA - Parent routes
    Route::any('/parents', [TenantController::class, 'getParentListing'])->name('ta_parentlist');
    Route::get('/create-parent', [TenantController::class, 'addParent'])->name('ta_addparent');
    Route::post('/create-parent', [TenantController::class, 'saveParent'])->name('ta_saveparent');
    Route::get('/edit-parent/{parent_id}', [TenantController::class, 'editParent'])->name('ta_editparent');
    Route::post('/update-parent', [TenantController::class, 'updateParent'])->name('ta_updateparent');

    //TA - Exam routes
    Route::any('/all-marks', [TenantController::class, 'getUserResultListing'])->name('ta_resultlist');

    //TA library related teacher routes
    Route::get('/library/add-content/{lesson_id}', [TenantController::class, 'taAddLibraryContent'])->name('ta_addlibrarycontent');
    Route::post('/library/add-content', [TenantController::class, 'taSaveLibraryContent'])->name('ta_savelibrarycontent');
    Route::get('/library/edit-content/{library_id}', [TenantController::class, 'taEditLibraryContent'])->name('ta_editlibrarycontent');
    Route::post('/library/edit-content', [TenantController::class, 'taUpdateLibraryContent'])->name('ta_updatelibrarycontent');
    Route::any('/library/{subject_id}', [TenantController::class, 'libraryContent'])->name('ta_librarycontent');
    Route::any('/library/content/{lesson_id}/{content_type}', [TenantController::class, 'libraryContentByType'])->name('ta_libcontentbytype');
    Route::any('/library', [TenantController::class, 'schoolLibrary'])->name('ta_library');

    //TA - Import ofstead
    Route::any('/ofstead/finance', [TenantController::class, 'getOfsteadFinanceListing'])->name('ta_ofinancelist');
    Route::get('/ofstead/import-finance', [TenantController::class, 'importOfsteadFinance'])->name('ta_importofinance');
    Route::post('/ofstead/import-finance', [TenantController::class, 'saveImportOfsteadFinance'])->name('ta_saveimportofinance');
    Route::get('/ofstead/view-finance-year/{year}', [TenantController::class, 'viewOfsteadFinanceYear'])->name('ta_viewofinanceyear');

    //TA Inbox
    Route::any('/inbox', [TenantController::class, 'taInbox'])->name('ta_inbox');
    Route::get('/add-message', [TenantController::class, 'taAddMessage'])->name('ta_addmsg');
    Route::post('/add-message', [TenantController::class, 'taSendMessage'])->name('ta_sendmsg');
    Route::any('/view-message/{message_id}', [TenantController::class, 'viewMessage'])->name('view_message');

    //TA KPI
    Route::any('/analytic-studio', [TenantController::class, 'adminKPI'])->name('ta_analyticstudio');

    //TU (student) - routes
    Route::any('/my-courses', [TenantController::class, 'studentMyCourse'])->name('tus_mycourses');
    Route::any('/my-courses/{subject_id}', [TenantController::class, 'studentMyCoursePlan'])->name('tus_mycourseplan');
    Route::any('/my-quizes', [TenantController::class, 'studentQuizes'])->name('tus_quizes');
    Route::get('/start-quiz/{examination_id}', [TenantController::class, 'studentStartQuiz'])->name('tus_quiz');
    Route::post('/save-student-quiz', [TenantController::class, 'studentSaveQuiz'])->name('tus_savequiz');
    Route::any('/quiz-marks', [TenantController::class, 'studentQuizesReviewed'])->name('tus_quiz_marks');
    Route::get('/view-answers/{user_result_id}', [TenantController::class, 'studentReviewedAnswers'])->name('tus_reviewed_answers');
    Route::any('/my-assessments', [TenantController::class, 'studentAssesments'])->name('tus_assesments');
    Route::get('/start-assessment/{examination_id}', [TenantController::class, 'studentStartAssesment'])->name('tus_assesment');
    Route::post('/save-student-assessment', [TenantController::class, 'studentSaveAssessment'])->name('tus_saveassessment');

    Route::any('/assessment-marks', [TenantController::class, 'studentAssessmentsReviewed'])->name('tus_assessment_marks');
    Route::any('/study-groups', [TenantController::class, 'studentStudyGroup'])->name('tus_studygroups');
    Route::get('/create-study-group', [TenantController::class, 'addStudentStudyGroup'])->name('tus_addstudygroup');
    Route::post('/create-study-group', [TenantController::class, 'saveStudentStudyGroup'])->name('tus_savestudygroup');
    Route::get('/edit-study-group/{employee_id}', [TenantController::class, 'editStudentStudyGroup'])->name('tus_editstudygroup');
    Route::post('/update-study-group', [TenantController::class, 'updateStudentStudyGroup'])->name('tus_updatestudygroup');
    Route::get('/study-group/{study_group_id}', [TenantController::class, 'viewStudentStudyGroup'])->name('tus_viewstudygroup');

    Route::post('/post-study-group-content', [TenantController::class, 'addContentStudentStudyGroup'])->name('tus_addcontentstudygroup');

    Route::get('/invite-to-study-group/{study_group_id}', [TenantController::class, 'inviteToStudentStudyGroup'])->name('tus_invitetostudygroup');
    Route::post('/invite-to-study-group', [TenantController::class, 'saveInviteeStudentStudyGroup'])->name('tus_saveinviteestudygroup');

    //----student library related teacher routes
    Route::any('/student/library', [TenantController::class, 'studentMyLibrary'])->name('tus_mylibrary');
    Route::any('/student/library/{subject_id}', [TenantController::class, 'studentMyLibraryContent'])->name('tus_mylibrarycontent');
    Route::any('/student/library/content/{lesson_id}/{content_type}', [TenantController::class, 'studentLibraryContentByType'])->name('tus_libcontentbytype');

    Route::any('/student/attendances', [TenantController::class, 'getStudentAttendanceListing'])->name('tus_attendances');
    Route::get('/student/attendances/filter', [TenantController::class, 'getStudentAttendanceListingFilter'])->name('tus_attendances_filter');

    Route::get('/student/edit-cover-image/{student_id}', [TenantController::class, 'editStudentCoverImage'])->name('tus_editstudentci');
    Route::post('/student/update-cover-image', [TenantController::class, 'updateStudentCoverImage'])->name('tus_updatestudentci');

    Route::get('/student/edit-profile-image/{student_id}', [TenantController::class, 'editStudentProfileImage'])->name('tus_editstudentpi');
    Route::post('/student/update-profile-image', [TenantController::class, 'updateStudentProfileImage'])->name('tus_updatestudentpi');

    Route::any('/student/adaptive-learning', [TenantController::class, 'getStudentSkillmap'])->name('tus_skillmap');
    Route::get('/student/targets', [TenantController::class, 'getStudentTargets'])->name('tus_starget');
    Route::any('/student/teacher-rating', [TenantController::class, 'shareTeacherRating'])->name('tus_teacherrating');
    Route::any('/student/addview-teacher-rating/{teacher_id}/{lesson_id}', [TenantController::class, 'addViewTeacherRating'])->name('tus_addview_teacherrating');
    Route::post('/student/save--content-teacher-rating', [TenantController::class, 'saveStudentsTeacherRating'])->name('tus_save_teacherrating');

    Route::any('/student/test-generator', [TenantController::class, 'studentTestGenerator'])->name('tus_testgen');
    Route::post('/student/proceed-test-generator', [TenantController::class, 'studentTestGeneratorProceed'])->name('tus_testgen_proceed');
    Route::post('/student/save-test-generator', [TenantController::class, 'studentTestGeneratorSave'])->name('tus_testgen_save');

    Route::any('/student/ntp-support', [TenantController::class, 'ntpSupport'])->name('tus_ntpsupport');
    Route::any('/student/student-dashboard', [TenantController::class, 'studentDashboard'])->name('tus_studentdashboard');
    Route::any('/student/student-reportcard', [TenantController::class, 'studentReportcard'])->name('tus_studentreportcard');
    Route::any('/student/student-signals', [TenantController::class, 'studentSignals'])->name('tus_studentsignals');
    Route::any('/student/student-analytic-studio', [TenantController::class, 'studentAnalyticStudio'])->name('tus_analyticstudio');
    Route::any('/student/student-leaderboard', [TenantController::class, 'studentLeaderboard'])->name('tus_leaderboard');
    Route::any('/student/student-achievement', [TenantController::class, 'studentAchievement'])->name('tus_achievement');
    Route::any('/student/student-reward', [TenantController::class, 'studentReward'])->name('tus_reward');
    Route::any('/student/student-target', [TenantController::class, 'studentTarget'])->name('tus_target');
    Route::any('/student/student-behavioral', [TenantController::class, 'studentBehavioral'])->name('tus_behavioral');
    Route::any('/student/student-reportcarddetail', [TenantController::class, 'studentReportcardDetail'])->name('tus_reportcarddetail');
    Route::any('/student/student-reportcardyear', [TenantController::class, 'studentReportcardYear'])->name('tus_reportcardyear');
    Route::any('/student/student-reportcardtype', [TenantController::class, 'studentReportcardType'])->name('tus_reportcardtype');
    Route::any('/student/course-completion-status', [TenantController::class, 'getStudentCourseStatus'])->name('tus_coursestatus');
    Route::any('/student/inbox', [TenantController::class, 'tusInbox'])->name('tus_inbox');
    Route::any('/student/lunch-menu', [TenantController::class, 'getLunchMenu'])->name('tus_lunchmenu');
    Route::any('/student/lunch-meal-activity', [TenantController::class, 'getLunchMealActivity'])->name('tus_lunchmealactivity');
    Route::any('/student/pastoral-care', [TenantController::class, 'tuspastoralcare'])->name('tus_pastoralcare');
    Route::any('/student/eshop', [TenantController::class, 'tuseshop'])->name('tus_eshop');
    Route::any('/student/eshop-description', [TenantController::class, 'tuseshopdesc'])->name('tus_eshopdesc');
    Route::any('/student/eshop-pay', [TenantController::class, 'tuseshoppay'])->name('tus_eshoppay');
    Route::any('/student/eshop-success', [TenantController::class, 'tuseshopsuccess'])->name('tus_eshopsuccess');
    Route::any('/student/order-history', [TenantController::class, 'getOrderHistory'])->name('tus_orderhistory');
    Route::any('/student/order-history-more', [TenantController::class, 'getOrderHistoryMore'])->name('tus_orderhistorymore');
    Route::any('/student/cart', [TenantController::class, 'getCart'])->name('tus_cart');
    Route::any('/student/cart-address', [TenantController::class, 'getCartAddress'])->name('tus_cartaddress');
    Route::any('/student/cart-address-fill', [TenantController::class, 'getCartAddressFill'])->name('tus_cartaddressfill');

    Route::any('/student/task-calendar', [TenantController::class, 'viewStudentTaskCalendar'])->name('tus_calendar');
    Route::any('/student/homework', [TenantController::class, 'viewStudentHomework'])->name('tus_homework');

    //TU (teacher) - routes
    Route::any('/teacher/dashboard', [TenantController::class, 'teacherDashboard'])->name('tut_dashboard');
    Route::any('/quizes', [TenantController::class, 'teacherQuizes'])->name('tut_quizes');
    Route::get('/create-quiz', [TenantController::class, 'teacherAddQuiz'])->name('tut_addquiz');
    Route::post('/create-quiz', [TenantController::class, 'teacherSaveQuiz'])->name('tut_savequiz');
    Route::get('/edit-quiz/{examination_id}', [TenantController::class, 'teacherEditQuiz'])->name('tut_editquiz');

    Route::get('/create-quiz-question/{examination_id}/{page_id}', [TenantController::class, 'teacherAddQuizQuestion'])->name('tut_addquizquestion');
    Route::post('/save-quiz-question', [TenantController::class, 'teacherSaveQuizQuestion'])->name('tut_savequizquestion');
    Route::get('/edit-quiz-question/{examination_id}/{page_id}/{examination_question_id}', [TenantController::class, 'teacherEditQuizQuestion'])->name('tut_editquizquestion');

    Route::get('/import-quiz-question/{examination_id}/{page_id}', [TenantController::class, 'teacherImportQuizQuestion'])->name('tut_importquizquestion');
    Route::post('/import-quiz-question', [TenantController::class, 'teacherSaveImportQuizQuestion'])->name('tut_saveimportquizquestion');

    Route::get('add-exam-page/{page}/{exam_id}', function ($subdomain, $page, $exam_id) {

        $url = route('tut_addquizquestion', [$subdomain, $exam_id, CommonHelper::encryptId($page)]);
        $urlImport = route('tut_importquizquestion', [$subdomain, $exam_id, CommonHelper::encryptId($page)]);

        return '<div class="col-md-12"><div class="card"><div class="card-header" style="height:50px;background-color: #f3f3f3; text-align:left"><span>Page ' . $page . '</span><span class="float-end"><ul class="list-inline"><li class="list-inline-item"><a href="javascript:void(0);" class="deletequizpage" data-exam-id="' . $exam_id . '" data-id="' . CommonHelper::encryptId($page) . '"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="14" height="14" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M4.59 59.41a2 2 0 0 0 2.83 0L32 34.83l24.59 24.58a2 2 0 0 0 2.83-2.83L34.83 32 59.41 7.41a2 2 0 0 0-2.83-2.83L32 29.17 7.41 4.59a2 2 0 0 0-2.82 2.82L29.17 32 4.59 56.59a2 2 0 0 0 0 2.82z" fill="#000000" data-original="#000000"></path></g></svg></a></li></ul></span></div><div class="card-body"><br><br><div class="btn-group"><button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add item</button><span class="containerid" data-value="' . $page . '"></span><ul class="dropdown-menu dropdown-menu-end"><li><button class="dropdown-item" type="button" onclick="questionModal(`' . $url . '`,`Add Question`)">Question</button></li><li><button class="dropdown-item" type="button" onclick="questionModal(`' . $urlImport . '`,`Import Question`)">Import Question</button></li></ul></div></div></div></div>';
    });

    Route::get('add-assesment-question/{rowid}', function ($subdomain, $rowid) {

        return '<hr><div class="row"><div class="col-md-7"><div class="col-md-12 mb-2"><div class="form-group required"><label for="question' . $rowid . '" class="form-label">Question</label><textarea name="question[]" id="question' . $rowid . '" cols="30" rows="3"  class="form-control ckeditor" required></textarea></div></div></div><div class="col-md-4"><div class="col-md-12 mb-2"><div class="form-group required"><label for="level' . $rowid . '" class="form-label">Difficulty Level</label><select name="level[]" id="level' . $rowid . '" class="form-control select2_el" required><option value="">Select your choice</option></select></div></div><div class="col-md-12 mb-2"><div class="form-group required"><label for="time_inseconds' . $rowid . '" class="form-label">Time Allocated <small>(in Minutes)</small></label><input type="number" step="any" name="time_inseconds[]" id="time_inseconds' . $rowid . '" class="form-control" required></div></div><div class="col-md-12 mb-2"><div class="form-group required"><label for="require_file_upload' . $rowid . '" class="form-label">Require File Submission</label><select name="require_file_upload[]" id="require_file_upload' . $rowid . '" class="form-control select2_el" required><option value="">Select your choice</option><option value="1">Yes</option><option value="0" selected>No</option></select></div></div><div class="col-md-12 mb-2"><div class="form-group required"><label for="point' . $rowid . '" class="form-label">Marks</label><input type="number" min=0 step="any" name="point[]" id="point' . $rowid . '" class="form-control" required><input type="hidden" name="question_type[]" id="question_type' . $rowid . '" value="text"></div></div></div><div class="col-md-1"><button class="btn btn-sm btn-success" style="margin-right: 15px;" type="button"  onclick="addOptionRow();"> <i class="fa fa-plus"></i> </button><button class="btn btn-sm btn-danger" type="button" onclick="remove_education_fields(' . $rowid . ');"><i class="fa fa-minus"></i></button></div></div>';

    });

    Route::get('add-assesment-question-single/{rowid}', function ($subdomain, $rowid) {

        return '<hr><div class="row mt-4"><div class="col-md-11"><div class="col-md-12 mb-2"><div class="form-group required"><label for="question' . $rowid . '" class="form-label">Question</label><textarea name="question[]" id="question' . $rowid . '" cols="30" rows="3"  class="form-control ckeditor" required></textarea></div></div></div><div class="col-md-1"><button class="btn btn-sm btn-danger pull-left" title="Remove Question" type="button" onclick="remove_row(' . $rowid . ');"><i class="fa fa-minus"></i></button></div><div class="col-md-12"><div class="row"><div class="col-md-3 mb-2">
                            <div class="form-group required">
                                <label for="topic_id' . $rowid . '" class="form-label">Topic</label>
                                <select name="topic_id[]" id="topic_id' . $rowid . '" data-id="' . $rowid . '" class="form-control topic select2_el" onchange="loadSubTopic(' . $rowid . ');" required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="form-group required">
                                <label for="sub_topic_id' . $rowid . '" class="form-label">Sub Topic</label>
                                <select name="sub_topic_id[]" id="sub_topic_id' . $rowid . '" class="form-control select2_el" required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>

                        </div><div class="col-md-3 mb-2"><div class="form-group required"><label for="level' . $rowid . '" class="form-label">Difficulty Level</label><select name="level[]" id="level' . $rowid . '" class="form-control select2_el" required><option value="">Select your choice</option></select></div></div><div class="col-md-3 mb-2"><div class="form-group required"><label for="time_inseconds' . $rowid . '" class="form-label">Time Allocated <small>(in Minutes)</small></label><input type="number" step="any" min="1" name="time_inseconds[]" id="time_inseconds' . $rowid . '" class="form-control" required></div></div><div class="col-md-3 mb-2"><div class="form-group required"><label for="require_file_upload' . $rowid . '" class="form-label">Require File Submission</label><select name="require_file_upload[]" id="require_file_upload' . $rowid . '" class="form-control select2_el" required><option value="">Select your choice</option><option value="1">Yes</option><option value="0" selected>No</option></select></div></div><div class="col-md-3 mb-2"><div class="form-group required"><label for="point' . $rowid . '" class="form-label">Marks</label><input type="number" min="0" step="any" name="point[]" id="point' . $rowid . '" class="form-control" required><input type="hidden" name="question_type[]" id="question_type' . $rowid . '" value="text"></div></div><div class="col-md-6 mb-2">
                            <div class="form-group required">
                                <label for="topic_id" class="form-label">Skill Tags</label>
                                <div class="row">
                                <div class="col-md-3 mb-2"><input type="checkbox" name="tc[]" value="1" class="float-start"><label class="float-start px-1" style=";font-weight:500;">TC</label></div>
                                <div class="col-md-3 mb-2"><input type="checkbox" name="ms[]"  value="1" class="float-start"><label class="float-start px-1" style="font-weight:500;">MS</label></div>
                                <div class="col-md-3 mb-2"><input type="checkbox" name="ps[]"  value="1" class="float-start"><label class="float-start px-1" style="font-weight:500;">PS</label></div>
                                <div class="col-md-3 mb-2"><input type="checkbox" name="at[]" value="1"  class="float-start"><label class="float-start px-1" style="font-weight:500;">AT</label></div>
                                </div>

                                </div></div></div>';

    });

    Route::get('add-assesment-question-link/{rowid}/{qcnt}', function ($subdomain, $rowid, $qcnt) {

        return '<hr><div class="row mt-4"><div class="col-md-7"><div class="col-md-12 mb-2"><div class="form-group required"><label for="question' . $rowid . '" class="form-label">Question</label><textarea name="question[]" id="question' . $rowid . '" cols="30" rows="3"  class="form-control ckeditor" required></textarea><input type="hidden" name="total_subquestions' . $qcnt . '" id="total_subquestions' . $qcnt . '" value="0"><input type="hidden" name="level[]" id="level' . $rowid . '"><input type="hidden" name="time_inseconds[]" id="time_inseconds' . $rowid . '"><input type="hidden" name="require_file_upload[]" id="require_file_upload' . $rowid . '"><input type="hidden" min=0 name="point[]" id="point' . $rowid . '"><input type="hidden" name="question_type[]" id="question_type' . $rowid . '" value="linked"></div></div></div><div class="col-md-3"><div class="col-md-12 mb-2">
                            <div class="form-group required">
                                <label for="topic_id' . $rowid . '" class="form-label">Topic</label>
                                <select name="topic_id[]" id="topic_id' . $rowid . '" data-id="' . $rowid . '" class="form-control topic select2_el" onchange="loadSubTopic(' . $rowid . ');" required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="form-group required">
                                <label for="sub_topic_id' . $rowid . '" class="form-label">Sub Topic</label>
                                <select name="sub_topic_id[]" id="sub_topic_id' . $rowid . '" class="form-control select2_el" required>
                                    <option value="">Select your choice</option>
                                </select>
                            </div>

                        </div><div class="col-md-12 mb-2">
                            <div class="form-group required">
                                <label for="topic_id" class="form-label">Skill Tags</label>
                                <div class="row">
                                <div class="col-md-3 mb-2"><input type="checkbox" name="tc[]" value="1" class="float-start"><label class="float-start px-1" style=";font-weight:500;">TC</label></div>
                                <div class="col-md-3 mb-2"><input type="checkbox" name="ms[]"  value="1" class="float-start"><label class="float-start px-1" style="font-weight:500;">MS</label></div>
                                <div class="col-md-3 mb-2"><input type="checkbox" name="ps[]"  value="1" class="float-start"><label class="float-start px-1" style="font-weight:500;">PS</label></div>
                                <div class="col-md-3 mb-2"><input type="checkbox" name="at[]" value="1"  class="float-start"><label class="float-start px-1" style="font-weight:500;">AT</label></div>
                                </div>

                                </div></div></div><div class="col-md-2"><button class="btn btn-sm btn-success pull-left mx-1" title="Add Sub Question"  type="button"  onclick="addSubQuestion(' . $qcnt . ');"> <i class="fa fa-plus"></i> </button><button class="btn btn-sm btn-danger pull-left" title="Remove Question" type="button" onclick="remove_row(' . $rowid . ');"><i class="fa fa-minus"></i></button></div></div>';

    });

    Route::get('add-assesment-question-sub/{rowid}/{parent}', function ($subdomain, $rowid, $parent) {

        return '<div class="row"><div class="col-md-7"><div class="col-md-12 mb-2"><div class="form-group required"><label for="question' . $rowid . '" class="form-label">Sub Question</label><textarea name="subquestion' . ($parent - 1) . '[]" id="question' . $rowid . '" cols="30" rows="3"  class="form-control ckeditor" required></textarea></div></div></div><div class="col-md-4"><div class="col-md-12 mb-2"><div class="form-group required"><label for="level' . $rowid . '" class="form-label">Difficulty Level</label><select name="level' . ($parent - 1) . '[]" id="level' . $rowid . '" class="form-control select2_el" required><option value="">Select your choice</option></select></div></div><div class="col-md-12 mb-2"><div class="form-group required"><label for="time_inseconds' . $rowid . '" class="form-label">Time Allocated <small>(in Minutes)</small></label><input type="number" min=0 step="any" name="time_inseconds' . ($parent - 1) . '[]" id="time_inseconds' . $rowid . '" class="form-control" required></div></div><div class="col-md-12 mb-2"><div class="form-group required"><label for="require_file_upload' . $rowid . '" class="form-label">Require File Submission</label><select name="require_file_upload' . ($parent - 1) . '[]" id="require_file_upload' . $rowid . '" class="form-control select2_el" required><option value="">Select your choice</option><option value="1">Yes</option><option value="0" selected>No</option></select></div></div><div class="col-md-12 mb-2"><div class="form-group required"><label for="point' . $rowid . '" class="form-label">Marks</label><input type="number" min=0 step="any" name="point' . ($parent - 1) . '[]" id="point' . $rowid . '" class="form-control" required><input type="hidden" name="question_type' . ($parent - 1) . '[]" id="question_type' . $rowid . '" value="text"></div></div></div><div class="col-md-1"><button class="btn btn-sm btn-danger pull-left" title="Remove Question" type="button" onclick="remove_row(' . $rowid . ');"><i class="fa fa-minus"></i></button></div></div>';

    });

    Route::get('get-disclaimer-data/{exam_id}', function ($subdomain, $exam_id) {

        $url = route('tus_quiz', [$subdomain, $exam_id]);

        return '<div class="row">
                    <div class="col-md-12 text-center" style="padding: 0 40px;" id="pop-up">
                        <div class="form-check mx-auto" style="max-width: 911px;">
                            <input class="ocean_color form-check-input agree_disclaimer" type="checkbox" value=""
                                id="flexCheckChecked" checked>
                            <label class="form-check-label" for="flexCheckChecked"
                                style="color: #434343;text-align:justify;font-weight:normal;">
                                In this quiz, the answer choices provided are not exhaustive and there may be multiple
                                correct answers. Carefully read and analyze each answer choice before you select the final answer. You will be graded based on the answer you select within the allocated time. Choose the best answer. <div class="text-center mt-2"> All the Best!!!</div>

                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 text-center" style="padding: 19px 40px;">
                        <a href="' . $url . '" class="btn start_quiz_btn"
                            style="background: #5BC2B9;padding:13px 31px;font: normal normal 600 18px/26px Open Sans;letter-spacing: 0px;color: #FFFFFF;">Start
                            Quiz</a>
                    </div>
                </div>';

    });

    Route::any('/teacher/subjects', [TenantController::class, 'teacherMyCourse'])->name('tut_mycourses');
    Route::any('/teacher/lessons/{subject_id}', [TenantController::class, 'getTeacherLessonListing'])->name('tut_lessons');
    Route::any('/teacher/all-lessons', [TenantController::class, 'getTeacherAllLessonListing'])->name('tut_alllessons');

    Route::get('/teacher/create-lesson', [TenantController::class, 'addLessonTeacher'])->name('tut_addlesson');
    Route::post('/teacher/create-lesson', [TenantController::class, 'saveLessonTeacher'])->name('tut_savelesson');
    Route::get('/teacher/edit-lesson/{lesson_id}', [TenantController::class, 'editLessonTeacher'])->name('tut_editlesson');
    Route::post('/teacher/update-lesson', [TenantController::class, 'updateLessonTeacher'])->name('tut_updatelesson');
    Route::get('/teacher/import-lesson', [TenantController::class, 'importLessonTeacher'])->name('tut_importlesson');
    Route::post('/teacher/import-lesson', [TenantController::class, 'saveImportLessonTeacher'])->name('tut_saveimportlesson');

    Route::any('/teacher/students', [TenantController::class, 'getTeacherStudentListing'])->name('tut_students');
    Route::any('/teacher/teacher-assistants', [TenantController::class, 'getTeacherTaListing'])->name('tut_teacherassistant');

    Route::any('/quiz-submissions', [TenantController::class, 'teacherReviewQuizes'])->name('tut_quiz_submitted');
    Route::get('/quiz-submission-review/{user_result_id}', [TenantController::class, 'teacherReviewQuiz'])->name('tut_quiz_review');

    Route::post('/quiz-save-review', [TenantController::class, 'teacherReviewQuizSave'])->name('tut_quiz_review_save');

    Route::any('/quiz-reviews', [TenantController::class, 'teacherQuizesReviewed'])->name('tut_quiz_reviews');

    Route::any('/assesments', [TenantController::class, 'teacherAssesments'])->name('tut_assesments');
    Route::get('/create-assesment', [TenantController::class, 'teacherAddAssesment'])->name('tut_addassesment');
    Route::post('/create-assesment', [TenantController::class, 'teacherSaveAssesment'])->name('tut_saveassesment');
    Route::get('/edit-assesment/{examination_id}', [TenantController::class, 'teacherEditAssesment'])->name('tut_editassesment');

    Route::any('/assessment-submissions', [TenantController::class, 'teacherReviewAssessments'])->name('tut_assessment_submitted');
    Route::get('/assessment-submission-review/{user_result_id}', [TenantController::class, 'teacherReviewAssessment'])->name('tut_assessment_review');
    Route::post('/assessment-save-review', [TenantController::class, 'teacherReviewAssessmentSave'])->name('tut_assessment_review_save');

    Route::any('/assessment-reviews', [TenantController::class, 'teacherAssessmentsReviewed'])->name('tut_assessment_reviews');

    Route::get('/teacher/view-answers/{user_result_id}', [TenantController::class, 'teacherReviewedAnswers'])->name('tut_reviewed_answers');

    //----teacher library related teacher routes
    Route::any('/teacher/library', [TenantController::class, 'teacherMyLibrary'])->name('tut_mylibrary');
    Route::any('/teacher/library/{subject_id}', [TenantController::class, 'teacherMyLibraryContent'])->name('tut_mylibrarycontent');
    Route::get('/create-library-content/{lesson_id}', [TenantController::class, 'teacherAddLibraryContent'])->name('tut_addlibrarycontent');
    Route::post('/create-library-content', [TenantController::class, 'teacherSaveLibraryContent'])->name('tut_savelibrarycontent');

    Route::get('/edit-library-content/{library_id}', [TenantController::class, 'teacherEditLibraryContent'])->name('tut_editlibrarycontent');
    Route::post('/edit-library-content', [TenantController::class, 'teacherUpdateLibraryContent'])->name('tut_updatelibrarycontent');

    Route::any('/teacher/library/content/{lesson_id}/{content_type}', [TenantController::class, 'teacherLibraryContentByType'])->name('tut_libcontentbytype');

    Route::any('/view-library-file/{library_id}', [TenantController::class, 'viewLibraryFile'])->name('view_lib_file');

    Route::any('/attendances', [TenantController::class, 'getTeacherAttendanceListing'])->name('tut_attendances');
    Route::get('/attendances/filter', [TenantController::class, 'getTeacherAttendanceListingFilter'])->name('tut_attendances_filter');
    Route::get('/attendances/import', [TenantController::class, 'importTeacherAttendance'])->name('tut_attendances_import');
    Route::post('/attendances/import', [TenantController::class, 'saveImportTeacherAttendance'])->name('ta_saveimportattendance');
    Route::get('/attendance/add', [TenantController::class, 'addAttendance'])->name('tut_attendance_add');
    Route::post('/attendance/add', [TenantController::class, 'saveAttendance'])->name('tut_attendance_save');

    Route::get('/attendance/users/{attendance_date}/{lesson_id}', [TenantController::class, 'getTeacherAttendanceUserListing'])->name('tut_attendance_users');

    Route::any('/teacher/skillmap', [TenantController::class, 'getTeacherSkillmap'])->name('tut_skillmap');
    Route::any('/teacher/adaptive-learning', [TenantController::class, 'getTeacherAdaptiveLearn'])->name('tut_adaptivelearn');

    Route::any('/teacher/student-target', [TenantController::class, 'getTeacherStudentTarget'])->name('tut_starget');
    Route::get('/teacher/student-target-add', [TenantController::class, 'getTeacherAddStudentTarget'])->name('tut_starget_add');
    Route::post('/teacher/student-target-add', [TenantController::class, 'getTeacherSaveStudentTarget'])->name('tut_starget_save');
    Route::get('/teacher/student-target-edit/{target_id}', [TenantController::class, 'editTeacherStudentTarget'])->name('tut_starget_edit');
    Route::post('/teacher/student-target-edit', [TenantController::class, 'teacherUpdateStudentTarget'])->name('tut_starget_update');

    Route::any('/teacher/course-completion-status', [TenantController::class, 'getTeacherCourseStatus'])->name('tut_coursestatus');
    Route::any('/teacher/inbox', [TenantController::class, 'tutInbox'])->name('tut_inbox');

    Route::any('/teacher/task-calendar', [TenantController::class, 'viewTeacherTaskCalendar'])->name('tut_calendar');
    Route::get('/teacher/add-task', [TenantController::class, 'teacherAddTask'])->name('tut_addtask');
    Route::post('/teacher/save-task', [TenantController::class, 'teacherSaveTask'])->name('tut_savetask');
    Route::get('/teacher/view-task/{task_id}', [TenantController::class, 'teacherViewTask'])->name('tut_viewtask');
    Route::get('/teacher/edit-task/{task_id}', [TenantController::class, 'teacherEditTask'])->name('tut_edittask');
    Route::post('/teacher/update-task', [TenantController::class, 'teacherUpdateTask'])->name('tut_updatetask');

    Route::any('/teacher/homework', [TenantController::class, 'viewTeacherHomework'])->name('tut_homework');
    Route::any('/teacher/delivery-rating/{lesson_id}', [TenantController::class, 'deliveryRating'])->name('tut_deliveryrating');
    Route::any('/teacher/content-rating/{lesson_id}', [TenantController::class, 'contentRating'])->name('tut_contentrating');

    Route::get('/teacher/view-task-students/{task_id}', [TenantController::class, 'teacherViewTaskStudents'])->name('tut_viewtaskstudent');

    Route::any('/teacher/rating', [TenantController::class, 'teacherMyRating'])->name('tut_myrating');
    Route::any('/teacher/analytic-studio', [TenantController::class, 'teacherKPI'])->name('tut_kpicharts');
    //--------------------

    //P (parent) - routes
    Route::any('/children', [TenantController::class, 'getChildListing'])->name('p_children');
    Route::get('/parent/add-child', [TenantController::class, 'addChild'])->name('p_addchild');
    Route::post('/parent/add-child', [TenantController::class, 'saveChild'])->name('p_savechild');
    Route::get('/parent/validate-child/{token}', [TenantController::class, 'verifyChild'])->name('p_verifychild');
    Route::post('/parent/validate-child', [TenantController::class, 'validateChild'])->name('p_validatechild');
    //P - Exam routes
    Route::any('/parent/all-quiz-marks', [TenantController::class, 'getParentUserQuizResultListing'])->name('p_resultqlist');
    Route::any('/parent/all--assessment-marks', [TenantController::class, 'getParentUserAssessmentResultListing'])->name('p_resultalist');
    Route::get('/parent/view-answers/{user_result_id}', [TenantController::class, 'parentReviewedAnswers'])->name('p_reviewed_answers');
    Route::any('/parent/attendance', [TenantController::class, 'getParentUserAttendanceListing'])->name('p_attendancelist');
    Route::get('/parent/attendance/filter', [TenantController::class, 'getParentUserAttendanceListingFilter'])->name('p_attendancelist_filter');
});
