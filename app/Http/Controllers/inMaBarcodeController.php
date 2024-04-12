<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;

class inMaBarcodeController extends Controller
{
    public function inMaBarcode(){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $supplies = Supply::all('maso');
        return view('Warehouse Management.Inside.inMaBarcode', compact('user', 'supplies'));
    }

    public function timKiemMaBarcode(Request $request) {
        $maso = $request->maso;
        // Tìm kiếm vật tư dựa trên mã số
        $supplies = Supply::with('order')->where('maso', $maso)->get();

        // Tạo mảng dữ liệu để trả về, bao gồm HTML của barcode cho mỗi vật tư
        $data = $supplies->map(function($supply) {
            // Tạo HTML cho barcode
            $barcodeHtml = DNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33);
            return [
                'donhang' => $supply->order ? $supply->order->sodonhang : 'Không có đơn hàng', // Kiểm tra và lấy số đơn hàng
                'tenvattu' => $supply->tenvattu,
                'maso' => $supply->maso,
                'barcode' => $barcodeHtml, // Giá trị này giờ là HTML của barcode
                'soluongin' => 'Số Lượng In' // Thay đổi này bằng cách lấy số lượng in thực tế
            ];
        });

        return response()->json(['data' => $data]);
    }
}
