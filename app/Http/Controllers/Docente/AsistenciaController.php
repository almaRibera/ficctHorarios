<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\HorarioDocente;
use App\Models\Asistencia;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Mostrar horarios del día actual para registrar asistencia
     */
    public function index()
    {
        $hoy = Carbon::today();
        $diaSemana = $this->getDiaSemana($hoy->dayOfWeek);
        
        // Obtener horarios del docente para hoy
        $horariosHoy = HorarioDocente::with(['grupoMateria.materia', 'grupoMateria.grupo', 'aula'])
            ->whereHas('grupoMateria', function($query) {
                $query->where('docente_id', Auth::id());
            })
            ->where('dia', $diaSemana)
            ->get()
            ->map(function($horario) use ($hoy) {
                $horario->puede_registrar = $this->puedeRegistrarAsistencia($horario, $hoy);
                $horario->asistencia_registrada = $this->getAsistenciaRegistrada($horario, $hoy);
                $horario->hora_inicio_permitido = $this->getHoraInicioPermitido($horario);
                $horario->hora_fin_permitido = $this->getHoraFinPermitido($horario);
                return $horario;
            });

        return view('docente.asistencia.index', compact('horariosHoy', 'hoy'));
    }

    /**
     * Registrar asistencia
     */
    public function store(Request $request, HorarioDocente $horario)
    {
        // Verificar que el docente es el asignado a esta materia
        if ($horario->grupoMateria->docente_id !== Auth::id()) {
            abort(403, 'No tiene permisos para registrar asistencia en este horario.');
        }

        $hoy = Carbon::today();
        
        // Verificar si ya existe asistencia para hoy
        $asistenciaExistente = Asistencia::where('horario_docente_id', $horario->id)
            ->where('fecha_clase', $hoy)
            ->exists();

        if ($asistenciaExistente) {
            return back()->withErrors(['asistencia' => 'Ya registró asistencia para esta clase.']);
        }

        // Verificar que está en el horario permitido
        if (!$this->puedeRegistrarAsistencia($horario, $hoy)) {
            return back()->withErrors(['horario' => 'No está en el horario permitido para registrar asistencia.']);
        }

        // Determinar estado (presente o tardanza)
        $horaActual = Carbon::now();
        $horaInicio = Carbon::createFromTimeString($horario->hora_inicio->format('H:i'));
        $minutosDiferencia = $horaInicio->diffInMinutes($horaActual, false);
        
        $estado = $minutosDiferencia <= 15 ? 'presente' : 'tardanza';

        // Registrar asistencia
        $asistencia = Asistencia::create([
            'horario_docente_id' => $horario->id,
            'docente_id' => Auth::id(),
            'fecha_clase' => $hoy,
            'hora_registro' => $horaActual->format('H:i'),
            'estado' => $estado,
            'observaciones' => $request->observaciones,
        ]);

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => Auth::id(),
            'accion_realizada' => 'Registró asistencia: ' . $horario->grupoMateria->materia->sigla . 
                                ' - ' . $horario->grupoMateria->grupo->sigla_grupo . 
                                ' (' . $estado . ')',
            'fecha_y_hora' => now(),
        ]);

        return back()->with('success', 'Asistencia registrada exitosamente.');
    }

    /**
     * Verificar si puede registrar asistencia en este momento
     */
    private function puedeRegistrarAsistencia($horario, $fecha)
    {
        $horaActual = Carbon::now();
        $horaInicio = Carbon::createFromTimeString($horario->hora_inicio->format('H:i'));
        
        // Permitir registro desde 15 minutos antes hasta 15 minutos después del inicio
        $horaInicioPermitido = $horaInicio->copy()->subMinutes(15);
        $horaFinPermitido = $horaInicio->copy()->addMinutes(15);
        
        // DEBUG: Mostrar información de horarios
        \Log::info("DEBUG Asistencia - Hora actual: " . $horaActual->format('H:i'));
        \Log::info("DEBUG Asistencia - Hora inicio clase: " . $horaInicio->format('H:i'));
        \Log::info("DEBUG Asistencia - Hora inicio permitido: " . $horaInicioPermitido->format('H:i'));
        \Log::info("DEBUG Asistencia - Hora fin permitido: " . $horaFinPermitido->format('H:i'));
        \Log::info("DEBUG Asistencia - Puede registrar: " . ($horaActual->between($horaInicioPermitido, $horaFinPermitido) ? 'SI' : 'NO'));
        
        return $horaActual->between($horaInicioPermitido, $horaFinPermitido);
    }

    /**
     * Obtener hora de inicio permitido
     */
    private function getHoraInicioPermitido($horario)
    {
        $horaInicio = Carbon::createFromTimeString($horario->hora_inicio->format('H:i'));
        return $horaInicio->copy()->subMinutes(15)->format('H:i');
    }

    /**
     * Obtener hora fin permitido
     */
    private function getHoraFinPermitido($horario)
    {
        $horaInicio = Carbon::createFromTimeString($horario->hora_inicio->format('H:i'));
        return $horaInicio->copy()->addMinutes(15)->format('H:i');
    }

    /**
     * Obtener asistencia registrada para un horario y fecha
     */
    private function getAsistenciaRegistrada($horario, $fecha)
    {
        return Asistencia::where('horario_docente_id', $horario->id)
            ->where('fecha_clase', $fecha)
            ->first();
    }

    /**
     * Convertir día de la semana numérico a texto
     */
    private function getDiaSemana($dayOfWeek)
    {
        $dias = [
            1 => 'Lunes',
            2 => 'Martes', 
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            0 => 'Domingo'
        ];
        
        return $dias[$dayOfWeek] ?? 'Domingo';
    }
}