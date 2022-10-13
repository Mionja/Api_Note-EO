<?php

namespace App\Imports;

use App\Models\Mark;
use App\Models\Module;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TestsImport implements ToArray, WithHeadingRow
{
        /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function array(array $data)
    {
        dd($data);
        foreach($data as  $row){
            $module = Module::all()->where('code', $row['module'])->first();
            $student = Student::all()->where('email', $row['email'])->first();
            if ($row['score'] < 10) {
                return Mark::create([
                    "module_id"=>$module['id']      ,
                    "student_id"=>$student['id']    ,
                    "semester"=>$row['semester']  ,
                    "score"=>$row['score']        ,
                    "retake_exam"=>1                ,
                ]);            
            }
            else{
                return Mark::create([
                    "module_id"=>$module['id']      ,
                    "student_id"=>$student['id']    ,
                    "semester"=>$row['semester']  ,
                    "score"=>$row['score']        ,
                ]);            
            }
        }
    }
}