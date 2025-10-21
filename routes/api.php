<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importe todos os seus controladores de API
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsuarioController;
use App\Http\Controllers\API\CursoController;
use App\Http\Controllers\API\CertificadoController;
use App\Http\Controllers\API\UsuarioHistoricoController;
use App\Http\Controllers\API\CertificadoHistoricoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// == 1. Rotas Públicas (Autenticação) ==
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});


// == 2. Rotas Protegidas (Exigem Autenticação) ==
Route::middleware('auth:sanctum')->group(function () {

    // Autenticação (Logout/Refresh)
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refreshToken');
    });

    // Usuários
    Route::apiResource('usuarios', UsuarioController::class);
    Route::get('/usuarios/{usuario}/progresso', [UsuarioController::class, 'showProgresso']);
    Route::get('/usuarios/{usuario}/historico', [UsuarioHistoricoController::class, 'index']);

    // Cursos
    Route::apiResource('cursos', CursoController::class);

    // Certificados
    Route::apiResource('certificados', CertificadoController::class);
    Route::patch('/certificados/{certificado}/avaliar', [CertificadoController::class, 'avaliar']);
    Route::get('/certificados/{certificado}/historico', [CertificadoHistoricoController::class, 'index']);

    // Rota padrão 'user' do Sanctum (para buscar o usuário logado)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
