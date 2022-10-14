<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash; 

class AuthsController extends Controller
{
    public function login(string $email, string $password)
    {

        //Check email
        $user = User::where('email',$email)->first();
        $student_id = Student::where('email',$email)->first();

        //Check password
        if (!$user) { 
            return [
                'message'=> 'Wrong email'
            ];
        }
        elseif (!Hash::check($password, $user->password)) {
            return [
                'message'=> 'Wrong password'
            ];
        }

        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user'=> $user['name'],
            'student_id'=> $student_id['id'],
            'token'=> $token,
        ];

        return response($response, 201); //201 is to confirm that everything was successfull and something is created
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'logged out'
        ];
    }

    public function test(Request $request)
    {
        return 'teest';
    }
}
