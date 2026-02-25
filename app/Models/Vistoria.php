<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Vistoria extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'vistorias';

    protected $fillable = [
        'data_abordagem',
        'nomes_pessoas',
        'quantidade_pessoas',
        'tipo_abordagem_id',
        'casal',
        'qtd_casais',
        'classificacao',
        'num_reduzido',
        'catador_reciclados',
        'resistencia',
        'fixacao_antiga',
        'excesso_objetos',
        'trafico_ilicitos',
        'crianca_adolescente',
        'idosos',
        'gestante',
        'lgbtqiapn',
        'cena_uso_caracterizada',
        'qtd_abrigos_provisorios',
        'abrigos_tipos',
        'deficiente',
        'agrupamento_quimico',
        'saude_mental',
        'animais',
        'qtd_animais',
        'conducao_forcas_seguranca',
        'conducao_forcas_observacao',
        'apreensao_fiscal',
        'auto_fiscalizacao_aplicado',
        'auto_fiscalizacao_numero',
        'e1_id',
        'e2_id',
        'e3_id',
        'e4_id',
        'e5_id',
        'e6_id',
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
            'casal' => 'boolean',
            'num_reduzido' => 'boolean',
            'catador_reciclados' => 'boolean',
            'resistencia' => 'boolean',
            'fixacao_antiga' => 'boolean',
            'excesso_objetos' => 'boolean',
            'trafico_ilicitos' => 'boolean',
            'crianca_adolescente' => 'boolean',
            'idosos' => 'boolean',
            'gestante' => 'boolean',
            'lgbtqiapn' => 'boolean',
            'cena_uso_caracterizada' => 'boolean',
            'abrigos_tipos' => 'array',
            'deficiente' => 'boolean',
            'agrupamento_quimico' => 'boolean',
            'saude_mental' => 'boolean',
            'animais' => 'boolean',
            'conducao_forcas_seguranca' => 'boolean',
            'apreensao_fiscal' => 'boolean',
            'auto_fiscalizacao_aplicado' => 'boolean',
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

    public function tipoAbordagem(): BelongsTo
    {
        return $this->belongsTo(TipoAbordagem::class, 'tipo_abordagem_id');
    }

    public function tipoAbrigoDesmontado(): BelongsTo
    {
        return $this->belongsTo(TipoAbrigoDesmontado::class, 'tipo_abrigo_desmontado_id');
    }

    public function resultadoAcao(): BelongsTo
    {
        return $this->belongsTo(ResultadoAcao::class, 'resultado_acao_id');
    }

    public function encaminhamento1(): BelongsTo
    {
        return $this->belongsTo(Encaminhamento::class, 'e1_id');
    }

    public function encaminhamento2(): BelongsTo
    {
        return $this->belongsTo(Encaminhamento::class, 'e2_id');
    }

    public function encaminhamento3(): BelongsTo
    {
        return $this->belongsTo(Encaminhamento::class, 'e3_id');
    }

    public function encaminhamento4(): BelongsTo
    {
        return $this->belongsTo(Encaminhamento::class, 'e4_id');
    }

    public function encaminhamento5(): BelongsTo
    {
        return $this->belongsTo(Encaminhamento::class, 'e5_id');
    }

    public function encaminhamento6(): BelongsTo
    {
        return $this->belongsTo(Encaminhamento::class, 'e6_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(VistoriaFoto::class, 'vistoria_id')->orderBy('ordem');
    }

    /**
     * Moradores que entraram no ponto nesta vistoria
     */
    public function moradoresEntrada(): HasMany
    {
        return $this->hasMany(MoradorHistorico::class, 'vistoria_entrada_id');
    }

    /**
     * Moradores que saíram do ponto nesta vistoria
     */
    public function moradoresSaida(): HasMany
    {
        return $this->hasMany(MoradorHistorico::class, 'vistoria_saida_id');
    }

    /**
     * Registrar coleção de mídia para fotos
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('fotos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Conversões de imagem (thumbnails)
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('fotos');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->performOnCollections('fotos');
    }
}
