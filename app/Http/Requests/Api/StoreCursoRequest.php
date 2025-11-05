<?php

namespace App\Http\Requests\Api;

use App\Models\Curso;
use Illuminate\Foundation\Http\FormRequest;

class StoreCursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autorização via CursoPolicy (método 'create') [cite: 109]
        return $this->user()->can('create', Curso::class);
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'horas_necessarias' => 'required|integer|min:1', // [cite: 110]
        ];
    }
}