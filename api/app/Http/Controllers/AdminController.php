<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Socio;

class AdminController extends Controller
{
    public function get_socios()
    {
        $socios = DB::table('socios')
        ->orderBy('id', 'asc')
        ->get();

        return response()->json(['socios' => $socios]);
    }

    public function create(Request $request)
    {
        $socio = Socio::create($request->all());

        return response()->json(['message' => 'Socio creado correctamente', 'socio' => $socio], 201);
    }

    public function edit(Request $request, $id)
    {
        $socio = Socio::find($id);

        if (!$socio) {
            return response()->json(['error' => 'Socio no encontrado'], 404);
        }

        $socio->update($request->all());

        return response()->json(['message' => 'Socio actualizado correctamente', 'socio' => $socio]);
    }

    public function delete($id)
    {
        $socio = Socio::find($id);

        if (!$socio) {
            return response()->json(['error' => 'Socio no encontrado'], 404);
        }

        // Eliminar el socio
        $socio->delete();
        
        return response()->json(['message' => 'Socio eliminado correctamente']);
    }
}
