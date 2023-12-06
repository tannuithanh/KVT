<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brand;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Outsite extends Controller
{
    public function listBrand(){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());

        // Truy vấn tất cả brands
        $brands = Brand::with(['projects' => function($query) {
            // Đối với mỗi dự án, tải thông tin vật tư liên quan và đếm tổng số lượng vật tư
            $query->withCount(['supplies as supplies_total' => function($query) {
                $query->select(DB::raw("sum(soluong)"));
            }]);
        }])
        ->withCount('projects') // Đếm số lượng dự án cho mỗi thương hiệu
        ->get();

        foreach ($brands as $brand) {
            $brandTotalSupplies = 0;
            foreach ($brand->projects as $project) {
                // Tính tổng số lượng vật tư của mỗi dự án
                $brandTotalSupplies += $project->supplies_total;
            }
            // Gán tổng số lượng vật tư của tất cả các dự án cho mỗi thương hiệu
            $brand->totalSupplies = $brandTotalSupplies;
        }
        return view('Warehouse Management.Outside.brand', compact('user', 'brands'));
    }

    public function Project($brandId)
    {
        $user = Auth::User();
        $projects = Project::where('brand_id', $brandId)->get();
        $brand = Brand::find($brandId);
        return view('Warehouse Management.Outside.project', compact('projects', 'user', 'brandId', 'brand'));
    }

    public function addProject(Request $request)
    {
        $data = $request->only(['name', 'description', 'brand_id']);
        $project = Project::create($data);

        if ($project) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function editProject(Request $request)
    {
        $project = Project::find($request->id);

        if ($project) {
            $project->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function deleteProject(Request $request)
    {
        $project = Project::find($request->id);

        if ($project) {
            $project->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
