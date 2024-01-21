<?php

namespace App\Exports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Series;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AthletesExport implements FromCollection, ShouldAutoSize, WithStyles
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Lấy dữ liệu vận động viên
        $athletes = Athlete::all();

        // Tính toán số vòng đấu và số trận đấu mỗi vòng
        $number_of_teams = count($athletes); // Tổng số đội tham gia thi đấu
        $constant = floor($number_of_teams / 2); // Hằng số
        $number_of_rounds = ceil($number_of_teams / $constant); // Số vòng đấu
        $number_of_matches_per_round = ceil($number_of_teams / ($number_of_rounds + 1)); // Số trận đấu mỗi vòng

        // Tạo mảng lưu trữ kết quả
        $results = [];

        // Lặp qua từng vận động viên
        foreach ($athletes as $athlete) {
            // Thêm kết quả vận động viên vào mảng
            $results[] = [
                'number' => $athlete['id'],
                'name' => $athlete['name'],
                'draw_number' => $athlete['draw_number'],
            ];
        }

        return Collection::make($results);
        // return $this->group(Athlete::all()->pluck('draw_number', 'name'));
    }


    public function styles(Worksheet $sheet)
    {
        // Tính toán số vòng đấu và số trận đấu mỗi vòng
    $number_of_teams = count($this->collection()); // Lấy tổng số đội từ kết quả truy vấn
    $constant = floor($number_of_teams / 2);
    $number_of_rounds = ceil($number_of_teams / $constant);
    $number_of_matches_per_round = ceil($number_of_teams / ($number_of_rounds + 1));
    
        // Tạo đường kẻ ngang
        for ($i = 1; $i <= $number_of_rounds; $i++) {
            $sheet->setCellValue('A' . $i, 'Vòng ' . $i);
            for ($j = 1; $j <= $number_of_matches_per_round; $j++) {
                $sheet->getStyle('A' . $i . $j)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        // Tạo đường kẻ dọc
        for ($i = 2; $i <= $number_of_rounds; $i++) {
            for ($j = 1; $j <= $number_of_matches_per_round; $j++) {
                $sheet->getStyle('A' . $i . $j)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('B' . $i . $j)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        // Vẽ các đường line
        for ($i = 1; $i <= $number_of_rounds; $i++) {
            for ($j = 1; $j < $number_of_matches_per_round; $j++) {
                $sheet->getStyle('B' . $i . $j)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('B' . $i . $j + 1)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
            }
        }
        
        // Thêm các đường line ngang
        for ($i = 2; $i <= $number_of_rounds; $i++) {
            for ($j = 1; $j <= $number_of_matches_per_round; $j++) {
                $sheet->getStyle('A' . $i . $j)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            }
        }
    }


    public function headings(): array
    {
        return [
            'Số thứ tự',
            'Tên vận động viên',
            'Vòng thi đấu',
        ];
    }

    // function group($athletes)
    // {
    //     // Tính toán số lượng bảng đấu cần chia
    //     $number_of_groups = ceil(count($athletes) / 2);

    //     // Chia danh sách các vận động viên thành các bảng đấu
    //     $groups = $athletes->chunk($number_of_groups);

    //     // Thêm một đội vào bảng đấu đầu tiên nếu cần
    //     if (count($athletes) % 2 == 1) {
    //         $groups[0][] = $athletes[count($athletes) - 1];
    //     }

    //     // Trả về danh sách các bảng đấu
    //     return $groups;
    // }
}
