<?php

namespace App\Http\Controllers;

use App\Mail\Retake_exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail()
    {
        $details = [
            'title'=> 'Test to send email',
            'body'=> 'This is the body of the test email'
        ];

        Mail::to("mionjaranaivoarison@gmail.com")->send(new Retake_exam($details));
        
        return "Email sent";
    }
}
