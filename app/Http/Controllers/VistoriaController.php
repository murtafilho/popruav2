<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVistoriaRequest;
use App\Http\Requests\UpdateVistoriaRequest;
use App\Models\Ponto;
use App\Repositories\PontoRepository;
use App\Repositories\VistoriaRepository;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
class VistoriaController extends AppBaseController
{
    private $vistoriaRepository;
    private $ponto;
    private $resultados_acoes;
    private $tipo_abordagem;
    private $encaminhamentos;
    private $tipo_abrigo_desmontado;

	public function __construct(VistoriaRepository $vistoriaRepo, PontoRepository $pontoRepository)
    {
        $this->vistoriaRepository = $vistoriaRepo;
        $this->ponto = $pontoRepository;
        $this->resultados_acoes = DB::table('resultados_acoes')->pluck('resultado','id');
        $this->tipo_abordagem = DB::table('tipo_abordagem')->pluck('tipo','id');
        $this->encaminhamentos = DB::table('encaminhamentos')->pluck('encaminhamento','id');
        $this->tipo_abrigo_desmontado = DB::table('tipo_abrigo_desmontado')->pluck('tipo_abrigo','id');

    }

    public function index(Request $request)
    {
    	$logradouro = $request->logradouro;
    	$numero = $request->numero;
        $vistorias = $this->vistoriaRepository->buscar($request);
        $fullUrl = $request->fullUrl();
        session(['url_lista_vistorias' => $fullUrl]);
        return view('vistorias.index',compact('vistorias','logradouro','numero'));
    }

    public function create()
    {
        $resultados_acoes = $this->resultados_acoes;
        $tipo_abordagem = $this->tipo_abordagem;
        $tipo_abrigo_desmontado = $this->tipo_abrigo_desmontado;
        $encaminhamentos = $this->encaminhamentos;
        $now = date('d-m-Y');

        return view('vistorias.create',compact('resultados_acoes','tipo_abordagem','tipo_abrigo_desmontado','encaminhamentos','now'));
    }

    public function createDetail($id){
        $ponto = $this->ponto->find($id);
        $resultados_acoes = $this->resultados_acoes;
        $tipo_abordagem = $this->tipo_abordagem;
        $tipo_abrigo_desmontado = $this->tipo_abrigo_desmontado;
        $encaminhamentos = $this->encaminhamentos;
        $now = date('d-m-Y');
	    return view('vistorias.create',compact('ponto','resultados_acoes','tipo_abordagem','tipo_abrigo_desmontado','encaminhamentos','now'));
    }

    public function store(CreateVistoriaRequest $request)
    {
        $input = $request->all();
	    $dt = $input['data_abordagem'];
        $input['data_abordagem'] = $this->dtus($dt);
        $vistoria = $this->vistoriaRepository->create($input);
        Flash::success('Vistoria adicionada com sucesso!');
        return redirect(route('vistorias.index'));
    }

    public function show($id)
    {
	    $vistoria = $this->vistoriaRepository->visualizar($id);

        if (empty($vistoria)) {
            Flash::error('Vistoria not found');

            return redirect(route('vistorias.index'));
        }
        return view('vistorias.show',compact('vistoria'));
    }

    public function edit($id)
    {
        $resultados_acoes = $this->resultados_acoes;
        $tipo_abordagem = $this->tipo_abordagem;
        $tipo_abrigo_desmontado = $this->tipo_abrigo_desmontado;
        $encaminhamentos = $this->encaminhamentos;



        $vistoria = $this->vistoriaRepository->findWithoutFail($id);
        $vistoria->data_abordagem = $this->toBR($vistoria->data_abordagem);
        $ponto_id = $vistoria->ponto_id;
        $ponto = $this->ponto->find($ponto_id);



        if (empty($vistoria)) {
            Flash::error('Vistoria não encontrada');

            return redirect(route('vistorias.index'));
        }
        
        return view('vistorias.edit',compact('vistoria','ponto','ponto_concat','resultados_acoes','tipo_abordagem','tipo_abrigo_desmontado','encaminhamentos'));
    }


    public function update($id, UpdateVistoriaRequest $request)
    {
        $vistoria = $this->vistoriaRepository->findWithoutFail($id);

        if (empty($vistoria)) {
            Flash::error('Vistoria not found');

            return redirect(route('vistorias.index'));
        }

        $request['data_abordagem'] = $this->dtus($request->data_abordagem);

        $vistoria = $this->vistoriaRepository->update($request->all(), $id);

        Flash::success('Vistoria atualizada com sucesso!');

        return redirect()->to(session('url_lista_vistorias'));
    }

    public function destroy($id)
    {
        $vistoria = $this->vistoriaRepository->findWithoutFail($id);

        if (empty($vistoria)) {
            Flash::error('Vistoria não encontrada');

            return redirect(route('vistorias.index'));
        }

        $this->vistoriaRepository->delete($id);

        Flash::success('Vistoria excluída com sucesso.');

        return redirect(route('vistorias.index'));
    }

    public function toBR($date1){
		$date2 = explode('-',$date1);
		$date2 = $date2[2].'-'.$date2[1].'-'.$date2[0];
		return $date2;
    }

	public function dtus($dt){
		$d = explode('-',$dt);
		$d = $d[2].'-'.$d[1].'-'.$d[0];
		return $d;
	}
}
