<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Foto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class FotoController extends Controller
{
 

    public function index($vistoria_id)
    {
        
        $fotos = DB::table('fotos')->where('vistoria_id','=',$vistoria_id)->orderBy('id','DESC')->get();
        $vistoria = DB::table('qry_vistorias_v2')->where('id','=',$vistoria_id)->first();
        return view('fotos', compact('fotos','vistoria_id','vistoria'));
    }
    public function store(Request $request)
    {
        $path = $request->file('url')->store('imagens','public');
        $foto = new Foto();
        $foto->vistoria_id = $request->vistoria_id;
        $foto->url = $path; 
        $foto->save();
        return redirect('/fotos/'.$request->vistoria_id);
    }
    public function destroy($id) {
        $foto = Foto::find($id);
        $vistoria_id = $foto->vistoria_id;
        if (isset($foto)) {
            Storage::disk('public')->delete($foto->url); 
            $foto->delete();
        }
        return redirect('/fotos/'.$foto->vistoria_id);
    }
    public function download($id) {
        $foto = Foto::find($id);
        $vistoria_id = $foto->vistoria_id;
        if (isset($foto)) {
            $path =  Storage::disk('public')->getDriver()->getAdapter()->applyPathPrefix($foto->url);
            return response()->download($path);
        }
        return redirect('/fotos/'.$vistoria_id);
    }
}