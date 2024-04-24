<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'name', 'description', 'nhacungcap','ngayhoanthanh'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function getProviderInfo()
    {
        return ProviderDetail::where('name', $this->nhacungcap)
            ->with('provider')
            ->first();
    }
}
