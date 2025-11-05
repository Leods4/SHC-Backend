<?php

namespace App\Http\Requests\Api;

use App\Models\Certificado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AvaliarCertificadoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // Autorização baseada na CertificadoPolicy (método 'evaluate')
        // Deve ser ADMIN, SECRETARIA ou COORDENADOR do curso [cite: 70, 106]
        $certificado = $this->route('certificado');
        return $this->user()->can('evaluate', $certificado);
    }

    /**
     * Retorna as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtém o certificado da rota
        $certificado = $this->route('certificado');

        // Status de avaliação válidos (todos exceto 'ENTREGUE')
        $statusAvaliados = [
            Certificado::STATUS_APROVADO,
            Certificado::STATUS_REPROVADO,
            Certificado::STATUS_APROVADO_RESSALVAS,
        ];

        // Status que exigem horas_validadas
        $statusQueExigemHoras = [
            Certificado::STATUS_APROVADO,
            Certificado::STATUS_APROVADO_RESSALVAS,
        ];

        return [
            'status' => [
                'required',
                'string',
                Rule::in($statusAvaliados), // Não pode ser 'ENTREGUE' [cite: 71, 107]
            ],
            'horas_validadas' => [
                // Obrigatório se status for APROVADO ou APROVADO_COM_RESSALVAS [cite: 72, 107]
                Rule::requiredIf(fn () => in_array($this->input('status'), $statusQueExigemHoras)),
                'nullable',
                'integer',
                'min:0',
                // Não pode ser maior que a carga horária solicitada [cite: 72, 107]
                'max:' . $certificado->carga_horaria_solicitada,
            ],
            'observacao' => 'nullable|string|max:1000', // Observação da avaliação
        ];
    }
}