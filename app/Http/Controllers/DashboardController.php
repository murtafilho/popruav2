<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $totalPontos = DB::table('pontos')->count();

        $totais = DB::table('vistorias')
            ->selectRaw('COUNT(*) as vistorias')
            ->selectRaw('COUNT(DISTINCT ponto_id) as pontos_vistoriados')
            ->whereNull('deleted_at')
            ->first();

        // Status do ponto = resultado da última vistoria (última do dia se houver mais de uma)
        // Deduplica: 1 registro por ponto por dia (o mais recente)
        $statusChanges = DB::select("
            SELECT ponto_id, resultado_acao_id, dia
            FROM (
                SELECT ponto_id, resultado_acao_id, data_abordagem::date as dia,
                       ROW_NUMBER() OVER (PARTITION BY ponto_id, data_abordagem::date ORDER BY data_abordagem DESC, id DESC) as rn
                FROM vistorias
                WHERE deleted_at IS NULL AND ponto_id IS NOT NULL
            ) sub
            WHERE rn = 1
            ORDER BY ponto_id, dia
        ");

        // Montar timeline por ponto: lista ordenada de (dia, status)
        $timeline = [];
        foreach ($statusChanges as $sc) {
            $timeline[$sc->ponto_id][] = [
                'dia' => $sc->dia,
                'status' => $sc->resultado_acao_id,
            ];
        }

        // Data de existência do ponto = primeira data_abordagem
        $pontosPrimeiraVistoria = DB::table('vistorias')
            ->selectRaw('ponto_id as id, MIN(data_abordagem)::date as primeira_abordagem')
            ->whereNull('deleted_at')
            ->whereNotNull('ponto_id')
            ->groupBy('ponto_id')
            ->get()
            ->keyBy('id');

        // Gerar série mensal
        $primeiraData = DB::table('vistorias')
            ->whereNull('deleted_at')
            ->whereNotNull('data_abordagem')
            ->min('data_abordagem');

        $start = Carbon::parse($primeiraData)->startOfMonth();
        $end = Carbon::now()->startOfMonth();

        $dadosMensais = [];

        while ($start <= $end) {
            $mesKey = $start->format('Y-m');
            $fimMes = $start->copy()->endOfMonth()->format('Y-m-d');

            $counts = [
                'mes' => $mesKey,
                'persiste' => 0,
                'impactado_parcial' => 0,
                'deixou_ocorrer' => 0,
                'ausente' => 0,
                'nao_constatado' => 0,
                'conformidade' => 0,
                'sem_vistoria' => 0,
                'total_existentes' => 0,
            ];

            foreach ($pontosPrimeiraVistoria as $pontoId => $ponto) {
                // Ponto só entra na contagem a partir do mês da primeira abordagem
                if ($ponto->primeira_abordagem > $fimMes) {
                    continue;
                }

                $counts['total_existentes']++;

                // Status = resultado da última vistoria (data_abordagem) até o fim do mês
                $lastStatus = null;
                foreach ($timeline[$pontoId] as $entry) {
                    if ($entry['dia'] <= $fimMes) {
                        $lastStatus = $entry['status'];
                    } else {
                        break;
                    }
                }

                match ($lastStatus) {
                    1 => $counts['persiste']++,
                    2 => $counts['impactado_parcial']++,
                    3 => $counts['deixou_ocorrer']++,
                    4 => $counts['ausente']++,
                    5 => $counts['nao_constatado']++,
                    6 => $counts['conformidade']++,
                    default => $counts['sem_vistoria']++,
                };
            }

            // Extintos = Deixou de ocorrer + Não constatado
            $counts['extintos'] = $counts['deixou_ocorrer'] + $counts['nao_constatado'];
            // Ativos = pontos vistoriados com fenômeno presente
            $counts['ativos'] = $counts['persiste'] + $counts['impactado_parcial']
                + $counts['ausente'] + $counts['conformidade'];
            // Total efetivo = total existentes - extintos
            $counts['total_efetivo'] = $counts['total_existentes'] - $counts['extintos'];
            $counts['total_pontos'] = $counts['total_existentes'];

            $dadosMensais[] = $counts;
            $start->addMonth();
        }

        $resultados = DB::table('resultados_acoes')->orderBy('id')->get();

        return view('dashboard', [
            'dadosMensais' => collect($dadosMensais),
            'totais' => $totais,
            'totalPontos' => $totalPontos,
            'resultados' => $resultados,
        ]);
    }
}
