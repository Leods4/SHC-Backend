<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Curso;
use App\Enums\StatusCertificado;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate; // Para Autorização

class UsuarioController extends Controller
{
    /**
     * Lista todos os usuários, com suporte a filtros.
     * Rota: GET /api/usuarios
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Usuario::class);

        $query = Usuario::query()->with('curso');

        // Exemplo de filtro
        if ($request->has('tipo')) {
            $query->where('tipo', $request->query('tipo'));
        }

        if ($request->has('curso_id')) {
            $query->where('curso_id', $request->query('curso_id'));
        }

        return $query->paginate(15);
    }

    /**
     * Cria um novo usuário (gerenciamento).
     * Rota: POST /api/usuarios
     */
    public function store(StoreUsuarioRequest $request)
    {
        Gate::authorize('create', Usuario::class);
        
        $validatedData = $request->validated();
        
        $usuario = Usuario::create([
            'nome' => $validatedData['nome'],
            'email' => $validatedData['email'],
            'senha' => Hash::make($validatedData['senha']),
            'matricula' => $validatedData['matricula'],
            'tipo' => $validatedData['tipo'],
            'curso_id' => $validatedData['curso_id'] ?? null,
            // ... outros campos
        ]);

        return response()->json($usuario, 201);
    }

    /**
     * Exibe um usuário específico.
     * Rota: GET /api/usuarios/{usuario}
     */
    public function show(Usuario $usuario)
    {
        Gate::authorize('view', $usuario);

        $usuario->load('curso', 'certificados'); // Carrega relacionamentos
        return response()->json($usuario);
    }

    /**
     * Atualiza um usuário específico.
     * Rota: PUT /api/usuarios/{usuario}
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        Gate::authorize('update', $usuario);

        $validatedData = $request->validated();

        // Não atualiza a senha se estiver vazia
        if (!empty($validatedData['senha'])) {
            $validatedData['senha'] = Hash::make($validatedData['senha']);
        } else {
            unset($validatedData['senha']);
        }

        $usuario->update($validatedData);

        return response()->json($usuario);
    }

    /**
     * Deleta um usuário.
     * Rota: DELETE /api/usuarios/{usuario}
     */
    public function destroy(Usuario $usuario)
    {
        Gate::authorize('delete', $usuario);

        $usuario->delete();

        return response()->json(null, 204);
    }

    /**
     * Exibe o progresso de horas complementares do usuário.
     * Rota: GET /api/usuarios/{usuario}/progresso
     */
    public function showProgresso(Usuario $usuario)
    {
        Gate::authorize('viewProgresso', $usuario);
        
        $curso = $usuario->curso;
        if (!$curso) {
            return response()->json(['message' => 'Usuário não está associado a um curso.'], 404);
        }

        // Utiliza a funcionalidade de cálculo
        $horasValidadas = $usuario->certificados()
                                ->where('status', StatusCertificado::APROVADO)
                                ->sum('horas_validadas');

        return response()->json([
            'usuario_id' => $usuario->id,
            'nome_usuario' => $usuario->nome,
            'curso' => $curso->nome,
            'horas_necessarias' => $curso->horasNecessarias,
            'horas_validadas' => (int) $horasValidadas,
            'progresso_percentual' => $curso->horasNecessarias > 0 ? round(($horasValidadas / $curso->horasNecessarias) * 100, 2) : 0,
        ]);
    }
}
