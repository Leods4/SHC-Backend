<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Autentica o usuário via CPF e Senha e retorna um token Sanctum.
     * 
     */
    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string',
            'password' => 'required|string',
        ]);

        // Busca o usuário pelo CPF (credencial principal) [cite: 11]
        $usuario = Usuario::where('cpf', $request->cpf)->first();

        // Regra: Verifica se o usuário existe
        if (!$usuario) {
            throw ValidationException::withMessages([
                'cpf' => ['CPF ou senha inválidos.'],
            ]);
        }

        // Regra: Login bloqueado se usuário desativado (soft delete) [cite: 12, 87]
        if ($usuario->deleted_at) {
            throw ValidationException::withMessages([
                'cpf' => ['Este usuário está desativado.'],
            ]);
        }
        
        // Regra: Verifica a senha
        if (!Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'cpf' => ['CPF ou senha inválidos.'],
            ]);
        }

        // Loga o usuário
        Auth::login($usuario);

        // Cria o token de API
        $token = $usuario->createToken('auth_token')->plainTextToken; // [cite: 18]

        return response()->json([
            'message' => 'Login bem-sucedido',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario,
        ]);
    }

    /**
     * Retorna o usuário autenticado.
     * 
     */
    public function me(Request $request)
    {
        $usuario = $request->user();
        
        // Otimização: Inclui progresso de horas se for Aluno [cite: 30, 117]
        $usuario->loadProgresso(); 

        return response()->json($usuario);
    }

    /**
     * Faz logout (revoga o token atual).
     * [cite: 87, 19]
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ], Response::HTTP_OK);
    }

    /*
    |--------------------------------------------------------------------------
    | Handlers de Reset de Senha (Password Reset) [cite: 35, 88]
    |--------------------------------------------------------------------------
    | Nota: A implementação completa disso requer configuração de Mailer
    | e a tabela 'password_reset_tokens'.
    */

    /**
     * Envia o link de redefinição de senha.
     * (Assume que a tabela 'password_reset_tokens' existe)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Link de redefinição enviado.'], Response::HTTP_OK)
            : response()->json(['message' => 'Não foi possível enviar o link.'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Conclui a redefinição de senha.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Senha redefinida com sucesso.'], Response::HTTP_OK)
            : response()->json(['message' => 'Token inválido ou expirado.'], Response::HTTP_BAD_REQUEST);
    }
}