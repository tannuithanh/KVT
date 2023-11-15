<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function login(){
        return view('Login.login');
    }

    public function loginPost(request $request){
        $credentials = $request->only('msnv', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'login_error' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }
    public function logout(){
        Auth::logout();
        return redirect()->route('login.get');
    }
}
