<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewVatTuChiTiet extends Model
{
    use HasFactory;

    protected $table = 'view_vattuchitiet'; // Đảm bảo rằng Laravel sử dụng đúng tên bảng

    protected $fillable = [
        'supply_id',
        'soluongnhapkho',
        'soluongdatchatluong',
        'status',
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class, 'supply_id');
    }
}
