<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bitacoras = Bitacora::with('user')->latest()->paginate(20);
        return view('admin.bitacoras.index', compact('bitacoras'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Bitacora $bitacora)
    {
        $bitacora->load('user');
        return view('admin.bitacoras.show', compact('bitacora'));
    }

    /**
     * Filtrar registros de bitácora
     */
    public function filtrar(Request $request)
    {
        $query = Bitacora::with('user');

        // Filtrar por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrar por fecha
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_y_hora', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_y_hora', '<=', $request->fecha_fin);
        }

        // Filtrar por término de búsqueda en acción
        if ($request->filled('search')) {
            $query->where('accion_realizada', 'like', '%' . $request->search . '%');
        }

        $bitacoras = $query->latest()->paginate(20);

        return view('admin.bitacoras.index', compact('bitacoras'));
    }
}