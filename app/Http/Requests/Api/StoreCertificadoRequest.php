<?php

namespace App\Http\Requests\Api;

use App\Models\Certificado;
use Illuminate\Foundation\Http\FormRequest;

class StoreCertificadoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // Autorização baseada na CertificadoPolicy (método 'create')
        // Deve ser um ALUNO [cite: 65]
        return $this->user()->can('create', Certificado::class);
    }

    /**
     * Retorna as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Dados
            'categoria_id' => 'required|integer|exists:categorias,id', // [cite: 66]
            'carga_horaria_solicitada' => 'required|integer|min:1', // [cite: 66]
            'nome_certificado' => 'required|string|max:255', // [cite: 67]
            'instituicao' => 'required|string|max:255', // [cite: 67]
            'data_emissao' => 'required|date|before_or_equal:today', // Não pode ser data futura [cite: 67]
            'observacao' => 'nullable|string',
            
            // Arquivo
            'arquivo' => 'required|file|mimes:pdf|max:5120', // PDF, max 5MB [cite: 67]
        ];
    }
}