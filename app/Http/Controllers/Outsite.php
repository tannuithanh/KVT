<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brand;
use App\Models\Project;
use App\Models\Segment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Outsite extends Controller
{
    public function listBrand(Request $request){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
    
        // Truy vấn tất cả brands
        $brands = Brand::with(['segments.projects.supplies'])
            ->get();
    
        foreach ($brands as $brand) {
            $brandTotalSupplies = 0;
    
            foreach ($brand->segments as $segment) {
                foreach ($segment->projects as $project) {
                    // Tính tổng số lượng vật tư của mỗi dự án
                    $projectTotalSupplies = $project->supplies->sum('soluong');
                    $brandTotalSupplies += $projectTotalSupplies;
                }
            }
    
            // Gán tổng số lượng vật tư của tất cả các dự án trong tất cả các phân khúc cho mỗi thương hiệu
            $brand->totalSupplies = $brandTotalSupplies;
        }
    
        $module = $request->query('module', 'defaultModule');
        return view('Warehouse Management.Outside.brand', compact('user', 'brands', 'module'));
    }
    

    public function Project($segmentId, Request $request)
    {
        $module = $request->query('module', 'defaultModule');
        $user = Auth::user();
        $projects = Project::where('segment_id', $segmentId)->get();

        // Lấy thông tin phân khúc, bao gồm cả thương hiệu liên quan
        $segment = Segment::with('brand')->find($segmentId);
        $brand = $segment ? $segment->brand : null;

        // Lấy tên phân khúc
        $segmentName = $segment ? $segment->name : '';

        return view('Warehouse Management.Outside.project', compact('projects', 'user', 'segment', 'brand', 'segmentName', 'module'));
    }

    
    
    public function addProject(Request $request)
    {
        // Cập nhật dữ liệu để lấy segment_id
        $data = $request->only(['name', 'description', 'segment_id']);
        
        // Tạo dự án mới với segment_id
        $project = new Project($data);
        $project->segment_id = $data['segment_id']; // Đảm bảo rằng bạn đang set segment_id
        $saved = $project->save();
    
        if ($saved) {
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
