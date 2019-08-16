<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Servicos\RoteiroService;


class RoteiroController extends Controller
{

    public function index(Request $request)
    {
        if(!$request->q){
            $term = "";
        }else{
            $term = $request->q;
        }

        $roteiros = DB::table('qry_tematica_sub_roteiros')
        ->select('Des_Grp','Des_Item','Idn_Item','Des_Subg_Item')
        ->where('Des_Item','LIKE',"%$term%")
        ->orWhere('Des_Tema','LIKE',"%$term%")
        ->orWhere('Des_Rote','LIKE',"%$term%")
        ->orWhere('Des_Subg_Item','LIKE',"%$term%")
        ->orderBy('Des_Grp','Des_Subg_Item')
        ->distinct()
        ->paginate(30);        
        return view('roteiros.index',compact('roteiros','request'));

    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
    }

    public function mostra_componente($id){

        $roteiros = DB::table('qry_tematica_sub_roteiros')
        ->select('Des_Grp','Des_Item','Des_Subg_Item','Des_Tema','Idn_Item')
        ->distinct()
        ->where('Idn_Item','=',$id)
        ->get(); 

        $item = DB::table('Item')->select('Idn_Item','Des_Item')->where('Idn_Item','=',$id)->get();

        $componente = DB::table('qry_componente_an')->find($id);
        return view('roteiros.componente',compact('roteiros','componente','item'));
    }
}
