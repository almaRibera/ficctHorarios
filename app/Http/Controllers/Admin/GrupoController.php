<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\User;
use App\Models\GrupoMateria;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grupos = Grupo::with(['materiasAsignadas.materia', 'materiasAsignadas.docente'])->latest()->get();
        return view('admin.grupos.index', compact('grupos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.grupos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sigla_grupo' => 'required|string|max:10|unique:grupos',
            'codigo_grupo' => 'required|string|max:20|unique:grupos',
        ]);

        $grupo = Grupo::create($request->all());

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Creó grupo: ' . $grupo->sigla_grupo . ' - ' . $grupo->codigo_grupo,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
   public function show(Grupo $grupo)
{
    $grupo->load(['materiasAsignadas.materia', 'materiasAsignadas.docente', 'materiasAsignadas.horarios.aula']);
    $materias = Materia::all();
    $docentes = User::where('rol', 'docente')->get();
    
    return view('admin.grupos.show', compact('grupo', 'materias', 'docentes'));
}

    /**
     * Asignar materia a grupo
     */
    public function asignarMateria(Request $request, Grupo $grupo)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'docente_id' => 'required|exists:users,id',
            'horas_semanales' => 'required|integer|min:1|max:20',
        ]);

        // Verificar si ya existe la materia en el grupo
        $existe = GrupoMateria::where('grupo_id', $grupo->id)
            ->where('materia_id', $request->materia_id)
            ->exists();

        if ($existe) {
            return back()->withErrors(['materia_id' => 'Esta materia ya está asignada al grupo.']);
        }

        GrupoMateria::create([
            'grupo_id' => $grupo->id,
            'materia_id' => $request->materia_id,
            'docente_id' => $request->docente_id,
            'horas_semanales' => $request->horas_semanales,
        ]);

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Asignó materia al grupo: ' . $grupo->sigla_grupo,
            'fecha_y_hora' => now(),
        ]);

        return back()->with('success', 'Materia asignada al grupo exitosamente.');
    }

    /**
     * Eliminar materia del grupo
     */
    public function eliminarMateria(GrupoMateria $grupoMateria)
    {
        $grupoMateria->delete();

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Eliminó materia del grupo',
            'fecha_y_hora' => now(),
        ]);

        return back()->with('success', 'Materia eliminada del grupo exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grupo $grupo)
    {
        $siglaGrupo = $grupo->sigla_grupo;
        $codigoGrupo = $grupo->codigo_grupo;
        $grupo->delete();

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Eliminó grupo: ' . $siglaGrupo . ' - ' . $codigoGrupo,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo eliminado exitosamente.');
    }
}