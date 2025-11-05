<?php

namespace App\Http\Requests\Api;

use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // A autorização é baseada na UsuarioPolicy (método 'create')
        // O usuário autenticado deve ser ADMIN ou SECRETARIA [cite: 56]
        return $this->user()->can('create', Usuario::class);
    }

    /**
     * Retorna as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obter os IDs dos papéis ALUNO e COORDENADOR para regras condicionais
        $alunoRoleId = Role::where('nome', 'ALUNO')->value('id');
        $coordenadorRoleId = Role::where('nome', 'COORDENADOR')->value('id');

        return [
            // Regras obrigatórias [cite: 57]
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email', // [cite: 57]
            'cpf' => 'required|string|max:14|unique:usuarios,cpf', // [cite: 57] (Validação de formato de CPF pode ser adicionada aqui)
            'matricula' => 'required|string|max:20|unique:usuarios,matricula', // [cite: 57]
            'data_nascimento' => 'required|date_format:Y-m-d', // [cite: 57]
            'role_id' => 'required|integer|exists:roles,id', // [cite: 58]
            'telefone' => 'nullable|string|max:20', // [cite: 58]

            // Regras Condicionais [cite: 59]
            'curso_id' => [
                // Obrigatório se o role_id for ALUNO ou COORDENADOR [cite: 59]
                Rule::requiredIf(function () use ($alunoRoleId, $coordenadorRoleId) {
                    return in_array($this->input('role_id'), [$alunoRoleId, $coordenadorRoleId]);
                }),
                'nullable',
                'integer',
                'exists:cursos,id'
            ],
            'fase' => [
                // Obrigatório se o role_id for ALUNO [cite: 60]
                Rule::requiredIf(fn () => $this->input('role_id') == $alunoRoleId),
                'nullable',
                'integer',
                'min:1'
            ],
        ];
    }
}