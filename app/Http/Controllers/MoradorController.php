<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMoradorRequest;
use App\Http\Requests\UpdateMoradorRequest;
use App\Models\Morador;
use App\Models\Ponto;
use App\Services\MoradorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MoradorController extends Controller
{
    public function __construct(
        private MoradorService $moradorService
    ) {}

    public function index(Request $request): View
    {
        $query = Morador::query()->with(['pontoAtual.enderecoAtualizado']);

        // Filtrar por termo de busca
        if ($request->filled('search')) {
            $termo = $request->search;
            $query->where(function ($q) use ($termo) {
                $q->where('nome_social', 'like', "%{$termo}%")
                    ->orWhere('apelido', 'like', "%{$termo}%")
                    ->orWhere('nome_registro', 'like', "%{$termo}%");
            });
        }

        // Filtrar por gênero
        if ($request->filled('genero')) {
            $query->where('genero', $request->genero);
        }

        // Filtrar com/sem ponto
        if ($request->filled('situacao')) {
            if ($request->situacao === 'com_ponto') {
                $query->whereNotNull('ponto_atual_id');
            } elseif ($request->situacao === 'sem_ponto') {
                $query->whereNull('ponto_atual_id');
            }
        }

        $moradores = $query->orderBy('nome_social')->paginate(15);

        // Gêneros únicos para filtro
        $generos = Morador::select('genero')
            ->distinct()
            ->whereNotNull('genero')
            ->orderBy('genero')
            ->pluck('genero');

        return view('moradores.index', [
            'moradores' => $moradores,
            'generos' => $generos,
        ]);
    }

    public function show(Morador $morador): View
    {
        $morador->load(['pontoAtual.enderecoAtualizado']);
        $historico = $this->moradorService->getHistorico($morador);

        return view('moradores.show', [
            'morador' => $morador,
            'historico' => $historico,
        ]);
    }

    public function create(Request $request): View
    {
        $ponto = null;
        if ($request->filled('ponto_id')) {
            $ponto = Ponto::with(['enderecoAtualizado'])->find($request->ponto_id);
        }

        return view('moradores.create', [
            'ponto' => $ponto,
        ]);
    }

    public function store(StoreMoradorRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        // Upload de foto se enviada
        if ($request->hasFile('fotografia')) {
            $dados['fotografia'] = $request->file('fotografia')
                ->store('moradores', 'public');
        }

        // Se tem ponto_id, criar com entrada no ponto
        if (! empty($dados['ponto_id'])) {
            $ponto = Ponto::findOrFail($dados['ponto_id']);
            unset($dados['ponto_id'], $dados['vistoria_id']);
            $morador = $this->moradorService->criarComEntrada($dados, $ponto);
        } else {
            unset($dados['ponto_id'], $dados['vistoria_id']);
            $morador = Morador::create($dados);
        }

        return redirect()
            ->route('moradores.show', $morador)
            ->with('success', 'Morador cadastrado com sucesso.');
    }

    public function edit(Morador $morador): View
    {
        return view('moradores.edit', [
            'morador' => $morador,
        ]);
    }

    public function update(UpdateMoradorRequest $request, Morador $morador): RedirectResponse
    {
        $dados = $request->validated();

        // Upload de nova foto se enviada
        if ($request->hasFile('fotografia')) {
            // Remove foto antiga
            if ($morador->fotografia) {
                Storage::disk('public')->delete($morador->fotografia);
            }
            $dados['fotografia'] = $request->file('fotografia')
                ->store('moradores', 'public');
        }

        $morador->update($dados);

        return redirect()
            ->route('moradores.show', $morador)
            ->with('success', 'Morador atualizado com sucesso.');
    }

    public function destroy(Morador $morador): RedirectResponse
    {
        // Remove foto se existir
        if ($morador->fotografia) {
            Storage::disk('public')->delete($morador->fotografia);
        }

        $morador->delete();

        return redirect()
            ->route('moradores.index')
            ->with('success', 'Morador removido com sucesso.');
    }
}
