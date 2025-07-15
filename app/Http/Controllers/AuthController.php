<?php

namespace App\Http\Controllers;

use App\Events\ProfileUpdated;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Mail\WelcomeMail;
use App\Models\Reader;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\PermissionService;



class AuthController extends Controller
{

    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8'
        ]);
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        //Mail::to($user->email)->send(new WelcomeMail($user));
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'User Registered Successfully.',
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        $reader = $user->reader;

        if (!$reader) {
            return response()->json([
                'message' => 'Login successfully But Profile not found for this user.',
                'token' => $token
            ], 200);
        }

        return response()->json([
            'message' => 'Login successfully',
            'first_name' => $reader->first_name,
            'last_name' => $reader->last_name,
            'picture' => $reader->picture,
            'nickname' => $reader->nickname,
            'token' => $token
        ], 200);
    }

    public function webLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(
                ['message' => 'invalid email or password'],401);
        }
        $user = User::where('email', $request->email)->FirstOrFail();
        $token = $user->createToken('auth_Token')->plainTextToken;
        $permissions = $this->permissionService->getUserPermissionMap($user);
        return response()->json([
            'message' => 'Login Successfully',
            'token' => $token,
            'permissions' => $permissions,
        ], 200);
    }

    public function setupProfile(StoreProfileRequest $request)
    {
        $userId = Auth::user()->id;
        $validated = $request->validated();
        $validated['user_id'] = $userId;
        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('images/readers', 'public');
            $validated['picture'] = $path;
        }
        $profile = Reader::create($validated);
        return response()->json(['message' => 'Profile created successfully.'], 201);
    }

    public function editProfile(UpdateProfileRequest $request)
    {
        $userId = Auth::id();

        $reader = Reader::where('user_id', $userId)->firstOrFail();

        if ($reader->user_id != $userId) {
            return response()->json(['message' => 'Unauthurized'], 403);
        }

        $validated = $request->validated();

        $reader->fill($validated);

        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('images/readers', 'public');
            $reader->picture = $path;
        }

        if ($reader->save()) {
            event(new ProfileUpdated($userId, array_keys($validated)));
            return response()->json(['message' => 'Profile updated successfully.'], 200);
        } else {
            return response()->json(['message' => 'some error happened.'], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout Successfully']);
    }
}
