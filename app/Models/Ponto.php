<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ponto extends Model
{
    protected $table = 'pontos';

    protected $fillable = [
        'numero',
        'complemento',
        'endereco_id',
        'caracteristica_abrigo_id',
        'lat',
        'lng',
    ];

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class, 'endereco_id');
    }

    public function vistorias(): HasMany
    {
        return $this->hasMany(Vistoria::class, 'ponto_id');
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

    public function scopeInBounds($query, float $north, float $south, float $east, float $west)
    {
        return $query->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [$south, $north])
            ->whereBetween('lng', [$west, $east]);
    }
}
