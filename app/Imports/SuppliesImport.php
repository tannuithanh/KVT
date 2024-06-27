<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Catalog;
use App\Models\Supply;
use App\Models\ProviderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuppliesImport implements ToModel {
    private $project_id;
    private $exportDrawings;
    private $catalog_id;
    private $rowNumber = 0;
    private $errors = [];
    private $catalogName = null;
    private $providerData = null;
    private $validProviderFound = false;

    public function __construct($project_id, $exportDrawings){
        $this->project_id = $project_id;
        $this->exportDrawings = $exportDrawings; // Gán giá trị cho biến
    }

    public function model(array $row){
        $this->rowNumber++;

        // Lưu tên catalog từ hàng đầu tiên để sử dụng sau
        if ($this->rowNumber == 1) {
            $this->catalogName = trim($this->parseCatalogName($row[10]));
            return null;
        }

        // Bắt đầu kiểm tra từ hàng thứ 2
        if ($this->rowNumber > 3) {
            $this->checkAndSetProvider($row[14]);
        }

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
            $description = $this->exportDrawings === 'X' ? "Yêu cầu xuất bản vẽ" : "Không xuất bản vẽ";
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
                $donvitinh = !empty($row[7]) ? $row[7] : 'default_value';
                return new Supply([
                    'catalog_id' => $this->catalog_id,
                    'tenvattu' => $row[1],
                    'maso' => $row[2],
                    'donvitinh' => $donvitinh,
                    'soluong' => $row[8],
                    'exportDrawings' => $this->exportDrawings,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        return null;
    }

    private function checkAndSetProvider($providerName){
        $providerName = trim($providerName);
        Log::info("Đang kiểm tra nhà cung cấp: '{$providerName}'");

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
