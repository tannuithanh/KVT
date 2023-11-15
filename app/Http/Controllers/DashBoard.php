<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashBoard extends Controller
{
    public function dashboard(){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        return view('Dashboard.index',compact('user'));
    }
}
