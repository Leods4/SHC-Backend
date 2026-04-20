<?php

namespace App\Http\Controllers;

use App\Enums\StatusCertificado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProgressoResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Lista usuários
     */
    public function index(Request $request)
    {
        $query = User::query()->with('curso');

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        return UserResource::collection($query->get());
    }

    /**
     * Cria usuário
     */
    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);

        return new UserResource($user);
    }

    /**
     * Atualiza usuário
     */
    public function update(UpdateUsuarioRequest $request, User $user)
    {
        $validated = $request->validated();
        $user->update($validated);

        return new UserResource($user);
    }

    /**
     * Remove usuário
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }

    /**
     * Retorna progresso do aluno (com horas por categoria)
     */
    public function getProgresso(User $user)
    {
        // 1. Busca os certificados aprovados junto com a relação de categoria
        $certificadosAprovados = $user->certificadosSubmetidos()
            ->with('categoria')
            ->whereIn('status', [
                StatusCertificado::APROVADO,
                StatusCertificado::APROVADO_COM_RESSALVAS
            ])
            ->get();

        // 2. Calcula o total geral de horas aprovadas
        $totalAprovadas = $certificadosAprovados->sum('horas_validadas');

        // 3. Agrupa as horas por nome da categoria
        $horasPorCategoria = $certificadosAprovados->groupBy(function ($certificado) {
            return $certificado->categoria->nome ?? 'Sem Categoria';
        })->map(function ($certificados) {
            return $certificados->sum('horas_validadas');
        });

        $horasNecessarias = $user->curso->horas_necessarias ?? 0;

        return new ProgressoResource([
            'total_horas_aprovadas' => (int) $totalAprovadas,
            'horas_necessarias' => (int) $horasNecessarias,
            'horas_por_categoria' => $horasPorCategoria,
        ]);
    }

    /**
     * Atualiza avatar do usuário logado
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar_url' => $path]);

        return response()->json([
            'avatar_url' => Storage::url($path)
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'usuarios' => ['required', 'array'],
            'usuarios.*.nome' => ['required'],
            'usuarios.*.email' => ['required', 'email'],
            'usuarios.*.cpf' => ['required'],
            'usuarios.*.tipo' => ['required'],
        ]);

        $count = 0;

        foreach ($request->usuarios as $userData) {

            $password = $userData['password'] ?? '12345678';

            if (!str_starts_with($password, '$2y$')) {

                if (isset($userData['data_nascimento']) && empty($userData['password'])) {
                    $password = \Carbon\Carbon::parse($userData['data_nascimento'])->format('dmY');
                }

                $password = Hash::make($password);
            }

            User::updateOrCreate(
                ['cpf' => $userData['cpf']],
                [
                    'nome' => $userData['nome'],
                    'email' => $userData['email'],
                    'data_nascimento' => $userData['data_nascimento'] ?? null,
                    'password' => $password,
                    'tipo' => $userData['tipo'],
                    'curso_id' => $userData['curso_id'] ?? null,
                    'fase' => $userData['fase'] ?? null,
                    'matricula' => $userData['matricula'] ?? null,
                ]
            );

            $count++;
        }

        return response()->json([
            'message' => "Importação concluída. {$count} usuários processados.",
        ]);
    }
}