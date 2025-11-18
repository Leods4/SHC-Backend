<?php

namespace App\Http\Requests\Usuario;

use App\Enums\TipoUsuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        // A autorização já é feita pela Policy/Gate na rota ('manage-users')
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'cpf' => ['required', 'string', 'max:14', 'unique:users'], // Formato 000.000.000-00
            'matricula' => ['nullable', 'string', 'unique:users'],
            'password' => ['required', 'string', 'min:6'], // Senha inicial
            'tipo' => ['required', Rule::enum(TipoUsuario::class)],

            // Condicionais: Curso e Fase são obrigatórios se for ALUNO
            'curso_id' => [
                'nullable',
                'exists:cursos,id',
                Rule::requiredIf($this->tipo === TipoUsuario::ALUNO->value)
            ],
            'fase' => [
                'nullable',
                'integer',
                Rule::requiredIf($this->tipo === TipoUsuario::ALUNO->value)
            ],
        ];
    }
}
