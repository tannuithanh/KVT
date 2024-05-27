<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supply;
use App\Models\Project;
use App\Models\Catalog;
use App\Models\Provider;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Brand;
use App\Models\Segment;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\imports\SuppliesImport;
use App\Models\QualityCheck;
use App\Models\Expense;
use App\Models\ViewVatTuChiTiet;
use Carbon\Carbon;
use Milon\Barcode\DNS1D as BarcodeDNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Insite extends Controller
{
// QUẢN LÝ KẾ HOẠCH
    public function listWarehouse($idProject, Request $request) {
        // Tải dự án cùng với Catalog và các Orders liên quan thông qua Catalog
        $project = Project::with([
            'catalogs.orders.supplies.transactions',
            'catalogs.orders.catalog'
        ])->find($idProject);
        if (!$project) {
            abort(404, 'Dự án không tìm thấy.');
        }
        $totalOrdersDanhan = 0;
        $totalOrdersChuanhan = 0;
        $totalOrdersDaxuat = 0;
        $orders = $project->catalogs->flatMap(function ($catalog) {
            return $catalog->orders;
        })->map(function ($order) use (&$totalOrdersDanhan, &$totalOrdersChuanhan, &$totalOrdersDaxuat) {
            $order->total_supplies = $order->supplies->sum('soluong');
            $order->total_danhan = $order->supplies->sum(function($supply) {
                return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            });
            $order->total_daxuat = $order->supplies->sum(function($supply) {
                return $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
            });
            $order->total_chuanhan = $order->supplies->sum(function($supply) {
                $totalReceived = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                return $supply->soluong - $totalReceived;
            });

            $totalOrdersDanhan += $order->total_danhan;
            $totalOrdersChuanhan += $order->total_chuanhan;
            $totalOrdersDaxuat += $order->total_daxuat;
            return $order;
        });

        $brandName = optional(optional($project->segment)->brand)->name;
        $segmentId = $project->segment->id ?? null;
        $segmentName = $project->segment->name ?? null;
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');

        $providers = Provider::with('details')->get();
        $totalSuppliesForProject = $orders->sum('total_supplies');
        // dd($orders->toarray());
        return view('Warehouse Management.Inside.quanlykehoach', compact('totalOrdersDanhan', 'totalOrdersChuanhan', 'totalOrdersDaxuat','orders', 'user', 'providers', 'module', 'segmentId', 'brandName', 'segmentName', 'project', 'totalSuppliesForProject'));
    }

    public function importSupplies(Request $request){
        $projectId = $request->input('project_id');
        $file = $request->file('file');
        $allowedExtensions = ['xlsx', 'xls'];

        if (!in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
            return back()->with('error', 'File không phải là file Excel.');
        }

        $import = new SuppliesImport($projectId);
        Excel::import($import, $file);

        // Kiểm tra và hiển thị lỗi sau khi nhập
        if (!empty($import->getErrors())) {
            return back()->with('errors', $import->getErrors());
        }

        return back()->with('success', 'Dữ liệu đã được nhập thành công!');
    }

    public function addQuantity(Request $request) {
        try {
            $supplyId = $request->input('supplyId');
            $quantity = $request->input('soluong');

            $supply = Supply::find($supplyId);
            if (!$supply) {
                // Nếu không tìm thấy vật tư, gửi thông báo lỗi
                return redirect()->back()->with('error', 'Vật tư không tồn tại.');
            }

            // Cập nhật số lượng
            $supply->soluong += $quantity;
            $supply->save();

            // Gửi thông báo thành công
            return redirect()->back()->with('success', 'Đã thêm số lượng thành công.');
        } catch (\Exception $e) {
            // Trường hợp xảy ra lỗi
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi thêm số lượng.');
        }
    }

    public function deleteDonHang(Request $request) {
        $orderId = $request->input('orderId');
        $order = Order::with(['supplies.transactions', 'supplies.qualityChecks', 'supplies.viewVatTuChiTiets'])->find($orderId);

        if ($order) {
            if($order->excel_file != null){
                $filePath = public_path($order->excel_file);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            foreach ($order->supplies as $supply) {
                foreach ($supply->transactions as $transaction) {
                    $transaction->delete();
                }

                foreach ($supply->qualityChecks as $qualityCheck) {
                    $qualityCheck->delete();
                }

                foreach ($supply->viewVatTuChiTiets as $viewDetail) {
                    $viewDetail->delete();
                }

                $supply->update(['status' => 0]);
            }

            $order->delete();

            return response()->json(['success' => 'Đơn hàng và tất cả bản ghi liên quan cùng file đính kèm đã được xử lý thành công. Trạng thái vật tư đã được cập nhật.']);
        } else {
            return response()->json(['error' => 'Không tìm thấy đơn hàng.'], 404);
        }
    }

    public function suavattu(Request $request) {
        dd($request->toarray());
        // Validation và lấy dữ liệu từ request
        $validatedData = $request->validate([
            'OrderEdit' => 'required|integer',
            'sodonhang-edit' => 'required|string',
            'nhacungcap-edit' => 'required|string',
            'noidungphancum-edit' => 'nullable|string',
            'chiphi-edit' => 'required|string',
            'note-edit' => 'nullable|string',
        ]);
        // dd($validatedData);
        // Tìm và cập nhật đơn hàng
        $Order = Order::find($validatedData['OrderEdit']);
        // dd($Order->toarray());
        if ($Order) {
            $Order->sodonhang = $validatedData['sodonhang-edit'];
            $Order->nhacungcap = $validatedData['nhacungcap-edit'];
            $Order->noidung = $validatedData['noidungphancum-edit'];
            $Order->chiphi = $validatedData['chiphi-edit'];
            $Order->ghichu = $validatedData['note-edit'];
            $Order->save();

            return back()->with('success', 'Vật tư đã được cập nhật thành công.');
        } else {
            return back()->with('error', 'Không tìm thấy vật tư.');
        }
    }

    public function duLieuVatTuChiTiet(Request $request) {
        $orderId = $request['id'];
        $order = Order::find($orderId);
        // Đảm bảo rằng cả transactions và viewVatTuChiTiets được tải cùng với Supply
        $supplies = Supply::with(['transactions', 'viewVatTuChiTiets'])->where('order_id', $orderId)->get();
        // dd($orderId);
        $totalSupplies = 0;
        $totalDanhan = 0;
        $totalChuanhan = 0;
        $totalDaxuat = 0;

        $suppliesDetail = $supplies->map(function ($supply) use (&$totalSupplies, &$totalDanhan, &$totalChuanhan, &$totalDaxuat) {
            $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
            $chuanhan = $supply->soluong - $danhan;
            $totalSupplies += $supply->soluong;
            $totalDanhan += $danhan;
            $totalChuanhan += $chuanhan;
            $totalDaxuat += $daxuat;

            // Tạo mã vạch
            $qrCode = Qrcode::size(100)->encoding('UTF-8')->generate($supply->maso);

            // Thêm thông tin từ viewVatTuChiTiets vào mỗi supply
            $viewVatTuChiTietData = $supply->viewVatTuChiTiets->map(function($vattuchitiet) {
                return [
                    'soluongnhapkho' => $vattuchitiet->soluongnhapkho,
                    'soluongdatchatluong' => $vattuchitiet->soluongdatchatluong,
                    'status' => $vattuchitiet->status,
                ];
            });

            return [
                'id' => $supply->id,
                'tenvattu' => $supply->tenvattu,
                'maso' => $supply->maso,
                'donvitinh' => $supply->donvitinh,
                'soluong' => $supply->soluong,
                'ghichu' => $supply->note,
                'danhan' => $danhan,
                'chuanhan' => $chuanhan,
                'daxuat' => $daxuat,
                'qrCode' => $qrCode->toHtml(),
                'viewVatTuChiTiet' => $viewVatTuChiTietData, // Thêm vào dữ liệu này
            ];
        });
        return response()->json([
            'orderName' => $order->sodonhang ?? '',
            'supplies' => $suppliesDetail,
            'totalSupplies' => $totalSupplies,
            'totalDanhan' => $totalDanhan,
            'totalChuanhan' => $totalChuanhan,
            'totalDaxuat' => $totalDaxuat,
            'orderId' => $orderId,
        ]);
    }


    public function xoavattuchitiet(Request $request){
        try {
            // Bắt đầu một transaction để đảm bảo tính nhất quán dữ liệu
            DB::beginTransaction();

            // Lấy ID của vật tư cần xóa từ request
            $supplyId = $request->id;

            // Kiểm tra và xóa các QualityChecks liên quan trước
            $qualityChecks = QualityCheck::where('supply_id', $supplyId)->get();
            foreach ($qualityChecks as $qualityCheck) {
                $qualityCheck->delete();
            }

            // Tiếp theo, kiểm tra và xóa các transactions liên quan
            $transactions = Transaction::where('supply_id', $supplyId)->get();
            foreach ($transactions as $transaction) {
                $transaction->delete();
            }

            // Xóa vật tư sau khi đã xóa các transactions và QualityChecks liên quan
            $supply = Supply::find($supplyId);
            if ($supply) {
                $supply->delete();
                DB::commit(); // Commit transaction nếu không có lỗi
                return response()->json(['success' => true, 'message' => 'Vật tư và các giao dịch, kiểm định chất lượng liên quan đã được xóa thành công.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy vật tư.']);
            }
        } catch (Exception $e) {
            DB::rollBack(); // Rollback transaction nếu có lỗi xảy ra
            return response()->json(['success' => false, 'message' => 'Lỗi trong quá trình xóa vật tư: ' . $e->getMessage()]);
        }
    }

    public function themvattuchitiet(Request $request){
        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
        }

        $projectId = $order->project_id;
        // Kiểm tra xem có vật tư nào trong cùng một dự án với maso trùng lặp không
        $existingSupply = Supply::whereHas('order', function($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->where('maso', $request->maso)->first();

        if ($existingSupply) {
            // Nếu tìm thấy vật tư với maso trùng lặp, trả về thông báo lỗi
            return response()->json(['success' => false, 'message' => 'Mã số vật tư đã tồn tại trong dự án']);
        }

        if ($request->has(['tenvattu', 'maso', 'donvitinh', 'soluong', 'order_id'])) {
            // Thêm vật tư mới vào cơ sở dữ liệu
            $supply = Supply::create([
                'tenvattu' => $request->tenvattu,
                'maso' => $request->maso,
                'donvitinh' => $request->donvitinh,
                'soluong' => $request->soluong,
                'order_id' => $request->order_id,
                'note' => $request->ghichu,
            ]);

            if ($supply) {
                // Lấy lại danh sách vật tư sau khi thêm thành công, bao gồm vật tư mới
                $supplies = Supply::with(['transactions'])->where('order_id', $request->order_id)->get();

                // Xử lý từng vật tư để tính toán danhan, chuanhan, daxuat và tạo mã vạch
                $suppliesDetail = $supplies->map(function ($supply) {
                    $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                    $chuanhan = $supply->transactions->where('loaigiaodich', 'Chưa nhận')->sum('soluong');
                    $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');

                    // Tạo mã vạch cho mã số vật tư
                    $barcodeHtml = BarcodeDNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33);

                    return [
                        'id' => $supply->id,
                        'tenvattu' => $supply->tenvattu,
                        'maso' => $supply->maso,
                        'donvitinh' => $supply->donvitinh,
                        'soluong' => $supply->soluong,
                        'ghichu' => $supply->note,
                        'danhan' => $danhan,
                        'chuanhan' => $chuanhan,
                        'daxuat' => $daxuat,
                        'barcodeHtml' => $barcodeHtml,
                    ];
                });

                // Trả về danh sách vật tư mới cùng thông tin khác trong phản hồi JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Vật tư đã được thêm thành công',
                    'supplies' => $suppliesDetail,
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Không thể thêm vật tư']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        }
    }

    public function suavattuchitiet(Request $request){
        $id = $request->id;
        $newQuantity = $request->soluong;
        $ghichu = $request->ghichu;

        $supply = Supply::with(['transactions'])->where('id', $id)->first();

        if (!$supply) {
            return response()->json(['error' => 'Vật tư không tồn tại.'], 404);
        }
        $hasTransactions = $supply->transactions()->count() > 0;
        if ($hasTransactions) {
            if ($newQuantity < $supply->soluong) {
                return response()->json(['error' => 'Vật tư này đã giao dịch, bạn không thể giảm số lượng.'], 400);
            }
        }
        $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
        $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
        $chuanhan = $request->soluong - $danhan;
        $barcodeHtml = BarcodeDNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33);
        $supply->update([
            'tenvattu' => $request->tenvattu,
            'maso' => $request->maso,
            'donvitinh' => $request->donvitinh,
            'soluong' => $newQuantity,
            'note' => $ghichu,
        ]);


        return response()->json([
            'success' => 'Cập nhật vật tư thành công.',
            'barcodeHtml' => $barcodeHtml,
            'id' => $supply->id,
            'danhan' => $danhan,
            'daxuat' => $daxuat,
            'chuanhan' => $chuanhan,
            'ghichu' => $supply->ghichu,
        ]);
    }

    public function lichsuvattu(Request $request){
        $supplyId = $request->id;
        $transactions = Transaction::with('supply.order')->where('supply_id', $supplyId)->get();
        return response()->json($transactions);
    }

    public function themdonhangthucong(Request $request){
        $projectId = $request->input('project_id');
        $nhacungcap = $request->input('nhacungcap');
        $chiphi = $request->input('chiphi');
        $noidung = $request->input('noidungphancum');
        $ghichu = $request->input('note');

        $thangNam = Carbon::now()->format('my');

        // Kiểm tra số thứ tự đơn hàng cao nhất hiện tại
        $orderNumberPrefix = "RD.%.{$thangNam}.{$nhacungcap}.{$chiphi}";
        $highestOrder = Order::where('sodonhang', 'LIKE', $orderNumberPrefix)
                            ->orderBy('sodonhang', 'desc')
                            ->first();

        $nextStt = 1; // Bắt đầu từ 01
        if ($highestOrder) {
            // Tách chuỗi để lấy phần số thứ tự và tăng lên 1
            $parts = explode('.', $highestOrder->sodonhang);
            $nextStt = (int)$parts[1] + 1;
        }
        $sttFormatted = str_pad($nextStt, 2, '0', STR_PAD_LEFT);
        $sodonhang = "RD.{$sttFormatted}.{$thangNam}.{$nhacungcap}.{$chiphi}";
        $newOrder = new Order([
            'project_id' => $projectId,
            'sodonhang' => $sodonhang,
            'nhacungcap' => $nhacungcap,
            'chiphi' => $chiphi,
            'noidung' => $noidung,
            'ghichu' => $ghichu,
        ]);
        $newOrder->save();
        return back()->with('success', 'Đơn hàng đã được thêm thành công.');
    }

    public function soDonHangvaNCC(Request $request){
        $projectId = $request->input('project_id');
        $orders = Order::where('project_id', $projectId)
                        ->get(['sodonhang', 'nhacungcap'])
                        ->unique('sodonhang')
                        ->values();
        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function timkiemvattuchitiet(Request $request) {
        // Khởi tạo truy vấn lấy thông tin các vật tư theo order_id
        $query = Supply::with(['transactions', 'viewVatTuChiTiets'])->where('order_id', $request->orderId);

        // Lọc theo tên vật tư nếu có
        if ($request->filled('tenvattu')) {
            $query->where('tenvattu', 'like', '%' . $request->tenvattu . '%');
        }

        // Lọc theo mã số nếu có
        if ($request->filled('maso')) {
            $query->where('maso', $request->maso);
        }

        // Lọc theo đơn vị tính nếu có
        if ($request->filled('donvitinh')) {
            $query->where('donvitinh', $request->donvitinh);
        }

        // Thực thi truy vấn và lấy danh sách các vật tư
        $supplies = $query->get();

        // Khởi tạo các biến tổng số liệu
        $totalSupplies = $totalDanhan = $totalChuanhan = $totalDaxuat = 0;

        // Tạo danh sách chi tiết các vật tư
        $suppliesDetail = $supplies->map(function ($supply) use (&$totalSupplies, &$totalDanhan, &$totalChuanhan, &$totalDaxuat) {
            // Tính tổng số lượng đã nhận, đã xuất và chưa nhận
            $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
            $chuanhan = $supply->soluong - $danhan;

            // Cập nhật các biến tổng số liệu
            $totalSupplies += $supply->soluong;
            $totalDanhan += $danhan;
            $totalChuanhan += $chuanhan;
            $totalDaxuat += $daxuat;

            // Tạo mã vạch cho mỗi vật tư
            $barcodeHtml = BarcodeDNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33);

            // Lấy thông tin chi tiết từ viewVatTuChiTiets
            $viewVatTuChiTietData = $supply->viewVatTuChiTiets->map(function($vattuchitiet) {
                return [
                    'soluongnhapkho' => $vattuchitiet->soluongnhapkho,
                    'soluongdatchatluong' => $vattuchitiet->soluongdatchatluong,
                    'status' => $vattuchitiet->status,
                ];
            });

            return [
                'supply' => $supply,
                'danhan' => $danhan,
                'daxuat' => $daxuat,
                'chuanhan' => $chuanhan,
                'barcodeHtml' => $barcodeHtml,
                'viewVatTuChiTietData' => $viewVatTuChiTietData,
            ];
        });

        // Trả về phản hồi JSON
        return response()->json([
            'suppliesDetail' => $suppliesDetail,
            'totalSupplies' => $totalSupplies,
            'totalDanhan' => $totalDanhan,
            'totalChuanhan' => $totalChuanhan,
            'totalDaxuat' => $totalDaxuat,
        ]);
    }


    public function vattutrongdanhmuc(Request $request){
        $catalogId = $request->input('catalog_id');  // Lấy ID của catalog từ request
        $supplies = Supply::where('catalog_id', $catalogId)->with(['transactions', 'qualityChecks', 'viewVatTuChiTiets'])->get();

        // Tính toán các thống kê nếu cần
        $stats = [
            'totalSupplies' => $supplies->count(),
            // Thêm các thống kê khác bạn cần
        ];

        return response()->json([
            'supplies' => $supplies,
            'stats' => $stats
        ]);
    }

    public function xoa_Danhmuc(Request $request){
        $catalogId = $request->input('catalog_id');

        try {
            DB::transaction(function () use ($catalogId) {
                $catalog = Catalog::findOrFail($catalogId);

                // Xóa các đơn hàng và vật tư liên quan đến từng đơn hàng
                foreach ($catalog->orders as $order) {
                    // Có thể xóa các bản ghi liên quan trong các bảng khác nếu cần
                    $order->supplies->each(function ($supply) {
                        $supply->transactions()->delete(); // Xóa các giao dịch liên quan
                        $supply->qualityChecks()->delete(); // Xóa các kiểm định chất lượng
                        $supply->delete(); // Sau đó mới xóa vật tư
                    });
                    $order->delete(); // Xóa đơn hàng
                }

                // Xóa các vật tư không thuộc đơn hàng nào
                $catalog->supplies()->delete();

                // Cuối cùng xóa chính danh mục đó
                $catalog->delete();
            });

            return response()->json(['success' => true, 'message' => 'Danh mục và tất cả dữ liệu liên quan đã được xóa thành công.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi khi xóa danh mục: ' . $e->getMessage()], 500);
        }
    }

    public function thayTheVatTu(Request $request){
        $note = $request->note;
        $supplies = $request->supplies; // Mảng các supplies từ request

        foreach ($supplies as $supplyData) {
            $supply = Supply::find($supplyData['supply_id']);
            if ($supply) {
                $supply->tenvattu = $supplyData['tenvattu'];
                $supply->maso = $supplyData['maso'];
                $supply->donvitinh = $supplyData['donvitinh'];
                $supply->soluong = $supplyData['soluong'];
                $supply->note = $note; // Cập nhật note cho mỗi supply
                $supply->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thành công.'
        ]);
    }

    public function timKiem_VatTu(Request $request){
        $id = $request->id;  // Lấy ID từ yêu cầu

        // Tìm kiếm vật tư theo ID
        $supply = Supply::find($id);

        // Kiểm tra nếu không tìm thấy vật tư
        if (!$supply) {
            return response()->json([
                'status' => 'not found',
                'message' => 'Không tìm thấy vật tư với ID này.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'supply' => $supply
        ]);
    }
// NHẬP KHO
    public function listNhapKho(Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');
        $orders = Order::with(['catalog', 'supplies', 'expense'])->get();
        return view('Warehouse Management.Inside.nhapkho', compact('user', 'module', 'orders'));
    }

    public function layThongTinDonHang(Request $request) {
        // dd($request->toarray());
        $order = null;

        if ($request->has('id')) {
            $orderId = $request['id'];
            $order = Order::with(['supplies.transactions', 'supplies.viewVatTuChiTiets'])->find($orderId);
        } elseif ($request->has('sodonhang')) {
            $sodonhang = $request['sodonhang'];
            $order = Order::with(['supplies.transactions', 'supplies.viewVatTuChiTiets'])->where('sodonhang', $sodonhang)->first();
        }

        if ($order) {
            $totalSupplies = 0;
            $totalDanhan = 0;
            $totalChuanhan = 0;
            $totalDaxuat = 0;

            $suppliesDetail = $order->supplies->map(function ($supply) use (&$totalSupplies, &$totalDanhan, &$totalChuanhan, &$totalDaxuat) {
                $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
                $chuanhan = $supply->soluong - $danhan;
                $totalSupplies += $supply->soluong;
                $totalDanhan += $danhan;
                $totalChuanhan += $chuanhan;
                $totalDaxuat += $daxuat;

                // Tạo mã vạch
                $qrCode = Qrcode::size(100)->encoding('UTF-8')->generate($supply->maso);
                // Thêm thông tin từ viewVatTuChiTiets vào mỗi supply
                $viewVatTuChiTietData = $supply->viewVatTuChiTiets->map(function($vattuchitiet) {
                    return [
                        'soluongnhapkho' => $vattuchitiet->soluongnhapkho,
                        'soluongdatchatluong' => $vattuchitiet->soluongdatchatluong,
                        'status' => $vattuchitiet->status,
                    ];
                });

                return [
                    'supply' => $supply,
                    'danhan' => $danhan,
                    'daxuat' => $daxuat,
                    'chuanhan' => $chuanhan,
                    'qrCode' => $qrCode->toHtml(),
                    'viewVatTuChiTietData' => $viewVatTuChiTietData,
                ];
            });

            return response()->json([
                'order' => $order,
                'suppliesDetail' => $suppliesDetail,
                'totalSupplies' => $totalSupplies,
                'totalDanhan' => $totalDanhan,
                'totalChuanhan' => $totalChuanhan,
                'totalDaxuat' => $totalDaxuat,
            ]);
        } else {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }
    }

    public function luuInMaBarcode(Request $request){
        $selectedItems = $request->input('selectedItems');

        foreach ($selectedItems as $item) {
            ViewVatTuChiTiet::create([
                'supply_id' => $item['supply_id'],
                'soluongnhapkho' => $item['soluongnhapkho'],
            ]);
        }

        return response()->json(['success' => 'Dữ liệu đã được lưu thành công.']);
    }

    public function updateVatTuNhap(Request $request){
        // Lấy dữ liệu từ request
        $data = $request->input('data');

        // Xử lý dữ liệu, ví dụ: cập nhật cơ sở dữ liệu
        foreach ($data as $item) {
            $maSo = $item['maSo'];
            $soLuongIn = $item['soLuongIn'];

            // Tìm vật tư theo mã số và cập nhật số lượng nhập
            $supply = Supply::where('maso', $maSo)->first();
            if ($supply) {
                $supply->soluongnhap += $soLuongIn;
                $supply->save();
            }
        }

        // Trả về phản hồi
        return response()->json(['success' => true]);
    }

    public function quetBarcodeNhapKho(Request $request){
        $selectedItems = $request->input('selectedItems', []);
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $supplyIds = array_column($selectedItems, 'id');

        // Tạo mảng kết hợp từ selectedItems
        $selectedItemsIndexed = array_column($selectedItems, null, 'id');

        $supplies = Supply::with(['order', 'order.catalog.project'])
                          ->whereIn('id', $supplyIds)
                          ->get();
        // Thêm thông tin daNhan vào mỗi supply
        foreach ($supplies as $supply) {
            $supplyId = $supply->id;
            if (isset($selectedItemsIndexed[$supplyId])) {
                $supply->daNhan = $selectedItemsIndexed[$supplyId]['soLuongNhapKho'];
            } else {
                $supply->daNhan = 'N/A'; // Hoặc một giá trị mặc định nào đó
            }
        }
        return view('Warehouse Management.Inside.nhapKhoBarcode', compact('supplies', 'user', 'selectedItems'));
    }

    public function updateQuanlity(Request $request) {
        $suppliesData = $request->input('supplies');
        foreach ($suppliesData as $supplyData) {
            // Tính tổng số lượng đã nhập từ các bản ghi QualityCheck hiện có cho supply_id này
            $totalEntered = QualityCheck::where('supply_id', $supplyData['id'])
                                        ->sum('soluongnhapkho');

            $supply = Supply::find($supplyData['id']);
            if (!$supply) {
                return response()->json(['error' => 'Không tìm thấy nhà vật tư.'], 404);
            }

            $newQuantity = $totalEntered + $supplyData['quantity'];
            if ($newQuantity > $supply->soluong) {
                return response()->json(['error' => 'Thất bại: Tổng số lượng đã nhập vượt quá tổng số lượng cung cấp.'], 400);
            }

            // Tạo bản ghi mới trong bảng QualityCheck
            QualityCheck::create([
                'supply_id' => $supplyData['id'],
                'soluongnhapkho' => $supplyData['quantity'],
            ]);

            $viewVatTuChiTiet = ViewVatTuChiTiet::where('supply_id', $supplyData['id'])->first();
            if ($viewVatTuChiTiet) {
                $viewVatTuChiTiet->increment('soluongnhapkho', $supplyData['quantity']);
            } else {
                ViewVatTuChiTiet::create([
                    'supply_id' => $supplyData['id'],
                    'soluongnhapkho' => $supplyData['quantity'],
                ]);
            }

            // Đặt lại giá trị của cột soluongnhap về rỗng cho các bản ghi liên quan
            $supply->soluongnhap = null;
            $supply->save();
        }

        return response()->json(['success' => 'Data updated successfully.']);
    }

// TỒN KHO
    public function trangTonKho(Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');
        $orders = Order::all();
        return view('Warehouse Management.Inside.quanLyTonKho', compact('user', 'module', 'orders'));
    }

    public function slectedPhanKhuc(Request $request){
        $thuongHieuId = $request->brand_id;
        $brand = Brand::find($thuongHieuId);
        if (!$brand) {
            return response()->json(['message' => 'Thương hiệu không tồn tại'], 404);
        }
        $segments = $brand->segments()->get(['id', 'name']);
        return response()->json($segments);
    }

    public function slectedDuAn(Request $request){
        $segmentId = $request->segmentId;
        $segment = Segment::find($segmentId);

        if (!$segment) {
            return response()->json(['message' => 'Phân khúc không tồn tại'], 404);
        }

        $projects = $segment->projects()->get(['id', 'name']);

        return response()->json($projects);
    }

    public function selectedDonHang(Request $request){
        $projectId = $request->input('projectId'); // Lấy project ID từ request

        if (empty($projectId)) {
            return response()->json(['success' => false, 'message' => 'Project ID is required.']);
        }

        // Truy vấn project và eager load các catalogs và orders
        $project = Project::with(['catalogs.orders'])->find($projectId);

        if (!$project) {
            return response()->json(['success' => false, 'message' => 'No project found with the given ID.']);
        }

        // Chuẩn bị mảng để chứa tất cả orders
        $allOrders = [];
        foreach ($project->catalogs as $catalog) {
            foreach ($catalog->orders as $order) {
                $allOrders[] = $order; // Thêm từng order vào mảng
            }
        }

        return response()->json(['success' => true, 'orders' => $allOrders]);
    }

    public function layVatTutheoDuAn(Request $request){
        $projectId = $request->input('projectId');  // Lấy ID dự án từ request

        // Tìm kiếm tất cả Catalogs liên kết với project này
        $catalogs = Catalog::where('project_id', $projectId)->with('supplies')->get();

        $data = [];
        foreach ($catalogs as $catalog) {
            foreach ($catalog->supplies as $supply) {
                // Lưu thông tin vật tư vào mảng data
                $data[] = [
                    'id' => $supply->id,
                    'maso' => $supply->maso,
                    'name' => $supply->tenvattu,
                    'quantity' => $supply->soluong,
                    'unit' => $supply->donvitinh
                ];
            }
        }

        return response()->json($data);  // Trả về dữ liệu dưới dạng JSON
    }

    public function selectedTenVatTu(Request $request){
        $donHangId = $request->donHangId;
        $vattuList = Supply::where('order_id', $donHangId)->get(['id', 'tenvattu']);
        if($vattuList->isEmpty()) {
            return response()->json(['message' => 'Không tìm thấy vật tư cho đơn hàng này'], 404);
        }
        return response()->json($vattuList);
    }

    public function listTonKhoDonHang(Request $request){
        $duAnId = $request->duAnIdDonHang;
        $phanKhucId = $request->phanKhucIdDonHang;
        $thuongHieuId = $request->thuongHieuIdDonHang;
        $donHangId = $request->donHangIdChiTiet;

        $ordersQuery = Order::query();

        // Load relationships through Catalog and include transactions and supplies
        $ordersQuery->with(['catalog.project.segment.brand', 'supplies.transactions']);

        if ($donHangId) {
            $ordersQuery->where('id', $donHangId);
        }

        if ($duAnId) {
            $ordersQuery->whereHas('catalog.project', function ($query) use ($duAnId) {
                $query->where('id', $duAnId);
            });
        }

        if ($phanKhucId) {
            $ordersQuery->whereHas('catalog.project.segment', function ($query) use ($phanKhucId) {
                $query->where('id', $phanKhucId);
            });
        }

        if ($thuongHieuId) {
            $ordersQuery->whereHas('catalog.project.segment.brand', function ($query) use ($thuongHieuId) {
                $query->where('id', $thuongHieuId);
            });
        }

        $orders = $ordersQuery->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng nào phù hợp'], 404);
        }

        $data = $orders->map(function ($order) {
            $tongSoLuongNhan = $order->supplies->reduce(function ($carry, $supply) {
                return $carry + $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            }, 0);

            $tongSoLuongXuat = $order->supplies->reduce(function ($carry, $supply) {
                return $carry + $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
            }, 0);

            $tongSoLuongVatTu = $order->supplies->sum('soluong'); // Calculate total quantity of supplies

            $soluongTon = $tongSoLuongNhan - $tongSoLuongXuat;

            // Access related entities through existing relationships
            $project = $order->catalog->project;
            $segment = $project ? $project->segment : null;
            $brand = $segment ? $segment->brand : null;

            return [
                'id' => $order->id,
                'sodonhang' => $order->sodonhang,
                'tongSoLuongNhan' => $tongSoLuongNhan,
                'tongSoLuongXuat' => $tongSoLuongXuat,
                'soluongTon' => $soluongTon,
                'tongSoLuongVatTu' => $tongSoLuongVatTu,
                'project' => $project ? $project->name : null,
                'segment' => $segment ? $segment->name : null,
                'brand' => $brand ? $brand->name : null
            ];
        });

        return response()->json($data);
    }

    public function layVatTuHienThi(Request $request){
        $projectId = $request->input('duAn');
        $supplyId = $request->input('idVatTu');

        // Eager loading để tải các quan hệ
        $relations = ['transactions', 'order'];

        if (!is_null($supplyId)) {
            $supplies = Supply::with($relations)->where('id', $supplyId)->get()->filter(function($supply) {
                $soluong_conlai = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong') -
                                $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
                $supply->soluong_conlai = $soluong_conlai;
                return $soluong_conlai > 0;
            });
        } else if (!is_null($projectId)) {
            $supplies = Supply::with($relations)->whereHas('catalog', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })->get()->map(function($supply) {
                $soluong_conlai = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong') -
                                $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
                $supply->soluong_conlai = $soluong_conlai;
                return $supply;
            })->filter(function($supply) {
                return $supply->soluong_conlai > 0;
            });
        } else {
            return response()->json([
                'error' => 'Thiếu thông tin dự án hoặc ID vật tư cần thiết cho truy vấn'
            ], 400);
        }

        // Trả về dữ liệu như response JSON
        return response()->json([
            'data' => $supplies
        ]);
    }

// XUẤT KHO
    public function listExportWarehouse(Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');
        $orders = Order::with(['catalog', 'supplies', 'expense'])->get();
        return view('Warehouse Management.Inside.xuatKho', compact('user', 'module', 'orders'));
    }

    public function searchSupplies(Request $request){
        $keyword = $request->keyword;
        $projectId = $request->project_id;

        // Tìm kiếm vật tư thông qua bảng Catalogs và Orders, kết hợp thông tin Transactions
        $supplies = Supply::whereHas('order.catalog', function($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })
        ->where(function($query) use ($keyword) {
            $query->where('tenvattu', 'like', '%' . $keyword . '%')
                  ->orWhere('maso', 'like', '%' . $keyword . '%');
        })
        ->with(['transactions', 'order.catalog.project']) // Tải sẵn thông tin transactions và project để tính toán
        ->get();

        // Chuẩn bị dữ liệu để trả về
        $data = $supplies->map(function($supply) {
            $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
            $soluong_conlai = $danhan - $daxuat;
            $order = $supply->order;
            $catalog = $order ? $order->catalog : null;
            $project = $catalog ? $catalog->project : null;

            return [
                'id' => $supply->id,
                'tenvattu' => $supply->tenvattu,
                'maso' => $supply->maso,
                'donvitinh' => $supply->donvitinh,
                'soluong' => $supply->soluong,
                'soluong_conlai' => $soluong_conlai,
                'note' => $supply->note,
                'status' => $supply->status,
                'order_id' => optional($order)->id,
                'sodonhang' => optional($order)->sodonhang,
                'nhacungcap' => optional($order)->nhacungcap,
                'chiphi' => optional($order)->chiphi,
                'ghichu' => optional($order)->ghichu,
                'project_name' => optional($project)->name, // Ví dụ thêm thông tin từ Project
            ];
        });

        return response()->json($data);
    }


    public function formTrinhKy(Request $request){
        $ngayTaoPhieu = $request->ngayTaoPhieu;
        $parsedDate = Carbon::parse($ngayTaoPhieu);
        $ngay = $parsedDate->day;
        $thang = $parsedDate->month;
        $nam = $parsedDate->year;
        $projectId = $request->input('projectId');
        $project = Project::find($projectId);
        $data = $request->only(['projectId', 'so', 'donViNhan', 'mucDichXuat', 'vattuThuongHieuXuat', 'nguoiNhan', 'nguoiCap', 'nguoiLap', 'selectedSupplies']);
        $data['ngay'] = $ngay;
        $data['thang'] = $thang;
        $data['nam'] = $nam;
        $data['project'] = $project;
        $request->session()->put('formData', $data);
        return response()->json([
            'redirectUrl' => route('formTrinhKyGet')
        ]);
    }

    public function formTrinhKyGet(Request $request){
        $formData = $request->session()->get('formData', []);
        $supplies = [];
        if (!empty($formData['selectedSupplies'])) {
            $supplies = Supply::with(['transactions', 'qualityChecks', 'viewVatTuChiTiets'])
                        ->whereIn('id', $formData['selectedSupplies'])
                        ->get();
            $supplies->each(function($supply) {
                $totalDanhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                $totalDaxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
                $supply->soluong_conlai = $totalDanhan - $totalDaxuat;
            });
        }
        // dd($supplies->toarray());
        return view('Warehouse Management.Inside.formTrinhKy', array_merge($formData, ['supplies' => $supplies]));
    }

    public function xuatKhoBarcode(Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        return view('Warehouse Management.Inside.xuatKhoBarcode',compact('user'));
    }

    public function checkVatTuXuatKho(Request $request){
        $maso = $request->input('barcode');  // Lấy mã số từ request

        // Tìm vật tư theo mã số
        $supply = Supply::with(['transactions', 'order'])->where('maso', $maso)->first();

        if (!$supply) {
            // Nếu không tìm thấy vật tư, trả về thông báo không tồn tại
            return response()->json(['exists' => false, 'message' => 'Vật tư không tồn tại.']);
        }

        // Tính toán số lượng đã nhận và đã xuất
        $totalReceived = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
        $totalExported = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
        $remaining = $totalReceived - $totalExported;

        if ($remaining <= 0) {
            // Nếu không còn hàng trong kho, trả về thông báo
            return response()->json(['exists' => true, 'message' => 'Không còn hàng trong kho.']);
        }

        // Trả về kết quả với thông tin vật tư, đơn hàng, số lượng còn lại và ID của vật tư
        return response()->json([
            'exists' => true,
            'id' => $supply->id, // Thêm ID vật tư vào kết quả trả về
            'name' => $supply->tenvattu,
            'orderName' => $supply->order->sodonhang ?? 'Không rõ đơn hàng',
            'remaining' => $remaining,
            'message' => 'Vật tư tồn tại. Số lượng còn lại: ' . $remaining
        ]);
    }

    public function xacNhanXuatKho(Request $request){
        $id = $request->id;
        $quantity = $request->quantity;

        $supply = Supply::with('transactions')->find($id);
        if (!$supply) {
            return response()->json(['success' => false, 'message' => 'Vật tư không tồn tại.']);
        }

        $totalReceived = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
        $totalExported = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong') + $quantity; // Thêm số lượng mới vào tổng đã xuất
        $remaining = $totalReceived - $totalExported;

        if ($quantity > $remaining + $quantity) { // Kiểm tra lại điều kiện này để đảm bảo tính hợp lệ
            return response()->json(['success' => false, 'message' => "Số lượng xuất không được vượt quá số lượng còn lại. Đơn hàng này chỉ có $remaining đơn vị."]);
        }

        // Lưu transaction mới
        $transaction = new Transaction([
            'supply_id' => $id,
            'soluong' => $quantity,
            'loaigiaodich' => 'Đã xuất',
            'ngaygiaodich' => Carbon::now(),
            // 'ghichu' => 'Xuất kho vật tư'
        ]);
        $transaction->save();

        return response()->json(['success' => true, 'remaining' => $remaining, 'message' => 'Xuất kho thành công.']);
    }

    public function xuongYeuCau(){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $brands = Brand::all(); // Lấy tất cả dữ liệu thương hiệu

        return view('Warehouse Management.Inside.xuongYeuCau', compact('user', 'brands'));
    }

// QUẢN LÝ ĐƠN HÀNG PHÒNG KẾ HOẠCH
    public function quanLyDonHang(){
        $catalogsCanCreateOrders = Catalog::whereHas('supplies', function ($query) {
            $query->where('status', 0);
        })->with(['project.segment.brand'])->get();

        $providers = Provider::with('details')->get();
        $chiPhi = Expense::all();
        // dd($chiPhi->toarray());
        $orders = Order::with(['catalog', 'supplies','expense'])->get();
        $orders->each(function ($order) {
            $totalSupplies = $order->supplies->sum('soluong');
            $totalReceived = $order->supplies->sum(function ($supply) {
                return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            });
            $order->isEqual = ($totalSupplies === $totalReceived);
        });
        // dd($orders->toarray());
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $countCatalogsWithoutOrders = $catalogsCanCreateOrders->count();
        return view('Warehouse Management.Inside.quanLyDonHang', compact('countCatalogsWithoutOrders', 'user', 'providers', 'orders','chiPhi'));
    }

    public function layDanhMucChuaCoDonHang() {
        $catalogsCanCreateOrders = Catalog::whereHas('supplies', function ($query) {
            $query->where('status', 0);
        })->with(['project.segment.brand'])
        ->get();

        foreach ($catalogsCanCreateOrders as $catalog) {
            $catalog->provider_info = $catalog->getProviderInfo();
        }
        // dd($catalogsCanCreateOrders->toarray());
        return response()->json($catalogsCanCreateOrders);
    }

    public function duLieuVatTuCuaDanhMuc(Request $request) {
        // Lấy ID của danh mục được gửi lên từ client
        $catalogId = $request->id;

        // Truy vấn để lấy danh sách vật tư thuộc danh mục này
        $supplies = Supply::where('catalog_id', $catalogId)->get();
        // dd($supplies->toarray());
        // Kiểm tra nếu danh sách rỗng
        if ($supplies->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không có vật tư nào trong danh mục này.'
            ]);
        }

        // Trả về dữ liệu vật tư dưới dạng JSON
        return response()->json([
            'status' => 'success',
            'supplies' => $supplies
        ]);
    }

    public function taoDonHangMoi(Request $request) {
        // dd($request->toarray());
        $expense = Expense::find($request->companyId);
        if (!$expense) {
            return response()->json(['error' => 'Chi phí không tồn tại.'], 404);
        }

        $firstSupply = Supply::with('catalog.project')->find($request->supplyIds[0]);
        if (!$firstSupply) {
            return response()->json(['error' => 'Vật tư không tồn tại.'], 404);
        }

        $project = $firstSupply->catalog->project;
        $projectId = $project->id;
        $year = Carbon::now()->format('y');
        $month = Carbon::now()->format('m');

        $lastOrder = Order::whereHas('catalog', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->whereYear('created_at', Carbon::now()->year)
          ->whereMonth('created_at', Carbon::now()->month)
          ->orderBy('created_at', 'desc')
          ->first();

        $nextNumber = $lastOrder ? ((int) substr($lastOrder->sodonhang, 3, 2)) + 1 : 1;
        $supplierCode = strtoupper(substr($request->nhacungcap, 0, 4));
        $formattedNumber = sprintf("RD_%02d%s%s_%s_%s", $nextNumber, $month, $year, $supplierCode, $expense->name);

        $order = new Order();
        $order->sodonhang = $formattedNumber;
        $order->expense_id = $request->companyId;
        $order->noidung = $request->noidung ?? ''; // Gán giá trị cho noidung, dùng ?? để xử lý trường hợp null
        $order->ghichu = $request->ghiChu;
        $order->ngayhoanthanh = $request->ngayHoanThanh;
        $order->ngaytaophieu = $request->ngayYeuCau;
        $order->catalog_id = $firstSupply->catalog_id;
        $order->save();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Tạo tên file mới dựa vào số đơn hàng
            $extension = $file->getClientOriginalExtension(); // Lấy phần mở rộng của file gốc
            $filename = $formattedNumber . '.' . $extension; // Tạo tên file mới

            // Đảm bảo thư mục tồn tại
            $destinationPath = public_path('File_DonHang'); // Đường dẫn thư mục lưu file
            $file->move($destinationPath, $filename); // Di chuyển file vào thư mục đích

            $order->excel_file = 'File_DonHang/' . $filename; // Lưu đường dẫn vào cơ sở dữ liệu
            $order->save();
        }

        if ($request->has('quantities') && $request->has('prices')) {
            // Update quantities and prices
            foreach ($request->supplyIds as $index => $id) {
                $cleanQuantity = preg_replace('/[^\d]/', '', $request->quantities[$index]);
                $cleanPrice = preg_replace('/[^\d]/', '', $request->prices[$index]);

                $quantity = intval($cleanQuantity);
                $price = intval($cleanPrice);


                Supply::where('id', $id)->update([
                    'order_id' => $order->id,
                    'don_gia' => $quantity,
                    'thanh_tien' => $price,
                    'status' => 1
                ]);
            }
        }else {
            // Just link supplies to order
            Supply::whereIn('id', $request->supplyIds)
                  ->update(['order_id' => $order->id, 'status' => 1]);
        }

        return response()->json(['message' => 'Đơn hàng đã được tạo thành công. Số đơn hàng là ' . $formattedNumber]);
    }

    public function suaDonHang(Request $request){
        $orderId = $request->input('order_id');
        $supplies = Supply::where('order_id', $orderId)->get();

        if ($supplies->isEmpty()) {
            return response()->json(['error' => 'Không có vật tư nào cho đơn hàng này'], 404);
        }

        $catalogId = $supplies->first()->catalog_id;

        $allSameCatalog = $supplies->every(function($supply) use ($catalogId) {
            return $supply->catalog_id == $catalogId;
        });

        if (!$allSameCatalog) {
            return response()->json(['error' => 'Vật tư thuộc nhiều danh mục khác nhau'], 400);
        }

        $catalog = Catalog::find($catalogId);
        $relatedSupplies = Supply::where('catalog_id', $catalogId)->get();
        return response()->json([
            'supplies' => $relatedSupplies,
            'catalogName' => $catalog ? $catalog->name : 'Unknown Catalog'
        ]);
    }

    public function capNhatDonHang(Request $request) {
        $supplies = $request->input('supplies');
        $orderId = $request->input('order_id');
        $removedSupplies = $request->input('removedSupplies');
        $addedSupplies = $request->input('addedSupplies');

        // Lấy tất cả các vật tư thuộc đơn hàng hiện tại
        $currentSupplies = Supply::where('order_id', $orderId)->get();

        // Đặt tất cả các vật tư thuộc đơn hàng hiện tại về trạng thái ban đầu
        foreach ($currentSupplies as $supplyModel) {
            $supplyModel->order_id = null;
            $supplyModel->status = 0;
            $supplyModel->save();  // Lưu thay đổi vào cơ sở dữ liệu
        }

        // Cập nhật các vật tư đã chọn
        foreach ($supplies as $supplyId) {
            $supplyModel = Supply::find($supplyId);
            if ($supplyModel) {
                $supplyModel->order_id = $orderId;
                $supplyModel->status = 1;
                $supplyModel->save();  // Lưu thay đổi vào cơ sở dữ liệu
            }
        }

        // Chỉ lưu lại lịch sử thay đổi nếu có sự thay đổi
        $changes = [];

        if (!empty($removedSupplies)) {
            $removedSuppliesText = array_map(function($supplyId) {
                $supply = Supply::find($supplyId);
                return "{$supply->tenvattu} - {$supply->maso}";
            }, $removedSupplies);
            $changes[] = "Bạn đã bỏ chọn các vật tư:\n" . implode("\n", $removedSuppliesText);
        }

        if (!empty($addedSupplies)) {
            $addedSuppliesText = array_map(function($supplyId) {
                $supply = Supply::find($supplyId);
                return "{$supply->tenvattu} - {$supply->maso}";
            }, $addedSupplies);
            $changes[] = "Bạn đã chọn thêm các vật tư:\n" . implode("\n", $addedSuppliesText);
        }

        if (!empty($changes)) {
            $finalChangesText = implode("\n\n", $changes);

            OrderHistory::create([
                'order_id' => $orderId,
                'content' => $finalChangesText,
                'reason' => 'Cập nhật vật tư', // Bạn có thể thay đổi lý do này tùy theo logic của bạn
            ]);
        }

        return response()->json(['success' => 'Thông tin đã được cập nhật']);
    }


    public function timKiemDanhMucVatTu(Request $request){
        $nhaCungCap = $request->input('nhaCungCap');

        $results = Catalog::whereHas('supplies', function ($query) use ($nhaCungCap) {
            $query->where('status', 0)->where('nhacungcap', $nhaCungCap);
        })->with(['project.segment.brand'])->get();

        return response()->json($results);
    }

    public function duLieuDaKy(Request $request){
        $orderId = $request->id;

        // Lấy đơn hàng và tải dữ liệu liên quan
        $order = Order::with(['supplies', 'catalog.project', 'expense'])
                    ->where('id', $orderId)
                    ->first();

        // Kiểm tra đơn hàng có tồn tại hay không
        if (!$order) {
            return response()->json(['error' => 'Đơn hàng không tồn tại'], 404);
        }

        // Tạo QR Code sau khi đã xác nhận đơn hàng tồn tại
        $qrCode = Qrcode::size(100)->encoding('UTF-8')->generate($order->sodonhang);

        // Lấy thông tin nhà cung cấp từ phương thức đã định nghĩa trong Catalog model
        $providerInfo = $order->catalog ? $order->catalog->getProviderInfo() : null;

        // Chuẩn bị dữ liệu để gửi về client
        $data = [
            'order' => $order->toArray(), // Chuyển đối tượng Order thành mảng
            'supplies' => $order->supplies,
            'catalog' => $order->catalog,
            'project' => $order->catalog->project ?? null,
            'expense' => $order->expense,
            'providerInfo' => $providerInfo,
            'qrCode' => $qrCode->toHtml(), // Đảm bảo mã QR là HTML
        ];
        // dd($data);
        return response()->json($data);
    }


    public function luuGiaTriIn(Request $request){
        $orderId = $request->id; // Lấy ID từ request
        $order = Order::find($orderId); // Tìm Order dựa trên ID

        if ($order) {
            $order->status = 1; // Cập nhật trạng thái của Order
            $order->save(); // Lưu thay đổi vào database

            return response()->json([
                'status' => 'success',
                'message' => 'Đơn hàng đã được cập nhật thành công.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Đơn hàng không tìm thấy.'
            ], 404);
        }
    }

    public function lichSuOrder(Request $request){
        $orderId = $request->input('order_id');

        // Truy xuất lịch sử đơn hàng từ bảng OrderHistory
        $orderHistories = OrderHistory::where('order_id', $orderId)->get();

        // Trả về kết quả dưới dạng HTML để hiển thị trong modal
        $html = '';
        foreach ($orderHistories as $index => $history) {
            $html .= '<tr>';
            $html .= '<td style="text-align: center; vertical-align: middle;">' . ($index + 1) . '</td>';
            $html .= '<td style="text-align: center; vertical-align: middle;">' . nl2br(e($history->content)) . '</td>';
            $html .= '</tr>';
        }

        return response()->json($html);
    }

