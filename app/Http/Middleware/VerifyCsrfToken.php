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
        'api/teacher'           ,
        'api/student/pass/{id}' ,
        'api/student/redouble/{id}',
        'api/student/finish/{id}'   ,
        'api/student/quit/{id}'     ,
        'api/student/retake_exam/{id}',
        'api/mark',
        'api/module/list/{grade}/{year}',
        'api/module/list-semester/{grade}/{year}/{semester}',
        'api/teacher/add/module/{id}',
        'api/teacher/detach/module/{id}',
        'api/student/data/graph-specific/{grade}/{year}',
        'api/student/data/graph-general/{grade}',
        'api/student/average_point_by_semester/{year}/{id}/{semester}'
    ];
}
