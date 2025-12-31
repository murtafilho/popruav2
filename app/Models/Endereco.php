<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endereco extends Model
{
    protected $table = 'ender';

    protected $fillable = [
        'logradouro_id',
        'tipo',
        'logradouro',
        'bairro',
        'regional',
    ];

    public function pontos(): HasMany
    {
        return $this->hasMany(Ponto::class, 'endereco_id');
    }
}
