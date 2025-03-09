<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Admin');
        })->get();
        $name = Auth::user()->name;
        return view('admin.auth.User.index', compact('users', 'name'));
    }
    public function create()
    {
        $name = Auth::user()->name;
        return view('admin.auth.user.create', compact('name'));
    }
    public function store(UserRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->save();
            return redirect()->route('user.index')->with('status', 'Thêm nhân sự thành công!');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra, vui lòng thử lại.']);
        }
    }
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            $name = Auth::user()->name;
            return view("admin.auth.user.edit", compact("name", "user"));
        } catch (Exception $e )
        {
            Log::debug($e->getMessage());
        }
    }
    public function update(UserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->name = $request->input("name");
            $user->email = $request->input("email");
            $user->password = Hash::make($request->input('password'));
            $user->save();
            return redirect()->route("user.index")->with("status", "Cập nhật nhân sự thành công!!!");
        } catch (Exception $e)
        {
            Log::debug($e->getMessage());
        }
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back();
    }
    public function toggleLock($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_locked = $user->is_locked ? 0 : 1;
            $user->save();
            return redirect()->route("user.index")->with("status", "Cập nhật trang thái tài khoản thành công!!!");
        } catch (Exception $e)
        {
            Log::debug($e->getMessage());
        }
    }
    public function phanvaitro($id)
    {
        $name = Auth::user()->name;
        $user = User::findOrFail($id);
        $roles = Role::all();
        $permission = Permission::all();
        $all_colum_roles = $user->roles()->first(); // Lấy vai trò hiện tại của người dùng
        return view('admin.auth.user.role.phanvaitro', compact('user', 'roles', 'all_colum_roles', 'permission', 'name'));
    }
    public function storeRole(Request $request, $id)
    {
        $data = $request->all();
        $user = User::find($id);
        $user->syncRoles($data['role']);
        $role_id = $user->roles()->first();
        return redirect()->route('user.index')->with("status", 'Cấp quyền thành công');
    }
    public function phanquyen($id)
    {
        $name = Auth::user()->name;
        $user = User::findOrFail($id);
        $permission = Permission::all();
        $name_role = $user->roles()->first()->name;
        $get_permission_via_role = $user->getPermissionsViaRoles();
        return view('admin.auth.user.permissions.phanquyen', compact('user', 'permission', 'name_role', 'get_permission_via_role', 'name'));
    }
    public function storePermission($id, Request $request)
    {   
        $data = $request->all();
        $user = User::find($id);
        $role_id = $user->roles()->first()->id;
        $role = Role::find($role_id);
        $role->syncPermissions([$data['permissions']]);
        return redirect()->route('user.index')->with("status", "Cập nhật quyền thành công");
    }
    public function createRole()
    {
        $users = User::paginate(10);
        return view('admin.auth.user.role.phanvaitro', compact('users'));
    }
    public function storeRoles(Request $request)
    {
        $data = $request->all();
        Role::create(['name' => $data['role']]);
        return redirect()->back();
    }
    public function createPermisstions()
    {
        $users = User::paginate(10);
        return view('admin.auth.user.role.phanvaitro', compact('users'));
    }
    public function Permissions(Request $request)
    {
        $data = $request->all();
        Permission::create(['name' => $data['permission']]);
        return redirect()->back();
    }
}
