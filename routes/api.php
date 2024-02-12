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
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\EmojiController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/getid', [AuthController::class, 'getAuthenticatedUserId']);
    Route::post('/finished-book', [StudentController::class, 'addFinishedBook']);
    Route::post('/finished-folder', [StudentController::class, 'addFinishedFolder']);
    Route::post('/addFormerStudent', [StudentController::class, 'addFormerStudent']);
    Route::get('/book_versions/{id}', [VersionController::class, 'index']);

    Route::apiResource('books', BookController::class);
    Route::apiResource('versions', VersionController::class);
    Route::apiResource('folders', FolderController::class);
    Route::apiResource('messages', MessageController::class);
    Route::apiResource('students', StudentController::class);

    Route::post('/approveExam',[ExamController::class,'approveExam']);
    Route::post('/statistics',[StatisticsController::class,'student_statistics']);
    Route::post('/teachers_statistics',[StatisticsController::class,'teachers_statistics']);

    Route::apiResource('emoji', EmojiController::class);
    Route::get('emojis/{branch_id}', 'App\Http\Controllers\EmojiController@getEmojisByBranch');
    Route::delete('emojis/{id}', 'App\Http\Controllers\EmojiController@delete');
    Route::get('emojis/student', 'App\Http\Controllers\EmojiController@emojis_student');
    Route::get('emojis_photo/{id}', 'App\Http\Controllers\EmojiController@getImage');

    Route::post('/changetheBook',[StudyProgressController::class,'changetheBook']);


    Route::post('/change_password', [AuthController::class, 'changepassword']);
    Route::get('/version_folders/{id}', [FolderController::class, 'index']);
    Route::get('/finished_students/{branch_id}',[StudentController::class,'student_finishedbook']);


});
    Route::apiResource('tests', TestController::class);
    Route::apiResource('exams', ExamController::class);

    Route::get('emojis/{branch_id}', 'App\Http\Controllers\EmojiController@getEmojisByBranch');
    Route::get('emojis_photo/{id}', 'App\Http\Controllers\EmojiController@getImage');

    Route::post('/finished-pages', [StudyProgressController::class, 'finishedPages']);
    Route::get('/version_folders/{id}', [FolderController::class, 'index']);

    Route::get('/bookStatus/{id}',[BookStudentController::class,'bookStatus']);

    Route::get('/student_search',[StudentController::class,'search']);
    Route::get('/profile',[StudentController::class,'profile']);
    Route::put('/students/{id}',[StudentController::class,'update']);
    Route::get('/students',[StudentController::class,'index']);
    Route::get('/student_image/{imageURL}',[StudentController::class,'student_image']);


    Route::apiResource('study-progresses', StudyProgressController::class);
    Route::apiResource('bookstudents', BookStudentController::class);


    Route::apiResource('branches', BranchController::class);
    Route::apiResource('teachers', TeacherController::class);





    Route::post('/change_student_photo/{id}','App\Http\Controllers\StudentController@changephoto');
    Route::put('/exams/{exam}/approve', 'App\Http\Controllers\ExamController@approveExam');






    Route::post('/calculatePercentage',[StudyProgressController::class,'calculatePercentage']);

    Route::get('/getidby_key/{key}',[StudentController::class,'getId']);

    Route::get('top3',[StudentController::class,'top3']);
    Route::get('/testby_id/{id}/{folder_id}',[TestController::class,'testforstudent']);
    Route::get('/pages_colors',[StudyProgressController::class,'pagescolors']);


    Route::get('get_delta/{id}',[StudentController::class,'get_delta']);


    Route::get('folder_info/{student_id}',[StudyProgressController::class,'get_start_end_page']);


    Route::get('exam_status/{student_id}/{version_id}',[BookStudentController::class,'exam_status']);
