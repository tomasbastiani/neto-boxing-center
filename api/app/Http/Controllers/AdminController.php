<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Socio;
use Carbon\Carbon;

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

    public function get_expirations()
    {
        // Obtén la fecha de hoy y añade un día
        // $fechaLimite = Carbon::now()->addDay()->toDateString(); // Esto te dará '2024-10-04' si hoy es '2024-10-03'
        $fechaLimite = Carbon::now()->subHours(3)->addDay()->toDateString();

        // Realiza la consulta para obtener los socios cuya fecha de expiración es igual a la fecha límite
        $sociosExpiracion = Socio::where('expiration', $fechaLimite)->get();

        // Devuelve la vista con los datos
        return response()->json([
            'message' => 'Socios que expiran pronto',
            'fechaLimite' => $fechaLimite, // Imprime la variable $fechaLimite
            'sociosExpiracion' => $sociosExpiracion
        ]);
    }
}
