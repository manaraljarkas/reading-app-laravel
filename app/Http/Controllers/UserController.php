<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   public function getAdminInfo($adminId){
  $admin=Auth::user();

  $admins=User::select('email','name','role')
  ->where('role','=','admin')->where('id',$adminId)->get();

  return response()->json(
   $admins
  );
    }

    public function AddAdmin(Request $request){
     $user=Auth::user();

     $validate=$request->validate([
    'name'=>'required|string',
    'email'=>'required|string|email|max:255|unique:users,email',
    'password'=> 'required|string|min:8'
     ]);

     $admin=User::create([
     'name'=>$request->name,
     'email'=>$request->email,
     'password'=>Hash::make($request->password),
     'role'=>'admin'
     ]);

     return response()->json(
     [
    'message'=>'Admin addedd sueccsufly'
     ]
     );
    }

    public function deleteAdmin($adminId){
    $user=Auth::user();
    $admin=User::where('role','=','admin')->where('id','=',$adminId)->delete();

    return response()->json(
    [
    'message'=>'admin deleted successfuly'
    ]
    );
    }
}
