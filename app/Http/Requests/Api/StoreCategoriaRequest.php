<?php

namespace App\Http\Requests\Api;

use App\Models\Categoria;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autorização via CategoriaPolicy (método 'create') [cite: 73]
        return $this->user()->can('create', Categoria::class);
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255|unique:categorias,nome', // [cite: 74]
            'limite_horas' => 'nullable|integer|min:1', // [cite: 75]
        ];
    }
}