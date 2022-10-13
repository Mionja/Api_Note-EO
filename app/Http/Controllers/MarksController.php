<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Grade;
use App\Models\Module;
use App\Models\Student;
use App\Imports\UsersImport;
use App\Imports\TestsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class MarksController extends Controller
{
    public function index()
    {
        return view('test');
    }

     /**
     * Store the imported file 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Excel::import(new UsersImport, request()->file('file'));
        // Excel::import(new TestsImport, request()->file('file'));

        $request->validate([
            // 'score' =>'required'    , 
            'module' => 'required'     ,
            'email' => 'required'      ,
            'semester' => 'required'   ,
            'year' => 'required'       ,
        ]);
        $module = Module::all()->where('code', $request->module)->first();
        $student = Student::all()->where('email', $request->email)->first();
       
        if ($request->score < 10) {
            return Mark::create([
                "module_id"=>$module['id']      ,
                "student_id"=>$student['id']    ,
                "semester"=>$request->semester  ,
                "year"=>$request->year          ,
                "score"=>$request->score        ,
                "retake_exam"=>1                ,
            ]);
    
        }

        return Mark::create([$request->all()]);

    }

    /**
     * Get all marks of a specified student in a specified year
     * 
     * @param  Integer $year
     * @param  \App\Models\Student  $id
     * @return \Illuminate\Http\Response
     */
    public function get_all_marks_by_year(Int $year,Int $id)
    {
        $marks = Mark::all()->where('student_id', $id);
        $all_marks = [];
        foreach ($marks as $mark) 
        {
            $module[]=[
                'name'=> $mark->module->name    ,
                'code'=> $mark->module->code    ,
            ];

            $year_mark = $mark->year;
            if ($year_mark == $year) 
            {
                $all_marks []= [
                    'marks'=> $mark
                ];
            }
        }
        
        return $all_marks;
        
    }

  /**
     *Get all list of modules of a specified grade
     *
     * @param  string  $grade
     * @return \Illuminate\Http\Response
     */
    public function list_module_by_grade(String $grade)
    {
        $list_module = [];
        $modules = Module::all();
        $module_number = 0;
        foreach ($modules as $module)
        {
            $teacher = $module->teachers;
            $code = explode('_', $module->code)[1];
            switch ($grade) {
                case 'L1':
                    if ($code < 300) {
                        $list_module[] = 
                        [
                            'module' => $module,
                        ];
                        $module_number++;           
                    }
                    break;
                case 'L2':
                    if ($code < 500 && $code >= 300) {
                        $list_module[] = 
                        [
                            'module' => $module
                        ];  
                        $module_number++;                    
                    }
                    break;
                case 'L3':
                    if ($code < 700 && $code >= 500) {
                        $list_module[] = 
                        [
                            'module' => $module
                        ];  
                        $module_number++;                    
                    }
                    break;
                case 'M1':
                    if ($code < 900 && $code >= 700) {
                        $list_module[] = 
                        [
                            'module' => $module
                        ];  
                        $module_number++;                    
                    }
                    break;
                case 'M2':
                    if ($code < 1000 && $code >= 900) {
                        $list_module[] = 
                        [
                            'module' => $module
                        ];         
                        $module_number++;             
                    }
                    break;
            }
        }
        
        return ['list_module'=>$list_module, 'module_number'=>$module_number];
    }

    /**
     * Get the average point of a student in a certain grade of a certain year
     * 
     * @param  int  $year
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_average_point_of_student_by_grade( int $year, int $id)
    {
       $grade = Grade::all()->where('student_id', $id)->where('school_year', $year)->first();
       $all_marks = $this->get_all_marks_by_year($year, $id);
       $module_number = $this->list_module_by_grade($grade->name)['module_number'];
        $sum_score = 0;
        $i = 0;
        foreach ($all_marks as $mark) 
        {
            $sum_score += $mark['marks']['score'];
            $i++;
        }

        if ($i != $module_number) 
        {
            return ['message'=> "Fail"];
        }
        $average_point = $sum_score / $module_number;
        return [
            'message'=> 'success',
            'data'=>$average_point
        ];
    }

 /**
     * Get the average point of all students in a certain grade of a certain year
     * 
     * @param  String  $grade
     * @param  int  $year
     * @return \Illuminate\Http\Response
     */
    public function get_average_point_of_all_students_by_grade(String $grade, int $year)
    {
        $students = Grade::all()->where('name', $grade)->where('school_year', $year)->where('quit', 0);
        $s = [];
        $retake_module = "";
        foreach ($students as $student) 
        {
            $average_point = $this->get_average_point_of_student_by_grade($grade, $year,  $student->student_id);
            $all_marks = $this->get_all_marks_by_year($year, $student->student_id);
            foreach ($all_marks as $mark) 
            {
                if (($mark['marks']['score']) < 10) {
                    $retake_module .= ", ". $mark['marks']['module']['code'];
                }
            }
           dd($student);
            $s[] = [
                'data'=>['student'=>$student->student, 
                        'average_point'=>$average_point,
                        'group'=>$student->group, 
                        'retake_module'=>$retake_module] 
            ];
        }

        return $s;
    }


   /**
     * Get the general average point of all students in a certain grade of a certain year
     * 
     * @param  String  $grade
     * @param  int  $year
     * @return \Illuminate\Http\Response
     */
    public function get_general_average_point_of_all_students_by_grade(String $grade, int $year)
    {
        $students = $this->get_average_point_of_all_students_by_grade($grade, $year);
        $number_students = 0;
        $sum_ap_all_students = 0;
        foreach ($students as $student) 
        {
            $number_students++;
           
            $sum_ap_all_students += $student['data']['average_point'];
        }

        if ($number_students) {
            $average_point = $sum_ap_all_students / $number_students;    
            return  [
                'status'=>200,
                'moyenne'=>$average_point,
                'nombre_etudiant'=>$number_students,
                ]  ;
        }
        
        else{
            return  [
                'status'=>400];
        }
    }

    //list of all general average points in a year
    public function get_general_average_point(String $grade)
    {
        $g = Grade::all()->where('name', $grade);
        return $g;

    }

    /**
     * Get the average point of all students in a certain grade of a certain year with a specified gender
     * 
     * @param  String  $gender
     * @param  String  $grade
     * @param  int  $year
     * @return \Illuminate\Http\Response
     */
    public function get_average_point_of_students_by_gender( String $gender, String $grade, int $year)
    {
        $students = Student::all()->where('gender', $gender);
        $all_students = [];
        $all_grade = Grade::all()->where('name', $grade)->where('school_year', $year);
        foreach ($students as $student) 
        {
            foreach ($all_grade as $grade) 
            {
                if ($student->id == $grade->student_id) 
                {
                    $all_students[] = [
                        $student
                    ];
                }
            }
        }

        $number_students = 0;
        $sum_ap_all_students = 0;
        foreach ($all_students as $student) 
        {
            $number_students++;
            $ap_all_students = $this->get_average_point_of_student_by_grade($grade, $year,  $student->student_id);
            // if (typeOf($ap_all_students) == IsArray) 
            // {
            //     return['message'=>'Fail'];
            // }
            $sum_ap_all_students += $ap_all_students;
        }
        $average_point = $sum_ap_all_students / $number_students;
        return  $average_point;
    }
}
