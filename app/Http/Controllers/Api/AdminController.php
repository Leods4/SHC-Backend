<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    /**
     * Sincroniza usuários de uma fonte externa.
     * (POST /api/admin/sync-users)
     */
    public function syncUsers(Request $request, UserSyncService $syncService)
    {
        // Autorização: Apenas Admin
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $resultado = $syncService->syncUsers();
            return response()->json($resultado);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro na sincronização', 'error' => $e->getMessage()], 500);
        }
    }
}