<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    // Khai báo các cột trong bảng supplies
    protected $fillable = [
        'project_id', 'sodonhang', 'nhacungcap', 'chiphi',
        'noidungphancum', 'stt', 'tenvattu', 'maso',
        'donvitinh', 'soluong', 'ngaynhan', 'note', 'status','barcode'
    ];

    // Quan hệ với bảng projects
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
