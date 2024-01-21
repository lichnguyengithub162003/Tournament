<?php

namespace App\Http\Controllers;

use App\Imports\AthletesImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    public function import()
    {
        $athletes = Excel::toCollection(new AthletesImport, request()->file('user_file'));

        $groups = $this->sort($athletes);
        $this->border($groups);

        return redirect()->back()->with('success', 'Success!!!');
    }

    public function sort($athletes)
    {
        $athletes = $athletes->sortBy('drawing_order');

        return $athletes;
    }

    function group($athletes)
    {
        $number_of_groups = ceil(count($athletes) / 2);
        $groups = $athletes->chunk($number_of_groups);

        if (count($athletes) % 2 == 1) {
            $groups[0][] = $athletes[count($athletes) - 1];
        }

        return $groups;
    }


    function border($groups)
    {
        $spreadsheet = new Spreadsheet();

        $worksheet = $spreadsheet->createSheet(0);
        $worksheet->setTitle('Bảng đấu');

        $round = 1;
        foreach ($groups as $group) {
            $worksheet->setCellValue('A' . $round, 'Vòng ' . $round);
            for ($i = 0; $i < count($group); $i++) {
                $worksheet->setCellValue('B' . $round . ($i + 1), $group[$i]['name']);
            }
            $round++;
        }

        $writer = new Xlsx($spreadsheet);

        $writer->save('groups.xlsx');
    }


}
