<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supply;
use App\Models\Project;
use App\Models\Provider;
use App\Models\Order;
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
use Carbon\Carbon;
use Milon\Barcode\DNS1D as BarcodeDNS1D;

class Insite extends Controller
{
// QUẢN LÝ KẾ HOẠCH
    public function listWarehouse($idProject, Request $request) {
        // dd($request->toarray());
        $sodonhangSelect = $request->get('sodonhangSelect');
        $nhacungcapSelect = $request->get('nhacungcapSelect');
        $nhacungcapSuppeliesSelect = $request->get('nhacungcapSuppeliesSelect');
        $project = Project::with(['orders' => function($query) {
            $query->with(['supplies' => function($query) {
                $query->with(['transactions']);
            }]);
        }])->find($idProject);
        if (!$project) {
            abort(404, 'Dự án không tìm thấy.');
        }
        $orders = $project->orders->map(function($order) use (&$totalOrdersDanhan, &$totalOrdersChuanhan, &$totalOrdersDaxuat) {
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
        if ($sodonhangSelect || $nhacungcapSelect || $nhacungcapSuppeliesSelect) {
            $orders = $orders->filter(function ($order) use ($sodonhangSelect, $nhacungcapSelect, $nhacungcapSuppeliesSelect) {
                $matchesSodonhang = $sodonhangSelect ? $order->sodonhang == $sodonhangSelect : true;
                $matchesNhacungcap = $nhacungcapSelect ? $order->nhacungcap == $nhacungcapSelect : true;

                $matchesSuppliesStatus = true;
                if ($nhacungcapSuppeliesSelect) {
                    switch ($nhacungcapSuppeliesSelect) {
                        case 'Đã nhận':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu đã nhận
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong') > 0;
                            });
                            break;
                        case 'Chưa nhận':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu chưa nhận
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Chưa nhận')->count() > 0 || $supply->transactions->count() == 0;
                            });
                            break;
                        case 'Đã xuất':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu đã xuất
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong') > 0;
                            });
                            break;
                        default:
                            $matchesSuppliesStatus = true;
                            break;
                    }
                }

