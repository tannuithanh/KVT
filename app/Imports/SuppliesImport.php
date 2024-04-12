<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Catalog;
use App\Models\Supply;
use Illuminate\Support\Facades\DB;

class SuppliesImport implements ToModel
{
    private $project_id;
    private $catalog_id;
    private $rowNumber = 0;
    private $errors = [];

    public function __construct($project_id)
    {
        $this->project_id = $project_id;
    }

    public function model(array $row)
    {
        $this->rowNumber++;
        if ($this->rowNumber == 1) {
            $lines = explode("\n", $row[10]);
            $catalogName = trim($lines[1]);

            // Kiểm tra xem Catalog với tên này đã tồn tại chưa
            $existingCatalog = Catalog::where('name', $catalogName)->where('project_id', $this->project_id)->first();
            if ($existingCatalog) {
                // Nếu đã tồn tại, lưu id để sử dụng cho các Supplies
                $this->catalog_id = $existingCatalog->id;
                $this->errors[] = "Catalog '{$catalogName}' đã tồn tại.";
                return null; // Bỏ qua tạo mới nếu không muốn tạo trùng
            }

            // Nếu không tồn tại, tạo mới
            $catalog = Catalog::create([
                'project_id' => $this->project_id,
                'name' => $catalogName,
            ]);
            $this->catalog_id = $catalog->id;
            return null;
        }

        if (stripos($row[1], "Tên Vật tư") !== false) {
            return null;
        }

        if (empty($row[1])) { // Giả sử cột B là cần thiết
            return null;
        }

        // Kiểm tra xem mã sản phẩm đã tồn tại chưa
        $existingSupply = Supply::where('maso', $row[2])->where('catalog_id', $this->catalog_id)->first();
        if ($existingSupply) {
            $this->errors[] = "Mã sản phẩm {$row[2]} đã tồn tại ở dòng {$this->rowNumber}.";
            return null; // Có thể bỏ qua hoặc xử lý tùy theo nhu cầu
        }

        // Tạo mới đối tượng Supply nếu không có lỗi
        return new Supply([
            'catalog_id' => $this->catalog_id,
            'tenvattu' => $row[1], // Cột B
            'maso' => $row[2], // Cột C
            'donvitinh' => $row[7], // Cột H
            'soluong' => $row[8], // Cột I
        ]);
    }


    public function getErrors()
    {
        return $this->errors;
    }
}
