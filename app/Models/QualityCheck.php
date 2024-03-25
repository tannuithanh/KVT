<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityCheck extends Model
{
    use HasFactory;

    protected $fillable = ['supply_id', 'soluongnhapkho', 'soluongdatchatluong', 'status', 'note','ngaykiemtra'];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}
