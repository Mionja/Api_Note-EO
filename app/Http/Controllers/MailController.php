<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Module;
use App\Mail\Retake_exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $details = $request->validate([
            'module' =>'required'            ,
            'body'=>'required'
        ]);
        $module = Module::where('id', $request->module)->first();
        $details['subject'] = 'Rattrapage pour le module '.$module['code'];
        $details['title'] = 'Rattrapage pour le module '.$module['code'];
        // return $details;
        $marks = Mark::all()
                        ->where('retake_exam', 1)           
                        ->where('module_id', $request->module) ;

        $list_student = [];
        foreach ($marks as $mark) {
            $list_student[] = [
                $mark->students->email
            ];
        }
        
        foreach ($list_student as $email) {
            Mail::to($email)->send(new Retake_exam($details));
        }
        
        return ['message'=>"Email sent"];
    }
}
