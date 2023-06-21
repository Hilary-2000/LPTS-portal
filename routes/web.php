<?php

use App\Http\Controllers\assignmentController;
use App\Http\Controllers\lessonPlan;
use App\Http\Controllers\parentCOntroller;
use App\Http\Controllers\processLogin;
use App\Http\Controllers\QuestionBank;
use App\Http\Controllers\studentController;
use App\Http\Controllers\teacherController;
use Google\Service\DisplayVideo\AssignedLocation;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// process login
Route::get('/', [processLogin::class,"byPassLogin"]);
Route::get("/Login",[processLogin::class,"byPassLogin"]);
// process logout
Route::get("/Logout",[processLogin::class,"logout"]);
// when password is forgotten reset
// Route::view("/ResetPassword","resetPassword");
// process login
Route::post("/processLogin",[processLogin::class,"procLogin"]);
// student dashboard
Route::get("/Student/Dashboard",[lessonPlan::class,"studentDash"]);
// parent dashboard
Route::get("/Parent/Dashboard",[parentCOntroller::class,"displayDash"]);
// teacher dashboard
Route::get("/Teacher/Dashboard",[lessonPlan::class,"teacherDash"]);
// student, teacher & parent create password after first time login
Route::view("/Student/ResetPassword","resetPassStudents");
Route::view("/Teacher/ResetPassword","resetPasswordTr");
Route::view("/Parent/ResetPassword","resetParents");
Route::post("/Student/CreatePassword",[processLogin::class,"createPasswords"]);
Route::post("/Parent/CreatePassword",[processLogin::class,"createParentPassword"]);

// academics
Route::get("/Teacher/LessonPlan",[lessonPlan::class,"createLessonPlan"]);
Route::get("/Teacher/Create/Lessonplan/{lesson_id}/Class/{class}",[lessonPlan::class,"editLessonPlan"]);
Route::get("/Teacher/HOD/Create/Lessonplan/{lesson_id}/Class/{class}",[lessonPlan::class,"editHODLessonPlan"]);
Route::get("/Teacher/CreatePlan/Long/{lesson_id}/class/{class}",[lessonPlan::class,"createLongTermPlan"]);
Route::get("/Teacher/HOD/CreatePlan/Long/{lesson_id}/class/{class}",[lessonPlan::class,"createLongTermPlan"]);
Route::post("/CreateLessonPlan/addStrands",[lessonPlan::class,"registerStrand"]);
Route::post("/CreateLessonPlan/addSubStrands",[lessonPlan::class,"registerSubStrands"]);
Route::post("/UpdateLessonPlan/updateStrand",[lessonPlan::class,"updateStrands"]);
Route::post("/EditLessonPlan/editSubStrands",[lessonPlan::class,"updateSubStrand"]);
Route::get("/deleteStrand/Subject/{lesson_id}/Class/{class}/Strand/{index}",[lessonPlan::class,"deleteStrands"]);
Route::get("/deleteSubStrand/Subject/{lesson_id}/Class/{class}/Strand/{sub_index}",[lessonPlan::class,"deleteSubStrand"]);
Route::get("/Teacher/CreatePlan/Medium/{lesson_id}/class/{class}",[lessonPlan::class,"createMediumPlan"]);
Route::get("/Teacher/HOD/LessonPlan",[lessonPlan::class,"hodLessonPlan"]);
Route::post("/updateMediumPlan",[lessonPlan::class,"updateMediumPlan"]);
Route::get("/Teacher/ChangeStatus/{id}/{lesson_id}/{class}",[lessonPlan::class,"changeStatusPlan"]);
Route::get("/Teacher/ChangeStatus/Medium/{id}/{lesson_id}/{class}",[lessonPlan::class,"changeStatusMediumPlan"]);
Route::get("/Teacher/ChangeStatus/Short/{id}/{lesson_id}/{class}",[lessonPlan::class,"changeStatusShortPlan"]);
Route::get("/Teacher/HOD/CreatePlan/Medium/{lesson_id}/class/{class}",[lessonPlan::class,"createMediumPlan"]);
Route::get("/Teacher/Profile",[teacherController::class,"teacherProfile"]);

Route::get("/Teacher/CreatePlan/Weekly/{lesson_id}/class/{class_id}",[lessonPlan::class,"CreateWeeklyPlan"]);
Route::get("/Teacher/HOD/CreatePlan/Weekly/{lesson_id}/class/{class_id}",[lessonPlan::class,"CreateWeeklyPlan"]);
Route::post("/UploadNotesFiles",[lessonPlan::class,"uploadNotesFiles"]);
Route::post("/DeleteFiles",[lessonPlan::class,"DeleteFiles"]);
Route::post("/UploadYoutube",[lessonPlan::class,"upload"]);
Route::get('/auth', [lessonPlan::class,"auth"]);
Route::get("/auth/callback",[lessonPlan::class,"handleProviderCallback"]);
Route::get("/DeleteYT/{videoId}",[lessonPlan::class,"deleteVideo"]);
Route::post("/ManageShortPlan",[lessonPlan::class,"ManageShortPlan"]);
Route::post("/Teacher/Updateprofile",[teacherController::class,"updateProfile"]);
Route::post("/Teacher/UpdatePass",[teacherController::class,"UpdatePassword"]);

