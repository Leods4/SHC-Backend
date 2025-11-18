<?php

namespace App\Http\Controllers;

use App\Enums\StatusCertificado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProgressoResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class UsuarioController extends Controller
{
    // [cite: 43]
    public function index(Request $request)
    {
        // Gate 'manage-users' (Admin/Secretaria) já foi aplicado na rota

        $query = User::query()->with('curso');

        // [cite: 43] Filtro por tipo
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        return UserResource::collection($query->get());
    }

    // [cite: 43]
    public function store(Request $request) // (Usar um StoreUsuarioRequest para validar)
    {
        // Validar dados (nome, email, cpf, tipo, curso_id, fase, password) [cite: 49]
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|unique:users,cpf',
            'password' => 'required|string|min:8',
            'tipo' => 'required|string', // Usar 'new Enum(TipoUsuario::class)'
            'curso_id' => 'nullable|exists:cursos,id',
            'fase' => 'nullable|integer',
        ]);

        $user = User::create($validated);
        return new UserResource($user);
    }

    // [cite: 44]
    public function update(Request $request, User $user) // (Usar um UpdateUsuarioRequest)
    {
        // Validar dados (similar ao store, mas 'email' e 'cpf' unique ignorando $user->id)
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'tipo' => 'required|string',
            'curso_id' => 'nullable|exists:cursos,id',
            'fase' => 'nullable|integer',
            // Não atualizar CPF ou Senha por aqui
        ]);

        $user->update($validated);
        return new UserResource($user);
    }

    // [cite: 45]
    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }

    // [cite: 32]
    public function getProgresso(User $user)
    {
        // Gate 'view-progresso' já foi aplicado na rota

        $totalAprovadas = $user->certificadosSubmetidos()
            ->whereIn('status', [StatusCertificado::APROVADO, StatusCertificado::APROVADO_COM_RESSALVAS])
            ->sum('horas_validadas');

        $horasNecessarias = $user->curso->horas_necessarias ?? 0;

        return new ProgressoResource([
            'total_horas_aprovadas' => (int) $totalAprovadas,
            'horas_necessarias' => (int) $horasNecessarias,
        ]);
    }

    // [cite: 55]
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // 2MB Imagem
        ]);

        $user = Auth::user();

        // Deleta avatar antigo
        if ($user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar_url' => $path]);

        return response()->json(['avatar_url' => Storage::url($path)]);
    }
}
