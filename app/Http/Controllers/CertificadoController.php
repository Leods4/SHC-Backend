<?php

namespace App\Http\Controllers;

use App\Enums\StatusCertificado;
use App\Models\Certificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Certificado\StoreCertificadoRequest;
use App\Http\Requests\Certificado\AvaliacaoRequest;
use App\Http\Resources\CertificadoResource;

class CertificadoController extends Controller
{
    /**
     * INDEX — Listagem com filtros por regras de permissão e filtros avançados
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Certificado::query()->with('aluno', 'coordenador', 'categoria');

        // 🔍 FILTROS

        if ($request->filled('aluno_id')) {
            $query->where('aluno_id', $request->aluno_id);
        }

        if ($request->filled('search')) {
            $term = $request->search;

            $query->whereHas('aluno', function ($q) use ($term) {
                $q->where('nome', 'like', "%{$term}%")
                  ->orWhere('cpf', 'like', "%{$term}%");
            });
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_emissao', [
                $request->data_inicio,
                $request->data_fim,
            ]);
        }

        if (
            $request->filled('curso_id') &&
            ($user->isSecretaria() || $user->isAdmin())
        ) {
            $query->whereHas('aluno', function ($q) use ($request) {
                $q->where('curso_id', $request->curso_id);
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // 👤 REGRAS POR PAPEL

        if ($user->isAluno()) {
            $query->where('aluno_id', $user->id);

        } elseif ($user->isCoordenador()) {

            $query->whereHas('aluno', fn($q) =>
                $q->where('curso_id', $user->curso_id)
            );

            if ($request->status === 'ENTREGUE') {
                $query->where('status', StatusCertificado::ENTREGUE);
            }

        }

        return CertificadoResource::collection(
            $query->latest()->get()
        );
    }

    /**
     * STORE — Aluno envia certificado
     */
    public function store(StoreCertificadoRequest $request)
    {
        $path = $request->file('arquivo')->store('certificados', 'public');

        $certificado = Certificado::create([
            ...$request->validated(),
            'arquivo_url' => $path,
            'aluno_id' => Auth::id(),
            'status' => StatusCertificado::ENTREGUE,
        ]);

        $certificado->load('categoria');

        return new CertificadoResource($certificado);
    }

    /**
     * AVALIAR — Coordenador aprova/reprova o certificado
     */
    public function avaliar(Certificado $certificado, AvaliacaoRequest $request)
    {
        $data = $request->validated();

        if ($data['status'] === StatusCertificado::REPROVADO->value) {
            $data['horas_validadas'] = 0;
        }

        $certificado->update([
            ...$data,
            'coordenador_id' => Auth::id(),
            'data_validacao' => now(),
        ]);

        $certificado->load('categoria');

        return new CertificadoResource($certificado);
    }

    /**
     * EXPORT — Exporta dados para um sistema externo
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        $query = Certificado::query()->with('aluno', 'coordenador', 'categoria');

        // 🔍 FILTROS

        if ($request->filled('aluno_id')) {
            $query->where('aluno_id', $request->aluno_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_emissao', [
                $request->data_inicio,
                $request->data_fim,
            ]);
        }

        // 👤 REGRAS POR PAPEL

        if ($user->isAluno()) {
            $query->where('aluno_id', $user->id);

        } elseif ($user->isCoordenador()) {

            $query->whereHas('aluno', fn($q) =>
                $q->where('curso_id', $user->curso_id)
            );
        }

        $certificados = $query->latest()->get();

        return CertificadoResource::collection($certificados);

        /*
        return response()->json([
            'data' => $certificados->map(function ($certificado) {
                return [
                    'id' => $certificado->id,
                    'aluno' => $certificado->aluno->nome,
                    'categoria' => $certificado->categoria->nome ?? null,
                    'status' => $certificado->status,
                    'horas' => $certificado->horas_validadas,
                    'data_emissao' => $certificado->data_emissao,
                ];
            })
        ]);
        */
    }
}
