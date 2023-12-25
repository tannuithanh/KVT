<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Khai báo các cột trong bảng transactions
    protected $fillable = [
        'supply_id', 'soluong', 'loaigiaodich', 'ngaygiaodich', 'ghichu'
    ];

    // Quan hệ với bảng supplies
    public function supply()
    {
        return $this->belongsTo(Supply::class, 'supply_id');
    }

}
