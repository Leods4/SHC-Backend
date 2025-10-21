<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Enums\StatusCertificado;
use App\Enums\CategoriaCertificado;
use App\Enums\RoleUsuario; // Importação necessária
use App\Http\Requests\StoreCertificadoRequest;
use App\Http\Requests\UpdateCertificadoRequest;
use App\Http\Requests\AvaliarCertificadoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class CertificadoController extends Controller
{
    /**
     * Lista todos os certificados, com suporte a filtros.
     * Rota: GET /api/certificados
     */
    public function index(Request $request)
    {
        // Autoriza se o usuário pode ver a lista (viewAny)
        Gate::authorize('viewAny', Certificado::class);
        
        $query = Certificado::query()->with('requerente:id,nome');

        // FORÇA o Aluno a ver apenas os seus (lógica de negócio/autorização)
        if (Auth::user()->tipo == RoleUsuario::ALUNO) {
            $query->where('usuario_id', Auth::id());
        }

        // Filtros da documentação
        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->has('usuario_id')) {
            $query->where('usuario_id', $request->query('usuario_id'));
        }

        return $query->paginate(15);
    }

    /**
     * Cria um novo certificado (com upload).
     * Rota: POST /api/certificados
     */
    public function store(StoreCertificadoRequest $request)
    {
        // Autoriza se o usuário pode criar (create)
        Gate::authorize('create', Certificado::class);

        $validatedData = $request->validated();

        // 1. Processa o upload do arquivo
        $path = $request->file('arquivo')->store('certificados', 'public');
        
        // 2. Cria o certificado no banco
        $certificado = Certificado::create([
            'usuario_id' => Auth::id(), // Associa ao usuário logado
            'categoria' => $validatedData['categoria'],
            'status' => StatusCertificado::ENTREGUE,
            'carga_horaria_solicitada' => $validatedData['carga_horaria_solicitada'],
            'horas_validadas' => 0,
            'nome_certificado' => $validatedData['nome_certificado'],
            'instituicao' => $validatedData['instituicao'],
            'data_emissao' => $validatedData['data_emissao'],
            'arquivo' => $path,
        ]);

        return response()->json($certificado, 201);
    }

    /**
     * Exibe um certificado específico.
     * Rota: GET /api/certificados/{certificado}
     */
    public function show(Certificado $certificado)
    {
        // Autoriza se o usuário pode ver (view) este certificado
        Gate::authorize('view', $certificado);
        
        $certificado->load('requerente', 'historico');
        return response()->json($certificado);
    }

    /**
     * Atualiza um certificado.
     * Rota: PUT /api/certificados/{certificado}
     */
    public function update(UpdateCertificadoRequest $request, Certificado $certificado)
    {
        // Autoriza se o usuário pode atualizar (update) este certificado
        Gate::authorize('update', $certificado);

        $validatedData = $request->validated();

        // Lógica de atualização de arquivo (opcional)
        if ($request->hasFile('arquivo')) {
            // Deleta o arquivo antigo
            Storage::disk('public')->delete($certificado->arquivo);
            // Salva o novo
            $path = $request->file('arquivo')->store('certificados', 'public');
            $validatedData['arquivo'] = $path;
        }

        $certificado->update($validatedData);

        return response()->json($certificado);
    }

    /**
     * Deleta um certificado.
     * Rota: DELETE /api/certificados/{certificado}
     */
    public function destroy(Certificado $certificado)
    {
        // Autoriza se o usuário pode deletar (delete) este certificado
        Gate::authorize('delete', $certificado);

        // Deleta o arquivo associado do storage
        Storage::disk('public')->delete($certificado->arquivo);

        $certificado->delete();

        return response()->json(null, 204);
    }

    /**
     * Avalia (aprova/reprova) um certificado.
     * Rota: PATCH /api/certificados/{certificado}/avaliar
     */
    public function avaliar(AvaliarCertificadoRequest $request, Certificado $certificado)
    {
        // Autoriza se o usuário pode avaliar (avaliar) este certificado
        Gate::authorize('avaliar', $certificado);

        $validatedData = $request->validated();

        // Se reprovado, zera as horas validadas
        if ($validatedData['status'] == StatusCertificado::REPROVADO) {
            $validatedData['horas_validadas'] = 0;
        }

        $certificado->update($validatedData);

        return response()->json($certificado);
    }
}