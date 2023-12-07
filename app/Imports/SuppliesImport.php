<?php

namespace App\Imports;

use App\Models\Supply;
use Maatwebsite\Excel\Concerns\ToModel;

class SuppliesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Supply([
            'sodonhang' => $row[1],
            'nhacungcap' => $row[2],
        ]);
    }
}
