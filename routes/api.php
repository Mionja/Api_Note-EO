<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use App\Http\Controllers\AuthsController;
use App\Http\Controllers\MarksController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\DownloadsController;

Route::middleware(['cors'])->group(function () 
{
    Route::get('/login/{email}/{password}', [AuthsController::class, 'login']);

    //CRUD rehetra
    Route::apiresource('teacher', TeachersController::class);
    Route::apiresource('module',  ModulesController::class);
    Route::apiresource('student', StudentsController::class);

    //Services 
    Route::post('student/pass/{id}',        [ServicesController::class, 'pass']);
    Route::post('student/redouble/{id}',    [ServicesController::class, 'redouble']);
    Route::post('student/finish/{id}',      [ServicesController::class, 'finish_study']);
    Route::post('student/quit/{id}',        [ServicesController::class, 'quit']);

    //Mahazo liste mpianatra spécifique 
    Route::get('student/list/{grade}/{school_year}',                       [ServicesController::class, 'get_student_by_grade']);
    Route::get('student/list/{grade}/{group}/{gender}/{school_year}',      [ServicesController::class, 'get_student_by_grade_and_gender']);        
    Route::post('student/list/quit',                                       [ServicesController::class, 'get_student_quitting']);      

    //Resaka rattrapage
    Route::post('student/retake_exam/{id}',                  [ServicesController::class, 'retake_exam']);
    Route::get('student/list-retaking-exam/{module}/{year}', [ServicesController::class, 'get_student_retaking_exam']);               
    Route::get('student/re-take-exam/{id}',                  [ServicesController::class, 'get_all_retake_exam']);
    Route::post('/send-email',                               [MailController::class, 'sendEmail']);//Mandefa notification manao rattrapage

    //Resaka noten mpianatra rehetra
    Route::post('mark',                                                   [MarksController::class, 'store']);
    Route::get('student/all-marks/{year}/{id}',                           [MarksController::class, 'get_all_marks_by_year']);
    Route::get('student/all-marks-by-semester/{year}/{id}/{semester}',    [MarksController::class, 'get_all_marks_by_semester']);
    Route::get('student/average_point/{year}/{id}',                       [MarksController::class, 'get_average_point_of_student_by_grade']); 
    Route::get('student/average_point_by_semester/{year}/{id}/{semester}',[MarksController::class, 'get_average_point_of_student_by_semester']); 
    Route::get('student/average-point/{grade}/{year}',                    [MarksController::class, 'get_average_point_of_all_students_by_grade']);
    Route::get('student/general/average_point/{grade}/{year}',            [MarksController::class, 'get_general_average_point_of_all_students_by_grade']);     
    Route::get('student/average_point/{gender}/{grade}/{year}',           [MarksController::class, 'get_average_point_of_students_by_gender']);     

    Route::get('student/data/graph-specific/{grade}/{year}',[MarksController::class, 'get_data_graph_specific']);     
    Route::get('student/data/graph-general/{grade}',        [MarksController::class, 'get_data_graph_general']);     

    //Autres(modules, teacher, downloads)
    Route::get('copy-all-modules/{from_year}/{to_year}',    [ModulesController::class, 'copy_modules_from_year']);
    Route::get('copy-modules/{grade}/{from_year}/{to_year}',[MarksController::class, 'copy_modules_from_year']);
    Route::get('module/list/{grade}/{year}',                [MarksController::class, 'list_module_by_grade']);
    Route::get('module/list-semester/{grade}/{year}/{semester}',[MarksController::class, 'list_module_by_semester']);
    Route::post('teacher/add-module/{id}',                      [TeachersController::class, 'add_module']);
    Route::post('teacher/detach-module/{id}',                   [TeachersController::class, 'detach_module']);
    Route::get('download/pdf/{year}/{semester}/{id}',           [DownloadsController::class, 'download_pdf_marks_students']);

    // protected routes
    Route::group(['middleware'=> 'auth:sanctum'],
    function () 
    {
        Route::post('/logout',   [AuthsController::class, 'logout']);
    });
});
