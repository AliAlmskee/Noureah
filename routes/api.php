<?php

use Illuminate\Http\Request;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\VersionController;

use App\Http\Controllers\BookStudentController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\StudyProgressController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\EmojiController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('admin')->group(function () {
    Route::resource('students', StudentController::class);


});

Route::apiResource('tests', TestController::class);
Route::apiResource('study-progresses', StudyProgressController::class);
Route::apiResource('bookstudents', BookStudentController::class);
Route::apiResource('folders', FolderController::class);
Route::apiResource('exam', ExamController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('branches', BranchController::class);
Route::apiResource('teachers', TeacherController::class);
Route::apiResource('versions', VersionController::class);
Route::Resource('admin', AdminController::class);
Route::apiResource('messages', MessageController::class);
Route::apiResource('emoji', EmojiController::class);
// add Former student
Route::post('/finished-book', 'App\Http\Controllers\StudentController@addFinishedBook');
Route::post('/finished-folder', 'App\Http\Controllers\StudentController@addFinishedFolder');
Route::post('/addFormerStudent', 'App\Http\Controllers\StudentController@addFormerStudent');





Route::post('/statistic/sort-by-pages', 'App\Http\Controllers\StatisticsController@sortallStudentsByPages');
Route::post('/statistic/sort-by-special-tests', 'App\Http\Controllers\StatisticsController@sortallStudentsBySpecialTests');
Route::post('/statistic/sort-by-approved-exams', 'App\Http\Controllers\StatisticsController@sortallStudentsByApprovedExams');


Route::post('/change-student-photo/{id}','App\Http\Controllers\StudentController@changephoto');
Route::put('/exams/{exam}/approve', 'App\Http\Controllers\ExamController@approveExam');



Route::get('emojis/{branch_id}', 'App\Http\Controllers\EmojiController@getEmojisByBranch');
Route::delete('emojis/{id}', 'App\Http\Controllers\EmojiController@delete');
Route::post('imojis/student', 'App\Http\Controllers\EmojiController@emojis_student');


Route::get('emojis_photo/{id}', 'App\Http\Controllers\EmojiController@getImage');




Route::post('/calculatePercentage',[StudyProgressController::class,'calculatePercentage']);




Route::post('/changetheBook',[StudyProgressController::class,'changetheBook']);
