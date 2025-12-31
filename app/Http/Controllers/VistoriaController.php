<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VistoriaController extends Controller
{
    public function create(Request $request): View
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        // Buscar ponto próximo ou criar novo
        $pontoProximo = null;
        if ($lat && $lng) {
            $pontoProximo = DB::table('pontos as p')
                ->join('ender as e', 'e.id', '=', 'p.endereco_id')
                ->select('p.id', 'p.numero', 'p.lat', 'p.lng', 'e.logradouro', 'e.bairro')
                ->whereNotNull('p.lat')
                ->whereNotNull('p.lng')
                ->whereRaw('ST_Distance_Sphere(POINT(p.lng, p.lat), POINT(?, ?)) < 50', [$lng, $lat])
                ->orderByRaw('ST_Distance_Sphere(POINT(p.lng, p.lat), POINT(?, ?))', [$lng, $lat])
                ->first();
        }

        // Dados para os selects
        $tiposAbordagem = DB::table('tipo_abordagem')->orderBy('id')->get();
        $tiposAbrigo = DB::table('tipo_abrigo_desmontado')->orderBy('id')->get();
        $resultadosAcao = DB::table('resultados_acoes')->orderBy('id')->get();

        return view('vistorias.create', [
            'lat' => $lat,
            'lng' => $lng,
            'pontoProximo' => $pontoProximo,
            'tiposAbordagem' => $tiposAbordagem,
            'tiposAbrigo' => $tiposAbrigo,
            'resultadosAcao' => $resultadosAcao,
        ]);
    }

    public function index(Request $request): View
    {
        $query = DB::table('vistorias as v')
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
                'e.logradouro',
                'e.tipo',
                'p.numero',
                'p.lat',
                'p.lng',
                'e.bairro',
                'e.regional',
                'ta.tipo as tipo_abordagem',
                'ra.resultado as resultado_acao',
                'u.name as usuario',
            ]);

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

        if ($request->filled('data_inicio')) {
            $query->whereDate('v.data_abordagem', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('v.data_abordagem', '<=', $request->data_fim);
        }

        $vistorias = $query->orderBy('v.data_abordagem', 'desc')
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

        return view('vistorias.index', [
            'vistorias' => $vistorias,
            'bairros' => $bairros,
            'regionais' => $regionais,
            'resultados' => $resultados,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ponto_id' => 'nullable|exists:pontos,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'data_abordagem' => 'required|date_format:Y-m-d\TH:i',
            'tipo_abordagem_id' => 'required|exists:tipo_abordagem,id',
            'quantidade_pessoas' => 'nullable|integer|min:0',
            'nomes_pessoas' => 'nullable|string|max:500',
            'resultado_acao_id' => 'required|exists:resultados_acoes,id',
            'tipo_abrigo_desmontado_id' => 'nullable|exists:tipo_abrigo_desmontado,id',
            'qtd_kg' => 'nullable|integer|min:0',
            'observacao' => 'nullable|string|max:1000',
            // Campos boolean de complexidade
            'resistencia' => 'nullable|boolean',
            'num_reduzido' => 'nullable|boolean',
            'casal' => 'nullable|boolean',
            'catador_reciclados' => 'nullable|boolean',
            'fixacao_antiga' => 'nullable|boolean',
            'estrutura_abrigo_provisorio' => 'nullable|boolean',
            'excesso_objetos' => 'nullable|boolean',
            'trafico_ilicitos' => 'nullable|boolean',
            'menores_idosos' => 'nullable|boolean',
            'deficiente' => 'nullable|boolean',
            'agrupamento_quimico' => 'nullable|boolean',
            'saude_mental' => 'nullable|boolean',
            'animais' => 'nullable|boolean',
        ]);

        // Se não tem ponto_id, precisa criar ou buscar ponto
        $pontoId = $validated['ponto_id'] ?? null;

        if (!$pontoId) {
            // Buscar ponto próximo (dentro de 50m)
            $pontoProximo = DB::table('pontos')
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->whereRaw('ST_Distance_Sphere(POINT(lng, lat), POINT(?, ?)) < 50', [$validated['lng'], $validated['lat']])
                ->first();

            if ($pontoProximo) {
                $pontoId = $pontoProximo->id;
            } else {
                // Criar novo ponto (simplificado - sem endereço)
                $pontoId = DB::table('pontos')->insertGetId([
                    'lat' => $validated['lat'],
                    'lng' => $validated['lng'],
                    'numero' => 'S/N',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Criar vistoria
        $dataAbordagem = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['data_abordagem']);

        DB::table('vistorias')->insert([
            'ponto_id' => $pontoId,
            'data_abordagem' => $dataAbordagem,
            'tipo_abordagem_id' => $validated['tipo_abordagem_id'],
            'quantidade_pessoas' => $validated['quantidade_pessoas'] ?? 0,
            'nomes_pessoas' => $validated['nomes_pessoas'] ?? '',
            'resultado_acao_id' => $validated['resultado_acao_id'],
            'tipo_abrigo_desmontado_id' => $validated['tipo_abrigo_desmontado_id'],
            'qtd_kg' => $validated['qtd_kg'] ?? 0,
            'observacao' => $validated['observacao'] ?? '',
            'user_id' => auth()->id(),
            'resistencia' => $request->boolean('resistencia') ? 1 : 0,
            'num_reduzido' => $request->boolean('num_reduzido') ? 1 : 0,
            'casal' => $request->boolean('casal') ? 1 : 0,
            'catador_reciclados' => $request->boolean('catador_reciclados') ? 1 : 0,
            'fixacao_antiga' => $request->boolean('fixacao_antiga') ? 1 : 0,
            'estrutura_abrigo_provisorio' => $request->boolean('estrutura_abrigo_provisorio') ? 1 : 0,
            'excesso_objetos' => $request->boolean('excesso_objetos') ? 1 : 0,
            'trafico_ilicitos' => $request->boolean('trafico_ilicitos') ? 1 : 0,
            'menores_idosos' => $request->boolean('menores_idosos') ? 1 : 0,
            'deficiente' => $request->boolean('deficiente') ? 1 : 0,
            'agrupamento_quimico' => $request->boolean('agrupamento_quimico') ? 1 : 0,
            'saude_mental' => $request->boolean('saude_mental') ? 1 : 0,
            'animais' => $request->boolean('animais') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('mapa.index')->with('success', 'Vistoria registrada com sucesso!');
    }
}
