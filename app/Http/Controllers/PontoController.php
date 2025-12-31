<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PontoController extends Controller
{
    public function index(Request $request): View
    {
        $query = DB::table('pontos as p')
            ->join('ender as e', 'e.id', '=', 'p.endereco_id')
            ->leftJoin(DB::raw('(SELECT ponto_id, MAX(id) as ultima_vistoria_id FROM vistorias GROUP BY ponto_id) as uv'), 'uv.ponto_id', '=', 'p.id')
            ->leftJoin('vistorias as v', 'v.id', '=', 'uv.ultima_vistoria_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->select([
                'p.id',
                'p.numero',
                'p.complemento',
                'p.lat',
                'p.lng',
                'e.logradouro',
                'e.bairro',
                'e.regional',
                'e.tipo',
                'v.resultado_acao_id',
                'ra.resultado as resultado_acao',
                DB::raw('(SELECT COUNT(*) FROM vistorias WHERE ponto_id = p.id) as total_vistorias'),
            ])
            ->whereNotNull('p.lat')
            ->whereNotNull('p.lng');

        // Filtros
        if ($request->filled('bairro')) {
            $query->where('e.bairro', 'like', '%' . $request->bairro . '%');
        }

        if ($request->filled('regional')) {
            $query->where('e.regional', $request->regional);
        }

        if ($request->filled('resultado')) {
            $query->where('v.resultado_acao_id', $request->resultado);
        }

        $pontos = $query->orderBy('e.logradouro')
            ->orderBy('p.numero')
            ->paginate(50);

        // Dados para filtros
        $bairros = DB::table('ender')
            ->select('bairro')
            ->distinct()
            ->whereNotNull('bairro')
            ->orderBy('bairro')
            ->pluck('bairro');

        $regionais = DB::table('ender')
            ->select('regional')
            ->distinct()
            ->whereNotNull('regional')
            ->orderBy('regional')
            ->pluck('regional');

        $resultados = DB::table('resultados_acoes')
            ->orderBy('id')
            ->get();

        return view('pontos.index', [
            'pontos' => $pontos,
            'bairros' => $bairros,
            'regionais' => $regionais,
            'resultados' => $resultados,
        ]);
    }

    public function show(int $id): View
    {
        // Buscar dados do ponto
        $ponto = DB::table('pontos as p')
            ->join('ender as e', 'e.id', '=', 'p.endereco_id')
            ->leftJoin(DB::raw('(SELECT ponto_id, MAX(id) as ultima_vistoria_id FROM vistorias GROUP BY ponto_id) as uv'), 'uv.ponto_id', '=', 'p.id')
            ->leftJoin('vistorias as v', 'v.id', '=', 'uv.ultima_vistoria_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->select([
                'p.id',
                'p.numero',
                'p.complemento',
                'p.lat',
                'p.lng',
                'e.logradouro',
                'e.bairro',
                'e.regional',
                'e.tipo',
                'v.resultado_acao_id',
                'ra.resultado as resultado_acao',
                DB::raw('(SELECT COUNT(*) FROM vistorias WHERE ponto_id = p.id) as total_vistorias'),
            ])
            ->where('p.id', $id)
            ->first();

        if (!$ponto) {
            abort(404, 'Ponto não encontrado');
        }

        // Buscar vistorias do ponto ordenadas por data decrescente
        $vistorias = DB::table('vistorias as v')
            ->join('pontos as p', 'p.id', '=', 'v.ponto_id')
            ->join('ender as e', 'e.id', '=', 'p.endereco_id')
            ->leftJoin('tipo_abordagem as ta', 'ta.id', '=', 'v.tipo_abordagem_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
            ->select([
                'v.id',
                'v.data_abordagem',
                'v.quantidade_pessoas',
                'v.qtd_kg',
                'v.observacao',
                'v.nomes_pessoas',
                'ta.tipo as tipo_abordagem',
                'ra.resultado as resultado_acao',
                'u.name as usuario',
            ])
            ->where('v.ponto_id', $id)
            ->orderBy('v.data_abordagem', 'desc')
            ->get();

        return view('pontos.show', [
            'ponto' => $ponto,
            'vistorias' => $vistorias,
        ]);
    }
}
