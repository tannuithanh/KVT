<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProviderDetail;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Supply;

class SuppliesImport implements ToModel
{
    private $rowNumber = 0;
    private $sodonhang;
    private $nhacungcap;
    private $chiphi;
    private $project_id;
    private $errors = [];

    public function __construct($sodonhang, $nhacungcap, $chiphi, $project_id)
    {
        $this->project_id = $project_id;
        $this->sodonhang = $sodonhang;
        $this->nhacungcap = $nhacungcap;
        $this->chiphi = $chiphi;
        $providerExists = ProviderDetail::where('name', $this->nhacungcap)->exists();
            if (!$providerExists) {
                throw new \Exception("Nhà cung cấp không tồn tại.");
            }
    }

    public function model(array $row){

        $this->rowNumber++;
        if ($this->rowNumber < 4) {
            return null;
        }
        // dd($row);
        if (empty($row[1])) {
            return null;
        }

        // Kiểm tra xem mã số vật tư đã tồn tại chưa
        $maSoExists = Supply::join('orders', 'supplies.order_id', '=', 'orders.id')
                            ->where('orders.project_id', $this->project_id)
                            ->where('supplies.maso', $row[2])
                            ->exists();

        if ($maSoExists) {
            $this->errors[] = "Trùng mã số vật tư";
            return null;
        }

        // Kiểm tra và thêm mới đơn hàng vào bảng `orders`
        $order = Order::firstOrCreate(
            ['project_id' => $this->project_id, 'sodonhang' => $this->sodonhang, 'nhacungcap' => $this->nhacungcap, 'chiphi' => $this->chiphi],
        );

        // Thêm thông tin vào bảng `supplies` với `order_id`
        return new Supply([
            'order_id' => $order->id,
            'tenvattu' => $row[1], // Cột B
            'maso' => $row[2], // Cột C
            'donvitinh' => $row[7], // Cột H
            'soluong' => $row[8], // Cột I
            'note' => $row[10], // Cột K
        ]);
    }


    public function getErrors() {
        return $this->errors;
    }
}
