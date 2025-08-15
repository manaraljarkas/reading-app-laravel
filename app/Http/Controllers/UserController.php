<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
}
