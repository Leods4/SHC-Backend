<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UsuarioHistoricoController extends Controller
{
    /**
     * Lista o histórico de alterações de um usuário específico.
     * Rota: GET /api/usuarios/{usuario}/historico
     * [cite: 27]
     */
    public function index(Usuario $usuario)
    {
        // !! AUTORIZAÇÃO: Apenas Coordenador/Secretaria/Admin
        // if (Gate::denies('viewHistory', $usuario)) {
        //     abort(403);
        // }

        // Busca o histórico polimórfico do usuário
        $historico = $usuario->historico()
                            ->with('responsavel:id,nome') // Carrega quem fez a alteração
                            ->latest('data_alteracao') // Ordena pelo mais recente
                            ->paginate(15);

        return response()->json($historico);
    }
}