<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSupply extends Model
{
    use HasFactory;

    protected $table = 'order_supply';

    protected $fillable = [
        'order_id', 'supply_id', 'soluong'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}
