<?php

namespace App\Http\Requests\Usuario;

use App\Enums\TipoUsuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // O ID do usuário sendo atualizado vem da rota
        $userId = $this->route('user')->id;

        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'cpf' => ['required', 'string', 'max:14', Rule::unique('users')->ignore($userId)],
            'matricula' => ['nullable', 'string', Rule::unique('users')->ignore($userId)],

            // Senha é opcional na edição (só envia se quiser trocar)
            'password' => ['nullable', 'string', 'min:6'],

            'tipo' => ['required', Rule::enum(TipoUsuario::class)],
            'curso_id' => ['nullable', 'exists:cursos,id'],
            'fase' => ['nullable', 'integer'],
        ];
    }
}
