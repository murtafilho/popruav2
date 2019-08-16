<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEnderecoRequest;
use App\Http\Requests\UpdateEnderecoRequest;
use App\Repositories\EnderecoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

class EnderecoController extends AppBaseController
{
    /** @var  EnderecoRepository */
    private $enderecoRepository;

    public function __construct(EnderecoRepository $enderecoRepo)
    {
        $this->enderecoRepository = $enderecoRepo;
    }

    /**
     * Display a listing of the Endereco.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
	    $enderecos = $this->enderecoRepository->buscar($request)
		    ->orderBy('logradouro')
		    ->orderBy('bairro')
		    ->paginate(10);
	    return view('enderecos.index',compact('enderecos','request'));

    }

    /**
     * Show the form for creating a new Endereco.
     *
     * @return Response
     */
    public function create()
    {

    	$tipos = DB::table('ender')->distinct()->select('tipo')->pluck('tipo','tipo');
    	$regionais = DB::table('ender')->distinct()->select('regional')->orderBy('regional')->pluck('regional','regional');
        return view('enderecos.create',compact('tipos','regionais'));
    }

    /**
     * Store a newly created Endereco in storage.
     *
     * @param CreateEnderecoRequest $request
     *
     * @return Response
     */
    public function store(CreateEnderecoRequest $request)
    {
        $input = $request->all();

        $endereco = $this->enderecoRepository->create($input);

        return redirect(route('enderecos.index'));
    }

    /**
     * Display the specified Endereco.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $endereco = $this->enderecoRepository->findWithoutFail($id);

        if (empty($endereco)) {
            Flash::error('Endereco not found');

            return redirect(route('enderecos.index'));
        }

        return view('enderecos.show')->with('endereco', $endereco);
    }

    /**
     * Show the form for editing the specified Endereco.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $endereco = $this->enderecoRepository->findWithoutFail($id);
	    $tipos = DB::table('ender')->distinct()->select('tipo')->pluck('tipo','tipo');
	    $regionais = DB::table('ender')->distinct()->select('regional')->orderBy('regional')->pluck('regional','regional');

        if (empty($endereco)) {
            Flash::error('Endereco not found');

            return redirect(route('enderecos.index'));
        }

        return view('enderecos.edit',compact('endereco','tipos','regionais'));
    }

    public function update($id, UpdateEnderecoRequest $request)
    {
        $endereco = $this->enderecoRepository->findWithoutFail($id);

        if (empty($endereco)) {
            Flash::error('Endereco not found');

            return redirect(route('enderecos.index'));
        }


        $endereco = $this->enderecoRepository->update($request->all(), $id);

        return redirect(route('enderecos.index'));
    }


    public function destroy($id)
    {
        $endereco = $this->enderecoRepository->findWithoutFail($id);

        if (empty($endereco)) {
            Flash::error('Endereco not found');

            return redirect(route('enderecos.index'));
        }

        $this->enderecoRepository->delete($id);

        Flash::success('Endereco deleted successfully.');

        return redirect(route('enderecos.index'));
    }

	public function autoComplete(Request $request){
		$q = $request->q;
		$data = DB::table('ender')
			->take(30)
			->select('id','logradouro','bairro','regional')
			->where('logradouro', 'LIKE', '%'.$q. '%')
			->orderBy('logradouro')
			->orderBy('regional')
			->orderBy('bairro')
			->get();

		foreach ($data as $item){
			$results[] = ['text'=>$item->logradouro.' - '.$item->bairro.' - '.$item->regional,'id'=>$item->id];
		}
		if(count($results) > 0){
			return response()->json($results);
		}else{
			return null;
		}

	}
}
