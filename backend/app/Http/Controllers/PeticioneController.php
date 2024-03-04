<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Peticione;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PeticioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $peticiones = Peticione::all();
        return $peticiones;
    }
    public function listMine(Request $request)
    {

        try {
            $user = Auth::user();
            $peticiones = Peticione::all()->where('user_id', $user->id);
            return $peticiones;
        }catch (\Exception $exception){
            return back()->withErrors( $exception->getMessage())->withInput();
        }

    }
    public function show(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        return $peticion;
    }
    public function update(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        $res = $peticion->update($request->all());
        if ($res){
            return response()->json(['message'=>'Petición actualizada satisfactoriamente.', 'peticion' => $peticion], 201);
        }
        return response()->json(['message'=>'Error actualizado la petición'], 500);
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'destinatario' => 'required',
            'categoria_id' => 'required',
// 'file' => 'required',
        ]);
        $input = $request->all();
        $category = Categoria::findOrFail($request->input('categoria_id'));
        $user=Auth::user();
//$user = Auth::user(); //asociarlo al usuario authenticado
        $peticion = new Peticione($input);
        $peticion->user()->associate($user);
        $peticion->categoria()->associate($category);
        $peticion->firmantes = 0;
        $peticion->estado = 'pendiente';
        $peticion->save();
        return $peticion;
}
    public function firmar(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        //$user = Auth::user();
        $user = 1;
        $user_id = [$user];
        //$user_id = [$user‐>id];
        $peticion->firmas()->attach($user_id);
        $peticion->firmantes = $peticion->firmantes + 1;
        $peticion->save();
return $peticion;
}
    public function cambiarEstado(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        $peticion->estado = 'aceptada';
        $peticion->save();
        return $peticion;
    }
    public function delete(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        $res = $peticion->delete();
        if ($res){
            return response()->json(['message' => 'Peticion eliminada satisfactoriamente'], 201);
        }
        return response()->json(['message'=>'Error eliminando la petición.'], 500);
    }

}