// KIỂM TRA CHẤT LƯỢNG
    public function kiemTraCLBarcode(Request $request) {
        $masos = json_decode(urldecode($request->query('masos')), true);
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        return view('Warehouse Management.Inside.kiemTraChatLuongBarcode', compact('user', 'masos'));
    }


    public function checkQuality(Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());

        // Kiểm tra các đơn hàng có vật tư đang cần kiểm tra chất lượng
        $qualityChecks = QualityCheck::where('status', 0)
            ->with(['supply' => function($query) {
                $query->with(['order' => function($query) {
                    $query->select('id', 'sodonhang');
                }]);
            }])
            ->get();

        // Tạo một mảng để lưu tổng số lượng vật tư cần kiểm tra cho mỗi đơn hàng
        $orderQuantities = [];

        foreach ($qualityChecks as $qualityCheck) {
            $orderId = $qualityCheck->supply->order->id;
            $orderNumber = $qualityCheck->supply->order->sodonhang;
            $quantity = $qualityCheck->soluongnhapkho;

            if (!isset($orderQuantities[$orderId])) {
                $orderQuantities[$orderId] = [
                    'order_number' => $orderNumber,
                    'total_quantity' => 0
                ];
            }

            $orderQuantities[$orderId]['total_quantity'] += $quantity;
        }
        // dd($orderQuantities);
        return view('Warehouse Management.Inside.kiemTraChatLuong', compact('user', 'orderQuantities'));
    }

    public function vatTuKiemTra(Request $request){
        // Lấy order_id từ request
        $orderId = $request->input('order_id');

        // Truy vấn QualityCheck với điều kiện status = 0 và order_id
        $qualityChecks = QualityCheck::where('status', 0)
            ->whereHas('supply.order', function ($query) use ($orderId) {
                $query->where('id', $orderId);
            })
            ->with('supply.order.catalog.project') // Truy cập thông qua Catalog
            ->get();

        // Trả về phản hồi (bạn có thể thay đổi phản hồi tùy theo nhu cầu của bạn)
        return response()->json($qualityChecks);
    }


    public function luuKiemTraChatLuong(Request $request){
        $qualityCheck = QualityCheck::where('supply_id', $request->idQualityCheck)
            ->where('status', 0)
            ->whereNull('soluongdatchatluong')
            ->first();

        // Kiểm tra số lượng đạt chất lượng so với số lượng nhập kho
        if ($request->quantityDat > $qualityCheck->soluongnhapkho) {
            // Nếu số lượng kiểm tra lớn hơn số lượng nhập kho, thông báo cho người dùng và dừng thực thi
            return response()->json(['success' => false, 'message' => 'Số lượng kiểm tra chất lượng không thể lớn hơn số lượng nhập kho.']);
        }

        // Cập nhật thông tin QualityCheck nếu số lượng kiểm tra hợp lệ
        $qualityCheck->soluongdatchatluong = $request->quantityDat;
        $qualityCheck->status = 1;
        $qualityCheck->ngaykiemtra = Carbon::now()->format('Y-m-d');
        if (!empty($request->ghiChu)) {
            $qualityCheck->note = $request->ghiChu; // Lưu ghi chú nếu có
        }
        $qualityCheck->save();

        $viewVatTuChiTiet = ViewVatTuChiTiet::where('supply_id', $qualityCheck->supply_id)->first();
        if (is_null($viewVatTuChiTiet->soluongdatchatluong)) {
            $viewVatTuChiTiet->soluongdatchatluong = $request->quantityDat;
        } else {
            $viewVatTuChiTiet->increment('soluongdatchatluong', $request->quantityDat);
        }
        $viewVatTuChiTiet->soluongnhapkho = $viewVatTuChiTiet->soluongdatchatluong;
        $viewVatTuChiTiet->save();

        // Tạo một Transaction mới với thông tin tương ứng
        $transaction = new Transaction([
            'supply_id' => $qualityCheck->supply_id,
            'soluong' => $request->quantityDat,
            'loaigiaodich' => 'Đã nhận',
            'ngaygiaodich' => now(),
            'ghichu' => $request->ghiChu // Lưu ghi chú vào Transaction nếu có
        ]);
        $transaction->save();

        // Bao gồm status mới trong phản hồi
        return response()->json([
            'success' => true,
            'message' => 'Dữ liệu đã được cập nhật thành công.',
            'status' => $qualityCheck->status,
            'ngaykiemtra' => $qualityCheck->ngaykiemtra // Thêm dòng này
        ]);
    }

    public function timKiemVatTuCheck(Request $request){
        $status = $request->status;
        $ngayKiemTra = $request->ngayKiemTra; // Thêm biến này
        $query = QualityCheck::query();
        if ($status !== null) {
            $query->where('status', $status);
        }

        if (!empty($ngayKiemTra)) {
            $query->whereDate('ngaykiemtra', '=', $ngayKiemTra);
        }

        $results = $query->with(['supply.order'])->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    public function takeIdByBarcode(Request $request){
        $maVatTu = $request->mavattu;
        // Bao gồm các mối quan hệ khi truy vấn
        $supply = Supply::with(['order', 'transactions', 'qualityChecks', 'viewVatTuChiTiets', 'catalog'])
                        ->where('maso', $maVatTu)
                        ->first();

        if ($supply) {
            // Kiểm tra trong bảng QualityCheck
            $qualityCheck = $supply->qualityChecks()->where('status', 0)->whereNull('soluongdatchatluong')->first();

            if ($qualityCheck) {
                return response()->json([
                    'status' => 'Thành công',
                    'data' => $supply
                ]);
            } else {
                return response()->json([
                    'status' => 'Lỗi',
                    'message' => 'Supply không đáp ứng điều kiện kiểm tra chất lượng.'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'Lỗi',
                'message' => 'Không tìm thấy nguồn cung cấp nào cho mã vật liệu được cung cấp.'
            ]);
        }
    }

}
