<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CertificadoHistoricoController extends Controller
{
    /**
     * Lista o histórico de alterações de um certificado específico.
     * Rota: GET /api/certificados/{certificado}/historico
     * [cite: 28]
     */
    public function index(Certificado $certificado)
    {
        // !! AUTORIZAÇÃO: Aluno (dono), Coordenador, Secretaria, Admin
        // if (Gate::denies('viewHistory', $certificado)) {
        //     abort(403);
        // }

        // Busca o histórico polimórfico do certificado
        $historico = $certificado->historico()
                                ->with('responsavel:id,nome') // Carrega quem fez a alteração
                                ->latest('data_alteracao') // Ordena pelo mais recente
                                ->paginate(15);

        return response()->json($historico);
    }
}