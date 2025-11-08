<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\User;
use App\Models\HorarioDocente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Mostrar reporte general de asistencias
     */
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $docenteId = $request->get('docente_id');
        
        $query = Asistencia::with(['docente', 'horario.grupoMateria.materia', 'horario.grupoMateria.grupo', 'horario.aula']);
        
        if ($fecha) {
            $query->where('fecha_clase', $fecha);
        }
        
        if ($docenteId) {
            $query->where('docente_id', $docenteId);
        }
        
        $asistencias = $query->latest()->paginate(20);
        $docentes = User::where('rol', 'docente')->get();

        // Estadísticas
        $totalAsistencias = $asistencias->total();
        $presentes = $asistencias->where('estado', 'presente')->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        $faltas = $totalAsistencias - $presentes - $tardanzas;

        return view('admin.asistencias.index', compact(
            'asistencias', 
            'docentes', 
            'fecha', 
            'docenteId',
            'totalAsistencias',
            'presentes',
            'tardanzas',
            'faltas'
        ));
    }

    /**
     * Mostrar asistencias por docente
     */
    public function porDocente(Request $request, User $docente = null)
    {
        $docentes = User::where('rol', 'docente')->get();
        
        if (!$docente && $docentes->count() > 0) {
            $docente = $docentes->first();
        }
        
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::today()->format('Y-m-d'));
        
        if ($docente) {
            $asistencias = Asistencia::with(['horario.grupoMateria.materia', 'horario.grupoMateria.grupo', 'horario.aula'])
                ->where('docente_id', $docente->id)
                ->whereBetween('fecha_clase', [$fechaInicio, $fechaFin])
                ->latest()
                ->paginate(20);

            // Estadísticas del docente
            $totalClases = HorarioDocente::whereHas('grupoMateria', function($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            })->count();

            $totalAsistencias = $asistencias->count();
            $presentes = $asistencias->where('estado', 'presente')->count();
            $tardanzas = $asistencias->where('estado', 'tardanza')->count();
            $faltas = $totalClases - $totalAsistencias;

            $porcentajeAsistencia = $totalClases > 0 ? ($totalAsistencias / $totalClases) * 100 : 0;
        } else {
            $asistencias = collect();
            $totalClases = 0;
            $totalAsistencias = 0;
            $presentes = 0;
            $tardanzas = 0;
            $faltas = 0;
            $porcentajeAsistencia = 0;
        }

        return view('admin.asistencias.por-docente', compact(
            'docentes',
            'docente',
            'asistencias',
            'fechaInicio',
            'fechaFin',
            'totalClases',
            'totalAsistencias',
            'presentes',
            'tardanzas',
            'faltas',
            'porcentajeAsistencia'
        ));
    }

    /**
     * Mostrar detalle de una asistencia
     */
    public function show(Asistencia $asistencia)
    {
        $asistencia->load(['docente', 'horario.grupoMateria.materia', 'horario.grupoMateria.grupo', 'horario.aula']);
        return view('admin.asistencias.show', compact('asistencia'));
    }
}