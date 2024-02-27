<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supply;
use App\Models\Project;
use App\Models\Provider;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\imports\SuppliesImport;
use Carbon\Carbon;



class Insite extends Controller
{
// QUẢN LÝ KẾ HOẠCH
    public function listWarehouse($idProject, Request $request) {
        // dd($request->toarray());
        $project = Project::with(['orders' => function($query) {
            $query->withSum('supplies as total_supplies', 'soluong');
        }])->find($idProject);

        if (!$project) {
            abort(404, 'Dự án không tìm thấy.');
        }
        $totalSuppliesForProject = $project->orders->sum('total_supplies');
        if (!$project) {
            abort(404, 'Dự án không tìm thấy.');
        }

        $brandName = optional(optional($project->segment)->brand)->name;
        $segmentId = $project->segment->id ?? null;
        $segmentName = $project->segment->name ?? null;

        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());

        // Lấy tổng số lượng vật tư thông qua đơn hàng
        $totalSupplies = $project->total_supplies;

        // Lấy số vật tư thông qua các đơn hàng của dự án
        $orders = Order::where('project_id', $idProject)->with('supplies')->get();
        $totals = [];
        foreach ($orders as $order) {
            $totalSupplies = $order->supplies->sum('soluong');
            $totals[$order->id] = $totalSupplies;
        }

        $supplies = $orders->flatMap->supplies;
        // dd($totalSupplies->toarray());
        $module = $request->query('module', 'defaultModule');
        $providers = Provider::with('details')->get();
        return view('Warehouse Management.Inside.quanlykehoach', compact('totals','orders', 'user', 'providers', 'module', 'segmentId', 'brandName', 'segmentName', 'project', 'totalSupplies', 'supplies','totalSuppliesForProject'));
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
        // dd($request->toarray());
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




// NHẬP KHO
    public function listNhapKho($idProject,Request $request){
        $project = Project::with('segment.brand')->withCount(['supplies as total_supplies' => function ($query) {
            $query->select(DB::raw("sum(soluong)"));
        }])->find($idProject);

        if (!$project) {
            // Xử lý trường hợp không tìm thấy dự án
            abort(404, 'Dự án không tìm thấy.');
        }

        // Lấy thông tin thương hiệu và phân khúc
        $brandName = optional(optional($project->segment)->brand)->name;
        $segmentId = $project->segment->id ?? null;
        $segmentName = $project->segment->name ?? null;

        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());

        // Lấy tổng số lượng vật tư
        $totalSupplies = $project->total_supplies;

        // Lấy số vật tư theo id dự án
        $supplies = Supply::where('project_id', $idProject)->get();
        $module = $request->query('module', 'defaultModule');
        $providers = Provider::with('details')->get();
        return view('Warehouse Management.Inside.nhapkho', compact('user', 'providers','module','segmentId', 'brandName', 'segmentName', 'project', 'totalSupplies', 'supplies'));
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
}
