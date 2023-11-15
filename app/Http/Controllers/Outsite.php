<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brand;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Outsite extends Controller
{
    public function listBrand(){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        $brand = Brand::all();
        return view('Warehouse Management.Outside.brand',compact('user','brand'));
    }

    public function Project($brandId) {
        $user = Auth::User();
        $projects = Project::where('brand_id', $brandId)->get();
        $brand = Brand::find($brandId);
        return view('Warehouse Management.Outside.project',compact('projects', 'user','brandId','brand'));
    }

    public function addProject(Request $request){
        $data = $request->only(['name', 'description', 'brand_id']);
        $project = Project::create($data);

        if ($project) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function editProject(Request $request){
        $project = Project::find($request->id);

        if ($project) {
            $project->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function deleteProject(Request $request){
        $project = Project::find($request->id);

        if ($project) {
            $project->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
