<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePontoRequest;
use App\Http\Requests\UpdatePontoRequest;
use App\Repositories\EnderecoRepository;
use App\Repositories\PontoRepository;
use App\Http\Controllers\AppBaseController;
use App\Repositories\VistoriaRepository;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
/*
SELECT
IF(vistorias.resistencia,1,0) +
IF(vistorias.num_reduzido,1,0) +
IF(vistorias.casal,1,0) +
IF(vistorias.catador_reciclados,1,0)+
IF(vistorias.resistencia,1,0) +
IF(vistorias.fixacao_antiga,1,0) +
IF(vistorias.excesso_objetos,1,0) +
IF(vistorias.resistencia,1,0) +
IF(vistorias.fixacao_antiga,1,0) +
IF(vistorias.excesso_objetos,1,0) +
IF(vistorias.trafico_ilicitos,1,0) +
IF(vistorias.menores_idosos,1,0) +
IF(vistorias.deficiente,1,0) +
IF(vistorias.agrupamento_quimico,1,0) +
IF(vistorias.saude_mental,1,0) +
IF(vistorias.animais,1,0) +
IF(vistorias.estrutura_abrigo_provisorio,1,0) AS nivel_complexidade
FROM
vistorias
 */

class PontoController extends AppBaseController
{
	private $pontoRepository;
	private $enderecoRepository;
	private $vistoriaRepository;
	private $caracteristica_abrigo;

	public function __construct(PontoRepository $pontoRepository,
	                            EnderecoRepository $enderecoRepository,
	                            VistoriaRepository $vistoriaRepository

	)
	{
		$this->pontoRepository    = $pontoRepository;
		$this->enderecoRepository = $enderecoRepository;
		$this->vistoriaRepository = $vistoriaRepository;
		$this->caracteristica_abrigo = DB::table('caracteristica_abrigo')->pluck('caracteristica_abrigo','id');

	}

	public function index(Request $request)
	{
		$logradouro = $request->logradouro;
		$numero     = $request->numero;
		$pontos     = $this->pontoRepository->buscar($request);
		$fullUrl = $request->fullUrl();
		session(['url_lista_pontos' => $fullUrl]);

		return view('pontos.index', compact('pontos', 'logradouro', 'numero'));
	}

	public function create()
	{
	    $caracteristica_abrigo = $this->caracteristica_abrigo;
		return view('pontos.create',compact('caracteristica_abrigo'));
	}

	public function store(CreatePontoRequest $request)
	{
		$input = $request->all();
		$ponto = $this->pontoRepository->create($input);
		Flash::success('Ponto adicionado com sucesso.');
		return redirect(route('pontos.index'));
	}


	public function show($id)
	{
		$ponto     = $this->pontoRepository->findWithoutFail($id);
		$vistorias = $this->vistoriaRepository->listarById($id);
		if (empty($ponto))
		{
			Flash::error('Ponto não cadastrado!');

			return redirect(route('pontos.index'));
		}
		return view('pontos.show', compact('ponto', 'vistorias'));
	}


	public function edit($id)
	{
		$ponto = $this->pontoRepository->findWithoutFail($id);
        $caracteristica_abrigo = $this->caracteristica_abrigo;
		if (empty($ponto))
		{
			Flash::error('Ponto não cadastrado');

			return redirect(route('pontos.index'));
		}
        $caracteristica_abrigo = $this->caracteristica_abrigo;
		return view('pontos.edit',compact('ponto','caracteristica_abrigo'));
	}


	public function update($id, UpdatePontoRequest $request)
	{
		$ponto = $this->pontoRepository->findWithoutFail($id);

		if (empty($ponto))
		{
			Flash::error('Ponto não cadastrado');

			return redirect(route('pontos.index'));
		}

		$ponto = $this->pontoRepository->update($request->all(), $id);

		Flash::success('Ponto atualizado com sucesso!');

		return redirect()->to(session('url_lista_pontos'));
	}


	public function destroy($id)
	{
		$ponto = $this->pontoRepository->findWithoutFail($id);

		if (empty($ponto))
		{
			Flash::error('Ponto não cadastrado...');

			return redirect(route('pontos.index'));
		}

		$this->pontoRepository->delete($id);

		Flash::success('Ponto excluído!');

		return redirect(route('pontos.index'));
	}

	public function migrar($id){
	    $vistorias = DB::table('qry_vistorias_v2')->where('ponto_id','=',$id)->paginate(10);
        $keys = $vistorias->pluck('id')->toArray();

        session(['vistorias_ids' => $keys]);
		session(['ponto_id' => $id]);

	    return view('pontos.migrar',compact('vistorias','keys'));

    }

    public function processar_migracao(Request $request){
        $vistorias_ids = session('vistorias_ids');
		$ponto_id = session('ponto_id');
        foreach ($vistorias_ids as $vistoria_id){
            DB::update("update vistorias set ponto_id = $request->ponto_id where id = $vistoria_id ");
        }
        DB::delete("delete from pontos where id = $ponto_id");
        Flash::success('Vistorias migradas com sucesso para o ponto #<a href= '.route('pontos.show',['id'=>$request->ponto_id]).'>'.$request->ponto_id.'</a>');

        return redirect()->to(session('url_lista_pontos'));
    }

    public function autoComplete(Request $request)
    {
        $q    = $request->q;
        $data = DB::table('qry_ponto_concat')
            ->take(30)
            ->select('id', 'ponto_concat')
            ->where('ponto_concat', 'LIKE', '%'. $q . '%')
            ->get();
        foreach ($data as $item)
        {
            $results[] = ['text' => $item->ponto_concat, 'id' => $item->id];
        }
        if (count($results) > 0)
        {
            return response()->json($results);
        }
        else
        {
            return null;
        }
    }


}
