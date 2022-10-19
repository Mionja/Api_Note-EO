<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Module;
use App\Mail\Retake_exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail()
    {
        $module = Module::all()->where('code', 'INFO_210')->first(); //Soloina request
        $marks = Mark::all()
                        ->where('retake_exam', 1)
                        ->where('module_id', $module['id']);

        $list_student = [];
        foreach ($marks as $mark) {
            $list_student []= [
                $mark->students->email
            ];
        }

        $m = 'INFO 210';
        $details = [ //Soloina request 
            'subject'=> 'Rattrapage '.$m.'',
            'title'=> 'Ceci est une alerte de rattrapage',
            'body'=> 'Manao rattrapage ianao am module '.$m.''
        ];
        
        foreach ($list_student as $email) {
            Mail::to($email)->send(new Retake_exam($details));
        }
        
        return ['message'=>"Email sent"];
    }
}
