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

    public function scopeInBounds($query, float $north, float $south, float $east, float $west)
    {
        return $query->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [$south, $north])
            ->whereBetween('lng', [$west, $east]);
    }
}
