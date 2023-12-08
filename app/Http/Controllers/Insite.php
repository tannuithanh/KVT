<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supply;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\imports\SuppliesImport;

class Insite extends Controller
{
    public function listWarehouse($idProject){
        $project = Project::withCount(['supplies as total_supplies' => function ($query) {
            $query->select(DB::raw("sum(soluong)"));
        }])->find($idProject);

        if (!$project) {
            // Xử lý trường hợp không tìm thấy dự án
        }

        $brandId = $project->brand->id;
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());

        // Lấy tổng số lượng vật tư
        $totalSupplies = $project->total_supplies;

        //Lấy số vật tư theo id dự án
        $supplies = Supply::where('project_id', $idProject)->get();
        // ->map(function ($supply) {
        // $barcodeData = 'Số đơn hàng: ' . $supply->sodonhang . "\n" .
        //                'Tên vật tư: ' . $supply->tenvattu . "\n" .
        //                'Nhà cung cấp: ' . $supply->nhacungcap . "\n" .
        //                'Mã số: ' . $supply->maso . "\n" .
        //                'Đơn vị tính: ' . $supply->donvitinh . "\n" .
        //                'Ngày nhận: ' . $supply->ngaynhan . "\n" .
        //                'Chi phí: ' . $supply->chiphi;

        // $supply->barcodeData = $barcodeData;
        // return $supply;
        // });

        return view('Warehouse Management.Inside.quanlykehoach', compact('user', 'brandId', 'project', 'totalSupplies','supplies'));
    }
    public function importSupplies(Request $request){
        $project_id = $request['project_id'];
        // Lấy tên file và phân tách để lấy thông tin
        $filename = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
        [$sodonhang, $nhacungcap, $chiphi] = explode('_', $filename);

        // Kiểm tra và cắt bỏ phần mở rộng từ $chiphi
        if (($pos = strpos($chiphi, '.')) !== false) {
            $chiphi = substr($chiphi, 0, $pos);
        }

        // Tạo instance của SuppliesImport với các thông tin từ tên file
        $import = new SuppliesImport($sodonhang, $nhacungcap, $chiphi, $project_id);

        // Thực hiện import
        Excel::import($import, $request->file('file'));

        // Trả về response
        return back()->with('success', 'Dữ liệu đã được nhập thành công!');
    }


}
