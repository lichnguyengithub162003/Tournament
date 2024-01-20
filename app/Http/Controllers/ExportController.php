<?php

namespace App\Http\Controllers;

use App\Exports\AthletesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export()
    {
        return Excel::download(new AthletesExport, 'xxx.xlsx');
    }
}
