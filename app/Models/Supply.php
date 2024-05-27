<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_id','order_id', 'tenvattu', 'maso', 'donvitinh', 'soluong', 'note', 'status','don_gia','thanh_tien', 'exportDrawings', 'soluongnhap'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'supply_id');
    }
    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }
    public function viewVatTuChiTiets()
    {
        return $this->hasMany(ViewVatTuChiTiet::class, 'supply_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    // Thay đổi hoặc thêm vào Supply model
    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

}
