<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\HorarioDocente;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class AsistenciaController extends Controller
{
    /**
     * Mostrar horarios del día con botones de asistencia
     */
    public function index()
    {
        $hoy = now();
        $diaSemana = $hoy->dayOfWeekIso; // 1=Lunes, 6=Sábado
        
        $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diaActual = $dias[$diaSemana];
        
        // Obtener horarios del docente para hoy
        $horariosHoy = HorarioDocente::with(['grupoMateria.materia', 'grupoMateria.grupo', 'aula'])
            ->whereHas('grupoMateria', function($query) {
                $query->where('docente_id', Auth::id());
            })
            ->where('dia', $diaActual)
            ->orderBy('hora_inicio')
            ->get();

        // Para cada horario, verificar si ya se registró asistencia
        $horariosHoy->each(function($horario) {
            $horario->ya_registrado = Asistencia::yaRegistrado($horario->id);
            $horario->asistencia_hoy = Asistencia::where('horario_docente_id', $horario->id)
                ->where('fecha', today())
                ->first();
        });

        return view('docente.asistencia.index', compact('horariosHoy', 'hoy'));
    }

    /**
     * Mostrar formulario para registrar asistencia
     */
    public function create(HorarioDocente $horario)
    {
        // Verificar que el horario pertenece al docente
        if ($horario->grupoMateria->docente_id !== Auth::id()) {
            abort(403, 'No tiene permisos para acceder a este horario.');
        }

        // Verificar que es el día correcto
        $diaSemana = now()->dayOfWeekIso;
        $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diaActual = $dias[$diaSemana];
        
        if ($horario->dia !== $diaActual) {
            abort(403, 'No puede registrar asistencia para este día.');
        }

        // Verificar que está en el horario de la clase (±15 minutos)
        $horaActual = now();
        $horaInicio = \Carbon\Carbon::parse($horario->hora_inicio);
        $horaFin = \Carbon\Carbon::parse($horario->hora_fin);
        
        $minutosAntes = $horaInicio->copy()->subMinutes(15);
        $minutosDespues = $horaFin->copy()->addMinutes(15);

        if (!$horaActual->between($minutosAntes, $minutosDespues)) {
            abort(403, 'Solo puede registrar asistencia 15 minutos antes o después de su horario de clase.');
        }

        // Verificar si ya se registró
        if (Asistencia::yaRegistrado($horario->id)) {
            return redirect()->route('docente.asistencia.index')
                ->with('info', 'Ya registró su asistencia para esta clase.');
        }

        return view('docente.asistencia.create', compact('horario'));
    }

    /**
     * Registrar asistencia con foto
     */
    public function store(Request $request, HorarioDocente $horario)
    {
        // Verificaciones de seguridad
        if ($horario->grupoMateria->docente_id !== Auth::id()) {
            abort(403, 'No tiene permisos para acceder a este horario.');
        }

        // Verificar que está en el horario permitido
        $horaActual = now();
        $horaInicio = \Carbon\Carbon::parse($horario->hora_inicio);
        $minutosAntes = $horaInicio->copy()->subMinutes(15);
        $minutosDespues = \Carbon\Carbon::parse($horario->hora_fin)->addMinutes(15);

        if (!$horaActual->between($minutosAntes, $minutosDespues)) {
            return back()->withErrors(['error' => 'Fuera del horario permitido para registrar asistencia.']);
        }

        // Verificar si ya se registró
        if (Asistencia::yaRegistrado($horario->id)) {
            return redirect()->route('docente.asistencia.index')
                ->with('info', 'Ya registró su asistencia para esta clase.');
        }

        $request->validate([
            'foto' => 'required|string', // Base64 image
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Procesar imagen base64
        $imageData = $request->foto;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = 'asistencia_' . $horario->id . '_' . now()->format('Y-m-d_H-i-s') . '.png';
        
        // Guardar imagen
        Storage::disk('public')->put('asistencias/' . $imageName, base64_decode($imageData));

        // Determinar estado (presente o tardanza)
        $horaInicioClase = \Carbon\Carbon::parse($horario->hora_inicio);
        $estado = $horaActual->gt($horaInicioClase) ? 'tardanza' : 'presente';

        // Crear registro de asistencia
        $asistencia = Asistencia::create([
            'horario_docente_id' => $horario->id,
            'docente_id' => Auth::id(),
            'fecha' => today(),
            'hora_registro' => $horaActual->format('H:i:s'),
            'estado' => $estado,
            'foto_evidencia' => $imageName,
            'observaciones' => $request->observaciones,
        ]);

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => Auth::id(),
            'accion_realizada' => 'Registró asistencia para: ' . $horario->grupoMateria->materia->sigla . ' - ' . $horario->grupoMateria->grupo->sigla_grupo,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('docente.asistencia.index')
            ->with('success', 'Asistencia registrada exitosamente.');
    }

    /**
     * Ver detalle de asistencia registrada
     */
    public function show(Asistencia $asistencia)
{
    // Verificar que la asistencia pertenece al docente
    if ($asistencia->docente_id !== Auth::id()) {
        abort(403, 'No tiene permisos para ver esta asistencia.');
    }

    $asistencia->load(['horario.grupoMateria.materia', 'horario.grupoMateria.grupo', 'horario.aula']);
    
    return view('docente.asistencia.show', compact('asistencia'));
}
}