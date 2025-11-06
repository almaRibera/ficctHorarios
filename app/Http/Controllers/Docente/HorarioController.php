<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\GrupoMateria;
use App\Models\HorarioDocente;
use App\Models\Aula;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HorarioController extends Controller
{
    /**
     * Mostrar materias asignadas al docente
     */
    public function index()
    {
        $materiasAsignadas = GrupoMateria::with(['grupo', 'materia', 'horarios.aula'])
            ->where('docente_id', Auth::id())
            ->get();

        return view('docente.horarios.index', compact('materiasAsignadas'));
    }

    /**
     * Mostrar formulario para asignar horario a una materia
     */
    public function create(GrupoMateria $grupoMateria)
    {
        // Verificar que el docente es el asignado a esta materia
        if ($grupoMateria->docente_id !== Auth::id()) {
            abort(403, 'No tiene permisos para acceder a esta materia.');
        }

        $aulas = Aula::where('estado', 'disponible')->get();
        $horariosExistentes = $grupoMateria->horarios;

        return view('docente.horarios.create', compact('grupoMateria', 'aulas', 'horariosExistentes'));
    }

    /**
     * Guardar horario para la materia
     */
    public function store(Request $request, GrupoMateria $grupoMateria)
    {
        // Verificar que el docente es el asignado a esta materia
        if ($grupoMateria->docente_id !== Auth::id()) {
            abort(403, 'No tiene permisos para acceder a esta materia.');
        }

        $request->validate([
            'aula_id' => 'required|exists:aulas,id',
            'dia' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        // Verificar conflicto de horarios para el docente
        $conflictoDocente = HorarioDocente::whereHas('grupoMateria', function($query) use ($grupoMateria) {
                $query->where('docente_id', $grupoMateria->docente_id);
            })
            ->where('dia', $request->dia)
            ->where(function($query) use ($request) {
                $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                      ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                      ->orWhere(function($q) use ($request) {
                          $q->where('hora_inicio', '<=', $request->hora_inicio)
                            ->where('hora_fin', '>=', $request->hora_fin);
                      });
            })
            ->exists();

        if ($conflictoDocente) {
            return back()->withErrors(['conflicto' => 'Ya tiene una clase asignada en este horario.']);
        }

        // Verificar conflicto de horarios para el aula
        $conflictoAula = HorarioDocente::where('aula_id', $request->aula_id)
            ->where('dia', $request->dia)
            ->where(function($query) use ($request) {
                $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                      ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                      ->orWhere(function($q) use ($request) {
                          $q->where('hora_inicio', '<=', $request->hora_inicio)
                            ->where('hora_fin', '>=', $request->hora_fin);
                      });
            })
            ->exists();

        if ($conflictoAula) {
            return back()->withErrors(['conflicto' => 'El aula seleccionada está ocupada en este horario.']);
        }

        // Calcular horas asignadas
        $horasAsignadas = $grupoMateria->horasAsignadas();
        $nuevasHoras = (strtotime($request->hora_fin) - strtotime($request->hora_inicio)) / 3600;
        $totalHoras = $horasAsignadas + $nuevasHoras;

        if ($totalHoras > $grupoMateria->horas_semanales) {
            return back()->withErrors(['horas' => 'Excede las horas semanales asignadas para esta materia.']);
        }

        HorarioDocente::create([
            'grupo_materia_id' => $grupoMateria->id,
            'aula_id' => $request->aula_id,
            'dia' => $request->dia,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
        ]);

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => Auth::id(),
            'accion_realizada' => 'Asignó horario para materia: ' . $grupoMateria->materia->sigla . ' - Grupo: ' . $grupoMateria->grupo->sigla_grupo,
            'fecha_y_hora' => now(),
        ]);

        return back()->with('success', 'Horario asignado exitosamente.');
    }

    /**
     * Eliminar horario
     */
    public function destroy(HorarioDocente $horario)
    {
        // Verificar que el docente es el asignado a esta materia
        if ($horario->grupoMateria->docente_id !== Auth::id()) {
            abort(403, 'No tiene permisos para eliminar este horario.');
        }

        $horario->delete();

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => Auth::id(),
            'accion_realizada' => 'Eliminó horario de materia: ' . $horario->grupoMateria->materia->sigla,
            'fecha_y_hora' => now(),
        ]);

        return back()->with('success', 'Horario eliminado exitosamente.');
    }
}