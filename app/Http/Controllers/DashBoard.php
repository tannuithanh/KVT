<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Catalog;
use App\Models\Project;
use App\Models\Supply;
use Carbon\Carbon;
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
        session()->forget('show_popup');

        // Lấy các đơn hàng cần kiểm tra chất lượng
        $ordersNeedQualityCheck = Order::whereHas('supplies.qualityChecks', function ($query) {
            $query->where('status', 0)
                  ->whereNull('soluongdatchatluong');
        })->get(['id', 'sodonhang']);

        // Lấy các catalog có thể tạo đơn hàng
        $catalogsCanCreateOrders = Catalog::with(['supplies', 'project.segment.brand'])
            ->doesntHave('orders')
            ->get();

        // Thêm thông tin nhà cung cấp vào từng catalog
        $catalogsCanCreateOrders->each(function ($catalog) {
            $catalog->provider_info = $catalog->getProviderInfo();
        });

        // Đếm tổng số danh mục vật tư
        $totalCatalogs = Catalog::count();

        // Tính tổng số vật tư của tất cả các danh mục
        $totalSupplies = Catalog::with('supplies')->get()->sum(function ($catalog) {
            return $catalog->supplies->sum('soluong');
        });

        // Tính tổng số vật tư đã nhận đủ
        $receivedSupplies = Supply::whereHas('transactions', function ($query) {
            $query->where('loaigiaodich', 'Đã nhận');
        })->get()->filter(function ($supply) {
            $totalReceived = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            return $totalReceived >= $supply->soluong;
        })->sum('soluong');

        // Tính số vật tư chưa nhận
        $pendingSupplies = $totalSupplies - $receivedSupplies;

        // Tính số danh mục vật tư hoàn thành
        $completedCatalogs = Catalog::with('supplies.transactions')->get()->filter(function ($catalog) {
            return $catalog->supplies->every(function ($supply) {
                $totalReceived = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                return $totalReceived >= $supply->soluong;
            });
        })->count();

        // Tính số danh mục vật tư chưa hoàn thành
        $countCatalogPending = $totalCatalogs - $completedCatalogs;

        // Lấy danh sách các dự án
        $projects = Project::all();

        // Đếm tổng số đơn hàng
        $totalOrders = Order::count();

        // Đếm số đơn hàng đã hoàn thành
        $completedOrders = Order::whereHas('supplies.transactions', function ($query) {
            $query->where('loaigiaodich', 'Đã nhận');
        })->get()->filter(function ($order) {
            $totalSupplies = $order->supplies->sum('pivot.soluong');
            $totalReceived = $order->supplies->sum(function ($supply) {
                return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            });
            return $totalSupplies === $totalReceived;
        })->count();

        // Đếm số đơn hàng chưa hoàn thành
        $pendingOrders = $totalOrders - $completedOrders;

        // Kiểm tra department_id của người dùng và điều hướng đến view tương ứng
        $view = ($user->department_id == 2) ? 'Dashboard.trangChuKeHoach' : 'Dashboard.trangChuTongQuat';
        return view($view, compact(
            'showPopup', 'user', 'ordersNeedQualityCheck', 'catalogsCanCreateOrders',
            'totalCatalogs', 'totalSupplies', 'receivedSupplies', 'pendingSupplies',
            'completedCatalogs', 'projects', 'totalOrders', 'completedOrders', 'pendingOrders','countCatalogPending'
        ));
    }


    public function orderByDashboard(Request $request) {
        $projectId = $request->input('project_id');
        if (!$projectId || !is_numeric($projectId)) {
            return response()->json(['error' => 'Dự án không hợp lệ'], 400);
        }
        $project = Project::with(['catalogs.orders' => function($query) {
            $query->with(['supplies' => function($q) {
                $q->select('order_supply.order_id', 'order_supply.supply_id', 'order_supply.soluong');
            }, 'supplies.transactions']);
        }])->find($projectId);
        if (!$project) {
            return response()->json(['error' => 'Dự án không tồn tại'], 404);
        }
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
        return response()->json([
            'project' => $project,
            'catalogs' => $catalogs
        ]);
    }

    public function apiOrder(Request $request) {
        $timeFrame = $request->get('timeframe', 'week');

        switch ($timeFrame) {
            case 'month':
                $labels = [
                    'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4',
                    'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8',
                    'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                ];
                $completedOrders = [];
                $pendingOrders = [];

                for ($month = 1; $month <= 12; $month++) {
                    $start = Carbon::create(null, $month, 1)->startOfMonth();
                    $end = Carbon::create(null, $month, 1)->endOfMonth();

                    $orders = Order::whereBetween('created_at', [$start, $end])->get();
                    $completedOrdersCount = $orders->filter(function($order) {
                        $totalSupplies = $order->supplies->sum('pivot.soluong');
                        $totalReceived = $order->supplies->sum(function ($supply) {
                            return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                        });
                        return $totalSupplies === $totalReceived;
                    })->count();

                    $pendingOrdersCount = $orders->count() - $completedOrdersCount;

                    $completedOrders[] = $completedOrdersCount;
                    $pendingOrders[] = $pendingOrdersCount;
                }
                break;

            case 'year':
                $labels = [Carbon::now()->year];
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();

                $orders = Order::whereBetween('created_at', [$start, $end])->get();
                $completedOrdersCount = $orders->filter(function($order) {
                    $totalSupplies = $order->supplies->sum('pivot.soluong');
                    $totalReceived = $order->supplies->sum(function ($supply) {
                        return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                    });
                    return $totalSupplies === $totalReceived;
                })->count();

                $pendingOrdersCount = $orders->count() - $completedOrdersCount;

                $completedOrders = [$completedOrdersCount];
                $pendingOrders = [$pendingOrdersCount];
                break;

            case 'week':
            default:
                $labels = [Carbon::now()->startOfWeek()->toDateString() . ' - ' . Carbon::now()->endOfWeek()->toDateString()];
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();

                $orders = Order::whereBetween('created_at', [$start, $end])->get();
                $completedOrdersCount = $orders->filter(function($order) {
                    $totalSupplies = $order->supplies->sum('pivot.soluong');
                    $totalReceived = $order->supplies->sum(function ($supply) {
                        return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                    });
                    return $totalSupplies === $totalReceived;
                })->count();

                $pendingOrdersCount = $orders->count() - $completedOrdersCount;

                $completedOrders = [$completedOrdersCount];
                $pendingOrders = [$pendingOrdersCount];
                break;
        }

        return response()->json([
            'labels' => $labels,
            'completedOrders' => $completedOrders,
            'pendingOrders' => $pendingOrders,
        ]);
    }



}
