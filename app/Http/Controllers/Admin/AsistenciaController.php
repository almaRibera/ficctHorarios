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
     * Mostrar reporte de asistencias
     */
    public function index(Request $request)
    {
        $query = Asistencia::with(['docente', 'horario.grupoMateria.materia', 'horario.grupoMateria.grupo', 'horario.aula']);
        
        // Filtros
        if ($request->filled('docente_id')) {
            $query->where('docente_id', $request->docente_id);
        }
        
        if ($request->filled('fecha')) {
            $query->where('fecha', $request->fecha);
        } else {
            $query->where('fecha', today());
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $asistencias = $query->latest()->paginate(20);
        $docentes = User::where('rol', 'docente')->get();

        return view('admin.asistencias.index', compact('asistencias', 'docentes'));
    }

    /**
     * Mostrar reporte por docente
     */
    public function porDocente(Request $request, User $docente)
    {
        $query = Asistencia::with(['horario.grupoMateria.materia', 'horario.grupoMateria.grupo', 'horario.aula'])
            ->where('docente_id', $docente->id);
        
        // Filtros por fecha
        if ($request->filled('fecha_inicio')) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }

        $asistencias = $query->latest()->paginate(20);
        
        // Estadísticas
        $totalAsistencias = $asistencias->total();
        $presentes = $asistencias->where('estado', 'presente')->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        $faltas = $totalAsistencias - ($presentes + $tardanzas); // Asumiendo que no registró = falta

        return view('admin.asistencias.por-docente', compact('asistencias', 'docente', 'presentes', 'tardanzas', 'faltas'));
    }

    /**
     * Ver detalle de asistencia
     */
    public function show(Asistencia $asistencia)
    {
        $asistencia->load(['docente', 'horario.grupoMateria.materia', 'horario.grupoMateria.grupo', 'horario.aula']);
        return view('admin.asistencias.show', compact('asistencia'));
    }

    /**
     * Reporte mensual de asistencias
     */
    public function reporteMensual(Request $request)
    {
        $mes = $request->get('mes', now()->month);
        $anio = $request->get('anio', now()->year);
        
        $docentes = User::where('rol', 'docente')->get();
        $reporte = [];

        foreach ($docentes as $docente) {
            $asistencias = Asistencia::where('docente_id', $docente->id)
                ->whereYear('fecha', $anio)
                ->whereMonth('fecha', $mes)
                ->get();

            $reporte[] = [
                'docente' => $docente,
                'total_clases' => $asistencias->count(),
                'presentes' => $asistencias->where('estado', 'presente')->count(),
                'tardanzas' => $asistencias->where('estado', 'tardanza')->count(),
                'porcentaje_asistencia' => $asistencias->count() > 0 ? 
                    (($asistencias->where('estado', 'presente')->count() / $asistencias->count()) * 100) : 0
            ];
        }

        return view('admin.asistencias.reporte-mensual', compact('reporte', 'mes', 'anio'));
    }
}