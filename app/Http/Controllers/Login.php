<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Catalog;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function login(){
        return view('Login.login');
    }

    public function loginPost(Request $request) {
        $credentials = $request->only('msnv', 'password');

        if (Auth::attempt($credentials)) {
            // Lưu trạng thái thông báo vào session
            session(['show_popup' => true]);

            // Lấy thông tin người dùng
            $user = Auth::user();

            // Kiểm tra function_id của người dùng và điều hướng đến route tương ứng
            switch ($user->function_id) {
                case 1:
                    return redirect()->route('listNhapKho', ['module' => 'Nhập kho']);
                case 2:
                    return redirect()->route('listExportWarehouse', ['module' => 'Xuất kho']);
                case 7:
                    return redirect()->route('checkQuality');
                default:
                    return redirect()->route('dashBoard');
            }
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
