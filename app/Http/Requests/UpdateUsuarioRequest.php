<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\RoleUsuario; //
use Illuminate\Validation\Rule;

/**
 * @method mixed route(string|null $parameter = null, $default = null)
 */


class UpdateUsuarioRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // Mude para 'true' ou implemente a lógica de Gate (Autorização) [cite: 18]
        return true;
    }

    /**
     * Define as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $usuarioId = $this->route('usuario')->id; // Pega o ID do usuário da rota

        return [
            'nome' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios')->ignore($usuarioId), // Ignora o email do próprio usuário
            ],
            'senha' => 'nullable|string|min:8|confirmed', // 'nullable' permite não atualizar
            'matricula' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('usuarios')->ignore($usuarioId),
            ],
            'tipo' => ['sometimes', 'required', new Enum(RoleUsuario::class)],
            'curso_id' => 'nullable|exists:cursos,id',
            'fase' => 'nullable|integer',
            'data_nascimento' => 'nullable|date',
        ];
    }
}