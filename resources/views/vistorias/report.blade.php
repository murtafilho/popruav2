<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relatorio Vistoria #{{ $vistoria->id }} - {{ config('app.name', 'POPRUA') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --text-primary: #1a1a1a;
            --text-secondary: #4a4a4a;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --accent-primary: #2563eb;
            --status-success: #10b981;
            --status-warning: #f59e0b;
            --status-danger: #ef4444;
            --status-info: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: var(--text-primary);
            background: var(--bg-primary);
        }

        .report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }

        /* Header */
        .report-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--accent-primary);
            margin-bottom: 24px;
        }

        .report-logo {
            font-size: 24pt;
            font-weight: 700;
            color: var(--accent-primary);
            margin-bottom: 4px;
        }

        .report-title {
            font-size: 16pt;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .report-subtitle {
            font-size: 10pt;
            color: var(--text-muted);
        }

        /* Sections */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12pt;
            font-weight: 600;
            color: var(--accent-primary);
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title svg {
            width: 18px;
            height: 18px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .info-grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .info-item {
            padding: 10px;
            background: var(--bg-secondary);
            border-radius: 6px;
            border-left: 3px solid var(--accent-primary);
        }

        .info-label {
            font-size: 9pt;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 11pt;
            font-weight: 500;
            color: var(--text-primary);
        }

        .info-full {
            grid-column: 1 / -1;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: 500;
        }

        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-secondary { background: #e5e7eb; color: #374151; }
        .badge-default { background: #f3f4f6; color: #6b7280; }

        /* Tags */
        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 10pt;
        }

        .tag svg {
            width: 14px;
            height: 14px;
            color: var(--status-warning);
        }

        /* Text block */
        .text-block {
            padding: 12px;
            background: var(--bg-secondary);
            border-radius: 6px;
            font-size: 10pt;
            white-space: pre-wrap;
        }

        /* Photo grid */
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .photo-item {
            aspect-ratio: 1;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Moradores list */
        .morador-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: var(--bg-secondary);
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .morador-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14pt;
        }

        .morador-info {
            flex: 1;
        }

        .morador-name {
            font-weight: 500;
            font-size: 11pt;
        }

        .morador-detail {
            font-size: 9pt;
            color: var(--text-muted);
        }

        /* Encaminhamento item */
        .enc-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: var(--bg-secondary);
            border-radius: 6px;
            margin-bottom: 8px;
            border-left: 3px solid var(--status-success);
        }

        .enc-item svg {
            width: 16px;
            height: 16px;
            color: var(--status-success);
        }

        /* Footer */
        .report-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 9pt;
            color: var(--text-muted);
        }

        .signature-area {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 40px;
            padding-top: 20px;
        }

        .signature-line {
            border-top: 1px solid var(--text-muted);
            padding-top: 8px;
            text-align: center;
            font-size: 10pt;
            color: var(--text-secondary);
        }

        /* Result highlight */
        .result-highlight {
            text-align: center;
            padding: 16px;
            background: var(--bg-secondary);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .result-label {
            font-size: 10pt;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .result-value {
            font-size: 14pt;
            font-weight: 600;
        }

        /* Print styles */
        @media print {
            .no-print { display: none !important; }
            body { font-size: 10pt; }
            .report-container { padding: 0; max-width: 100%; }
            .section { page-break-inside: avoid; }
            .photo-grid { grid-template-columns: repeat(4, 1fr); }
            .photo-item { break-inside: avoid; }
        }

        @page {
            margin: 1.5cm;
            size: A4;
        }
    </style>
</head>
<body>
    <!-- Action buttons (not printed) -->
    <div class="no-print">
        <a href="{{ route('vistorias.show', $vistoria) }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimir
        </button>
    </div>

    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="report-logo">POPRUA</div>
            <div class="report-title">Relatorio de Vistoria</div>
            <div class="report-subtitle">
                Vistoria #{{ $vistoria->id }} - Gerado em {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Result Highlight -->
        @if($vistoria->resultadoAcao)
            @php
                $badgeClass = match(true) {
                    str_contains($vistoria->resultadoAcao->resultado, 'persiste') => 'badge-danger',
                    str_contains($vistoria->resultadoAcao->resultado, 'parcialmente') => 'badge-warning',
                    str_contains($vistoria->resultadoAcao->resultado, 'ausente') => 'badge-default',
                    str_contains($vistoria->resultadoAcao->resultado, 'constatado') => 'badge-info',
                    str_contains($vistoria->resultadoAcao->resultado, 'Conformidade') => 'badge-success',
                    default => 'badge-secondary',
                };
            @endphp
            <div class="result-highlight">
                <div class="result-label">Resultado da Acao</div>
                <div class="result-value">
                    <span class="badge {{ $badgeClass }}">{{ $vistoria->resultadoAcao->resultado }}</span>
                </div>
            </div>
        @endif

        <!-- Dados da Vistoria -->
        <div class="section">
            <h2 class="section-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Dados da Vistoria
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Data/Hora</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($vistoria->data_abordagem)->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tipo de Abordagem</div>
                    <div class="info-value">{{ $vistoria->tipoAbordagem->tipo ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Quantidade de Pessoas</div>
                    <div class="info-value">{{ $vistoria->quantidade_pessoas ?? 0 }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Material Recolhido</div>
                    <div class="info-value">{{ $vistoria->qtd_kg ?? 0 }} kg</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Registrado por</div>
                    <div class="info-value">{{ $vistoria->user->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">ID Vistoria</div>
                    <div class="info-value">#{{ $vistoria->id }}</div>
                </div>
            </div>
        </div>

        <!-- Localizacao -->
        <div class="section">
            <h2 class="section-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Localizacao
            </h2>
            <div class="info-grid">
                @if($vistoria->ponto && $vistoria->ponto->enderecoAtualizado)
                    <div class="info-item info-full">
                        <div class="info-label">Endereco</div>
                        <div class="info-value">
                            {{ $vistoria->ponto->enderecoAtualizado->SIGLA_TIPO_LOGRADOURO ?? '' }}
                            {{ $vistoria->ponto->enderecoAtualizado->NOME_LOGRADOURO }},
                            {{ $vistoria->ponto->enderecoAtualizado->NUMERO_IMOVEL ?? $vistoria->ponto->numero }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Bairro</div>
                        <div class="info-value">{{ $vistoria->ponto->enderecoAtualizado->NOME_BAIRRO_OFICIAL ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Regional</div>
                        <div class="info-value">{{ $vistoria->ponto->enderecoAtualizado->NOME_REGIONAL ?? '-' }}</div>
                    </div>
                @endif
                @if($vistoria->ponto && $vistoria->ponto->lat && $vistoria->ponto->lng)
                    <div class="info-item info-full">
                        <div class="info-label">Coordenadas</div>
                        <div class="info-value">{{ $vistoria->ponto->lat }}, {{ $vistoria->ponto->lng }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Caracteristicas de Complexidade -->
        @php
            $caracteristicas = [
                ['campo' => 'casal', 'label' => 'Casal', 'extra' => $vistoria->qtd_casais ? "({$vistoria->qtd_casais})" : null],
                ['campo' => 'num_reduzido', 'label' => 'Numero Reduzido'],
                ['campo' => 'catador_reciclados', 'label' => 'Catador de Reciclados'],
                ['campo' => 'resistencia', 'label' => 'Resistencia'],
                ['campo' => 'fixacao_antiga', 'label' => 'Fixacao Antiga'],
                ['campo' => 'excesso_objetos', 'label' => 'Excesso de Objetos'],
                ['campo' => 'trafico_ilicitos', 'label' => 'Trafico/Ilicitos'],
                ['campo' => 'crianca_adolescente', 'label' => 'Crianca/Adolescente'],
                ['campo' => 'idosos', 'label' => 'Idosos'],
                ['campo' => 'gestante', 'label' => 'Gestante'],
                ['campo' => 'lgbtqiapn', 'label' => 'LGBTQIAPN+'],
                ['campo' => 'cena_uso_caracterizada', 'label' => 'Cena de Uso'],
                ['campo' => 'deficiente', 'label' => 'Deficiente'],
                ['campo' => 'agrupamento_quimico', 'label' => 'Agrupamento Quimico'],
                ['campo' => 'saude_mental', 'label' => 'Saude Mental'],
                ['campo' => 'animais', 'label' => 'Animais', 'extra' => $vistoria->qtd_animais ? "({$vistoria->qtd_animais})" : null],
            ];
            $caracteristicasAtivas = collect($caracteristicas)->filter(fn($c) => $vistoria->{$c['campo']});
        @endphp

        @if($caracteristicasAtivas->count() > 0)
            <div class="section">
                <h2 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Fatores de Complexidade
                </h2>
                <div class="tag-list">
                    @foreach($caracteristicasAtivas as $c)
                        <span class="tag">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            {{ $c['label'] }}
                            @if(isset($c['extra']) && $c['extra'])
                                {{ $c['extra'] }}
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Abrigos -->
        @if($vistoria->qtd_abrigos_provisorios > 0 || count($tiposAbrigoSelecionados) > 0 || $vistoria->tipoAbrigoDesmontado)
            <div class="section">
                <h2 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Abrigos
                </h2>
                <div class="info-grid info-grid-3">
                    @if($vistoria->qtd_abrigos_provisorios > 0)
                        <div class="info-item">
                            <div class="info-label">Abrigos Provisorios</div>
                            <div class="info-value">{{ $vistoria->qtd_abrigos_provisorios }}</div>
                        </div>
                    @endif
                    @if($vistoria->tipoAbrigoDesmontado)
                        <div class="info-item">
                            <div class="info-label">Tipo Desmontado</div>
                            <div class="info-value">{{ $vistoria->tipoAbrigoDesmontado->tipo_abrigo }}</div>
                        </div>
                    @endif
                </div>
                @if(count($tiposAbrigoSelecionados) > 0)
                    <div class="tag-list" style="margin-top: 12px;">
                        @foreach($tiposAbrigoSelecionados as $tipo)
                            <span class="tag">{{ $tipo }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Fiscalizacao -->
        @if($vistoria->conducao_forcas_seguranca || $vistoria->apreensao_fiscal || $vistoria->auto_fiscalizacao_aplicado)
            <div class="section">
                <h2 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Fiscalizacao
                </h2>
                <div class="info-grid">
                    @if($vistoria->conducao_forcas_seguranca)
                        <div class="info-item">
                            <div class="info-label">Conducao Forcas de Seguranca</div>
                            <div class="info-value">
                                <span class="badge badge-danger">Sim</span>
                                @if($vistoria->conducao_forcas_observacao)
                                    <div style="margin-top: 6px; font-size: 10pt; color: var(--text-secondary);">
                                        {{ $vistoria->conducao_forcas_observacao }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($vistoria->apreensao_fiscal)
                        <div class="info-item">
                            <div class="info-label">Apreensao Fiscal</div>
                            <div class="info-value"><span class="badge badge-danger">Sim</span></div>
                        </div>
                    @endif
                    @if($vistoria->auto_fiscalizacao_aplicado)
                        <div class="info-item">
                            <div class="info-label">Auto de Fiscalizacao</div>
                            <div class="info-value">
                                <span class="badge badge-warning">Aplicado</span>
                                @if($vistoria->auto_fiscalizacao_numero)
                                    <div style="margin-top: 6px; font-size: 10pt;">
                                        N {{ $vistoria->auto_fiscalizacao_numero }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Encaminhamentos -->
        @php
            $encaminhamentos = collect([
                $vistoria->encaminhamento1,
                $vistoria->encaminhamento2,
                $vistoria->encaminhamento3,
                $vistoria->encaminhamento4,
                $vistoria->encaminhamento5,
                $vistoria->encaminhamento6,
            ])->filter();
        @endphp

        @if($encaminhamentos->count() > 0)
            <div class="section">
                <h2 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    Encaminhamentos ({{ $encaminhamentos->count() }})
                </h2>
                @foreach($encaminhamentos as $enc)
                    <div class="enc-item">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $enc->encaminhamento }}
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Nomes das Pessoas -->
        @if($vistoria->nomes_pessoas)
            <div class="section">
                <h2 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Nomes das Pessoas
                </h2>
                <div class="text-block">{{ $vistoria->nomes_pessoas }}</div>
            </div>
        @endif

        <!-- Observacao -->
        @if($vistoria->observacao)
            <div class="section">
                <h2 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Observacao
                </h2>
                <div class="text-block">{{ $vistoria->observacao }}</div>
            </div>
        @endif

        <!-- Moradores Registrados -->
        @if($vistoria->moradoresEntrada->count() > 0)
            <div class="section">
                <h2 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Moradores Registrados ({{ $vistoria->moradoresEntrada->count() }})
                </h2>
                @foreach($vistoria->moradoresEntrada as $historico)
                    @if($historico->morador)
                        <div class="morador-item">
                            <div class="morador-avatar">
                                {{ substr($historico->morador->nome_social, 0, 1) }}
                            </div>
                            <div class="morador-info">
                                <div class="morador-name">{{ $historico->morador->nome_social }}</div>
                                @if($historico->morador->apelido)
                                    <div class="morador-detail">"{{ $historico->morador->apelido }}"</div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Fotos -->
        @php
            $fotos = $vistoria->getMedia('fotos');
        @endphp

        @if($fotos->count() > 0)
            <div class="section">
                <h2 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Fotos ({{ $fotos->count() }})
                </h2>
                <div class="photo-grid">
                    @foreach($fotos as $foto)
                        <div class="photo-item">
                            <img src="{{ $foto->getUrl('preview') }}" alt="Foto da vistoria">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="report-footer">
            <div class="signature-area">
                <div class="signature-line">Responsavel pela Vistoria</div>
                <div class="signature-line">Supervisor</div>
            </div>
            <p style="margin-top: 30px;">
                Documento gerado pelo sistema POPRUA v2 em {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</body>
</html>
