<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CertificadoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoria' => $this->categoria,
            'nome_certificado' => $this->nome_certificado,
            'instituicao' => $this->instituicao,
            'carga_horaria_solicitada' => $this->carga_horaria_solicitada,
            'status' => $this->status, // O Laravel converte o Enum para string automaticamente

            // Formatação de datas para o front-end
            'data_emissao' => $this->data_emissao->format('Y-m-d'),
            'data_emissao_formatada' => $this->data_emissao->format('d/m/Y'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),

            // URL pública para download do PDF
            'arquivo_url' => Storage::url($this->arquivo_url),

            // Campos de Validação (se existirem)
            'horas_validadas' => $this->horas_validadas,
            'observacao' => $this->observacao,
            'data_validacao' => $this->data_validacao?->format('d/m/Y H:i'),

            // Relacionamentos (carregados apenas se necessário)
            'aluno' => new UserResource($this->whenLoaded('aluno')),
            'coordenador' => new UserResource($this->whenLoaded('coordenador')),
        ];
    }
}
