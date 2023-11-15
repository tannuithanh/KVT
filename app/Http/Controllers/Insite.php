<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Insite extends Controller
{
    public function listWarehouse(){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        return view('Warehouse Management.Inside.Warehouse',compact('user'));
    }
}
