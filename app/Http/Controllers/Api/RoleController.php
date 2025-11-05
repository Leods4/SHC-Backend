<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Lista todos os papéis (Roles) disponíveis no sistema.
     * (GET /api/roles) [cite: 40, 98]
     */
    public function index()
    {
        // Qualquer usuário autenticado pode ver os papéis (para formulários)
        $this->authorize('viewAny', Role::class);
        
        return Role::all();
    }
}