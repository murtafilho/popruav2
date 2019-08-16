<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
	public function autoComplete(Request $request){
		$q = $request->q;
		$data = DB::table('ender')
			->take(8)
			->select('id','logradouro','bairro')
			->where('logradouro', 'LIKE', $q. '%')
			->orderBy('logradouro')
			->get();

		foreach ($data as $item){
			$results[] = ['text'=>$item->logradouro.' - '.$item->bairro,'id'=>$item->id];
		}

		return response()->json($results);

	}

	public function index(Request $request){
		$data = $this->autoComplete($request);
		return view('teste',compact('data'));
	}


}
