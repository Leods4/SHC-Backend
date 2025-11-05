<?php

use App\Http\Controllers\Api\AdminController; // Importar
use App\Http\Controllers\Api\CategoriaController; // Importar
use App\Http\Controllers\Api\CursoController; // Importar
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController; // Importar
use App\Http\Controllers\Api\UsuarioController; // Importar
use App\Http\Controllers\Api\CertificadoController; // Importar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Módulo 2: Rotas Públicas (Autenticação)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Rotas Protegidas (Exigem Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // Módulo 2: Rotas Protegidas (Autenticação)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Módulo 3: Gestão de Usuários (Admin e Perfis) 

    // Módulo 7: Endpoints de Histórico e Progresso (Usuários)
    Route::get('usuarios/{usuario}/progresso', [UsuarioController::class, 'getProgresso']); //
    Route::get('usuarios/{usuario}/historico', [UsuarioController::class, 'getHistorico']); //
    
    // Rotas de Usuários (CRUD) [cite: 38]
    Route::apiResource('usuarios', UsuarioController::class); //

    // Rota de Papéis (Roles) [cite: 40]
    Route::get('roles', [RoleController::class, 'index']); //

    // Módulo 7: Endpoint de Histórico (Certificados)
    Route::get('certificados/{certificado}/historico', [CertificadoController::class, 'getHistorico']); //

    // Rota para download seguro 
    Route::get('certificados/{certificado}/download', [CertificadoController::class, 'download']);
    Route::post('certificados/{certificado}/avaliacao', [CertificadoController::class, 'avaliarCertificado']); //
    Route::apiResource('certificados', CertificadoController::class);

    // Módulo 6: Administração (Cursos e Categorias)
    Route::apiResource('cursos', CursoController::class); // [cite: 109, 110]
    Route::apiResource('categorias', CategoriaController::class); // [cite: 111, 112]

    // Módulo 7: Sincronização (Admin)
    Route::prefix('admin')->group(function () {
        Route::post('sync-users', [AdminController::class, 'syncUsers']); //
    });

});