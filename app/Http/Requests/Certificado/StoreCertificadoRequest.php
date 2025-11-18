<?php
namespace App\Http\Requests\Certificado;
use Illuminate\Foundation\Http\FormRequest;
class StoreCertificadoRequest extends FormRequest
{
    public function authorize(): bool { return true; } // Autorização é feita no Gate da rota
    public function rules(): array
    {
        return [
            'categoria' => ['required', 'string', 'max:255'],
            'nome_certificado' => ['required', 'string', 'max:255'],
            'instituicao' => ['required', 'string', 'max:255'],
            'data_emissao' => ['required', 'date'],
            'carga_horaria_solicitada' => ['required', 'integer', 'min:1'],
            'arquivo' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB PDF [cite: 27]
        ];
    }
}
