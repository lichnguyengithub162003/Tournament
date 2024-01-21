<?php

namespace App\Imports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class AthletesImport implements ToModel, WithHeadingRow, WithMapping
{
    public function headingRow(): int
    {
        return 1;
    }

    // thêm hàm map để chuyển đổi tên trường
    public function map($row): array
    {
        return [
            'name'          => $row['name'] ?? $row['Họ tên'],
            'organization'  => $row['organization'] ?? $row['Đơn vị'],
            'drawing_order' => $row['drawing_order'] ?? $row['Bốc thăm'],
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // sử dụng tên trường tiếng Anh
        return new Athlete([
            'name'          => $row['name'],
            'organization'  => $row['organization'],
            'drawing_order' => $row['drawing_order'],
        ]);
    }
}
