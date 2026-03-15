<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVistoriaRequest;
use App\Http\Requests\UpdateVistoriaRequest;
use App\Jobs\UploadMediaToDriveJob;
use App\Models\Ponto;
use App\Models\Vistoria;
use App\Services\EnderecoService;
use App\Services\MoradorService;
use Illuminate\Http\JsonResponse;
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
        $this->authorize('view', $vistoria);

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
            'media',
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
        $this->authorize('view', $vistoria);

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
            'media',
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
        $this->authorize('update', $vistoria);

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

    public function update(UpdateVistoriaRequest $request, Vistoria $vistoria): RedirectResponse
    {
        $validated = $request->validated();

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
            'tipo_abrigo_desmontado_id' => $validated['tipo_abrigo_desmontado_id'] ?? null,
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
            // Campos de fiscalizacao
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
                    $media = $vistoria->addMedia($foto)
                        ->usingName($foto->getClientOriginalName())
                        ->toMediaCollection('fotos');

                    if (config('services.google_drive.client_id')) {
                        UploadMediaToDriveJob::dispatch($media->id);
                    }
                }
            }
        }

        return redirect()->route('vistorias.show', $vistoria)->with('success', 'Vistoria atualizada com sucesso!');
    }

    public function destroy(Vistoria $vistoria): RedirectResponse
    {
        $this->authorize('delete', $vistoria);

        $vistoria->delete();

        return redirect()->route('vistorias.index')->with('success', 'Vistoria excluida com sucesso!');
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

        // Ponto sem coordenadas, vai para criacao normal
        return redirect()->route('vistorias.create');
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Vistoria::class);

        $lat = $request->query('lat');
        $lng = $request->query('lng');

        // Buscar ponto proximo ou criar novo
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

        // Dados do endereco de referencia (passados do mapa)
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

            // Gerar referencia automatica para novos pontos
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

    /**
     * Autocomplete de logradouros que possuem vistorias.
     */
    public function buscarLogradouros(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
            'numero' => 'nullable|integer|min:1',
        ]);

        $termo = $validated['q'];
        $numero = $validated['numero'] ?? null;

        $baseQuery = DB::table('vistorias as v')
            ->join('pontos as p', 'p.id', '=', 'v.ponto_id')
            ->join('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->where('ea.NOME_LOGRADOURO', 'ilike', '%'.$termo.'%')
            ->whereRaw('ea."NUMERO_IMOVEL" ~ \'^[0-9]+$\'');

        if ($numero !== null) {
            // Com numero: buscar enderecos com numeros proximos
            $subquery = (clone $baseQuery)
                ->selectRaw('DISTINCT ea."SIGLA_TIPO_LOGRADOURO" as tipo, ea."NOME_LOGRADOURO" as logradouro, CAST(ea."NUMERO_IMOVEL" AS INTEGER) as numero, ea."NOME_REGIONAL" as regional, ABS(CAST(ea."NUMERO_IMOVEL" AS INTEGER) - ?) as diff', [$numero]);

            $resultados = DB::query()
                ->fromSub($subquery, 'sub')
                ->select(['tipo', 'logradouro', 'numero', 'regional'])
                ->orderByRaw('CASE WHEN logradouro ILIKE ? THEN 0 ELSE 1 END', [$termo.'%'])
                ->orderBy('diff')
                ->limit(20)
                ->get();
        } else {
            // Sem numero: listar enderecos distintos com numero
            $subquery = (clone $baseQuery)
                ->selectRaw('DISTINCT ea."SIGLA_TIPO_LOGRADOURO" as tipo, ea."NOME_LOGRADOURO" as logradouro, CAST(ea."NUMERO_IMOVEL" AS INTEGER) as numero, ea."NOME_REGIONAL" as regional');

            $resultados = DB::query()
                ->fromSub($subquery, 'sub')
                ->select(['tipo', 'logradouro', 'numero', 'regional'])
                ->orderByRaw('CASE WHEN logradouro ILIKE ? THEN 0 ELSE 1 END', [$termo.'%'])
                ->orderBy('logradouro')
                ->orderBy('numero')
                ->limit(20)
                ->get();
        }

        return response()->json($resultados);
    }

    public function minhas(Request $request): View
    {
        $query = DB::table('vistorias as v')
            ->join('pontos as p', 'p.id', '=', 'v.ponto_id')
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->leftJoin('tipo_abordagem as ta', 'ta.id', '=', 'v.tipo_abordagem_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->whereNull('v.deleted_at')
            ->where('v.user_id', auth()->id())
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
                'p.complemento',
            ]);

        if ($request->filled('data_inicio')) {
            $query->whereDate('v.data_abordagem', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('v.data_abordagem', '<=', $request->data_fim);
        }

        if ($request->filled('resultado')) {
            $query->where('v.resultado_acao_id', $request->resultado);
        }

        $vistorias = $query->orderBy('v.data_abordagem', 'desc')
            ->paginate(10);

        $resultados = DB::table('resultados_acoes')
            ->orderBy('id')
            ->get();

        return view('vistorias.minhas', [
            'vistorias' => $vistorias,
            'resultados' => $resultados,
        ]);
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Vistoria::class);

        $query = DB::table('vistorias as v')
            ->join('pontos as p', 'p.id', '=', 'v.ponto_id')
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->leftJoin('tipo_abordagem as ta', 'ta.id', '=', 'v.tipo_abordagem_id')
            ->leftJoin('resultados_acoes as ra', 'ra.id', '=', 'v.resultado_acao_id')
            ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
            ->whereNull('v.deleted_at')
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
                'p.complemento',
            ]);

        // Filtros - autocomplete (match exato)
        if ($request->filled('endereco')) {
            $query->where('ea.NOME_LOGRADOURO', $request->endereco);
        }

        if ($request->filled('numero_endereco')) {
            $query->where('ea.NUMERO_IMOVEL', $request->numero_endereco);
        }

        // Filtros - busca avancada (match parcial)
        if ($request->filled('logradouro')) {
            $query->where('ea.NOME_LOGRADOURO', 'ilike', '%'.$request->logradouro.'%');
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

    public function store(StoreVistoriaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Se nao tem ponto_id, precisa criar ou buscar ponto
        $pontoId = $validated['ponto_id'] ?? null;
        $pontoNovo = false;

        if (! $pontoId) {
            // Buscar ponto proximo (dentro de 50m)
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

                // Buscar e vincular endereco mais proximo da base de enderecos
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
            'tipo_abrigo_desmontado_id' => $validated['tipo_abrigo_desmontado_id'] ?? null,
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
            // Campos de fiscalizacao
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
                    $media = $vistoria->addMedia($foto)
                        ->usingName($foto->getClientOriginalName())
                        ->toMediaCollection('fotos');

                    if (config('services.google_drive.client_id')) {
                        UploadMediaToDriveJob::dispatch($media->id);
                    }
                }
            }
        }

        // Atualizar complemento do ponto existente se informado
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

        // Atualizar presenca dos moradores existentes
        if (! empty($validated['moradores_presentes'])) {
            $this->moradorService->atualizarPresencaVistoria(
                $vistoria,
                $validated['moradores_presentes']
            );
        }

        return redirect()->route('vistorias.show', $vistoria)->with('success', 'Vistoria registrada com sucesso!');
    }
}
