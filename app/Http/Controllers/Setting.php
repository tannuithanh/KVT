<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Position;
use App\Models\Department;
use App\Models\AppFunction;
use App\Models\Provider;
use Illuminate\Http\Request;
use App\Models\ProviderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Setting extends Controller
{
    public function profile(){
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        // dd($user->appFunction->toarray());
        return view('Setting.profile',compact('user'));
    }
    public function changePassword(Request $request){
        $user = Auth::user();

        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json(['error' => 'Mật khẩu cũ không đúng.']);
        }

        if ($request->input('newpassword') !== $request->input('renewpassword')) {
            return response()->json(['error' => 'Mật khẩu mới và xác nhận mật khẩu không khớp.']);
        }

        $user->password = Hash::make($request->input('newpassword'));
        $user->save();

        return response()->json(['success' => 'Mật khẩu đã được thay đổi thành công.']);
    }

    public function setting(){
        $position = Position::all();
        $departments = Department::all();
        $AppFunction = AppFunction::all();
        $providers = Provider::all();
        $user = User::with('department', 'position', 'appFunction')->find(Auth::id());
        return view('Setting.setting',compact('user','position','departments','AppFunction','providers'));
    }

    public function listDepartment() {
        $departments = Department::all();
        return response()->json($departments);
    }

    public function listPosition() {
        $positions = Position::all();
        return response()->json($positions);
    }

    public function editDepartment(Request $request){
        $validatedData = $request->validate([
            'id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
        ]);
        $department = Department::find($validatedData['id']);
        $department->name = $validatedData['name'];
        $department->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công']);
    }

    public function listUser(){
        $users = User::with('department', 'position', 'appFunction')->get();
        return response()->json($users);
    }

    public function editUser(Request $request){
        $user = User::where('msnv', $request->msnv)->first();

        if ($user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'function_id' => $request->app_function_id
            ]);
            return response()->json(['message' => 'Cập nhật thành công!'], 200);
        } else {
            return response()->json(['message' => 'Không tìm thấy người dùng!'], 404);
        }
    }

    public function deleteUser(Request $request){
        try {
            $user = User::find($request->id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Người dùng không tồn tại.']);
            }

            $user->delete();

            return response()->json(['success' => true, 'message' => 'Người dùng đã được xóa thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa người dùng.']);
        }
    }

    public function addUser(Request $request){
        try {
            // Tạo một người dùng mới
            $user = new User();
            $user->name = $request->input('name');
            $user->msnv = $request->input('msnv');
            $user->email = $request->input('email');
            $user->department_id = $request->input('department_id');
            $user->position_id = $request->input('position_id');
            $user->function_id = $request->input('function_id');
            $user->password = bcrypt('123456');
            // Nếu bạn muốn thêm mật khẩu hoặc các trường khác, bạn có thể thêm ở đây
            $user->save();

            // Trả về phản hồi thành công
            return response()->json(['success' => true, 'message' => 'Người dùng đã được thêm thành công.']);
        } catch (\Exception $e) {
            // Trả về phản hồi lỗi
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function listProviderDetail(){
        $providerDetails = ProviderDetail::with('provider')->get();
        return response()->json($providerDetails);
    }

    public function addProviderDetail(Request $request){
        $data = $request->all();
        ProviderDetail::create([
            'name' => $data['name'],
            'describe' => $data['describe'],
            'provider_id' => $data['provider_id']
        ]);
        return response()->json(['message' => 'Thêm nhà cung cấp thành công!']);
    }

    public function editProviderDetail(Request $request) {
        $data = $request->all();

        $providerDetail = ProviderDetail::find($data['id']);
        if (!$providerDetail) {
            return response()->json(['message' => 'Nhà cung cấp không tồn tại!'], 404);
        }

        $providerDetail->update([
            'name' => $data['name'],
            'describe' => $data['describe'],
            'provider_id' => $data['provider_id']
        ]);

        return response()->json(['message' => 'Cập nhật nhà cung cấp thành công!']);
    }

    public function deleteProviderDetail(Request $request){
        $id = $request->input('id');
        ProviderDetail::find($id)->delete();
        return response()->json(['message' => 'Xóa nhà cung cấp thành công!']);
    }

}
