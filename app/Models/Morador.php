<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Morador extends Model
{
    /** @use HasFactory<\Database\Factories\MoradorFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'moradores';

    protected $fillable = [
        'ponto_atual_id',
        'nome_social',
        'nome_registro',
        'apelido',
        'genero',
        'observacoes',
        'documento',
        'contato',
        'fotografia',
    ];

    public function pontoAtual(): BelongsTo
    {
        return $this->belongsTo(Ponto::class, 'ponto_atual_id');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(MoradorHistorico::class)->orderByDesc('data_entrada');
    }

    /**
     * Retorna todos os pontos onde o morador já esteve
     */
    public function pontos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Ponto::class, 'morador_historico')
            ->withPivot(['vistoria_entrada_id', 'vistoria_saida_id', 'data_entrada', 'data_saida'])
            ->withTimestamps();
    }
}
