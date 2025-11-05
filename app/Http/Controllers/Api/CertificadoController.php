<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCertificadoRequest;
use App\Http\Requests\Api\UpdateCertificadoRequest;
use App\Http\Requests\Api\AvaliarCertificadoRequest;
use App\Models\Certificado;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificadoController extends Controller
{
    public function __construct()
    {
        // Aplica as autorizações da CertificadoPolicy
        // 'store' -> 'create'
        // 'update' -> 'update'
        // 'destroy' -> 'delete'
        // 'show' -> 'view'
        // 'index' -> 'viewAny'
        $this->authorizeResource(Certificado::class, 'certificado');
    }

    /**
     * Lista certificados.
     * (GET /api/certificados)
     */
    public function index(Request $request)
    {
        $usuario = $request->user();
        $query = Certificado::query()->with('categoria');

        // Se for Aluno, vê apenas os seus [cite: 103]
        if ($usuario->isAluno()) {
            $query->where('aluno_id', $usuario->id);
        }
        
        // Se for Coordenador, vê apenas os do seu curso
        elseif ($usuario->isCoordenador()) {
            $query->whereHas('aluno', function ($q) use ($usuario) {
                $q->where('curso_id', $usuario->curso_id);
            });
        }
        
        // Se for Admin/Secretaria, vê todos (a query não é filtrada)

        // Filtros (Query Parameters) [cite: 41]
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('aluno_id')) {
            // Permite que Admin filtre por aluno
            $query->where('aluno_id', $request->input('aluno_id'));
        }

        return $query->orderBy('data_emissao', 'desc')->paginate(15);
    }

    /**
     * Submete um novo certificado (com upload).
     * (POST /api/certificados)
     */
    public function store(StoreCertificadoRequest $request)
    {
        $data = $request->validated();
        
        // 1. Processar o upload (Multipart-form-data) 
        // Armazena em storage/app/certificados (disco 'local')
        $path = $request->file('arquivo')->store('certificados', 'local'); // 

        // 2. Criar o registro no banco
        $certificado = Certificado::create([
            'aluno_id' => Auth::id(), // Dono é o usuário autenticado
            'arquivo' => $path, // Caminho relativo salvo no banco [cite: 24]
            'status' => Certificado::STATUS_ENTREGUE, // Status inicial 
            'categoria_id' => $data['categoria_id'],
            'carga_horaria_solicitada' => $data['carga_horaria_solicitada'],
            'nome_certificado' => $data['nome_certificado'],
            'instituicao' => $data['instituicao'],
            'data_emissao' => $data['data_emissao'],
            'observacao' => $data['observacao'] ?? null,
        ]);

        return response()->json($certificado, Response::HTTP_CREATED);
    }

    /**
     * Exibe um certificado específico.
     * (GET /api/certificados/{id})
     */
    public function show(Certificado $certificado)
    {
        $certificado->load('aluno', 'categoria');
        return response()->json($certificado);
    }

    /**
     * Atualiza um certificado.
     * (PUT /api/certificados/{id})
     */
    public function update(UpdateCertificadoRequest $request, Certificado $certificado)
    {
        $data = $request->validated();

        // Se um novo arquivo foi enviado, atualiza o antigo
        if ($request->hasFile('arquivo')) {
            // Deleta o arquivo antigo
            Storage::disk('local')->delete($certificado->arquivo);
            // Salva o novo
            $data['arquivo'] = $request->file('arquivo')->store('certificados', 'local');
        }

        $certificado->update($data);

        return response()->json($certificado);
    }

    /**
     * Deleta um certificado.
     * (DELETE /api/certificados/{id})
     */
    public function destroy(Certificado $certificado)
    {
        // A autorização (delete) [cite: 102] já foi verificada pelo authorizeResource
        
        // Deleta o arquivo do storage
        Storage::disk('local')->delete($certificado->arquivo);
        
        // Deleta o registro do banco
        $certificado->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Faz o download do arquivo PDF privado do certificado.
     * (GET /api/certificados/{id}/download)
     */
    public function download(Request $request, Certificado $certificado)
    {
        // Reutiliza a política 'view' para autorizar o download 
        $this->authorize('view', $certificado);

        // Verifica se o arquivo existe no disco 'local'
        if (!Storage::disk('local')->exists($certificado->arquivo)) {
            return response()->json(['message' => 'Arquivo não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        // Gera um nome de arquivo mais amigável
        $nomeArquivo = sprintf(
            '%s_%s_%s.pdf',
            $certificado->aluno->nome,
            $certificado->nome_certificado,
            $certificado->id
        );

        // Retorna o download seguro
        return Storage::disk('local')->download($certificado->arquivo, $nomeArquivo);
    }

    /*
    |--------------------------------------------------------------------------
    | MÓDULO 5: Avaliação
    |--------------------------------------------------------------------------
    */

    /**
     * Submete uma avaliação (aprova/reprova)[cite: 43].
     * (POST /api/certificados/{id}/avaliacao)
     */
    public function avaliarCertificado(AvaliarCertificadoRequest $request, Certificado $certificado)
    {
        // O AvaliarCertificadoRequest já validou os dados e a permissão [cite: 106]
        $data = $request->validated();

        // Se o status for 'REPROVADO', as horas validadas devem ser 0
        if ($data['status'] === Certificado::STATUS_REPROVADO) {
            $data['horas_validadas'] = 0;
        }

        $certificado->update($data);
        
        // (O Observer (Módulo 7) registrará esta alteração automaticamente)

        return response()->json($certificado);
    }


    public function getHistorico(Request $request, Certificado $certificado)
    {
        // Autorização (mesma lógica do 'show')
        $this->authorize('view', $certificado);

        $historico = $certificado->historico()
            ->with('responsavel:id,nome')
            ->orderBy('data_alteracao', 'desc')
            ->paginate(15);
            
        return response()->json($historico);
    }
}