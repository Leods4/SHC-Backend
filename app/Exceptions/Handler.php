<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request; // Importar
use Illuminate\Http\Response; // Importar
use Illuminate\Database\Eloquent\ModelNotFoundException; // Importar
use Illuminate\Auth\Access\AuthorizationException; // Importar
use Throwable;

class Handler extends ExceptionHandler
{
    // ... (propriedades existentes)

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handler para rotas de API
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                // 404 - Recurso não encontrado
                if ($e instanceof ModelNotFoundException) {
                    return response()->json([
                        'message' => 'O recurso solicitado não foi encontrado.'
                    ], Response::HTTP_NOT_FOUND);
                }

                // 403 - Não autorizado
                if ($e instanceof AuthorizationException) {
                    return response()->json([
                        'message' => 'Você não tem permissão para executar esta ação.'
                    ], Response::HTTP_FORBIDDEN);
                }
            }
        });
    }
}