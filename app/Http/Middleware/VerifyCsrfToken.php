<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/student'           ,
        'api/send-email'        ,
        'api/student/{id}'      ,
        'api/module'            ,
        'api/module/{id}'            ,
        'api/teacher'           ,
        'api/student/redouble/{id}',
        'api/student/finish/{id}'   ,
        'api/student/quit/{id}'     ,
        'api/student/retake_exam/{id}',
        'api/mark',
        'api/module/list/{grade}/{year}',
        'api/module/list-semester/{grade}/{year}/{semester}',
        'api/copy-modules/{grade}/{from_year}/{to_year}',
        'api/copy-all-modules/{from_year}/{to_year}',
        'api/teacher/add/module/{id}',
        'api/teacher/detach/module/{id}',
        'api/student/data/graph-specific/{grade}/{year}',
        'api/student/data/graph-general/{grade}',
        'api/student/average_point_by_semester/{year}/{id}/{semester}',
        'api/student/all-marks-by-semester/{year}/{id}/{semester}',
        'api/student/pass/{id}' ,
        'api/import',
        'api/import/students',
    ];
}
