<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\CategoriaCertificado; // [cite: 30]

/**
 * @method \Illuminate\Http\UploadedFile|null file(string $key = null, $default = null)
 * @method bool hasFile(string $key)
 */

class StoreCertificadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Use Gates para autorização real
    }

    public function rules(): array
    {
        return [
            'categoria' => ['required', new Enum(CategoriaCertificado::class)], // [cite: 33]
            'carga_horaria_solicitada' => 'required|integer|min:1', // [cite: 33]
            'nome_certificado' => 'required|string|max:255', // [cite: 33]
            'instituicao' => 'required|string|max:255', // [cite: 33]
            'data_emissao' => 'required|date|before_or_equal:today', // [cite: 33]
            'arquivo' => 'required|file|mimes:pdf|max:5120', // 5MB Max, PDF [cite: 21]
        ];
    }
}