<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        dd($row);
        $t = [];
        foreach ($row as $row) {
            $t[] = [
                $row['grade']
            ]    ;
        }
        return $t;
        // return new User([
        return ([
            "grade" => $row["grade"]       ,
            "score" => $row["score"]       ,
            "module" => $row["module"]     ,
            "semester" => $row["semester"]
        ]);
       
    }
}
