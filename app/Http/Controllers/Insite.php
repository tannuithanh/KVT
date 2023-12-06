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

        return view('Warehouse Management.Inside.quanlykehoach', compact('user', 'brandId', 'project', 'totalSupplies'));
    }


    public function importSupplies(Request $request){
        $file = $request->file('file-upload');
        $filename = $file->getClientOriginalName();

        // Kiểm tra tên file
        if (substr_count($filename, '_') !== 2) {
            return response()->json(['error' => 'Tên file không hợp lệ'], 422);
        }

        // Phân tích tên file
        [$sodonhang, $nhacungcap, $chiphi] = explode('_', pathinfo($filename, PATHINFO_FILENAME));

        // Đọc file Excel
        Excel::import(new class($sodonhang, $nhacungcap, $chiphi) implements ToCollection {
        private $sodonhang;
        private $nhacungcap;
        private $chiphi;

        public function __construct($sodonhang, $nhacungcap, $chiphi) {
            $this->sodonhang = $sodonhang;
            $this->nhacungcap = $nhacungcap;
            $this->chiphi = $chiphi;
        }

        public function collection(Collection $rows)
        {
            foreach ($rows as $index => $row) {
                if ($index < 2) continue; // Bỏ qua 2 dòng đầu

                // Tạo nội dung cho barcode
                $barcodeContent = "Nội dung cụ thể cho barcode"; // Cần thay đổi theo yêu cầu

                $barcodeFileName = 'barcode_' . uniqid() . '.png';
                $barcodePath = public_path('barcode/' . $barcodeFileName);
                QrCode::format('png')->size(200)->encoding('UTF-8')->generate($barcodeContent, $barcodePath);

                $supply = new Supply([
                    'sodonhang' => $this->sodonhang,
                    'nhacungcap' => $this->nhacungcap,
                    'chiphi' => $this->chiphi,
                    // ... các trường thông tin khác
                    'barcode' => $barcodeFileName,
                ]);

                $supply->save();
            }
            }
        }, $file);

        return response()->json(['success' => 'Dữ liệu đã được nhập thành công']);
    }
}