                return $matchesSodonhang && $matchesNhacungcap && $matchesSuppliesStatus;
            });
        }
        $brandName = optional(optional($project->segment)->brand)->name;
        $segmentId = $project->segment->id ?? null;
        $segmentName = $project->segment->name ?? null;
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');

        $providers = Provider::with('details')->get();
        $totalSuppliesForProject = $orders->sum('total_supplies');

        return view('Warehouse Management.Inside.quanlykehoach', compact('totalOrdersDanhan', 'totalOrdersChuanhan', 'totalOrdersDaxuat','orders', 'user', 'providers', 'module', 'segmentId', 'brandName', 'segmentName', 'project', 'totalSuppliesForProject'));
    }

    public function importSupplies(Request $request){
        $project_id = $request['project_id'];

        // Lấy file từ request
        $file = $request->file('file');

        // TH1: Kiểm tra định dạng file (chỉ chấp nhận Excel)
        $allowedExtensions = ['xlsx', 'xls'];
        if (!in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
            return back()->with('error', 'File không phải là file Excel.');
        }

        // Lấy tên file và phân tách để lấy thông tin
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // TH2: Kiểm tra định dạng tên file
        if (substr_count($filename, '_') != 2) {
            return back()->with('error', 'Tên file không đúng định dạng. Định dạng yêu cầu là sodonhang_nhacungcap_chiphi.');
        }

        // Tách thông tin từ tên file
        [$sodonhang, $nhacungcap, $chiphi] = explode('_', $filename);

        // Kiểm tra và cắt bỏ phần mở rộng từ $chiphi
        if (($pos = strpos($chiphi, '.')) !== false) {
            $chiphi = substr($chiphi, 0, $pos);
        }

        DB::beginTransaction();

        try {
            $import = new SuppliesImport($sodonhang, $nhacungcap, $chiphi, $project_id);
            Excel::import($import, $file);
            // dd($import);
            if ($import->getErrors()) {
                DB::rollBack();
                $errorMessages = implode(' ', $import->getErrors());
                return back()->with('error', 'Có lỗi xảy ra trong quá trình nhập dữ liệu: ' . $errorMessages);
            }

            DB::commit();
            return back()->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra trong quá trình nhập dữ liệu: ' . $e->getMessage());
        }

        // Trả về response thành công
        return back()->with('success', 'Dữ liệu đã được nhập thành công!');
    }

    public function importThuCong(Request $request) {
        // Xác thực dữ liệu (tuỳ chọn)
        $validatedData = $request->validate([
            'project_id' => 'required|integer',
            'maso'=>'required|string',
            'sodonhang' => 'required|string',
            'tenvattu' => 'required|string',
            'nhacungcap' => 'required|string',
            'noidungphancum' => 'nullable|string',
            'donvitinh' => 'required|string',
            'soluong' => 'required|integer',
            'chiphi' => 'required|string',
            'note' => 'nullable|string', // Ghi chú có thể không cần thiết
        ]);
         // Kiểm tra xem đã có vật tư với tên hoặc mã số giống nhau trong cùng dự án chưa
        $existingSupply = Supply::where('project_id', $validatedData['project_id'])
                                ->where(function ($query) use ($validatedData) {
                                    $query->where('maso', $validatedData['maso']);
                                })->exists();

        if ($existingSupply) {
            // Nếu tìm thấy vật tư trùng tên hoặc mã số, trả về lỗi
            return back()->with('error', 'Vật tư đã tồn tại trong dự án, không thể thêm.');
        }

        $supply = new Supply([
            'project_id' => $validatedData['project_id'],
            'maso'=> $validatedData['maso'],
            'sodonhang' => $validatedData['sodonhang'],
            'tenvattu' => $validatedData['tenvattu'],
            'nhacungcap' => $validatedData['nhacungcap'],
            'noidungphancum' => $validatedData['noidungphancum'],
            'donvitinh' => $validatedData['donvitinh'],
            'soluong' => $validatedData['soluong'],
            'chiphi' => $validatedData['chiphi'],
            'stt' => '1',
            'ngaynhan' => null,
            'note' => $validatedData['note'],
        ]);
        // Tạo và lưu vật tư mới
        $supply->save();
        // Chuyển hướng người dùng hoặc trả về response
        return redirect()->back()->with('success', 'Vật tư đã được thêm thành công');
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
        $orderId = $request->input('ordersId'); // Sửa lại tên biến cho phù hợp
        $order = Order::with(['supplies.transactions', 'supplies.qualityChecks'])->find($orderId);

        if ($order) {
            // Xóa các transactions liên quan đến supplies của đơn hàng
            foreach ($order->supplies as $supply) {
                foreach ($supply->transactions as $transaction) {
                    $transaction->delete();
                }

                // Thêm bước xóa các bản ghi QualityCheck liên quan đến supply
                foreach ($supply->qualityChecks as $qualityCheck) {
                    $qualityCheck->delete();
                }
            }

            // Xóa tất cả supplies của đơn hàng
            $order->supplies()->delete();

            // Cuối cùng, xóa đơn hàng
            $order->delete();

            return response()->json(['success' => 'Đơn hàng, vật tư liên quan và kiểm định chất lượng đã được xóa thành công.']);
        } else {
            return response()->json(['error' => 'Không thể tìm thấy đơn hàng.'], 404);
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
        $supplies = Supply::with(['transactions'])->where('order_id', $orderId)->get();

        $totalSupplies = 0; // Tổng số lượng vật tư
        $totalDanhan = 0; // Tổng số lượng đã nhận
        $totalChuanhan = 0; // Tổng số lượng chưa nhận
        $totalDaxuat = 0; // Tổng số lượng đã xuất

        $suppliesDetail = $supplies->map(function ($supply) use (&$totalSupplies, &$totalDanhan, &$totalChuanhan, &$totalDaxuat) {
            $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
            $chuanhan = $supply->soluong - $danhan;
            $totalSupplies += $supply->soluong;
            $totalDanhan += $danhan;
            $totalChuanhan += $chuanhan;
            $totalDaxuat += $daxuat;

            // Tạo mã vạch
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

        return response()->json([
            'orderName' => $order->sodonhang ?? '',
            'supplies' => $suppliesDetail,
            'totalSupplies' => $totalSupplies,
            'totalDanhan' => $totalDanhan,
            'totalChuanhan' => $totalChuanhan,
            'totalDaxuat' => $totalDaxuat,
            'orderId' => $orderId ,

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
        $validated = $request->validate([
            'project_id' => 'required|numeric',
            'sodonhang' => 'required|string',
            'nhacungcap' => 'required|string',
            'noidungphancum' => 'required|string',
            'chiphi' => 'required|string',
            'note' => 'nullable|string',
        ]);
        $existingOrder = Order::where('sodonhang', $validated['sodonhang'])->first();
        if ($existingOrder) {
            return redirect()->back()->with('error', 'Số đơn hàng đã tồn tại. Vui lòng kiểm tra lại.')->withInput();
        }
        $order = new Order;
        $order->project_id = $validated['project_id'];
        $order->sodonhang = $validated['sodonhang'];
        $order->nhacungcap = $validated['nhacungcap'];
        $order->noidung = $validated['noidungphancum'];
        $order->chiphi = $validated['chiphi'];
        $order->ghichu = $validated['note'];
        $order->save();
        return redirect()->back()->with('success', 'Thêm đơn hàng thành công.');
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

        // Bỏ qua orderId vì chúng ta đang tìm kiếm trên toàn bộ cơ sở dữ liệu
        $query = Supply::with(['transactions'])->where('order_id', $request->orderId);
        if ($request->filled('tenvattu')) {
            $query->where('tenvattu', 'like', '%' . $request->tenvattu . '%');
        }

        if ($request->filled('maso')) {
            $query->where('maso', $request->maso);
        }

        if ($request->filled('donvitinh')) {
            $query->where('donvitinh', $request->donvitinh);
        }

        $supplies = $query->get();

        $totalSupplies = $totalDanhan = $totalChuanhan = $totalDaxuat = 0;

        $suppliesDetail = $supplies->map(function ($supply) use (&$totalSupplies, &$totalDanhan, &$totalChuanhan, &$totalDaxuat) {
            $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
            $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
            $chuanhan = $supply->soluong - $danhan;

            $totalSupplies += $supply->soluong;
            $totalDanhan += $danhan;
            $totalChuanhan += $chuanhan;
            $totalDaxuat += $daxuat;

            // Giả sử bạn vẫn muốn tạo mã vạch cho mỗi vật tư
            $barcodeHtml = BarcodeDNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33); // Thay thế bằng cách thực tế tạo mã vạch của bạn

            return [
                'id' => $supply->id,
                'tenvattu' => $supply->tenvattu,
                'maso' => $supply->maso,
                'donvitinh' => $supply->donvitinh,
                'soluong' => $supply->soluong,
                'danhan' => $danhan,
                'chuanhan' => $chuanhan,
                'daxuat' => $daxuat,
                'barcodeHtml' => $barcodeHtml,
            ];
        });

        return response()->json([
            'supplies' => $suppliesDetail,
            'totalSupplies' => $totalSupplies,
            'totalDanhan' => $totalDanhan,
            'totalChuanhan' => $totalChuanhan,
            'totalDaxuat' => $totalDaxuat,
        ]);
    }
// NHẬP KHO
    public function listNhapKho($idProject,Request $request){
        $sodonhangSelect = $request->get('sodonhangSelect');
        $nhacungcapSelect = $request->get('nhacungcapSelect');
        $nhacungcapSuppeliesSelect = $request->get('nhacungcapSuppeliesSelect');
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');
        $project = Project::with(['orders' => function($query) {
            $query->with(['supplies' => function($query) {
                $query->with(['transactions']);
            }]);
        }])->find($idProject);
        $totalOrdersDanhan = 0;
        $totalOrdersChuanhan = 0;
        $totalOrdersDaxuat = 0;
        $orders = $project->orders->map(function($order) use (&$totalOrdersDanhan, &$totalOrdersChuanhan, &$totalOrdersDaxuat) {
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

        if ($sodonhangSelect || $nhacungcapSelect || $nhacungcapSuppeliesSelect) {
            $orders = $orders->filter(function ($order) use ($sodonhangSelect, $nhacungcapSelect, $nhacungcapSuppeliesSelect) {
                $matchesSodonhang = $sodonhangSelect ? $order->sodonhang == $sodonhangSelect : true;
                $matchesNhacungcap = $nhacungcapSelect ? $order->nhacungcap == $nhacungcapSelect : true;

                $matchesSuppliesStatus = true;
                if ($nhacungcapSuppeliesSelect) {
                    switch ($nhacungcapSuppeliesSelect) {
                        case 'Đã nhận':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu đã nhận
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong') > 0;
                            });
                            break;
                        case 'Chưa nhận':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu chưa nhận
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Chưa nhận')->count() > 0 || $supply->transactions->count() == 0;
                            });
                            break;
                        case 'Đã xuất':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu đã xuất
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong') > 0;
                            });
                            break;
                        default:
                            $matchesSuppliesStatus = true;
                            break;
                    }
                }

                return $matchesSodonhang && $matchesNhacungcap && $matchesSuppliesStatus;
            });
        }
        $brandName = optional(optional($project->segment)->brand)->name;
        $segmentName = $project->segment->name ?? null;
        $segmentId = $project->segment->id ?? null;
        $providers = Provider::with('details')->get();
        $totalSuppliesForProject = $orders->sum('total_supplies');
        return view('Warehouse Management.Inside.nhapkho', compact('totalOrdersDanhan', 'totalOrdersChuanhan', 'totalOrdersDaxuat','orders', 'user', 'providers', 'module', 'segmentId', 'brandName', 'segmentName', 'project', 'totalSuppliesForProject'));
    }

    public function nhapKho(Request $request){
        // Lấy dữ liệu từ request
        $supplyId = $request->input('supply_id');
        $soLuong = $request->input('soluong');

        // Tạo bản ghi mới trong bảng transactions
        $transaction = Transaction::create([
            'supply_id' => $supplyId,
            'soluong' => $soLuong,
            'loaigiaodich' => 'Nhập kho',
            'ngaygiaodich' => Carbon::now(),
            'ghichu' => ''
        ]);

        // Kiểm tra và trả về phản hồi
        if ($transaction) {
            return response()->json([
                'success' => true,
                'message' => 'Giao dịch được thêm thành công',
                'transaction' => $transaction
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình thêm giao dịch'
            ]);
        }
    }

    public function laySoLuongKho(Request $request){
        // dd($request->toarray());
        $itemId = $request->id;
        $transaction = Transaction::where('supply_id', $itemId)->first();

        // Giả sử mỗi supply_id chỉ có một transaction liên quan
        return response()->json([
            'soluong' => $transaction ? $transaction->soluong : 0
        ]);
    }

    public function laythongtinvattu(Request $request){
        $ids = $request->ids;
        // Nạp trước (eager load) mối quan hệ order và order.project khi truy vấn supplies
        $supplies = Supply::with('order.project')->whereIn('id', $ids)->get();

        $data = $supplies->map(function($supply) {
            $barcodeHtml = BarcodeDNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33);
            return [
                'tenvattu' => $supply->tenvattu,
                'maso' => $supply->maso,
                'donvitinh' => $supply->donvitinh,
                'soluong' => $supply->soluong,
                'barcode' => $barcodeHtml,
                // Thêm thông tin đơn hàng và dự án từ mối quan hệ
                'donhang' => $supply->order ? [
                    'sodonhang' => $supply->order->sodonhang,
                    'nhacungcap' => $supply->order->nhacungcap,
                    'chiphi' => $supply->order->chiphi,
                    'noidung' => $supply->order->noidung,
                    'ghichu' => $supply->order->ghichu,
                ] : null,
                // Thêm thông tin dự án từ mối quan hệ thông qua đơn hàng
                'duan' => $supply->order && $supply->order->project ? [
                    'name' => $supply->order->project->name,
                    'description' => $supply->order->project->description,
                    // Bạn có thể thêm 'segment_id' và thông tin phân khúc nếu cần
                ] : null,
            ];
        });
        return response()->json($data);
    }


    public function KiemTraSoluongTruocKhiNhapKho(Request $request){
        // dd($request->toarray());
        $supplyId = $request->input('supplyId');
        $quantity = $request->input('quantity');

        // Tính số lượng "Đã nhận" từ bảng transactions
        $receivedQuantity = Transaction::where('supply_id', $supplyId)
                                        ->where('loaigiaodich', 'Đã nhận')
                                        ->sum('soluong');

        // Tính số lượng đang kiểm tra chất lượng (status = 0)
        $checkingQuantity = QualityCheck::where('supply_id', $supplyId)
                                         ->where('status', 0)
                                         ->sum('soluongnhapkho');
                                        //  dd($checkingQuantity);
        $totalQuantity = $receivedQuantity + $checkingQuantity;

        // Lấy số lượng tổng khai báo ban đầu từ bảng supplies
        $initialQuantity = Supply::find($supplyId)->soluong;

        $remainingQuantity = $initialQuantity - $totalQuantity;

        // So sánh số lượng nhập với số lượng còn lại
        if ($quantity <= $remainingQuantity) {
            return response()->json(['message' => 'Số lượng nhập hợp lệ.']);
        } else {
            return response()->json(['message' => 'Số lượng nhập vượt quá số lượng còn lại.'], 400);
        }
    }

    public function KiemTraSoluongTruocKhiNhapKhoV2(Request $request){
        $supplyIds = $request->input('supplyIds');
        // dd($supplyIds);
        $results = [];

        foreach ($supplyIds as $supplyId) {
            $receivedQuantity = Transaction::where('supply_id', $supplyId)
                                            ->where('loaigiaodich', 'Đã nhận')
                                            ->sum('soluong');

            $checkingQuantity = QualityCheck::where('supply_id', $supplyId)
                                             ->where('status', 0)
                                             ->sum('soluongnhapkho');

            $totalQuantity = $receivedQuantity + $checkingQuantity;
            $supply = Supply::find($supplyId);
            $initialQuantity = $supply->soluong;
            $remainingQuantity = $initialQuantity - $totalQuantity;

            $results[$supplyId] = $remainingQuantity;
        }

        return response()->json($results);
    }


    public function updateQuanlity(Request $request){
        // dd($request->toarray());
        $suppliesData = $request->input('supplies');

        foreach ($suppliesData as $supplyData) {
            QualityCheck::create([
                'supply_id' => $supplyData['id'],
                'soluongnhapkho' => $supplyData['quantity'],
            ]);
        }

        return response()->json(['success' => 'Dữ liệu đã được cập nhật thành công.']);
    }

    public function quetBarcodeNhapKho(Request $request){
        // Lấy mảng các ID được gửi lên từ request
        $selectedItems = $request->input('selectedItems', []);
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $supplies = Supply::with(['order.project'])->whereIn('id', $selectedItems)->get();
        return view('Warehouse Management.Inside.nhapKhoBarcode', compact('supplies','user'));
    }

    public function checkQuality($id, Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        if ($user->department_id == 3 || $user->is_admin != 1) {
            // Nếu không thỏa mãn, chuyển hướng người dùng trở lại trang trước
            return back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
        $project = Project::with(['orders.supplies.qualityChecks'])->find($id);
        $supplies = [];
        if ($project) {
            foreach ($project->orders as $order) {
                foreach ($order->supplies as $supply) {
                    array_push($supplies, $supply);
                }
            }
        }
        // dd($project->toarray());
        return view('Warehouse Management.Inside.kiemTraChatLuong', compact('id', 'user', 'project', 'supplies'));
    }


    public function luuKiemTraChatLuong(Request $request){
        $qualityCheck = QualityCheck::find($request->idQualityCheck);

        // Kiểm tra xem QualityCheck đã có status là 1 chưa
        if ($qualityCheck->status == 1) {
            // Nếu đã có status là 1, trả về thông báo đã cập nhật
            return response()->json(['success' => false, 'message' => 'Vật tư đã được kiểm tra chất lượng rồi.']);
        }

        // Nếu status chưa là 1, tiếp tục cập nhật
        $qualityCheck->soluongdatchatluong = $request->quantityDat;
        $qualityCheck->status = 1;
        $qualityCheck->ngaykiemtra = Carbon::now()->format('Y-m-d');
        $qualityCheck->save();

        $transaction = new Transaction([
            'supply_id' => $qualityCheck->supply_id,
            'soluong' => $request->quantityDat,
            'loaigiaodich' => 'Đã nhận',
            'ngaygiaodich' => now(),
            'ghichu' => ''
        ]);
        $transaction->save();

        // Bao gồm status mới trong phản hồi
        return response()->json(['success' => true, 'message' => 'Dữ liệu đã được cập nhật thành công.', 'status' => $qualityCheck->status]);
    }

    public function timKiemVatTuCheck(Request $request){
        $idVatTu = $request->idVatTu;
        $status = $request->status;
        $ngayKiemTra = $request->ngayKiemTra; // Thêm biến này
        $query = QualityCheck::query();

        if (!empty($idVatTu)) {
            $query->where('supply_id', $idVatTu);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if (!empty($ngayKiemTra)) {
            // Thêm điều kiện tìm kiếm theo ngày kiểm tra
            $query->whereDate('ngaykiemtra', '=', $ngayKiemTra);
        }

        $results = $query->with(['supply.order'])->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
// TỒN KHO
    public function trangTonKho(Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');
        $orders = Order::all();
        return view('Warehouse Management.Inside.quanLyTonKho', compact('user', 'module', 'orders'));
    }

    public function slectedPhanKhuc(Request $request){
        $thuongHieuId = $request->thuongHieuId;
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
        $projectId = $request->projectId;
        $orders = Order::with(['supplies' => function($query){
            $query->with('transactions');
        }])->where('project_id', $projectId)->get();
        // dd($orders->toarray());
        return response()->json($orders);
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

        $project = null;
        $segment = null;
        $brand = null;
        $ordersQuery = null;

        if ($duAnId) {
            $project = Project::find($duAnId);
        }
        if ($phanKhucId) {
            $segment = Segment::find($phanKhucId);
        }
        if ($thuongHieuId) {
            $brand = Brand::find($thuongHieuId);
        }

        if ($donHangId) {
            // Nếu chỉ có donHangId, tìm đơn hàng đó và lấy thông tin project, segment, brand từ đó
            $order = Order::with(['project.segment.brand', 'supplies.transactions'])->find($donHangId);
            if ($order) {
                $project = $order->project;
                $segment = $project ? $project->segment : null;
                $brand = $segment ? $segment->brand : null;
                $ordersQuery = collect([$order]); // Tạo collection với 1 đơn hàng để giữ cùng format
            }
        } else if ($project) {
            // Nếu có duAnId, lọc đơn hàng theo dự án
            $ordersQuery = $project->orders()->with(['supplies.transactions']);
            if ($donHangId) {
                $ordersQuery = $ordersQuery->where('id', $donHangId);
            }
            $ordersQuery = $ordersQuery->get();
        }

        if (!$ordersQuery) {
            return response()->json(['message' => 'Thông tin đơn hàng không tồn tại'], 404);
        }

        $data = [
            'brand' => $brand ? ['id' => $brand->id, 'name' => $brand->name] : null,
            'segment' => $segment ? ['id' => $segment->id, 'name' => $segment->name] : null,
            'project' => $project ? ['id' => $project->id, 'name' => $project->name] : null,
            'orders' => $ordersQuery->map(function ($order) {
                $tongSoLuongNhan = $order->supplies->reduce(function ($carry, $supply) {
                    // Tính tổng số lượng nhận từ các giao dịch có loại giao dịch là 'Đã nhận'
                    return $carry + $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                }, 0);

                $tongSoLuongXuat = $order->supplies->reduce(function ($carry, $supply) {
                    // Tính tổng số lượng xuất từ các giao dịch có loại giao dịch là 'Đã xuất'
                    return $carry + $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
                }, 0);

                // Tính số lượng tồn dựa trên tổng số lượng nhận và tổng số lượng xuất
                $soluongTon = $tongSoLuongNhan - $tongSoLuongXuat;

                // Trả về thông tin cần thiết của đơn hàng
                return [
                    'id' => $order->id,
                    'sodonhang' => $order->sodonhang,
                    'tongSoLuongNhan' => $tongSoLuongNhan,
                    'tongSoLuongXuat' => $tongSoLuongXuat,
                    'soluongTon' => $soluongTon,
                ];
            }),
        ];

        return response()->json($data);
    }

// XUẤT KHO
    public function listExportWarehouse($id,request $request){
        $sodonhangSelect = $request->get('sodonhangSelect');
        $nhacungcapSelect = $request->get('nhacungcapSelect');
        $nhacungcapSuppeliesSelect = $request->get('nhacungcapSuppeliesSelect');
        $project = Project::with(['orders' => function($query) {
            $query->with(['supplies' => function($query) {
                $query->with(['transactions']);
            }]);
        }])->find($id);
        if (!$project) {
            abort(404, 'Dự án không tìm thấy.');
        }
        $orders = $project->orders->map(function($order) use (&$totalOrdersDanhan, &$totalOrdersChuanhan, &$totalOrdersDaxuat) {
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
        if ($sodonhangSelect || $nhacungcapSelect || $nhacungcapSuppeliesSelect) {
            $orders = $orders->filter(function ($order) use ($sodonhangSelect, $nhacungcapSelect, $nhacungcapSuppeliesSelect) {
                $matchesSodonhang = $sodonhangSelect ? $order->sodonhang == $sodonhangSelect : true;
                $matchesNhacungcap = $nhacungcapSelect ? $order->nhacungcap == $nhacungcapSelect : true;

                $matchesSuppliesStatus = true;
                if ($nhacungcapSuppeliesSelect) {
                    switch ($nhacungcapSuppeliesSelect) {
                        case 'Đã nhận':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu đã nhận
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong') > 0;
                            });
                            break;
                        case 'Chưa nhận':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu chưa nhận
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Chưa nhận')->count() > 0 || $supply->transactions->count() == 0;
                            });
                            break;
                        case 'Đã xuất':
                            // Kiểm tra đơn hàng có ít nhất một vật liệu đã xuất
                            $matchesSuppliesStatus = $order->supplies->some(function ($supply) {
                                return $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong') > 0;
                            });
                            break;
                        default:
                            $matchesSuppliesStatus = true;
                            break;
                    }
                }

                return $matchesSodonhang && $matchesNhacungcap && $matchesSuppliesStatus;
            });
        }
        $brandName = optional(optional($project->segment)->brand)->name;
        $segmentId = $project->segment->id ?? null;
        $segmentName = $project->segment->name ?? null;
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');

        $providers = Provider::with('details')->get();
        $totalSuppliesForProject = $orders->sum('total_supplies');

        return view('Warehouse Management.Inside.xuatKho', compact('totalOrdersDanhan', 'totalOrdersChuanhan', 'totalOrdersDaxuat','orders', 'user', 'providers', 'module', 'segmentId', 'brandName', 'segmentName', 'project', 'totalSuppliesForProject'));
    }


}
