<?php

namespace App\Repositories;

use App\Models\Endereco;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;
class EnderecoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'logradouro_id',
        'tipo',
        'logradouro',
        'bairro',
        'regional'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Endereco::class;
    }

	public function  buscar($request){
		$result = $this->model->query();
    	if($request->logradouro or $request->bairro or $request->regional)
	    {

		    if (!empty($request->logradouro))
		    {
			    $result = $result->where('logradouro', 'like', '%' . $request->logradouro . '%');
		    }
		    if (!empty($request->bairro))
		    {
			    $result = $result->where('bairro', 'like', '%' . $request->bairro . '%');
		    }
		    if (!empty($request->regional))
		    {
			    $result = $result->where('regional', 'like', '%' . $request->regional . '%');
		    }
		    return $result;
	    }else{
    		$result = $result->where('id',-1);
    		return $result;
	    }
	}

	public function autoComplete(){

		$q = 'maria';
		$data = $this->model()
			->take(8)
			->select('id','logradouro','bairro','regional','tipo')
			->where('logradouro', 'LIKE', '%'.$q. '%')
			->orderBy('logradouro')
			->get();

		foreach ($data as $item){
			$results[] = ['text'=>$item->logradouro.' - '.$item->bairro.' - '.$item->regional,'id'=>$item->id];
		}

		return response()->json($results);

	}


}
