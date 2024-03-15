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
        $order = Order::with('supplies.transactions')->find($orderId);

        if ($order) {
            // Xóa các transactions liên quan đến supplies của đơn hàng
            foreach ($order->supplies as $supply) {
                foreach ($supply->transactions as $transaction) {
                    $transaction->delete();
                }
            }

            // Xóa tất cả supplies của đơn hàng
            $order->supplies()->delete();

            // Cuối cùng, xóa đơn hàng
            $order->delete();

            return response()->json(['success' => 'Đơn hàng và vật tư liên quan đã được xóa thành công.']);
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

            // Kiểm tra và xóa các transactions liên quan trước khi xóa vật tư
            $transactions = Transaction::where('supply_id', $supplyId)->get();
            if ($transactions) {
                foreach ($transactions as $transaction) {
                    $transaction->delete();
                }
            }

            // Xóa vật tư sau khi đã xóa các transactions liên quan
            $supply = Supply::find($supplyId);
            if ($supply) {
                $supply->delete();
                 DB::commit(); // Commit transaction nếu không có lỗi
                return response()->json(['success' => true, 'message' => 'Vật tư và các giao dịch liên quan đã được xóa thành công.']);
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
        // dd($request->toarray());
        $id = $request->id;
        $newQuantity = $request->soluong;
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
        ]);


        return response()->json([
            'success' => 'Cập nhật vật tư thành công.',
            'barcodeHtml' => $barcodeHtml,
            'id' => $supply->id,
            'danhan' => $danhan,
            'daxuat' => $daxuat,
            'chuanhan' => $chuanhan,
        ]);
    }

    public function lichsuvattu(Request $request){
        $supplyId = $request->id;
        $transactions = Transaction::where('supply_id', $supplyId)->get();
        // dd($transactions->toarray());
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
        // dd($request->toarray());
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
        $supplies = Supply::whereIn('id', $ids)->get();

        $data = $supplies->map(function($supply) {
            $barcodeHtml = BarcodeDNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33);
            return [
                'tenvattu' => $supply->tenvattu,
                'maso' => $supply->maso,
                'donvitinh' => $supply->donvitinh,
                'soluong' => $supply->soluong,
                'barcode' => $barcodeHtml,
            ];
        });
        return response()->json($data);
    }

// TỒN kho
    public function trangTonKho(Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $module = $request->query('module', 'defaultModule');
        return view('Warehouse Management.Inside.quanLyTonKho', compact('user','module'));}

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

    public function listTonKho(Request $request){
        $duAnId = $request->duAnId;
        $phanKhucId = $request->phanKhucId;
        $thuongHieuId = $request->thuongHieuId;
        $donHangId = $request->donHangId;

        // Lấy thông tin dự án, phân khúc, và thương hiệu dựa trên ID
        $project = Project::find($duAnId);
        $segment = Segment::find($phanKhucId);
        $brand = Brand::find($thuongHieuId);

        if (!$project || !$segment || !$brand) {
            return response()->json(['message' => 'Thông tin không tồn tại'], 404);
        }

        $ordersQuery = $project->orders();

        // Nếu donHangId có giá trị, chỉ lấy các supplies từ đơn hàng đó
        if ($donHangId) {
            $ordersQuery = $ordersQuery->where('id', $donHangId);
        }

        $orders = $ordersQuery->with(['supplies' => function($query) {
            $query->with(['transactions']);
        }])->get();

        $data = [
            'brand' => [
                'id' => $brand->id,
                'name' => $brand->name,
            ],
            'segment' => [
                'id' => $segment->id,
                'name' => $segment->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'sodonhang' => $order->sodonhang,
                        'nhacungcap' => $order->nhacungcap,
                        'chiphi' => $order->chiphi,
                        'noidung' => $order->noidung,
                        'ghichu' => $order->ghichu,
                        'supplies' => $order->supplies->map(function ($supply) {
                            $danhan = $supply->transactions->where('loaigiaodich', 'Đã nhận')->sum('soluong');
                            $daxuat = $supply->transactions->where('loaigiaodich', 'Đã xuất')->sum('soluong');
                            $soLuongTon = $danhan - $daxuat;
                            return [
                                'id' => $supply->id,
                                'tenvattu' => $supply->tenvattu,
                                'maso' => $supply->maso,
                                'donvitinh' => $supply->donvitinh,
                                'soluongTon' => $soLuongTon,
                                'note' => $supply->note,
                                'status' => $supply->status,
                            ];
                        }),
                    ];
                }),
            ]
        ];

        return response()->json($data);
    }


    public function selectedDonHang(Request $request){
        $projectId = $request->projectId;
        $orders = Order::where('project_id', $projectId)->get();
        // dd($orders->toarray());
        return response()->json($orders);
    }

    public function quetBarcodeNhapKho(Request $request){
        // Lấy mảng các ID được gửi lên từ request
        $selectedItems = $request->input('selectedItems', []);
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $supplies = Supply::with('order')->whereIn('id', $selectedItems)->get();
        // dd($supplies->toArray());
        return view('Warehouse Management.Inside.nhapKhoBarcode', compact('supplies','user'));
    }

}
