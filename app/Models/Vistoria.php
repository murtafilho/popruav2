<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vistoria extends Model
{
    protected $table = 'vistorias';

    protected $fillable = [
        'data_abordagem',
        'nomes_pessoas',
        'quantidade_pessoas',
        'tipo_abordagem_id',
        'conformidade',
        'casal',
        'classificacao',
        'num_reduzido',
        'catador_reciclados',
        'resistencia',
        'fixacao_antiga',
        'estrutura_abrigo_provisorio',
        'excesso_objetos',
        'trafico_ilicitos',
        'menores_idosos',
        'deficiente',
        'agrupamento_quimico',
        'saude_mental',
        'animais',
        'e1_id',
        'e2_id',
        'e3_id',
        'e4_id',
        'material_apreendido',
        'material_descartado',
        'tipo_abrigo_desmontado_id',
        'qtd_kg',
        'resultado_acao_id',
        'movimento_migratorio',
        'observacao',
        'ponto_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'data_abordagem' => 'date',
            'conformidade' => 'boolean',
            'casal' => 'boolean',
            'num_reduzido' => 'boolean',
            'catador_reciclados' => 'boolean',
            'resistencia' => 'boolean',
            'fixacao_antiga' => 'boolean',
            'estrutura_abrigo_provisorio' => 'boolean',
            'excesso_objetos' => 'boolean',
            'trafico_ilicitos' => 'boolean',
            'menores_idosos' => 'boolean',
            'deficiente' => 'boolean',
            'agrupamento_quimico' => 'boolean',
            'saude_mental' => 'boolean',
            'animais' => 'boolean',
        ];
    }

    public function ponto(): BelongsTo
    {
        return $this->belongsTo(Ponto::class, 'ponto_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
