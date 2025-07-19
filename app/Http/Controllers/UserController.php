<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $admins = User::where('role', '=', 'admin')->
        select('id', 'name', 'email')->paginate(10);

        return response()->json([$admins]);
    }

    public function show($adminId)
    {
        $user = Auth::user();

        $admins = User::select('email', 'name', 'role')->where('role', '=', 'admin')->where('id', $adminId)->first();

        return response()->json([
        'success'=>true,
        'data'=> $admins]);
    }

    public function store(StoreAdminRequest $request)
    {
        $user = Auth::user();
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return response()->json(['message' => 'Admin addedd sueccsufly']);
    }

    public function destroy($adminId)
    {
        $user = Auth::user();
        $admin = User::where('role', 'admin')->where('id', $adminId)->first();
        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }
        $admin->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }

    public function getAdmin()
    {
        $user = Auth::user();
        if ($user->role == 'admin' || $user->role == 'superAdmin') {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user?->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized: You are not an admin or superAdmin.'
        ], 403);
    }

    public function update(UpdateAdminRequest $request, $id)
    {
        $user = Auth::user();
        $admin = User::findOrFail($id);

        if ($request->has('name')) {
            $admin->name = $request->name;
        }
        if ($request->has('email')) {
            $admin->email = $request->email;
        }
        if ($request->has('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $admin->name,
                'email' => $admin->email,
            ]
        ]);
    }
}
