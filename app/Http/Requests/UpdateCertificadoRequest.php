<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\CategoriaCertificado;

/**
 * @method \Illuminate\Http\UploadedFile|null file(string $key = null, $default = null)
 * @method bool hasFile(string $key)
 */

class UpdateCertificadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Implementar Gate (ex: Aluno só edita se status == ENTREGUE)
    }

    public function rules(): array
    {
        return [
            'categoria' => ['sometimes', 'required', new Enum(CategoriaCertificado::class)],
            'carga_horaria_solicitada' => 'sometimes|required|integer|min:1',
            'nome_certificado' => 'sometimes|required|string|max:255',
            'instituicao' => 'sometimes|required|string|max:255',
            'data_emissao' => 'sometimes|required|date|before_or_equal:today',
            'arquivo' => 'nullable|file|mimes:pdf|max:5120', // [cite: 21]
        ];
    }
}