<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashBoard extends Controller
{
    public function dashboard(){
        // Kiểm tra trạng thái popup trong session
        $showPopup = session('show_popup', false);
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        // Xóa trạng thái popup để nó chỉ hiển thị một lần
        session()->forget('show_popup');

        // Lấy các đơn hàng cần kiểm tra chất lượng
        $ordersNeedQualityCheck = Order::whereHas('supplies.qualityChecks', function ($query) {
            $query->where('status', 0)
                ->whereNull('soluongdatchatluong');
        })->get(['id', 'sodonhang']); // Chỉ lấy các đơn hàng cần kiểm tra

        return view('Dashboard.index', compact('showPopup', 'user', 'ordersNeedQualityCheck'));
    }

}
