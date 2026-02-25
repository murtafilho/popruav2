<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WarmCacheCommand extends Command
{
    protected $signature = 'cache:warm {--force : Força regeneração mesmo se cache existir}';
    protected $description = 'Aquece o cache com dados estáticos (geo, etc.)';

    private const TTL = 86400;

    public function handle(): int
    {
        $this->info('Aquecendo cache...');
        $start = microtime(true);

        $items = [
            'geo:bairros'          => fn () => $this->loadBairros(),
            'geo:regionais'        => fn () => $this->loadRegionais(),
            'geo:limite-municipio' => fn () => $this->loadLimite(),
        ];

        foreach ($items as $key => $loader) {
            if ($this->option('force')) {
                Cache::forget($key);
            }

            if (Cache::has($key) && ! $this->option('force')) {
                $this->line("  <comment>SKIP</comment>  {$key} (ja em cache)");
                continue;
            }

            $this->line("  <info>LOAD</info>  {$key}...");
            $t = microtime(true);
            Cache::remember($key, self::TTL, $loader);
            $ms = round((microtime(true) - $t) * 1000);
            $this->line("  <info>OK</info>    {$key} ({$ms}ms)");
        }

        $total = round((microtime(true) - $start) * 1000);
        $this->info("Cache aquecido em {$total}ms.");

        return 0;
    }

    private function loadBairros(): array
    {
        return DB::table('geo_bairros')
            ->select('id', 'codigo', 'nome', 'area_km2', 'perimetro_m', 'geometry')
            ->get()
            ->map(fn ($b) => [
                'type'       => 'Feature',
                'properties' => [
                    'id' => $b->id, 'codigo' => $b->codigo, 'nome' => $b->nome,
                    'area_km2' => $b->area_km2, 'perimetro_m' => $b->perimetro_m,
                ],
                'geometry' => json_decode($b->geometry),
            ])->all();
    }

    private function loadRegionais(): array
    {
        return DB::table('geo_regionais')
            ->select('id', 'codigo', 'sigla', 'nome', 'area_km2', 'perimetro_m', 'geometry')
            ->get()
            ->map(fn ($r) => [
                'type'       => 'Feature',
                'properties' => [
                    'id' => $r->id, 'codigo' => $r->codigo, 'sigla' => $r->sigla,
                    'nome' => $r->nome, 'area_km2' => $r->area_km2, 'perimetro_m' => $r->perimetro_m,
                ],
                'geometry' => json_decode($r->geometry),
            ])->all();
    }

    private function loadLimite(): ?array
    {
        $limite = DB::table('geo_limite_municipio')
            ->select('id', 'area_km2', 'perimetro_m', 'geometry')
            ->first();

        if (! $limite) return null;

        return [[
            'type'       => 'Feature',
            'properties' => [
                'id' => $limite->id, 'area_km2' => $limite->area_km2, 'perimetro_m' => $limite->perimetro_m,
            ],
            'geometry' => json_decode($limite->geometry),
        ]];
    }
}
