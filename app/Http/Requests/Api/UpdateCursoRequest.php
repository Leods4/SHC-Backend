<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autorização via CursoPolicy (método 'update') [cite: 110]
        $curso = $this->route('curso');
        return $this->user()->can('update', $curso);
    }

    public function rules(): array
    {
        return [
            'nome' => 'sometimes|required|string|max:255',
            'horas_necessarias' => 'sometimes|required|integer|min:1', // [cite: 110]
        ];
    }
}