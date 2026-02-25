<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ponto extends Model
{
    use HasFactory;

    protected $table = 'pontos';

    protected $fillable = [
        'numero',
        'complemento',
        'endereco_atualizado_id',
        'caracteristica_abrigo_id',
        'lat',
        'lng',
    ];

    public function enderecoAtualizado(): BelongsTo
    {
        return $this->belongsTo(EnderecoAtualizado::class, 'endereco_atualizado_id');
    }

    public function vistorias(): HasMany
    {
        return $this->hasMany(Vistoria::class, 'ponto_id');
    }

    /**
     * Última vistoria realizada neste ponto
     */
    public function ultimaVistoria(): HasOne
    {
        return $this->hasOne(Vistoria::class, 'ponto_id')->latestOfMany();
    }

    /**
     * Característica do abrigo (lookup table)
     */
    public function caracteristicaAbrigo(): BelongsTo
    {
        return $this->belongsTo(CaracteristicaAbrigo::class, 'caracteristica_abrigo_id');
    }

    /**
     * Moradores atualmente neste ponto
     */
    public function moradores(): HasMany
    {
        return $this->hasMany(Morador::class, 'ponto_atual_id');
    }

    /**
     * Histórico de todos moradores que já passaram por este ponto
     */
    public function historicoMoradores(): HasMany
    {
        return $this->hasMany(MoradorHistorico::class, 'ponto_id')->orderByDesc('data_entrada');
    }

    /**
     * Scope: pontos dentro de uma bounding box geográfica
     */
    public function scopeInBounds(Builder $query, float $north, float $south, float $east, float $west): Builder
    {
        return $query->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [$south, $north])
            ->whereBetween('lng', [$west, $east]);
    }

    /**
     * Scope: pontos georreferenciados (com coordenadas válidas)
     */
    public function scopeGeorreferenciado(Builder $query): Builder
    {
        return $query->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('lat', '!=', 0)
            ->where('lng', '!=', 0);
    }

    /**
     * Scope: pontos não georreferenciados (sem coordenadas válidas)
     */
    public function scopeNaoGeorreferenciado(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('lat')
                ->orWhereNull('lng')
                ->orWhere('lat', '=', 0)
                ->orWhere('lng', '=', 0);
        });
    }

    /**
     * Scope: pontos com endereço vinculado
     */
    public function scopeComEndereco(Builder $query): Builder
    {
        return $query->whereNotNull('endereco_atualizado_id');
    }

    /**
     * Scope: filtro por regional
     */
    public function scopeRegional(Builder $query, string $regional): Builder
    {
        return $query->whereHas('enderecoAtualizado', function ($q) use ($regional) {
            $q->where('NOME_REGIONAL', $regional);
        });
    }

    /**
     * Scope: filtro por bairro
     */
    public function scopeBairro(Builder $query, string $bairro): Builder
    {
        return $query->whereHas('enderecoAtualizado', function ($q) use ($bairro) {
            $q->where('NOME_BAIRRO_OFICIAL', 'like', "%{$bairro}%");
        });
    }

    /**
     * Scope: filtro por logradouro
     */
    public function scopeLogradouro(Builder $query, string $logradouro): Builder
    {
        return $query->whereHas('enderecoAtualizado', function ($q) use ($logradouro) {
            $q->where('NOME_LOGRADOURO', 'like', "%{$logradouro}%");
        });
    }

    /**
     * Retorna o número do endereço
     */
    public function getNumeroEnderecoAttribute(): ?string
    {
        if ($this->relationLoaded('enderecoAtualizado') && $this->enderecoAtualizado) {
            return (string) $this->enderecoAtualizado->numero;
        }

        return $this->numero;
    }

    /**
     * Retorna o endereço (alias para enderecoAtualizado)
     */
    public function getEnderecoAttribute(): ?EnderecoAtualizado
    {
        if ($this->relationLoaded('enderecoAtualizado')) {
            return $this->enderecoAtualizado;
        }

        return null;
    }

    /**
     * Retorna total de vistorias do ponto
     */
    public function getTotalVistoriasAttribute(): int
    {
        if ($this->relationLoaded('vistorias')) {
            return $this->vistorias->count();
        }

        return $this->vistorias()->count();
    }

    /**
     * Calcula complexidade baseada na última vistoria
     */
    public function getComplexidadeAttribute(): int
    {
        $vistoria = $this->relationLoaded('ultimaVistoria')
            ? $this->ultimaVistoria
            : $this->ultimaVistoria()->first();

        if (! $vistoria) {
            return 0;
        }

        return (int) $vistoria->resistencia
            + (int) $vistoria->num_reduzido
            + (int) $vistoria->casal
            + (int) $vistoria->catador_reciclados
            + (int) $vistoria->fixacao_antiga
            + (int) $vistoria->excesso_objetos
            + (int) $vistoria->trafico_ilicitos
            + (int) $vistoria->crianca_adolescente
            + (int) $vistoria->idosos
            + (int) $vistoria->gestante
            + (int) $vistoria->lgbtqiapn
            + (int) $vistoria->cena_uso_caracterizada
            + (int) $vistoria->deficiente
            + (int) $vistoria->agrupamento_quimico
            + (int) $vistoria->saude_mental
            + (int) $vistoria->animais;
    }

    /**
     * Retorna o endereço formatado completo
     */
    public function getEnderecoCompletoAttribute(): string
    {
        if (! $this->relationLoaded('enderecoAtualizado') || ! $this->enderecoAtualizado) {
            return $this->complemento ?? '';
        }

        $endereco = $this->enderecoAtualizado->endereco_completo;

        if ($this->complemento) {
            $endereco .= " ({$this->complemento})";
        }

        return $endereco;
    }
}
