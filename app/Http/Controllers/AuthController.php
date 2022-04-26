<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Requests\RegisterRequest;

use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        return response()->json($this->response($user), 201);
    }

    protected function validateLogin(Request $request){
        $this->validate($request,[
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
    }
    
    public function response($user){
        return [
            'user' => $user,
            'token' => $user->createToken('AppName')->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(Carbon::now()->addMinute(6))->toDateTimeString()
        ];
    }

    public function login(Request $request){
        $this->validateLogin($request);
        if (!Auth::attempt( request(['email', 'password']) )) {
            return $this->unauthorized();
        }
        $user = Auth::user();
        return response()->json($this->response($user), 200);
    }

    public function logout( Request $request ) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' =>'session was closed']);
    }

    public function unauthorized() { 
        return response()->json(['message' =>"unauthorized"], 401); 
    }
}