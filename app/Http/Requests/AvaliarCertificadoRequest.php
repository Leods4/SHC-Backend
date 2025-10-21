<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
use App\Enums\StatusCertificado; // [cite: 30]

/**
 * @method mixed input(string $key = null, $default = null)
 * @method mixed route(string|null $parameter = null, $default = null)
 */

class AvaliarCertificadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Implementar Gate (ex: Apenas Coordenador/Secretaria)
    }

    public function rules(): array
    {
        return [
            // Não deve ser 'ENTREGUE' ao avaliar [cite: 30]
            'status' => ['required', new Enum(StatusCertificado::class), Rule::notIn([StatusCertificado::ENTREGUE])], 
            
            // Horas validadas: obrigatório se o status for APROVADO [cite: 33]
            'horas_validadas' => [
                'required_if:status,' . StatusCertificado::APROVADO->value . ',' . StatusCertificado::APROVADO_COM_RESSALVAS->value,
                'nullable',
                'integer',
                'min:0',
                // Não pode validar mais horas do que o solicitado
                'max:' . $this->input('carga_horaria_solicitada', $this->route('certificado')?->carga_horaria_solicitada ?? 999)
            ],
            
            'observacao' => 'nullable|string|max:1000', // [cite: 33]
        ];
    }
}