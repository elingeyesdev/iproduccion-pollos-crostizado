<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuar;   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'message'=>'credenciales incorrectas'
                ],401);
        }

        $user = Auth::user();
        $token = $user->createToken('flutter')->plainTextToken;
        return response()->json([
            'token'=>$token,
            'user'=>$user,
        ],200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
