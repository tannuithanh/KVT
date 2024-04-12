<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_id', 'sodonhang', 'nhacungcap', 'chiphi', 'noidung', 'ghichu'
    ];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }


    public function supplies()
    {
        return $this->hasMany(Supply::class, 'order_id');
    }

}
