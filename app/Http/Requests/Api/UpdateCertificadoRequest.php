<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificadoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // Autorização baseada na CertificadoPolicy (método 'update')
        // Deve ser o dono E status = 'ENTREGUE' [cite: 69]
        $certificado = $this->route('certificado');
        return $this->user()->can('update', $certificado);
    }

    /**
     * Retorna as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Regras 'sometimes' (opcionais), mas se enviadas, devem ser válidas [cite: 70]
        return [
            'categoria_id' => 'sometimes|required|integer|exists:categorias,id',
            'carga_horaria_solicitada' => 'sometimes|required|integer|min:1',
            'nome_certificado' => 'sometimes|required|string|max:255',
            'instituicao' => 'sometimes|required|string|max:255',
            'data_emissao' => 'sometimes|required|date|before_or_equal:today',
            'observacao' => 'nullable|string',
            
            // Permite atualizar o arquivo
            'arquivo' => 'sometimes|required|file|mimes:pdf|max:5120',
        ];
    }
}