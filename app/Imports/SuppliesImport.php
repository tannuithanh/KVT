<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Supply;

class SuppliesImport implements ToModel
{
    private $rowNumber = 0;
    private $sodonhang;
    private $nhacungcap;
    private $chiphi;
    private $project_id;

    public function __construct($sodonhang, $nhacungcap, $chiphi, $project_id)
    {
        $this->project_id = $project_id;
        $this->sodonhang = $sodonhang;
        $this->nhacungcap = $nhacungcap;
        $this->chiphi = $chiphi;
    }

    public function model(array $row){
        $this->rowNumber++;
        if ($this->rowNumber < 3 ) {
            return null;
        }
        // dd($row);
        return new Supply([
            'project_id' => $this->project_id,
            'sodonhang' => $this->sodonhang,
            'nhacungcap' => $this->nhacungcap,
            'chiphi' => $this->chiphi,
            'noidungphancum' => $row[3], // Cột D
            'stt' => 1, // Giá trị mặc định
            'tenvattu' => $row[1], // Cột B
            'maso' => $row[2], // Cột C
            'donvitinh' => $row[7], // Cột H
            'soluong' => $row[8], // Cột I
            'ngaynhan' => null, // Để trống
            'note' => $row[9], // Cột J
        ]);
    }
}
