<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function login(){
        return view('Login.login');
    }

    public function loginPost(Request $request){
        $credentials = $request->only('msnv', 'password');

        if (Auth::attempt($credentials)) {
            // Lưu trạng thái thông báo vào session
            session(['show_popup' => true]);

            // Lấy thông tin người dùng
            $user = Auth::user();

            // Điều hướng đến route 'trangChu'
            return redirect()->route('trangChu');
        }

        // Nếu thông tin đăng nhập không chính xác, trả về với lỗi
        return back()->withErrors([
            'login_error' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }



    public function logout(request $request){
        Auth::logout();
        return redirect()->route('login.get');
    }
}
