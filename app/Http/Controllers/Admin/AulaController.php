<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aulas = Aula::latest()->get();
        return view('admin.aulas.index', compact('aulas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.aulas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:aulas',
            'piso' => 'required|in:1,2,3,4',
            'tipo' => 'required|in:teorica,laboratorio',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
            'capacidad' => 'nullable|integer|min:1|max:200',
            'equipamiento' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $aula = Aula::create($request->all());

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Creó aula: ' . $aula->nombre,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.aulas.index')
            ->with('success', 'Aula creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aula $aula)
    {
        return view('admin.aulas.show', compact('aula'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aula $aula)
    {
        return view('admin.aulas.edit', compact('aula'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aula $aula)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('aulas')->ignore($aula->id)
            ],
            'piso' => 'required|in:1,2,3,4',
            'tipo' => 'required|in:teorica,laboratorio',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
            'capacidad' => 'nullable|integer|min:1|max:200',
            'equipamiento' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $aula->update($request->all());

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Actualizó aula: ' . $aula->nombre,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.aulas.index')
            ->with('success', 'Aula actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aula $aula)
    {
        $nombreAula = $aula->nombre;
        $aula->delete();

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Eliminó aula: ' . $nombreAula,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.aulas.index')
            ->with('success', 'Aula eliminada exitosamente.');
    }

    /**
     * Cambiar estado rápido del aula
     */
    public function cambiarEstado(Request $request, Aula $aula)
    {
        $request->validate([
            'estado' => 'required|in:disponible,ocupada,mantenimiento'
        ]);

        $estadoAnterior = $aula->estado;
        $aula->update(['estado' => $request->estado]);

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => "Cambió estado de aula {$aula->nombre} de {$estadoAnterior} a {$request->estado}",
            'fecha_y_hora' => now(),
        ]);

        return back()->with('success', 'Estado del aula actualizado exitosamente.');
    }
}