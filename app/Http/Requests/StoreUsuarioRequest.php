<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\RoleUsuario; //

class StoreUsuarioRequest extends FormRequest
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
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'senha' => 'required|string|min:8|confirmed',
            'matricula' => 'required|string|unique:usuarios',
            'tipo' => ['required', new Enum(RoleUsuario::class)],
            'curso_id' => 'nullable|exists:cursos,id',
            'fase' => 'nullable|integer',
            'data_nascimento' => 'nullable|date',
        ];
    }
}