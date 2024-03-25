<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'tenvattu', 'maso', 'donvitinh',
        'soluong', 'note', 'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'supply_id');
    }
    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }
}
