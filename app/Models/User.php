<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['msnv', 'name', 'email', 'password', 'is_admin', 'department_id', 'position_id', 'function_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function appFunction()
    {
        return $this->belongsTo(AppFunction::class, 'function_id');
    }

}
