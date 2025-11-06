<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materias = Materia::latest()->get();
        return view('admin.materias.index', compact('materias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.materias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sigla' => 'required|string|max:10|unique:materias',
            'nombre' => 'required|string|max:255',
            'nivel' => 'required|integer|min:1|max:10',
            'tipo' => 'required|in:truncal,electiva',
        ]);

        $materia = Materia::create($request->all());

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Creó materia: ' . $materia->sigla . ' - ' . $materia->nombre,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Materia $materia)
    {
        return view('admin.materias.show', compact('materia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materia $materia)
    {
        return view('admin.materias.edit', compact('materia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Materia $materia)
    {
        $request->validate([
            'sigla' => [
                'required',
                'string',
                'max:10',
                Rule::unique('materias')->ignore($materia->id)
            ],
            'nombre' => 'required|string|max:255',
            'nivel' => 'required|integer|min:1|max:10',
            'tipo' => 'required|in:truncal,electiva',
        ]);

        $materia->update($request->all());

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Actualizó materia: ' . $materia->sigla . ' - ' . $materia->nombre,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materia $materia)
    {
        $siglaMateria = $materia->sigla;
        $nombreMateria = $materia->nombre;
        $materia->delete();

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Eliminó materia: ' . $siglaMateria . ' - ' . $nombreMateria,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia eliminada exitosamente.');
    }
}