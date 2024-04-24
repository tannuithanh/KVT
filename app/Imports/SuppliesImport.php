<?php
namespace App\Imports;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Catalog;
use App\Models\Supply;
use App\Models\ProviderDetail;
use Illuminate\Support\Facades\DB;

class SuppliesImport implements ToModel{
    private $project_id;
    private $catalog_id;
    private $rowNumber = 0;
    private $errors = [];
    private $catalogName = null;
    private $providerData = null;
    private $validProviderFound = false;
    private $requiresBlueprint = false;  // Biến để kiểm tra yêu cầu xuất bản vẽ


    public function __construct($project_id){
        $this->project_id = $project_id;
    }

    public function model(array $row){
        $this->rowNumber++;

        // Lưu tên catalog từ hàng đầu tiên để sử dụng sau
        if ($this->rowNumber == 1) {
            $this->catalogName = trim($this->parseCatalogName($row[10]));
            return null;
        }

        if ($this->rowNumber >= 4) {
            if ($row[5] == 'X' || $row[11] == 'X' || $row[12] == 'X' || $row[13] == 'X') {  // Chỉ số cột bắt đầu từ 0
                $this->requiresBlueprint = true;
            }
            $this->checkAndSetProvider($row[14]);
        }

        // Xử lý tạo Catalog và Supply sau khi đã kiểm tra tất cả các hàng
        if ($this->rowNumber > 1 && $this->validProviderFound && !$this->catalog_id && $this->catalogName) {
            $this->setupCatalog();
        }

        // Kiểm tra và tạo mới Supply nếu có đủ điều kiện
        return $this->maybeCreateSupply($row);
    }

    private function setupCatalog(){
        $existingCatalog = Catalog::where('name', $this->catalogName)->where('project_id', $this->project_id)->first();
        if ($existingCatalog) {
            $this->catalog_id = $existingCatalog->id;
            $this->errors[] = "Danh mục '{$this->catalogName}' đã tồn tại trong dự án.";
        } else {
            $description = $this->requiresBlueprint ? "Yêu cầu xuất bản vẽ" : "Không xuất bản vẽ";
            $catalog = Catalog::create([
                'project_id' => $this->project_id,
                'name' => $this->catalogName,
                'nhacungcap' => $this->providerData,
                'description' => $description  // Thêm trường description vào khi tạo Catalog mới
            ]);
            $this->catalog_id = $catalog->id;
        }
    }



    private function maybeCreateSupply($row){
        if ($this->catalog_id && !empty($row[1]) && stripos($row[1], "Tên Vật tư") === false) {
            $existingSupply = Supply::where('maso', $row[2])->where('catalog_id', $this->catalog_id)->first();
            if ($existingSupply) {
                $this->errors[] = "Vật tư với mã số '{$row[2]}' đã tồn tại trong danh mục.";
                return null;
            } else {
                // Kiểm tra xem hàng này có yêu cầu xuất bản vẽ không
                $exportDrawings = ($row[5] == 'X' || $row[11] == 'X' || $row[12] == 'X' || $row[13] == 'X') ? 'X' : null;

                return new Supply([
                    'catalog_id' => $this->catalog_id,
                    'tenvattu' => $row[1],
                    'maso' => $row[2],
                    'donvitinh' => $row[7],
                    'soluong' => $row[8],
                    'exportDrawings' => $exportDrawings  // Gán giá trị dựa trên điều kiện đã kiểm tra
                ]);
            }
        }
        return null;
    }




    private function checkAndSetProvider($providerName){
        $providerName = trim($providerName);
        if (!empty($providerName)) {
            $provider = ProviderDetail::where('name', $providerName)->first();
            if ($provider) {
                $this->providerData = $providerName;
                $this->validProviderFound = true;
            } else {
                $this->errors[] = "Nhà cung cấp '{$providerName}' không tồn tại.";
            }
        }
    }

    private function parseCatalogName($data){
        $lines = explode("\n", $data);
        return trim($lines[1]);
    }

    public function getErrors(){
        return $this->errors;
    }
}
