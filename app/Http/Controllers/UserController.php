<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->select('id', 'name', 'email')
            ->paginate(5);

        if ($admins->count() > 0) {
            return response()->json([
                'message' => 'Admins retrieved successfully.',
                'data' => $admins
            ]);
        } else {
            return response()->json([
                'message' => 'No admins found.',
                'data' => $admins
            ]);
        }
    }

    public function show($adminId)
    {
        $user = Auth::user();

        $admins = User::select('email', 'name', 'role')->where('role', '=', 'admin')->where('id', $adminId)->first();

        return response()->json([
            'success' => true,
            'data' => $admins
        ]);
    }

    public function store(StoreAdminRequest $request)
    {
        $user = Auth::user();
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_password_changed' => false
        ]);

        return response()->json([
            'message' => 'Admin addedd sueccsufly',
            'data' => $admin
        ]);
    }

    public function destroy($adminId)
    {
        $user = Auth::user();
        $admin = User::where('role', 'admin')->where('id', $adminId)->first();
        if($admin){
            $admin->delete();
            return response()->json(['message' => 'Admin deleted successfully']);
        }
        else {
            return response()->json(['message' => 'Admin not found Or not true role'], 404);
        }

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
        $admin = User::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $admin->update($data);

        return response()->json([
            'message' => 'Admin updated successfully',
            'data' => $admin
        ], 200);
    }
    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = User::where('role', 'admin')->select('id', 'name', 'email');
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        $admins = $query->paginate(5)->through(function ($admin) {
            return [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email
            ];
        });

        return response()->json(['admins' => $admins]);
    }

    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!$request->filled('new_password')) {
            return response()->json(['message' => 'No password provided.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
            'is_password_changed' => true,
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
            'data' => $user,
        ]);
    }
}
