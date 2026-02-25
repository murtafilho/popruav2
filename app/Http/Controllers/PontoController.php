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
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->leftJoin(DB::raw('(SELECT ponto_id, MAX(id) as ultima_vistoria_id FROM vistorias GROUP BY ponto_id) as uv'), 'uv.ponto_id', '=', 'p.id')
            ->leftJoin('vistorias as v', 'v.id', '=', 'uv.ultima_vistoria_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->select([
                'p.id',
                DB::raw('COALESCE(ea."NUMERO_IMOVEL", p.numero) as numero'),
                'p.complemento',
                'p.lat',
                'p.lng',
                DB::raw('ea."NOME_LOGRADOURO" as logradouro'),
                DB::raw('ea."NOME_BAIRRO_POPULAR" as bairro'),
                DB::raw('ea."NOME_REGIONAL" as regional'),
                DB::raw('ea."SIGLA_TIPO_LOGRADOURO" as tipo'),
                'v.resultado_acao_id',
                'ra.resultado as resultado_acao',
                DB::raw('(SELECT COUNT(*) FROM vistorias WHERE ponto_id = p.id) as total_vistorias'),
                DB::raw('(COALESCE(v.resistencia::int, 0) + COALESCE(v.num_reduzido::int, 0) + COALESCE(v.casal::int, 0) + COALESCE(v.catador_reciclados::int, 0) + COALESCE(v.fixacao_antiga::int, 0) + COALESCE(v.excesso_objetos::int, 0) + COALESCE(v.trafico_ilicitos::int, 0) + COALESCE(v.crianca_adolescente::int, 0) + COALESCE(v.idosos::int, 0) + COALESCE(v.gestante::int, 0) + COALESCE(v.lgbtqiapn::int, 0) + COALESCE(v.cena_uso_caracterizada::int, 0) + COALESCE(v.deficiente::int, 0) + COALESCE(v.agrupamento_quimico::int, 0) + COALESCE(v.saude_mental::int, 0) + COALESCE(v.animais::int, 0)) as complexidade'),
                'v.quantidade_pessoas',
            ])
            ->whereNotNull('p.lat')
            ->whereNotNull('p.lng')
            ->whereNotNull('p.endereco_atualizado_id');

        // Filtros
        if ($request->filled('logradouro')) {
            $query->where('ea.NOME_LOGRADOURO', 'like', '%'.$request->logradouro.'%');
        }

        if ($request->filled('regional')) {
            $query->where('ea.NOME_REGIONAL', $request->regional);
        }

        if ($request->filled('numero')) {
            $query->where('ea.NUMERO_IMOVEL', $request->numero);
        }

        if ($request->filled('bairro')) {
            $query->where('ea.NOME_BAIRRO_POPULAR', 'like', '%'.$request->bairro.'%');
        }

        if ($request->filled('resultado')) {
            $query->where('v.resultado_acao_id', $request->resultado);
        }

        $pontos = $query->orderBy('logradouro')
            ->orderByRaw('NULLIF(regexp_replace(numero, \'[^0-9]\', \'\', \'g\'), \'\')::int NULLS LAST')
            ->paginate(15);

        // Dados para filtros - usando endereco_atualizado
        $bairros = DB::table('endereco_atualizados')
            ->select('NOME_BAIRRO_POPULAR as bairro')
            ->distinct()
            ->whereNotNull('NOME_BAIRRO_POPULAR')
            ->orderBy('NOME_BAIRRO_POPULAR')
            ->pluck('bairro');

        $regionais = DB::table('endereco_atualizados')
            ->select('NOME_REGIONAL as regional')
            ->distinct()
            ->whereNotNull('NOME_REGIONAL')
            ->orderBy('NOME_REGIONAL')
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
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->leftJoin(DB::raw('(SELECT ponto_id, MAX(id) as ultima_vistoria_id FROM vistorias GROUP BY ponto_id) as uv'), 'uv.ponto_id', '=', 'p.id')
            ->leftJoin('vistorias as v', 'v.id', '=', 'uv.ultima_vistoria_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->select([
                'p.id',
                DB::raw('COALESCE(ea."NUMERO_IMOVEL", p.numero) as numero'),
                'p.complemento',
                'p.lat',
                'p.lng',
                DB::raw('ea."NOME_LOGRADOURO" as logradouro'),
                DB::raw('ea."NOME_BAIRRO_POPULAR" as bairro'),
                DB::raw('ea."NOME_REGIONAL" as regional'),
                DB::raw('ea."SIGLA_TIPO_LOGRADOURO" as tipo'),
                'v.resultado_acao_id',
                'ra.resultado as resultado_acao',
                DB::raw('(SELECT COUNT(*) FROM vistorias WHERE ponto_id = p.id) as total_vistorias'),
            ])
            ->where('p.id', $id)
            ->first();

        if (! $ponto) {
            abort(404, 'Ponto não encontrado');
        }

        // Buscar vistorias do ponto ordenadas por data decrescente
        $vistorias = DB::table('vistorias as v')
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

    public function naoGeorreferenciados(Request $request): View
    {
        $query = DB::table('pontos as p')
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->leftJoin(DB::raw('(SELECT ponto_id, MAX(id) as ultima_vistoria_id FROM vistorias GROUP BY ponto_id) as uv'), 'uv.ponto_id', '=', 'p.id')
            ->leftJoin('vistorias as v', 'v.id', '=', 'uv.ultima_vistoria_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->select([
                'p.id',
                DB::raw('COALESCE(ea."NUMERO_IMOVEL", p.numero) as numero'),
                'p.complemento',
                'p.lat',
                'p.lng',
                DB::raw('ea."NOME_LOGRADOURO" as logradouro'),
                DB::raw('ea."NOME_BAIRRO_POPULAR" as bairro'),
                DB::raw('ea."NOME_REGIONAL" as regional'),
                DB::raw('ea."SIGLA_TIPO_LOGRADOURO" as tipo'),
                'v.resultado_acao_id',
                'ra.resultado as resultado_acao',
                DB::raw('(SELECT COUNT(*) FROM vistorias WHERE ponto_id = p.id) as total_vistorias'),
            ])
            ->where(function ($q) {
                $q->whereNull('p.lat')
                    ->orWhereNull('p.lng')
                    ->orWhere('p.lat', '=', 0)
                    ->orWhere('p.lng', '=', 0);
            });

        // Filtros
        if ($request->filled('logradouro')) {
            $query->where('ea.NOME_LOGRADOURO', 'like', '%'.$request->logradouro.'%');
        }

        if ($request->filled('numero')) {
            $query->where('ea.NUMERO_IMOVEL', $request->numero);
        }

        if ($request->filled('bairro')) {
            $query->where('ea.NOME_BAIRRO_POPULAR', 'like', '%'.$request->bairro.'%');
        }

        if ($request->filled('regional')) {
            $query->where('ea.NOME_REGIONAL', $request->regional);
        }

        if ($request->filled('resultado')) {
            $query->where('v.resultado_acao_id', $request->resultado);
        }

        $pontos = $query->orderBy('logradouro')
            ->orderByRaw('NULLIF(regexp_replace(numero, \'[^0-9]\', \'\', \'g\'), \'\')::int NULLS LAST')
            ->paginate(15);

        // Dados para filtros - usando endereco_atualizado
        $bairros = DB::table('endereco_atualizados')
            ->select('NOME_BAIRRO_POPULAR as bairro')
            ->distinct()
            ->whereNotNull('NOME_BAIRRO_POPULAR')
            ->orderBy('NOME_BAIRRO_POPULAR')
            ->pluck('bairro');

        $regionais = DB::table('endereco_atualizados')
            ->select('NOME_REGIONAL as regional')
            ->distinct()
            ->whereNotNull('NOME_REGIONAL')
            ->orderBy('NOME_REGIONAL')
            ->pluck('regional');

        $resultados = DB::table('resultados_acoes')
            ->orderBy('id')
            ->get();

        return view('pontos.nao-georreferenciados', [
            'pontos' => $pontos,
            'bairros' => $bairros,
            'regionais' => $regionais,
            'resultados' => $resultados,
        ]);
    }
}
