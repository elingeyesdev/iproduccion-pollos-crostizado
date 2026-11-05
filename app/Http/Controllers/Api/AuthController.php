<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuar;   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/user',
    summary: 'Obtener el usuario autenticado',
    security: [['sanctum' => []]],
    tags: ['Autenticación'],
    responses: [
        new OA\Response(response: 200, description: 'Usuario autenticado obtenido correctamente'),
        new OA\Response(response: 401, description: 'No autenticado'),
    ]
)]
class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/login',
        summary: 'Iniciar sesión y obtener token',
        tags: ['Autenticación'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@krosty.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Secreto123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Inicio de sesión correcto',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: '1|abcdef123456'),
                        new OA\Property(property: 'user', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciales incorrectas'),
            new OA\Response(response: 429, description: 'Demasiados intentos'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/logout',
        summary: 'Cerrar sesión (revocar token actual)',
        security: [['sanctum' => []]],
        tags: ['Autenticación'],
        responses: [
            new OA\Response(response: 200, description: 'Sesión cerrada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
