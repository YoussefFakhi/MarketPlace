<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Auth;
use Hash;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make(value: $request->password),
                'role' => $request->role ?? 'client', // ← add this

        ]);
        Auth::login($user);

        return new UserResource($user);
    }

    public function login(LoginRequest $request){
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json(['message'=>'invalid credentials'],401);
        }
        // return response()->json();
        $user = User::where('email',$request->email)->first();
        // the method that ISMAIL shows me
        $token = $user->createToken('login-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user)
        ]);// Return both token and user data

        // return $token;
        // return new UserResource(Auth::user());
    }

    // we will use it for user profile
    public function user(){
        return  new UserResource(Auth::user());
    }



    public function logout(Request $request){
        // Auth::logout();
        // request()->session()->invalidate();
        // request()->session()->regenerateToken();
        $user= $request->user();
        $user->currentAccessToken()->delete();

        return response()->json(['message'=>'logged out']);

    }

}
