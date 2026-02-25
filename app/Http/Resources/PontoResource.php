<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PontoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'numero' => $this->numero_endereco,
            'complemento' => $this->complemento,
            'endereco_completo' => $this->endereco_completo,

            // Dados do endereço
            'logradouro' => $this->whenLoaded('enderecoAtualizado', fn () => $this->enderecoAtualizado?->logradouro),
            'tipo' => $this->whenLoaded('enderecoAtualizado', fn () => $this->enderecoAtualizado?->tipo),
            'bairro' => $this->whenLoaded('enderecoAtualizado', fn () => $this->enderecoAtualizado?->bairro),
            'regional' => $this->whenLoaded('enderecoAtualizado', fn () => $this->enderecoAtualizado?->regional),

            // Dados da última vistoria
            'resultado_acao_id' => $this->whenLoaded('ultimaVistoria', fn () => $this->ultimaVistoria?->resultado_acao_id),
            'quantidade_pessoas' => $this->whenLoaded('ultimaVistoria', fn () => $this->ultimaVistoria?->quantidade_pessoas),
            'data_ultima_vistoria' => $this->whenLoaded('ultimaVistoria', fn () => $this->ultimaVistoria?->data_abordagem?->format('Y-m-d')),

            // Estatísticas
            'total_vistorias' => $this->when($this->relationLoaded('vistorias'), fn () => $this->total_vistorias),
            'complexidade' => $this->when($this->relationLoaded('ultimaVistoria'), fn () => $this->complexidade),

            // Relationships
            'endereco' => new EnderecoAtualizadoResource($this->whenLoaded('enderecoAtualizado')),
            'ultima_vistoria' => new VistoriaResource($this->whenLoaded('ultimaVistoria')),
            'moradores' => MoradorResource::collection($this->whenLoaded('moradores')),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
