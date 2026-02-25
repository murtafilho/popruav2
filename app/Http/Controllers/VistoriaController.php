<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use App\Models\Vistoria;
use App\Services\EnderecoService;
use App\Services\MoradorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VistoriaController extends Controller
{
    public function __construct(
        private EnderecoService $enderecoService,
        private MoradorService $moradorService
    ) {}

    public function show(Vistoria $vistoria): View
    {
        $vistoria->load([
            'ponto.enderecoAtualizado',
            'user',
            'tipoAbordagem',
            'tipoAbrigoDesmontado',
            'resultadoAcao',
            'encaminhamento1',
            'encaminhamento2',
            'encaminhamento3',
            'encaminhamento4',
            'encaminhamento5',
            'encaminhamento6',
            'moradoresEntrada.morador',
        ]);

        // Buscar tipos de abrigo selecionados se houver
        $tiposAbrigoSelecionados = [];
        if ($vistoria->abrigos_tipos) {
            $tiposAbrigoSelecionados = DB::table('tipo_abrigo_desmontado')
                ->whereIn('id', $vistoria->abrigos_tipos)
                ->pluck('tipo_abrigo')
                ->toArray();
        }

        return view('vistorias.show', [
            'vistoria' => $vistoria,
            'tiposAbrigoSelecionados' => $tiposAbrigoSelecionados,
        ]);
    }

    public function report(Vistoria $vistoria): View
    {
        $vistoria->load([
            'ponto.enderecoAtualizado',
            'user',
            'tipoAbordagem',
            'tipoAbrigoDesmontado',
            'resultadoAcao',
            'encaminhamento1',
            'encaminhamento2',
            'encaminhamento3',
            'encaminhamento4',
            'encaminhamento5',
            'encaminhamento6',
            'moradoresEntrada.morador',
        ]);

        // Buscar tipos de abrigo selecionados se houver
        $tiposAbrigoSelecionados = [];
        if ($vistoria->abrigos_tipos) {
            $tiposAbrigoSelecionados = DB::table('tipo_abrigo_desmontado')
                ->whereIn('id', $vistoria->abrigos_tipos)
                ->pluck('tipo_abrigo')
                ->toArray();
        }

        return view('vistorias.report', [
            'vistoria' => $vistoria,
            'tiposAbrigoSelecionados' => $tiposAbrigoSelecionados,
        ]);
    }

    public function edit(Vistoria $vistoria): View
    {
        $vistoria->load([
            'ponto.enderecoAtualizado',
            'ponto.moradores' => function ($query) {
                $query->whereNotNull('ponto_atual_id');
            },
        ]);

        // Dados para os selects
        $tiposAbordagem = DB::table('tipo_abordagem')->orderBy('id')->get();
        $tiposAbrigo = DB::table('tipo_abrigo_desmontado')->orderBy('id')->get();
        $resultadosAcao = DB::table('resultados_acoes')->orderBy('id')->get();
        $encaminhamentos = DB::table('encaminhamentos')->orderBy('id')->get();

        return view('vistorias.edit', [
            'vistoria' => $vistoria,
            'tiposAbordagem' => $tiposAbordagem,
            'tiposAbrigo' => $tiposAbrigo,
            'resultadosAcao' => $resultadosAcao,
            'encaminhamentos' => $encaminhamentos,
        ]);
    }

    public function update(Request $request, Vistoria $vistoria): RedirectResponse
    {
        $validated = $request->validate([
            'data_abordagem' => 'required|date_format:Y-m-d\TH:i',
            'tipo_abordagem_id' => 'required|exists:tipo_abordagem,id',
            'quantidade_pessoas' => 'nullable|integer|min:0',
            'nomes_pessoas' => 'nullable|string',
            'resultado_acao_id' => 'required|exists:resultados_acoes,id',
            'tipo_abrigo_desmontado_id' => 'nullable|exists:tipo_abrigo_desmontado,id',
            'qtd_kg' => 'nullable|integer|min:0',
            'observacao' => 'nullable|string',
            'fotos.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
            'remover_fotos' => 'nullable|array',
            'remover_fotos.*' => 'integer',
            // Campos boolean de complexidade
            'resistencia' => 'nullable|boolean',
            'num_reduzido' => 'nullable|boolean',
            'casal' => 'nullable|boolean',
            'qtd_casais' => 'nullable|integer|min:0',
            'catador_reciclados' => 'nullable|boolean',
            'fixacao_antiga' => 'nullable|boolean',
            'excesso_objetos' => 'nullable|boolean',
            'trafico_ilicitos' => 'nullable|boolean',
            'crianca_adolescente' => 'nullable|boolean',
            'idosos' => 'nullable|boolean',
            'gestante' => 'nullable|boolean',
            'lgbtqiapn' => 'nullable|boolean',
            'cena_uso_caracterizada' => 'nullable|boolean',
            'deficiente' => 'nullable|boolean',
            'agrupamento_quimico' => 'nullable|boolean',
            'saude_mental' => 'nullable|boolean',
            'animais' => 'nullable|boolean',
            'qtd_animais' => 'nullable|integer|min:0',
            // Campos de abrigos
            'qtd_abrigos_provisorios' => 'nullable|integer|min:0',
            'abrigos_tipos' => 'nullable|array',
            'abrigos_tipos.*' => 'nullable|exists:tipo_abrigo_desmontado,id',
            // Campos de fiscalização
            'conducao_forcas_seguranca' => 'nullable|in:0,1',
            'conducao_forcas_observacao' => 'nullable|string',
            'apreensao_fiscal' => 'nullable|boolean',
            'auto_fiscalizacao_aplicado' => 'nullable|in:0,1',
            'auto_fiscalizacao_numero' => 'nullable|string|max:100',
            // Encaminhamentos
            'e1_id' => 'nullable|exists:encaminhamentos,id',
            'e2_id' => 'nullable|exists:encaminhamentos,id',
            'e3_id' => 'nullable|exists:encaminhamentos,id',
            'e4_id' => 'nullable|exists:encaminhamentos,id',
            'e5_id' => 'nullable|exists:encaminhamentos,id',
            'e6_id' => 'nullable|exists:encaminhamentos,id',
        ]);

        $dataAbordagem = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['data_abordagem']);

        // Processar tipos de abrigo (filtrar valores vazios)
        $abrigosTipos = null;
        if (! empty($validated['abrigos_tipos'])) {
            $abrigosTipos = array_values(array_filter($validated['abrigos_tipos'], fn ($v) => ! empty($v)));
            if (empty($abrigosTipos)) {
                $abrigosTipos = null;
            }
        }

        $vistoria->update([
            'data_abordagem' => $dataAbordagem,
            'tipo_abordagem_id' => $validated['tipo_abordagem_id'],
            'quantidade_pessoas' => $validated['quantidade_pessoas'] ?? 0,
            'nomes_pessoas' => $validated['nomes_pessoas'] ?? '',
            'resultado_acao_id' => $validated['resultado_acao_id'],
            'tipo_abrigo_desmontado_id' => $validated['tipo_abrigo_desmontado_id'],
            'qtd_kg' => $validated['qtd_kg'] ?? 0,
            'observacao' => $validated['observacao'] ?? '',
            // Campos boolean de complexidade
            'resistencia' => $request->boolean('resistencia') ? 1 : 0,
            'num_reduzido' => $request->boolean('num_reduzido') ? 1 : 0,
            'casal' => $request->boolean('casal') ? 1 : 0,
            'qtd_casais' => $request->boolean('casal') ? ($validated['qtd_casais'] ?? 1) : 0,
            'catador_reciclados' => $request->boolean('catador_reciclados') ? 1 : 0,
            'fixacao_antiga' => $request->boolean('fixacao_antiga') ? 1 : 0,
            'excesso_objetos' => $request->boolean('excesso_objetos') ? 1 : 0,
            'trafico_ilicitos' => $request->boolean('trafico_ilicitos') ? 1 : 0,
            'crianca_adolescente' => $request->boolean('crianca_adolescente') ? 1 : 0,
            'idosos' => $request->boolean('idosos') ? 1 : 0,
            'gestante' => $request->boolean('gestante') ? 1 : 0,
            'lgbtqiapn' => $request->boolean('lgbtqiapn') ? 1 : 0,
            'cena_uso_caracterizada' => $request->boolean('cena_uso_caracterizada') ? 1 : 0,
            'deficiente' => $request->boolean('deficiente') ? 1 : 0,
            'agrupamento_quimico' => $request->boolean('agrupamento_quimico') ? 1 : 0,
            'saude_mental' => $request->boolean('saude_mental') ? 1 : 0,
            'animais' => $request->boolean('animais') ? 1 : 0,
            'qtd_animais' => $request->boolean('animais') ? ($validated['qtd_animais'] ?? 1) : 0,
            // Campos de abrigos
            'qtd_abrigos_provisorios' => $validated['qtd_abrigos_provisorios'] ?? 0,
            'abrigos_tipos' => $abrigosTipos,
            // Campos de fiscalização
            'conducao_forcas_seguranca' => ($validated['conducao_forcas_seguranca'] ?? '0') === '1',
            'conducao_forcas_observacao' => ($validated['conducao_forcas_seguranca'] ?? '0') === '1'
                ? ($validated['conducao_forcas_observacao'] ?? '')
                : null,
            'apreensao_fiscal' => $request->boolean('apreensao_fiscal') ? 1 : 0,
            'auto_fiscalizacao_aplicado' => ($validated['auto_fiscalizacao_aplicado'] ?? '0') === '1',
            'auto_fiscalizacao_numero' => ($validated['auto_fiscalizacao_aplicado'] ?? '0') === '1'
                ? ($validated['auto_fiscalizacao_numero'] ?? '')
                : null,
            // Encaminhamentos
            'e1_id' => $validated['e1_id'] ?? null,
            'e2_id' => $validated['e2_id'] ?? null,
            'e3_id' => $validated['e3_id'] ?? null,
            'e4_id' => $validated['e4_id'] ?? null,
            'e5_id' => $validated['e5_id'] ?? null,
            'e6_id' => $validated['e6_id'] ?? null,
        ]);

        // Remover fotos selecionadas
        if (! empty($validated['remover_fotos'])) {
            $vistoria->getMedia('fotos')
                ->whereIn('id', $validated['remover_fotos'])
                ->each(fn ($media) => $media->delete());
        }

        // Processar upload de novas fotos
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                if ($foto->isValid()) {
                    $vistoria->addMedia($foto)
                        ->usingName($foto->getClientOriginalName())
                        ->toMediaCollection('fotos');
                }
            }
        }

        return redirect()->route('vistorias.index')->with('success', 'Vistoria atualizada com sucesso!');
    }

    public function createForPonto(Ponto $ponto): RedirectResponse
    {
        // Redireciona para o create com as coordenadas do ponto
        if ($ponto->lat && $ponto->lng) {
            return redirect()->route('vistorias.create', [
                'lat' => $ponto->lat,
                'lng' => $ponto->lng,
            ]);
        }

        // Ponto sem coordenadas, vai para criação normal
        return redirect()->route('vistorias.create');
    }

    public function create(Request $request): View
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        // Buscar ponto próximo ou criar novo
        $pontoProximo = null;
        if ($lat && $lng) {
            $pontoProximo = Ponto::with(['enderecoAtualizado', 'moradores' => function ($query) {
                $query->whereNotNull('ponto_atual_id');
            }])
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->whereRaw('ST_Distance(geom::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) < 50', [$lng, $lat])
                ->orderByRaw('ST_Distance(geom::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography)', [$lng, $lat])
                ->first();
        }

        // Dados do endereço de referência (passados do mapa)
        $enderecoReferencia = null;
        $referenciaAutomatica = null;
        if ($request->filled('endereco_logradouro')) {
            $enderecoReferencia = [
                'tipo' => $request->query('endereco_tipo'),
                'logradouro' => $request->query('endereco_logradouro'),
                'numero' => $request->query('endereco_numero'),
                'bairro' => $request->query('endereco_bairro'),
                'regional' => $request->query('endereco_regional'),
                'distancia' => $request->query('endereco_distancia'),
            ];

            // Gerar referência automática para novos pontos
            if (! $pontoProximo && $enderecoReferencia['distancia']) {
                $referenciaAutomatica = sprintf(
                    'A %dm de %s %s, %s',
                    (int) $enderecoReferencia['distancia'],
                    $enderecoReferencia['tipo'],
                    $enderecoReferencia['logradouro'],
                    $enderecoReferencia['numero']
                );
            }
        }

        // Dados para os selects
        $tiposAbordagem = DB::table('tipo_abordagem')->orderBy('id')->get();
        $tiposAbrigo = DB::table('tipo_abrigo_desmontado')->orderBy('id')->get();
        $resultadosAcao = DB::table('resultados_acoes')->orderBy('id')->get();
        $encaminhamentos = DB::table('encaminhamentos')->orderBy('id')->get();

        return view('vistorias.create', [
            'lat' => $lat,
            'lng' => $lng,
            'pontoProximo' => $pontoProximo,
            'enderecoReferencia' => $enderecoReferencia,
            'referenciaAutomatica' => $referenciaAutomatica,
            'tiposAbordagem' => $tiposAbordagem,
            'tiposAbrigo' => $tiposAbrigo,
            'resultadosAcao' => $resultadosAcao,
            'encaminhamentos' => $encaminhamentos,
        ]);
    }

    public function index(Request $request): View
    {
        $query = DB::table('vistorias as v')
            ->join('pontos as p', 'p.id', '=', 'v.ponto_id')
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->leftJoin('tipo_abordagem as ta', 'ta.id', '=', 'v.tipo_abordagem_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
            ->select([
                'v.id',
                'v.data_abordagem',
                'v.quantidade_pessoas',
                'v.qtd_kg',
                'v.observacao',
                DB::raw('ea."NOME_LOGRADOURO" as logradouro'),
                DB::raw('ea."SIGLA_TIPO_LOGRADOURO" as tipo'),
                DB::raw('COALESCE(ea."NUMERO_IMOVEL", p.numero) as numero'),
                'p.lat',
                'p.lng',
                DB::raw('ea."NOME_BAIRRO_POPULAR" as bairro'),
                DB::raw('ea."NOME_REGIONAL" as regional'),
                'ta.tipo as tipo_abordagem',
                'ra.resultado as resultado_acao',
                'u.name as usuario',
            ]);

        // Filtros
        if ($request->filled('bairro')) {
            $query->where('ea.NOME_BAIRRO_POPULAR', 'like', '%'.$request->bairro.'%');
        }

        if ($request->filled('regional')) {
            $query->where('ea.NOME_REGIONAL', $request->regional);
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
            ->paginate(10);

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
            'nomes_pessoas' => 'nullable|string',
            'resultado_acao_id' => 'required|exists:resultados_acoes,id',
            'tipo_abrigo_desmontado_id' => 'nullable|exists:tipo_abrigo_desmontado,id',
            'qtd_kg' => 'nullable|integer|min:0',
            'observacao' => 'nullable|string',
            'complemento_ponto' => 'nullable|string|max:255',
            'fotos.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
            // Campos boolean de complexidade
            'resistencia' => 'nullable|boolean',
            'num_reduzido' => 'nullable|boolean',
            'casal' => 'nullable|boolean',
            'qtd_casais' => 'nullable|integer|min:0',
            'catador_reciclados' => 'nullable|boolean',
            'fixacao_antiga' => 'nullable|boolean',
            'excesso_objetos' => 'nullable|boolean',
            'trafico_ilicitos' => 'nullable|boolean',
            'crianca_adolescente' => 'nullable|boolean',
            'idosos' => 'nullable|boolean',
            'gestante' => 'nullable|boolean',
            'lgbtqiapn' => 'nullable|boolean',
            'cena_uso_caracterizada' => 'nullable|boolean',
            'deficiente' => 'nullable|boolean',
            'agrupamento_quimico' => 'nullable|boolean',
            'saude_mental' => 'nullable|boolean',
            'animais' => 'nullable|boolean',
            'qtd_animais' => 'nullable|integer|min:0',
            // Novos campos de abrigos
            'qtd_abrigos_provisorios' => 'nullable|integer|min:0',
            'abrigos_tipos' => 'nullable|array',
            'abrigos_tipos.*' => 'nullable|exists:tipo_abrigo_desmontado,id',
            // Campos de fiscalização
            'conducao_forcas_seguranca' => 'nullable|in:0,1',
            'conducao_forcas_observacao' => 'nullable|string',
            'apreensao_fiscal' => 'nullable|boolean',
            'auto_fiscalizacao_aplicado' => 'nullable|in:0,1',
            'auto_fiscalizacao_numero' => 'nullable|string|max:100',
            // Encaminhamentos
            'e1_id' => 'nullable|exists:encaminhamentos,id',
            'e2_id' => 'nullable|exists:encaminhamentos,id',
            'e3_id' => 'nullable|exists:encaminhamentos,id',
            'e4_id' => 'nullable|exists:encaminhamentos,id',
            'e5_id' => 'nullable|exists:encaminhamentos,id',
            'e6_id' => 'nullable|exists:encaminhamentos,id',
            // Moradores
            'moradores_presentes' => 'nullable|array',
            'moradores_presentes.*' => 'exists:moradores,id',
            'novos_moradores' => 'nullable|array',
            'novos_moradores.*.nome_social' => 'required|string|max:255',
            'novos_moradores.*.apelido' => 'nullable|string|max:255',
            'novos_moradores.*.genero' => 'nullable|string|max:100',
            'novos_moradores.*.documento' => 'nullable|string|max:50',
            'novos_moradores.*.contato' => 'nullable|string|max:50',
            'novos_moradores.*.observacoes' => 'nullable|string',
        ]);

        // Se não tem ponto_id, precisa criar ou buscar ponto
        $pontoId = $validated['ponto_id'] ?? null;
        $pontoNovo = false;

        if (! $pontoId) {
            // Buscar ponto próximo (dentro de 50m)
            $pontoProximo = DB::table('pontos')
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->whereRaw('ST_Distance(geom::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) < 50', [$validated['lng'], $validated['lat']])
                ->first();

            if ($pontoProximo) {
                $pontoId = $pontoProximo->id;
            } else {
                // Criar novo ponto
                $pontoId = DB::table('pontos')->insertGetId([
                    'lat' => $validated['lat'],
                    'lng' => $validated['lng'],
                    'numero' => 'S/N',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Buscar e vincular endereço mais próximo da base de endereços
                // Passa o complemento informado pelo usuário
                $this->enderecoService->vincularEnderecoAoPonto(
                    $pontoId,
                    (float) $validated['lat'],
                    (float) $validated['lng'],
                    $validated['complemento_ponto'] ?? null
                );

                $pontoNovo = true;
            }
        }

        // Criar vistoria usando Eloquent para ter acesso ao Spatie Media Library
        $dataAbordagem = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['data_abordagem']);

        // Processar tipos de abrigo (filtrar valores vazios)
        $abrigosTipos = null;
        if (! empty($validated['abrigos_tipos'])) {
            $abrigosTipos = array_values(array_filter($validated['abrigos_tipos'], fn ($v) => ! empty($v)));
            if (empty($abrigosTipos)) {
                $abrigosTipos = null;
            }
        }

        $vistoria = Vistoria::create([
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
            // Campos boolean de complexidade
            'resistencia' => $request->boolean('resistencia') ? 1 : 0,
            'num_reduzido' => $request->boolean('num_reduzido') ? 1 : 0,
            'casal' => $request->boolean('casal') ? 1 : 0,
            'qtd_casais' => $request->boolean('casal') ? ($validated['qtd_casais'] ?? 1) : 0,
            'catador_reciclados' => $request->boolean('catador_reciclados') ? 1 : 0,
            'fixacao_antiga' => $request->boolean('fixacao_antiga') ? 1 : 0,
            'excesso_objetos' => $request->boolean('excesso_objetos') ? 1 : 0,
            'trafico_ilicitos' => $request->boolean('trafico_ilicitos') ? 1 : 0,
            'crianca_adolescente' => $request->boolean('crianca_adolescente') ? 1 : 0,
            'idosos' => $request->boolean('idosos') ? 1 : 0,
            'gestante' => $request->boolean('gestante') ? 1 : 0,
            'lgbtqiapn' => $request->boolean('lgbtqiapn') ? 1 : 0,
            'cena_uso_caracterizada' => $request->boolean('cena_uso_caracterizada') ? 1 : 0,
            'deficiente' => $request->boolean('deficiente') ? 1 : 0,
            'agrupamento_quimico' => $request->boolean('agrupamento_quimico') ? 1 : 0,
            'saude_mental' => $request->boolean('saude_mental') ? 1 : 0,
            'animais' => $request->boolean('animais') ? 1 : 0,
            'qtd_animais' => $request->boolean('animais') ? ($validated['qtd_animais'] ?? 1) : 0,
            // Novos campos de abrigos
            'qtd_abrigos_provisorios' => $validated['qtd_abrigos_provisorios'] ?? 0,
            'abrigos_tipos' => $abrigosTipos,
            // Campos de fiscalização
            'conducao_forcas_seguranca' => ($validated['conducao_forcas_seguranca'] ?? '0') === '1',
            'conducao_forcas_observacao' => ($validated['conducao_forcas_seguranca'] ?? '0') === '1'
                ? ($validated['conducao_forcas_observacao'] ?? '')
                : null,
            'apreensao_fiscal' => $request->boolean('apreensao_fiscal') ? 1 : 0,
            'auto_fiscalizacao_aplicado' => ($validated['auto_fiscalizacao_aplicado'] ?? '0') === '1',
            'auto_fiscalizacao_numero' => ($validated['auto_fiscalizacao_aplicado'] ?? '0') === '1'
                ? ($validated['auto_fiscalizacao_numero'] ?? '')
                : null,
            // Encaminhamentos
            'e1_id' => $validated['e1_id'] ?? null,
            'e2_id' => $validated['e2_id'] ?? null,
            'e3_id' => $validated['e3_id'] ?? null,
            'e4_id' => $validated['e4_id'] ?? null,
            'e5_id' => $validated['e5_id'] ?? null,
            'e6_id' => $validated['e6_id'] ?? null,
        ]);

        // Processar upload de fotos usando Spatie Media Library
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                if ($foto->isValid()) {
                    $vistoria->addMedia($foto)
                        ->usingName($foto->getClientOriginalName())
                        ->toMediaCollection('fotos');
                }
            }
        }

        // Atualizar complemento do ponto existente se informado
        // (pontos novos já tiveram o complemento definido pelo serviço)
        $ponto = Ponto::find($pontoId);
        if ($ponto && ! $pontoNovo && ! empty($validated['complemento_ponto'])) {
            $ponto->update(['complemento' => $validated['complemento_ponto']]);
        }

        // Criar novos moradores e vincular ao ponto
        if (! empty($validated['novos_moradores'])) {
            foreach ($validated['novos_moradores'] as $dadosMorador) {
                $this->moradorService->criarComEntrada($dadosMorador, $ponto, $vistoria);
            }
        }

        // Atualizar presença dos moradores existentes
        if (! empty($validated['moradores_presentes'])) {
            $this->moradorService->atualizarPresencaVistoria(
                $ponto,
                $validated['moradores_presentes'],
                $vistoria
            );
        }

        return redirect()->route('mapa.index')->with('success', 'Vistoria registrada com sucesso!');
    }
}
