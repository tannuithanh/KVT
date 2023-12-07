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

        Excel::import(new SuppliesImport, $request->file('file'));
        dd($tan->toarray());
        return back()->with('success', 'Dữ liệu đã được nhập thành công!');
    }
}
