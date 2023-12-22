<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supply;
use App\Models\Project;
use App\Models\Provider;
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
    public function listWarehouse($idProject,Request $request){
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
        return view('Warehouse Management.Inside.quanlykehoach', compact('user', 'providers','module','segmentId', 'brandName', 'segmentName', 'project', 'totalSupplies', 'supplies'));
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
            DB::commit();
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
            'sodonhang' => 'required|string',
            'tenvattu' => 'required|string',
            'nhacungcap' => 'required|string',
            'noidungphancum' => 'nullable|string',
            'donvitinh' => 'required|string',
            'soluong' => 'required|integer',
            'chiphi' => 'required|string',
            'note' => 'required|string',
        ]);
        $supply = new Supply([
            'project_id' => $validatedData['project_id'],
            'sodonhang' => $validatedData['sodonhang'],
            'tenvattu' => $validatedData['tenvattu'],
            'nhacungcap' => $validatedData['nhacungcap'],
            'noidungphancum' => $validatedData['noidungphancum'],
            'donvitinh' => $validatedData['donvitinh'],
            'soluong' => $validatedData['soluong'],
            'chiphi' => $validatedData['chiphi'],
            'stt' => 1,
            'ngaynhan' => null,
            'note' => $validatedData['note'],
        ]);
        // Tạo và lưu vật tư mới
        $supply = new Supply($validated);
        $supply->save();

        // Chuyển hướng người dùng hoặc trả về response
        return redirect()->back()->with('success', 'Vật tư đã được thêm thành công');
    }


}
