<?php

namespace App\Http\Controllers;

use App\Enums\StatusCertificado;
use App\Models\Certificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Certificado\StoreCertificadoRequest;
use App\Http\Requests\Certificado\AvaliacaoRequest;
use App\Http\Resources\CertificadoResource; // Criar este Resource

class CertificadoController extends Controller
{
    // [cite: 29, 35, 46]
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Certificado::query()->with('aluno', 'coordenador');

        if ($user->isAluno()) {
            // [cite: 29] Aluno vê apenas os seus
            $query->where('aluno_id', $user->id);

        } elseif ($user->isCoordenador()) {
            // Coordenador vê alunos do seu curso
            $query->whereHas('aluno', fn($q) => $q->where('curso_id', $user->curso_id));

            // [cite: 35] (Tela de validação)
            if ($request->status === 'ENTREGUE') {
                $query->where('status', StatusCertificado::ENTREGUE);
            }
            // [cite: 40] (Histórico do coordenador)

        } elseif ($user->isSecretaria()) {
            // [cite: 46] Secretaria vê todos (pode ter filtros)
            // Nenhum filtro de permissão necessário
        }
        // Admin (default) vê todos

        return CertificadoResource::collection($query->latest()->get());
    }

    // [cite: 28]
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

    // [cite: 39]
    public function avaliar(Certificado $certificado, AvaliacaoRequest $request)
    {
        // Gate 'avaliar-certificado' já foi aplicado na rota

        $data = $request->validated();

        // Se reprovado, zera as horas
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
