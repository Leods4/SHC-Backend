<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autorização via CategoriaPolicy (método 'update') [cite: 76]
        $categoria = $this->route('categoria');
        return $this->user()->can('update', $categoria);
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categoria')->id;

        return [
            'nome' => [ // [cite: 76]
                'sometimes', 
                'required', 
                'string', 
                'max:255',
                Rule::unique('categorias')->ignore($categoriaId),
            ],
            'limite_horas' => 'sometimes|nullable|integer|min:1', // [cite: 76]
        ];
    }
}