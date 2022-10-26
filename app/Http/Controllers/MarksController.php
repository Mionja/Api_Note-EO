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

    public function importMarks(Request $request)
    {
        $test = [];
        foreach ($request->all() as $data) {
            $module = Module::all()->where('code', $data['module'])->where('year', $data['year'])->first();
            $student = Student::all()->where('email', $data['email'])->first();
            $test[]=[
                'student_id'=>$student->id      ,
                'module_id'=>$module->id        ,
                'semester'=>$data['semester']   ,
                'year'=>$data['year']           ,
                'score'=>$data['score']         ,
            ];

            if ($data['score'] < 10) 
            {
                Mark::create([
                "module_id"=>$module->id       ,
                "student_id"=>$student->id     ,
                "semester"=>$data['semester']  ,
                "year"=>$data['year']          ,
                "score"=>$data['score']        ,
                "retake_exam"=>1               ,
                ]);
            } 
            else{
                Mark::create([
                    "module_id"=>$module->id        ,
                    "student_id"=>$student->id      ,
                    "semester"=>$data['semester']   ,
                    "year"=>$data['year']           ,
                    "score"=>$data['score']         ,
                ]);
            }                                
    
        }
        return $test;
    }
     /**
     * Store the imported file 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            // 'score' =>'required'    , 
            'module' => 'required'     ,
            'email' => 'required'      ,
            'semester' => 'required'   ,
            'year' => 'required'       ,
        ]);
        $module = Module::all()->where('code', $request->module)->where('year', $request->year)->first();
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

        return Mark::create([
            "module_id"=>$module['id']      ,
            "student_id"=>$student['id']    ,
            "semester"=>$request->semester  ,
            "year"=>$request->year          ,
            "score"=>$request->score        ,
        ]);

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
        // $marks = Mark::orderBy('score', 'desc')->get();
        $all_marks = [];
        foreach ($marks as $mark) 
        {
            $module=$mark->module;

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
     * Get all marks of a specified student in a specified year and semester
     * 
     * @param  Integer $year
     * @param  Integer $semester
     * @param  \App\Models\Student  $id
     * @return \Illuminate\Http\Response
     */
    public function get_all_marks_by_semester(Int $year,Int $id, Int $semester)
    {
        $marks = Mark::all()->where('student_id', $id)->where('semester', $semester);
        // $marks = Mark::orderBy('score', 'desc')->get();
        $all_marks = [];
        foreach ($marks as $mark) 
        {
            $module=$mark->module;

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
     * @param  int  $year
     * @return \Illuminate\Http\Response
     */
    public function list_module_by_grade(String $grade, int  $year)
    {
        $list_module = [];
        $modules = Module::all()->where('year', $year);
        $sum_credits = 0;
        $number_module = 0;
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
                        $sum_credits += $module->credits;    
                        $number_module++;       
                    }
                    break;
                case 'L2':
                    if ($code < 500 && $code >= 300) {
                        $list_module[] = 
                        [
                            'module' => $module
                        ];  
                        $sum_credits += $module->credits;  
                        $number_module++; 
                    }
                    break;
                case 'L3':
                    if ($code < 700 && $code >= 500) {
                        $list_module[] = 
                        [
                            'module' => $module
                        ];  
                        $sum_credits += $module->credits;  
                        $number_module++; 
                    }
                    break;
                case 'M1':
                    if ($code < 900 && $code >= 700) {
                        $list_module[] = 
                        [
                            'module' => $module
                        ];  
                        $sum_credits += $module->credits;  
                        $number_module++; 
                    }
                    break;
                case 'M2':
                    if ($code < 1000 && $code >= 900) {
                        $list_module[] = 
                        [
                            'module' => $module
                        ];         
                        $sum_credits += $module->credits;  
                        $number_module++; 
                    }
                    break;
            }
        }
        
        return ['list_module'=>$list_module, 
                'sum_credits'=>$sum_credits, 
                'number_module'=>$number_module];
    }

    /**
     *Get all list of modules of a specified grade and semester
     *
     * @param  string  $grade
     * @param  int  $year
     * @param  int  $semester 
     * @return \Illuminate\Http\Response
     */
    public function list_module_by_semester(String $grade, int  $year, int $semester)
    {
        $list_module = [];
        $modules = Module::all()->where('year', $year);
        $sum_credits = 0;
        $number_module = 0;

        foreach ($modules as $module)
        {
            $teacher = $module->teachers;
            $code = explode('_', $module->code)[1];
            switch ($grade) {
                case 'L1':
                    if ($code < 300) {
                        if ($code < 200 & $semester == 1) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                            $sum_credits += $module->credits; 
                            $number_module++;     
                        }
                        else if ($code >= 200 & $semester == 2) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                            $sum_credits += $module->credits;  
                            $number_module++;    
                        }           
                    }
                    break;
                case 'L2':
                    if ($code < 500 && $code >= 300) {
                        if ($code < 400 & $semester == 1) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                             $sum_credits += $module->credits;  
                             $number_module++;    
                        }
                        else if ($code >= 400 & $semester == 2) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                             $sum_credits += $module->credits;   
                             $number_module++;   
                        }
                    }
                    break;
                case 'L3':
                    if ($code < 700 && $code >= 500) {
                        if ($code < 600 & $semester == 1) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                             $sum_credits += $module->credits;   
                             $number_module++;   
                        }
                        else if ($code >= 600 & $semester == 2) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                             $sum_credits += $module->credits;  
                             $number_module++;    
                        }
                    }
                    break;
                case 'M1':
                    if ($code < 900 && $code >= 700) {
                        if ($code < 800 & $semester == 1) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                             $sum_credits += $module->credits;   
                             $number_module++;   
                        }
                        else if ($code >= 800 & $semester == 2) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                             $sum_credits += $module->credits;   
                             $number_module++;   
                        }
                    }
                    break;
                case 'M2':
                    if ($code < 1100 && $code >= 900) {
                        if ($code < 1000 & $semester == 1) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                             $sum_credits += $module->credits;   
                             $number_module++;   
                        }
                        else if ($code >= 1000 & $semester == 2) {
                            $list_module[] = 
                            [
                                'module' => $module,
                            ];
                             $sum_credits += $module->credits;   
                             $number_module++;   
                        }                 
                    }
                    break;
            }
        }
        
        return ['list_module'=>$list_module,
                'sum_credits'=>$sum_credits,
                'number_module'=>$number_module];
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
       $module_number = $this->list_module_by_grade($grade->name, $year)['number_module'];
       $sum_credits = $this->list_module_by_grade($grade->name, $year)['sum_credits'];
       $sum_score = 0;
       
       $i = 0;
        foreach ($all_marks as $mark) 
        {
            $sum_score += ( $mark['marks']['score'] * $mark['marks']['module']['credits'] );
            $i++;
        }

        if ($i != $module_number || $sum_score==0) 
        {
            return ['message'=> "Fail"];
        }
        $average_point = $sum_score / $sum_credits;
        return [
            'message' => 'success'    ,
            'data'    =>round($average_point, 2)
        ];
    }

    /**
     * Get the average point of a student in a certain grade of a certain year and semester
     * 
     * @param  int  $year
     * @param  int  $id
     * @param  int  $semenster 
     * @return \Illuminate\Http\Response
     */
    public function get_average_point_of_student_by_semester( int $year, int $id, int $semester)
    {
       $grade = Grade::all()->where('student_id', $id)->where('school_year', $year)->first();
       $all_marks = $this->get_all_marks_by_year($year, $id);
       $module_number = $this->list_module_by_semester($grade->name, $year, $semester)['number_module'];
       $sum_credits = $this->list_module_by_semester($grade->name, $year, $semester)['sum_credits'];
       
        $sum_score = 0;
        $i = 0;

        foreach ($all_marks as $mark) 
        {
            if ($mark['marks']['semester'] == $semester) {
                $sum_score += ( $mark['marks']['score'] * $mark['marks']['module']['credits'] );
                $i++;
            }
        }

        if ($i != $module_number || $sum_score==0) 
        {
            return ['message'=> "Fail"];
        }
        $average_point = $sum_score / $sum_credits;
        return [
            'message'=> 'success',
            'data'=>round($average_point, 2)
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
        $number = 0;
        $s = [];
        $students = Grade::all()->where('name', $grade)->where('school_year', $year)->where('quit', 0);
        if (! $students->isNotEmpty()) //Raha tsy misy mpianatra mits ao amnio classe ray io
        {
            $s [] = [
                'data'=>['student'=>'', 
                        'average_point'=>[
                            'message'=>'Fail',
                            'data'=>0,
                        ],
                        'group'=>'', 
                        'retake_module'=>'',
                        'message'=>'Fail',
                        'number_student'=>$number,
                        ] 
            ];
        }
        else{
            foreach ($students as $student) 
            {
                $retake_module = "";
                $number++;
                $average_point = $this->get_average_point_of_student_by_grade( $year,  $student->student_id);
                if ($average_point['message'] == 'Fail') 
                { 
                    $s [] = [
                        'data'=>['student'=>'', 
                                'average_point'=>[
                                    'message'=>'Fail',
                                    'data'=>0,
                                ],
                                'group'=>'', 
                                'retake_module'=>'',
                                'message'=>'Fail',
                                'number_student'=>$number,
                                ] 
                    ];
                }

                else
                {
                    $all_marks = $this->get_all_marks_by_year($year, $student->student_id);
                    foreach ($all_marks as $mark) 
                    {
                        if (($mark['marks']['score']) < 10) {
                            $retake_module .= $mark['marks']['module']['code'].", ";
                        }
                    }

                    
                    $s[] = [
                        'data'=>['student'=>$student->student,
                                'number_student'=>$number, 
                                'average_point'=>$average_point,
                                'group'=>$student->group, 
                                'retake_module'=>$retake_module,
                                'message'=>'Success'] 
                    ];
                    
                }
            }
        }
        return $s;
    }

    /**
     * Get the average point of all students in a certain grade of a certain year
     * 
     * @param  String  $grade
     * @param  int  $year
     * @param  int  $semester
     * @return \Illuminate\Http\Response
    */
    public function get_average_point_of_all_students_by_semester(String $grade, int $year, int $semester)
    {
        $number = 0;
        $s = [];
        $students = Grade::all()->where('name', $grade)->where('school_year', $year)->where('quit', 0);
        if (! $students->isNotEmpty()) //Raha tsy misy mpianatra mits ao amnio classe ray io
        {
            $s [] = [
                'data'=>['student'=>'', 
                        'average_point'=>[
                            'message'=>'Fail',
                            'data'=>0,
                        ],
                        'group'=>'', 
                        'retake_module'=>'',
                        'message'=>'Fail',
                        'number_student'=>$number,
                        ] 
            ];
        }
        else{
            foreach ($students as $student) 
            {
                $retake_module = "";
                $number++;
                $average_point = $this->get_average_point_of_student_by_semester( $year,  $student->student_id, $semester);
                if ($average_point['message'] == 'Fail') 
                { 
                    $s [] = [
                        'data'=>['student'=>'', 
                                'average_point'=>[
                                    'message'=>'Fail',
                                    'data'=>0,
                                ],
                                'group'=>'', 
                                'retake_module'=>'',
                                'message'=>'Fail',
                                'number_student'=>$number,
                                ] 
                    ];
                }

                else
                {
                    $all_marks = $this->get_all_marks_by_year($year, $student->student_id);
                    foreach ($all_marks as $mark) 
                    {
                        if ($mark['marks']['semester'] == $semester) {
                            if (($mark['marks']['score']) < 10) {
                                $retake_module .= $mark['marks']['module']['code'].", ";
                            }
                        }
                    }

                    
                    $s[] = [
                        'data'=>['student'=>$student->student,
                                'number_student'=>$number, 
                                'average_point'=>$average_point,
                                'group'=>$student->group, 
                                'retake_module'=>$retake_module,
                                'message'=>'Success'] 
                    ];
                    
                }
            }
        }
        return $s;
    }

    /**
     * Get the GENERAL average point of all students in a certain grade of a certain year
     * 
     * @param  String  $grade
     * @param  int  $year
     * @return \Illuminate\Http\Response
    */
    public function get_general_average_point_of_all_students_by_grade(String $grade, int $year)
    {
        $students = $this->get_average_point_of_all_students_by_grade($grade, $year);
        $t = 0;
        $number_students = 0;
        $sum_ap_all_students = 0;
        foreach ($students as $student) 
        {
            // return $student['data']['message'];
            // if ($student['data']['message'] == 'Fail') {
            //     return [
            //         'message'=> 'Fail'
            //     ];
            // }
            if ($student['data']['message'] == 'Fail') {
               $t = 1;
            }
            
            $number_students = $student['data']['number_student'];
            $sum_ap_all_students += $student['data']['average_point']['data'];
        }

        if ($t == 1) {
            return [
                        'nombre_etudiant'=>$number_students,
                        'message'=> 'Fail',
                    ];
        }
        $average_point = $sum_ap_all_students / $number_students;    
        return  [
            'moyenne'=>$average_point,
            'nombre_etudiant'=>$number_students,
            'message'=>'Success'
            ]  ;

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

    /**
    * Mandefa donnée ho affichena any am le graphe anaky 4
    * 
    * @param  String  $grade
    * @param  int  $year
    * @return \Illuminate\Http\Response
    */
    public function get_data_graph_specific(String $grade, int $year)
    {
        $test = $this->get_average_point_of_all_students_by_grade($grade, $year);
        $participating = 0;
        $not_participating = 0;
        $ap = 0; //nombre de personne nahazo moyenne
        $nap = 0; //nombre de personne tsy nahazo moyenne
        $girl_Gt10 = 0; //Nombre de fille ayant une note plus de 10
        $boy_Gt10 = 0; //Nombre de garçon ayant une note plus de 10
        $girl_Lt10 = 0; //Nombre de fille ayant une note moins de 10
        $boy_Lt10 = 0; //Nombre de garçon ayant une note moins de 10

        foreach ($test as $d) {
            if ($d['data']['average_point']['data'] == 0) {         //Tsy nanao examen
                $not_participating++;
            }
            else if ($d['data']['average_point']['data'] >= 10) {   //Nahazo moyenne
                $participating++;
                $ap++;
                if ($d['data']['student']['gender'] == 'F') 
                {
                    $girl_Gt10++;
                }
                else if ($d['data']['student']['gender'] == 'M') 
                {
                    $boy_Gt10++;
                }
            }
            else if ($d['data']['average_point']['data'] < 10) { // TSY Nahazo moyenne
                $participating++;
                $nap++;
                if ($d['data']['student']['gender'] == 'F') 
                {
                    $girl_Lt10++;
                }
                else if ($d['data']['student']['gender'] == 'M') 
                {
                    $boy_Lt10++;
                }
            }
        }
        return [
            'participating'=> $participating,
            'not_participating'=> $not_participating,
            'ap'=> $ap,
            'nap'=> $nap,
            'girl_Gt10'=> $girl_Gt10,
            'boy_Gt10'=> $boy_Gt10,
            'girl_Lt10'=> $girl_Lt10,
            'boy_Lt10'=> $boy_Lt10,
        ];
    }

    /**
    * Donnée ho affichena am le graphe any amle général kokoa
    * 
    * @param  String  $grade
    * @return \Illuminate\Http\Response
    */
    public function get_data_graph_general(String $grade)
    {
        $res = [];
        $year = [ 2022]; //Mbola ampiana fa 2022 ftsn aloha zao no misy

        foreach ($year as $y) {
            $ap =$this->get_general_average_point_of_all_students_by_grade($grade, $y) ;
            
            if ($ap['message'] == 'Fail') {
                $res[] = [
                    'LX'.$y => [
                        'moyenne'=> 0   ,
                        'nombre_etudiant'=>$ap['nombre_etudiant'],
                        'message'=>'Fail'
                        ] 
                ];
            }
            else{
                $res[] = [
                    'LX'.$y =>$ap
                ];
            }
        }
        return $res;
    }

    /**
     * Copie de tous les modules d'une année vers une autre
     * 
     * @param String $grade
     * @param int $from_year
     * @param int $to_year
     * @return \Illuminate\Http\Response
     */
    public function copy_modules_from_year(String $grade, int $from_year, int $to_year)
    {
        $modules = $this->list_module_by_grade($grade, $from_year)['list_module'];
        
        foreach ($modules as $module) {
            Module::create([
                "code"=>$module['module']['code']    ,
                "name"=>$module['module']['name']    ,
                "credits"=>$module['module']['credits']    ,
                "hour"=>$module['module']['hour']    ,
                "category"=>$module['module']['category']    ,
                "year"=>$to_year    ,
            ]);
    
        }
        return ['message'=>'Success'];
    }

}
