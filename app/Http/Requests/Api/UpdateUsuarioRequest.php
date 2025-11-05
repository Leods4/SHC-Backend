<?php

namespace App\Http\Requests\Api;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // A autorização é baseada na UsuarioPolicy (método 'update')
        // Deve ser o próprio usuário ou um Admin/Secretaria [cite: 61]
        // O usuário (modelo) a ser atualizado é obtido da rota
        $usuarioToUpdate = $this->route('usuario'); 
        
        return $this->user()->can('update', $usuarioToUpdate);
    }

    /**
     * Retorna as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // O usuário (modelo) a ser atualizado
        $usuarioId = $this->route('usuario')->id;

        // Obter os IDs dos papéis ALUNO e COORDENADOR
        $alunoRoleId = Role::where('nome', 'ALUNO')->value('id');
        $coordenadorRoleId = Role::where('nome', 'COORDENADOR')->value('id');

        // Determina o role_id (o novo, se enviado, ou o antigo)
        $roleId = $this->input('role_id', $this->route('usuario')->role_id);

        return [
            // Regras são opcionais ('sometimes') mas devem ser válidas se enviadas [cite: 62]
            'nome' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes', 'required', 'string', 'email', 'max:255',
                Rule::unique('usuarios')->ignore($usuarioId), // [cite: 62]
            ],
            'cpf' => [
                'sometimes', 'required', 'string', 'max:14',
                Rule::unique('usuarios')->ignore($usuarioId), // [cite: 62]
            ],
            'matricula' => [
                'sometimes', 'required', 'string', 'max:20',
                Rule::unique('usuarios')->ignore($usuarioId), // [cite: 62]
            ],
            'data_nascimento' => 'sometimes|required|date_format:Y-m-d',
            'telefone' => 'nullable|string|max:20',
            
            // A alteração de 'role_id' é controlada na UsuarioPolicy [cite: 14, 91]
            'role_id' => 'sometimes|required|integer|exists:roles,id',

            // Senha opcional [cite: 63]
            'password' => 'sometimes|required|string|min:8|confirmed', // [cite: 63]

            // Regras Condicionais (baseadas no roleId que está sendo definido)
            'curso_id' => [
                Rule::requiredIf(fn () => in_array($roleId, [$alunoRoleId, $coordenadorRoleId])),
                'nullable',
                'integer',
                'exists:cursos,id'
            ],
            'fase' => [
                Rule::requiredIf(fn () => $roleId == $alunoRoleId),
                'nullable',
                'integer',
                'min:1'
            ],
        ];
    }
}