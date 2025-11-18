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
     * Atende requisitos: [cite: 29, 35, 40, 46] e novos requisitos enviados
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Certificado::query()->with('aluno', 'coordenador');

        /**
         * 🔍 FILTROS GLOBAIS (para qualquer tipo de usuário)
         */

        // 1. Filtro por aluno específico (necessário para o Coordenador ver histórico de um aluno)
        if ($request->has('aluno_id')) {
            $query->where('aluno_id', $request->aluno_id);
        }

        // 2. Busca por nome/cpf do aluno (Fluxo da Secretaria — Histórico Geral)
        if ($request->has('search')) {
            $term = $request->search;

            $query->whereHas('aluno', function ($q) use ($term) {
                $q->where('nome', 'like', "%{$term}%")
                  ->orWhere('cpf', 'like', "%{$term}%");
            });
        }

        // 3. Filtro por intervalo de datas
        if ($request->has('data_inicio') && $request->has('data_fim')) {
            $query->whereBetween('data_emissao', [
                $request->data_inicio,
                $request->data_fim
            ]);
        }

        // 4. Filtro por curso (somente Secretaria e Admin)
        if ($request->has('curso_id') && ($user->isSecretaria() || $user->isAdmin())) {
            $query->whereHas('aluno', function ($q) use ($request) {
                $q->where('curso_id', $request->curso_id);
            });
        }

        /**
         * 👤 REGRAS POR PAPEL DO USUÁRIO
         */

        if ($user->isAluno()) {
            // [cite: 29] — Aluno vê apenas seus certificados
            $query->where('aluno_id', $user->id);

        } elseif ($user->isCoordenador()) {

            // Coordenador vê apenas certificados de alunos do seu curso
            $query->whereHas('aluno', fn($q) =>
                $q->where('curso_id', $user->curso_id)
            );

            // [cite: 35] — Tela de validação: listar apenas ENTREGUES se solicitado
            if ($request->status === 'ENTREGUE') {
                $query->where('status', StatusCertificado::ENTREGUE);
            }

        } elseif ($user->isSecretaria()) {
            // [cite: 46] — Secretaria vê todos (mas pode aplicar filtros avançados)
            // Nenhuma restrição adicional

        }
        // Admin também vê tudo (com filtros opcionais)

        return CertificadoResource::collection(
            $query->latest()->get()
        );
    }

    /**
     * STORE — Envio de certificado pelo aluno
     * Atende [cite: 28]
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

        return new CertificadoResource($certificado);
    }

    /**
     * AVALIAR — Aprovação/Reprovação pelo Coordenador
     * Atende [cite: 39]
     */
    public function avaliar(Certificado $certificado, AvaliacaoRequest $request)
    {
        $data = $request->validated();

        // Se reprovado → zera horas validadas
        if ($data['status'] === StatusCertificado::REPROVADO->value) {
            $data['horas_validadas'] = 0;
        }

        $certificado->update([
            ...$data,
            'coordenador_id' => Auth::id(),
            'data_validacao' => now(),
        ]);

        return new CertificadoResource($certificado);
    }
}