// questions banks
Route::get("/Teacher/QuestionBank",[QuestionBank::class,"getSubjects"]);
Route::get("/Teacher/QuestionBank/{lesson_id}/Create/{class_id}",[QuestionBank::class,"createQB"]);
Route::get("/Teacher/QuestionBank/{lesson_id}/BankQuestions/{class_id}",[QuestionBank::class,"bankQuestions"]);
Route::post("/Bankit",[QuestionBank::class,"bankIt"]);
Route::get("/Teacher/QuestionBank/{lesson_id}/EditQB/{class_id}/sub_id/{questions}",[QuestionBank::class,"editQB"]);
Route::post("/UpdateQuizBank",[QuestionBank::class,"updateTable"]);
Route::get("/Delete/QB/{qid}/Sid/{lesson_id}/Class/{class_id}",[QuestionBank::class,"deleteQuestion"]);
Route::get("/Teacher/QuestionBank/{lesson_id}/CreateTest/{class_id}",[QuestionBank::class,"createTest"]);
Route::post("/createTest/QB",[QuestionBank::class,"createTestQB"]);

// assignments
Route::get("/Teacher/Assignment",[assignmentController::class,"getSubjects"]);
Route::get("/Teacher/Assignments/{lesson_plan}/Create/{class}",[assignmentController::class,"createAssignments"]);
Route::get("/Create/Assignments/{subject_id}/{class_name}",[assignmentController::class,"createAssign"]);
Route::post("/CreateAssignments",[assignmentController::class,"createAssignment"]);
Route::get("/Assignments/Set/{assignment_id}",[assignmentController::class,"setAssignment"]);
Route::get("/Assignments/Edit/{assignment_id}",[assignmentController::class,"editAssignment"]);
Route::post("/Assignments/uploadAss",[assignmentController::class,"uploadAssignments"]);
Route::post("/Assignments/deleteAssResources",[assignmentController::class,"DeleteFiles"]);
Route::post("/Assignments/Add",[assignmentController::class,"addAssignments"]);
Route::get("/DeleteQuiz/{assignment_id}/{questions}",[assignmentController::class,"deleteQuiz"]);
Route::get("Assignment/Status/{assignment_id}",[assignmentController::class,"assignmentStatus"]);
Route::get("/Assignment/Delete/{assignment_id}",[assignmentController::class,"deleteAssignment"]);
Route::post("/Assignment/Update/",[assignmentController::class,"updateAssignments"]);
Route::get("/Students/Assignment",[lessonPlan::class,"studentAssignments"]);
Route::get("/Student/Assignment/Attempt/{assignment_id}",[assignmentController::class,"assignmentIds"]);
Route::post("/Submit/Assignment",[assignmentController::class,"submitAnswers"]);
Route::get("/Student/Assignment/ViewDone/{assignment_id}", [assignmentController::class,"reviewMyAnswers"]);
Route::get("/Assignments/Mark/{assignment_id}",[assignmentController::class,"markAssignments"]);
Route::get("/Teacher/Assignment/Mark/{assignment_id}/{adm_no}",[assignmentController::class,"markStudentAssignments"]);
Route::post("/Teacher/Mark/Submit",[assignmentController::class,"markedAnswers"]);
Route::get("/Teacher/Redo/{assignment_id}/{student_id}",[assignmentController::class,"redoAssignment"]);


// student dashboard
Route::get("/Student/CourseMaterial",[studentController::class,"studentCourseMaterials"]);
Route::get("/Student/CM/view/{subject_id}",[studentController::class,"getCourseMaterials"]);
Route::get("/Students/Messages",[studentController::class,"getStudentNotification"]);
Route::get("/Student/Alert/Read/{notification_id}",[studentController::class,"readStudentNotifications"]);

// parents routes
Route::get("/Parent/Fees",[parentCOntroller::class,"parentFees"]);
Route::get("/Parent/View/StudentFeesDetails/{student_adm}",[parentCOntroller::class,"studentFeesHistory"]);
Route::get("/Parent/Print/Fees",[parentCOntroller::class,"printFeesStatement"]);
Route::get("/Parent/Fees/View/{fees_id}/{adm_no}",[parentCOntroller::class,"getFeesDetails"]);
Route::get("/Parent/Peformance",[parentCOntroller::class,"parentPerfomance"]);
Route::get("/Parent/Alert",[parentCOntroller::class,"parentAlert"]);
Route::get("/Parent/Alert/Read/{alert_id}",[parentCOntroller::class,"readParentAlert"]);

// notification
Route::get("/Teacher/Messages",[teacherController::class,"teacherMessage"]);
Route::get("/Teacher/Messages/CreateAlert",[teacherController::class,"createAlert"]);
Route::post("/Teacher/SaveAlert",[teacherController::class,"createAlertnMessage"]);
Route::get("/Teacher/Messages/Manage",[teacherController::class,"manageAlerts"]);
Route::get("/Teacher/Messages/Manage/{message_id}",[teacherController::class,"manageExistingAlert"]);
Route::post("/Teacher/UpdateAlert",[teacherController::class,"updateAlert"]);
Route::get("/Teacher/Delete/Notice/{notification_id}",[teacherController::class,"deleteAlert"]);
Route::get("/Teacher/Alert/Read/{notification_id}",[teacherController::class,"readNotification"]);