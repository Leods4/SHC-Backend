<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUsuarioRequest;
use App\Http\Requests\Api\UpdateUsuarioRequest;
use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Certificado; //

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
        // Aplica as autorizações da UsuarioPolicy a todos os métodos
        $this->authorizeResource(Usuario::class, 'usuario');
    }

    /**
     * Lista todos os usuários.
     * (GET /api/usuarios) [cite: 38]
     */
    public function index(Request $request)
    {
        // Filtros (Query Parameters) [cite: 38]
        $query = Usuario::query()->with('role', 'curso');

        if ($request->has('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }

        if ($request->has('curso_id')) {
            $query->where('curso_id', $request->input('curso_id'));
        }

        return $query->paginate(15);
    }

    /**
     * Cria (registra) um novo usuário.
     * (POST /api/usuarios) [cite: 38, 94]
     */
    public function store(StoreUsuarioRequest $request)
    {
        // O StoreUsuarioRequest já validou os dados e a permissão [cite: 95]
        $data = $request->validated();
        
        // O UsuarioService cuida da lógica de criação (ex: senha padrão) 
        $usuario = $this->usuarioService->criarUsuario($data);

        return response()->json($usuario, Response::HTTP_CREATED);
    }

    /**
     * Exibe um usuário específico.
     * (GET /api/usuarios/{id}) [cite: 38]
     */
    public function show(Usuario $usuario)
    {
        // Carrega os relacionamentos
        $usuario->load('role', 'curso');
        return response()->json($usuario);
    }

    /**
     * Atualiza um usuário.
     * (PUT /api/usuarios/{id}) [cite: 38, 97]
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        // O UpdateUsuarioRequest já validou os dados e a permissão [cite: 61, 97]
        $data = $request->validated();

        $usuario = $this->usuarioService->atualizarUsuario($usuario, $data);

        return response()->json($usuario);
    }

    /**
     * Desativa (Soft Deletes) um usuário.
     * (DELETE /api/usuarios/{id}) [cite: 21, 38]
     */
    public function destroy(Usuario $usuario)
    {
        // A autorização (delete) foi verificada pelo authorizeResource
        
        $usuario->delete(); // Soft delete [cite: 21]

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Mostra o progresso de horas do usuário.
     * (GET /api/usuarios/{id}/progresso)
     */
    public function getProgresso(Request $request, Usuario $usuario)
    {
        // Autorização (mesma lógica do 'show')
        $this->authorize('view', $usuario);

        if (!$usuario->isAluno()) {
            return response()->json(['message' => 'Usuário não é aluno.'], Response::HTTP_BAD_REQUEST);
        }
        
        // Otimização: Se o ID da rota for o usuário autenticado,
        // o 'loadProgresso()' já foi chamado no AuthController@me
        if (Auth::id() == $usuario->id && $usuario->relationLoaded('progresso_horas')) {
             return response()->json([
                'progresso_horas' => $usuario->progresso_horas,
                'curso_horas_necessarias' => $usuario->curso_horas_necessarias,
            ]);
        }
        
        // Cálculo (conforme Módulo 2 e Documentação [cite: 29])
        $totalHoras = $usuario->certificados()
            ->whereIn('status', [
                Certificado::STATUS_APROVADO,
                Certificado::STATUS_APROVADO_RESSALVAS
            ])
            ->sum('horas_validadas'); //

        return response()->json([
            'progresso_horas' => (int) $totalHoras,
            'curso_horas_necessarias' => $usuario->curso?->horas_necessarias ?? 0,
        ]);
    }

    /**
     * Mostra o histórico de alterações do usuário.
     * (GET /api/usuarios/{id}/historico)
     */
    public function getHistorico(Request $request, Usuario $usuario)
    {
        // Apenas Admin/Secretaria podem ver o histórico
        if (!$request->user()->isStaff()) {
            $this->authorize('view', $usuario); // Permite ver o próprio histórico (se necessário)
        }
        
        $historico = $usuario->historico()
            ->with('responsavel:id,nome') // Otimiza a query
            ->orderBy('data_alteracao', 'desc')
            ->paginate(15);

        return response()->json($historico);
    }
}