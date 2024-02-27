<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'sodonhang', 'nhacungcap', 'chiphi', 'noidung','ghichu'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class, 'order_id');
    }
}
