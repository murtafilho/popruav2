<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $num_pontos = \Illuminate\Support\Facades\DB::table('pontos')->count();
        $num_vistorias = \Illuminate\Support\Facades\DB::table('vistorias')->count();
        return view('home',compact('num_pontos','num_vistorias'));
    }
}
