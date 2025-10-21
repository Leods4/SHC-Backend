<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate; //
use Illuminate\Validation\Rule;

/**
 * @method mixed route(string|null $parameter = null, $default = null)
 */


class UpdateCursoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // Mude para 'true' ou implemente a lógica de Gate (Autorização)
        // Ex: return Gate::allows('update', $this->route('curso'));
        return true;
    }

    /**
     * Define as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $cursoId = $this->route('curso')->id;

        return [
            'nome' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cursos')->ignore($cursoId), // Ignora o nome do próprio curso
            ],
            'horasNecessarias' => 'sometimes|required|integer|min:1', //
        ];
    }
}