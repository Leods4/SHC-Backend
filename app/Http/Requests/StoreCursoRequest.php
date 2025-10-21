<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate; //

class StoreCursoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // Mude para 'true' ou implemente a lógica de Gate (Autorização)
        // Ex: return Gate::allows('create', Curso::class);
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
            'nome' => 'required|string|max:255|unique:cursos,nome',
            'horasNecessarias' => 'required|integer|min:1', //
        ];
    }
}