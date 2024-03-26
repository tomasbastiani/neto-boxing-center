<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Socio;

class AdminController extends Controller
{
    public function get_socios()
    {
        // Realizar la consulta a la base de datos
        $socios = DB::table('socios')
        ->orderByDesc('id') // Orden descendente por el campo 'id'
        ->get();

        // Retornar los resultados en un objeto
        return response()->json(['socios' => $socios]);
    }

    public function create(Request $request)
    {
        // Crear un nuevo socio con los datos proporcionados en la solicitud
        $socio = Socio::create($request->all());

        // Retornar una respuesta con el socio creado
        return response()->json(['message' => 'Socio creado correctamente', 'socio' => $socio], 201);
    }

    public function edit(Request $request, $id)
    {
        // Obtener el socio a editar
        $socio = Socio::find($id);

        if (!$socio) {
            // Si no se encuentra el socio, retornar un mensaje de error
            return response()->json(['error' => 'Socio no encontrado'], 404);
        }

        // Actualizar los datos del socio
        $socio->update($request->all());

        // Retornar una respuesta con el socio actualizado
        return response()->json(['message' => 'Socio actualizado correctamente', 'socio' => $socio]);
    }

    public function delete($id)
    {
        // Buscar el socio por su ID
        $socio = Socio::find($id);

        if (!$socio) {
            // Si no se encuentra el socio, retornar un mensaje de error
            return response()->json(['error' => 'Socio no encontrado'], 404);
        }

        // Eliminar el socio
        $socio->delete();

        // Retornar una respuesta con un mensaje de éxito
        return response()->json(['message' => 'Socio eliminado correctamente']);
    }
}
