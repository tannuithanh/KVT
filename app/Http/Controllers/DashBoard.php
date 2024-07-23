<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Catalog;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashBoard extends Controller
{
    public function trangChu(){
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

    public function dashBoard() {
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

        // Lấy các catalog có thể tạo đơn hàng
        $catalogsCanCreateOrders = Catalog::with(['supplies', 'project.segment.brand'])
            ->doesntHave('orders')
            ->get();

        // Thêm thông tin nhà cung cấp vào từng catalog
        $catalogsCanCreateOrders->each(function ($catalog) {
            $catalog->provider_info = $catalog->getProviderInfo();
        });

        // Lấy danh sách các dự án
        $projects = Project::all();

        // Kiểm tra department_id của người dùng và điều hướng đến view tương ứng
        if ($user->department_id == 2) {
            return view('Dashboard.trangChuKeHoach', compact('showPopup', 'user', 'ordersNeedQualityCheck', 'catalogsCanCreateOrders', 'projects'));
        } else {
            return view('Dashboard.trangChuTongQuat', compact('showPopup', 'user', 'ordersNeedQualityCheck', 'catalogsCanCreateOrders', 'projects'));
        }
    }


    public function orderByDashboard(Request $request) {
        // Kiểm tra xem có id dự án không
        $projectId = $request->input('project_id');

        // Kiểm tra tính hợp lệ của project_id
        if (!$projectId || !is_numeric($projectId)) {
            return response()->json(['error' => 'Dự án không hợp lệ'], 400);
        }

        // Lấy dự án dựa trên id
        $project = Project::with(['catalogs.orders' => function($query) {
            $query->with(['supplies' => function($q) {
                $q->select('order_supply.order_id', 'order_supply.supply_id', 'order_supply.soluong');
            }, 'supplies.transactions']);
        }])->find($projectId);

        // Kiểm tra nếu không tìm thấy dự án
        if (!$project) {
            return response()->json(['error' => 'Dự án không tồn tại'], 404);
        }

        // Lấy danh sách catalog và các đơn hàng của từng catalog
        $catalogs = $project->catalogs->map(function ($catalog) {
            return [
                'catalog' => $catalog,
                'orders' => $catalog->orders->map(function ($order) {
                    $totalSupplies = $order->supplies->sum('pivot.soluong');
                    $totalReceived = $order->supplies->sum(function ($supply) {
                        return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                    });
                    $isEqual = ($totalSupplies === $totalReceived);

                    return [
                        'id' => $order->id,
                        'sodonhang' => $order->sodonhang,
                        'ngaytaophieu' => $order->ngaytaophieu,
                        'total_quantity' => $totalSupplies,
                        'isEqual' => $isEqual,
                    ];
                }),
            ];
        });
        // dd($catalogs->toarray());
        return response()->json([
            'project' => $project,
            'catalogs' => $catalogs
        ]);
    }




}
