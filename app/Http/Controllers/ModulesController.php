<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Teacher;
use Illuminate\Http\Request;


class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $modules = Module::all();
        foreach($modules as $module){
            $teachers = $module->teachers;
           $data[] = [
            "module" => $module,
           ];
        }
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' =>'required'                 ,
            'code' => 'required|min:8'          ,
            'hour'=>'required'                  ,
            'year'=>'required'                  ,
            'credits'=>'required'               ,
            'category'=>'required'              ,
        ]
        );
        if ($request->teacher_id) 
        {
            $module = Module::create($request->except('teacher_id'));  
            $teacher = Teacher::findOrFail($request->teacher_id);
    
            $module_insert = $module->teachers()->save($teacher);
        }
        else
        {
            $module_insert = Module::create($request->all());
        }
        
        if ($module_insert)
        {
            return response()->json([
                'message' => 'success',
                'module'    => $module_insert
            ], 200);
        };   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Module $module)
    {
        $module = Module::find($module)->first();
        $teachers = $module->teachers;
        return [
            'module' => $module        ,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' =>'required'           ,
            'code' => 'required|min:8|unique:modules,code'    ,
            'hour'=>'required'                  ,
            'year'=>'required'                  ,
            'credits'=>'required'               ,
            'category'=>'required'              ,
        ]
        );
        $module = Module::find($id);	
        if ($module->update($request->all())) {
            return [
                'message' => 'update success'		,
                'module'  =>  $module
            ];   
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Module::destroy($id);   
    }

        /**
     * Copie de tous les modules d'une année vers une autre
     * 
     * @param int $from_year
     * @param int $to_year
     * @return \Illuminate\Http\Response
     */
    public function copy_modules_from_year(int $from_year, int $to_year)
    {
        $modules = Module::all()->where('year', $from_year);
        foreach ($modules as $module) {
            Module::create([
                "code"=>$module['code']    ,
                "name"=>$module['name']    ,
                "credits"=>$module['credits']    ,
                "hour"=>$module['hour']    ,
                "category"=>$module['category']    ,
                "year"=>$to_year    ,
            ]);
    
        }
        return ['message'=>'Success'];
    }
  
}
