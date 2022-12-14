<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $module = [];
        $students = Student::all();
        foreach($students as $student){
            foreach ($student->marks as $mark) {
                $module[] = [
                            'code' => $mark->module->code,
                            'name' => $mark->module->name
                         ];
            }
            $grade = $student->grades;
            $data[] = [
             "student" => $student,
             
            ];
         }
        return $data;
    }
    
    /**
     * Store imported students.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importStudents(Request $request)
    {
        $test = [];
        foreach ($request->all() as $data) 
        {
            $test[]=[
                $data['name'],
                $data['email'],
                $data['age'],
                $data['group'],
                $data['grade'],
                $data['gender'],
                $data['password'],
                $data['year'],
                
            ];
            $user = User::create([
                'name'=> $data['name'],
                'email'=> $data['email'],
                'password'=> bcrypt($data['password']),
            ]);

            $student = Student::create([
                'name'=> $data['name'], 
                'email'=> $data['email'], 
                'age'=> $data['age'], 
                'gender'=> $data['gender'], 
            ]);

            $grade = Grade::create([
                 'student_id' => $student->id           ,
                 'name' => $data['grade']               ,
                 'group' => $data['group']              ,
                 'school_year' => $data['year']         
            ]);
        }
        return $test;
        // return $request->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' =>'required'     ,
            'email' => 'required|string|unique:users,email'   ,
            'gender'=>'required'    ,   //Either F or M
            'age'=>'required'       ,
            'grade' =>'required'    ,
            'group' =>'required'    ,
            'school_year' =>'required'  ,
            'password'=>'required|string', 
        ]);

        if ($request->hasFile('photo')) 
        {
            $filename = time() . '.' . $request-> photo ->extension();
            $request->file('photo')->move('img/student_pic/', $filename);
            $request->photo = $filename;
        }

        $user = User::create([
            'name'=> $fields['name'],
            'email'=> $fields['email'],
            'password'=> bcrypt($fields['password']),
        ]);
        $student = Student::create($request->except(['grade', 'school_year', 'group', 'password']));
        $grade = Grade::create([
             'student_id' => $student->id            ,
             'name' => $request->grade               ,
             'group' => $request->group              ,
             'school_year' => $request->school_year  ,
        ]);

        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'status'=> 200          ,
            'user'=> $user          ,
            'token'=> $token        ,
            'student'=> $student    ,
            'grade'=> $grade        
        ];

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $student = Student::all()->where('id',$id)->first();  
        $module = [];
        foreach ($student->marks as $mark) {
            $module[] = [
                        'code' => $mark->module->code,
                        'name' => $mark->module->name
                     ];
        }
        $grade = $student->grades;
        return [
            'student'=> $student           ,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' =>'required'     ,
            'email' => 'required'   ,
            'gender'=>'required'    ,
            'age'=>'required'       ,
        ]);

        $student = Student::find($student);

        if ($request->hasFile('photo')) 
        {
            $destination = "img/student_pic/". $student->photo; 
            File::delete($destination); 

            $filename = time() . '.' . $request-> photo ->extension();
            $request->file('photo')->move('img/student_pic/', $filename);
            $request->photo = $filename;
        }
        
        $student->update($request->all());
        return $student;
    }

    /**
     * Remove everything about the specified student
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        $student = Student::find($student->id);
        $grade = Grade::all()->where('student_id', $student->id);
        $user = User::all()->where('email', $student->email)->first();
        if (Grade::destroy($grade) && Student::destroy($student->id) && User::destroy($user->id)) 
        {
            return ['message' => 'student deleted successfully'];   
        }
        else {
            return ['message' => 'failed to delete'];   
        }
    }
}
