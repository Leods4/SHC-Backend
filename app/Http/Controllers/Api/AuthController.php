<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Enums\RoleUsuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class AuthController extends Controller
{
    /**
     * Registra um novo usuário no sistema.
     * Rota: POST /api/auth/register
     */
    public function register(Request $request)
    {
        // Validação dos dados de entrada
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'senha' => 'required|string|min:8|confirmed',
            'matricula' => 'required|string|unique:usuarios',
            'tipo' => ['required', new Enum(RoleUsuario::class)],
            'curso_id' => 'nullable|exists:cursos,id',
        ]);

        // Cria o usuário
        $usuario = Usuario::create([
            'nome' => $validatedData['nome'],
            'email' => $validatedData['email'],
            'senha' => Hash::make($validatedData['senha']),
            'matricula' => $validatedData['matricula'],
            'tipo' => $validatedData['tipo'],
            'curso_id' => $validatedData['curso_id'] ?? null,
        ]);

        // Cria um token de API para o novo usuário
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario
        ], 201);
    }

    /**
     * Autentica um usuário e retorna um token de acesso.
     * Rota: POST /api/auth/login
     */
    public function login(Request $request)
    {
        // Validação
        $credentials = $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ]);

        // Tenta autenticar
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        // Busca o usuário autenticado
        $usuario = Usuario::where('email', $request->email)->firstOrFail();

        // Cria e retorna o token
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login bem-sucedido!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario
        ], 200);
    }

    /**
     * Faz logout do usuário (Revoga o token atual).
     * Rota: POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        // Revoga o token que foi usado para autenticar a requisição atual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!'
        ], 200);
    }

    /**
     * Gera um novo token para o usuário autenticado.
     * Rota: POST /api/auth/refresh
     */
    public function refreshToken(Request $request)
    {
        $usuario = $request->user();

        // Revoga o token antigo
        $usuario->currentAccessToken()->delete();

        // Cria e retorna um novo token
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
