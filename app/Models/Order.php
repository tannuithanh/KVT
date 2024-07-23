<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_id', 'sodonhang', 'nhacungcap', 'expense_id', 'noidung', 'ghichu', 'ngayhoanthanh', 'excel_file', 'ngaytaophieu','status','ketoan','nguoilap','requestOrder'
    ];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }


    public function supplies()
    {
        return $this->belongsToMany(Supply::class, 'order_supply')
                    ->withPivot('soluong')
                    ->withTimestamps();
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
