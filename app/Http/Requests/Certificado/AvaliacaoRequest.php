<?php
namespace App\Http\Requests\Certificado;

use App\Enums\StatusCertificado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AvaliacaoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    } 

    public function rules(): array
    {
        return [
            // Regras originais
            'status' => ['required', new Enum(StatusCertificado::class)],
            'horas_validadas' => ['required_if:status,APROVADO,APROVADO_COM_RESSALVAS', 'nullable', 'integer', 'min:0'],
            'observacao' => ['nullable', 'string', 'max:1000'],

            // Novos campos permitidos para edição
            'categoria_id' => ['sometimes', 'exists:categorias,id'],
            'nome_certificado' => ['sometimes', 'string', 'max:255'],
            'instituicao' => ['sometimes', 'string', 'max:255'],
            'data_emissao' => ['sometimes', 'date'],
            'carga_horaria_solicitada' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}