<?php

namespace App\Http\Controllers;

use App\Events\ProfileUpdated;
use App\Http\Requests\LoginRequest;
use App\http\Requests\ProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Mail\WelcomeMail;
use App\Models\Reader;
use App\Models\User;
use App\Services\PermissionService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;



class AuthController extends Controller
{

    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
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

    // public function login(LoginRequest $request)
    // {
    //     if (!Auth::attempt($request->only('email', 'password'))) {
    //         return response()->json([
    //             'message' => 'Invalid email or password'
    //         ], 401);
    //     }

    //     $user = User::where('email', $request->email)->firstOrFail();
    //     $token = $user->createToken('auth_token')->plainTextToken;
    //     $reader = $user->reader;

    //     if (!$reader) {
    //         return response()->json([
    //             'message' => 'Login successfully but profile not found for this user.',
    //             'token' => $token
    //         ], 200);
    //     }

    //     return response()->json([
    //         'message' => 'Login successfully',
    //         'first_name' => $reader->first_name,
    //         'last_name' => $reader->last_name,
    //         'picture' => $reader->picture,
    //         'nickname' => $reader->nickname,
    //         'token' => $token
    //     ], 200);
    // }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Update FCM token if provided in the request
        if ($request->has('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $reader = $user->reader;

        if (!$reader) {
            return response()->json([
                'message' => 'Login successfully but profile not found for this user.',
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


    public function webLogin(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        $permissions = $this->permissionService->getUserPermissionMap($user);

        return response()->json([
            'message' => 'Login successfully',
            'token' => $token,
            'role' => $user->role,
            'permissions' => $permissions,
        ], 200);
    }

    // public function saveProfile(ProfileRequest $request)
    // {
    //     $userId = Auth::id();
    //     if (!$userId) {
    //         return response()->json(['message' => 'Unauthenticated'], 401);
    //     }

    //     $hasProfile = Reader::where('user_id', $userId)->exists();

    //     $validated = $request->validated();

    //     if (!$hasProfile) {
    //         if (empty($validated['first_name']) || empty($validated['last_name'])) {
    //             return response()->json([
    //                 'message' => 'First name and last name are required for new profile.'
    //             ], 422);
    //         }
    //     }

    //     if ($request->hasFile('picture')) {
    //         $uploadResult = Cloudinary::uploadApi()->upload(
    //             $request->file('picture')->getRealPath(),
    //             ['folder' => 'reading-app/profiles']
    //         );
    //         $validated['picture'] = $uploadResult['secure_url'];
    //     }

    //     $reader = Reader::updateOrCreate(
    //         ['user_id' => $userId],
    //         $validated
    //     );

    //     if ($reader->wasRecentlyCreated) {
    //         return response()->json(['message' => 'Profile created successfully.'], 201);
    //     }

    //     event(new ProfileUpdated($userId, array_keys($validated)));
    //     return response()->json(['message' => 'Profile updated successfully.'], 200);
    // }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout Successfully']);
    }

    public function setupProfile(StoreProfileRequest $request)
    {
        $userId = Auth::user()->id;

        if (Reader::where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Profile already exists.'], 409);
        }

        $validated = $request->validated();
        $validated['user_id'] = $userId;

        if ($request->hasFile('picture')) {
            $uploadResult = Cloudinary::uploadApi()->upload(
                $request->file('picture')->getRealPath(),
                ['folder' => 'reading-app/profiles']
            );
            $validated['picture'] = $uploadResult['secure_url'];
        }

        $profile = Reader::create($validated);

        return response()->json(['message' => 'Profile created successfully.'], 201);
    }

    public function editProfile(UpdateProfileRequest $request)
    {
        $userId = Auth::id();
        $reader = Reader::where('user_id', $userId)->firstOrFail();

        if ($reader->user_id != $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();
        $reader->fill($validated);

        if ($request->hasFile('picture')) {
            $uploadResult = Cloudinary::uploadApi()->upload(
                $request->file('picture')->getRealPath(),
                ['folder' => 'reading-app/profiles']
            );
            $reader->picture = $uploadResult['secure_url'];
        }

        if ($reader->save()) {
            event(new ProfileUpdated($userId, array_keys($validated)));
            return response()->json(['message' => 'Profile updated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Some error happened.'], 500);
        }
    }
}
