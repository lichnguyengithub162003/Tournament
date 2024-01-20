<?php

namespace App\Imports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\ToModel;

class AthletesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Athlete([
            //
        ]);
    }
}
