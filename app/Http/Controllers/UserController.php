<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAdmins()
    {
        $user = Auth::user();
        $admins = User::where('role', '=', 'admin')->select('id', 'name', 'email')->paginate(10);

        return response()->json([$admins]);
    }

    public function getAdminInfo($adminId)
    {
        $user = Auth::user();

        $admins = User::select('email', 'name', 'role')->where('role', '=', 'admin')->where('id', $adminId)->get();

        return response()->json($admins);
    }

    public function AddAdmin(Request $request)
    {
        $user = Auth::user();

        $validate = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return response()->json(['message' => 'Admin addedd sueccsufly']);
    }

    public function deleteAdmin($adminId)
    {
        $user = Auth::user();
        $admin = User::where('role', 'admin')->where('id', $adminId)->first();
        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }
        $admin->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }
}
