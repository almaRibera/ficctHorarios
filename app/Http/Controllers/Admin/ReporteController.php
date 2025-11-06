<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Bitacora;
use App\Models\Materia;
use App\Models\Aula;
use App\Models\User;
use App\Models\Grupo;
use App\Models\GrupoMateria;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * Mostrar página principal de reportes
     */
    public function index()
    {
        return view('admin.reportes.index');
    }

    /**
     * Generar reporte según tipo y formato
     */
    public function generar(Request $request)
    {
        $request->validate([
            'tipo_reporte' => 'required|in:asistencias,bitacora,materias,aulas,docentes,grupos',
            'formato' => 'required|in:html,excel,pdf',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'docente_id' => 'nullable|exists:users,id',
        ]);

        $tipoReporte = $request->tipo_reporte;
        $formato = $request->formato;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;
        $docenteId = $request->docente_id;

        // Generar datos según el tipo de reporte
        $datos = $this->obtenerDatosReporte($tipoReporte, $fechaInicio, $fechaFin, $docenteId);
        $titulo = $this->obtenerTituloReporte($tipoReporte, $fechaInicio, $fechaFin);

        // Devolver según formato
        switch ($formato) {
            case 'excel':
                return $this->exportarExcel($tipoReporte, $datos, $titulo);
            case 'pdf':
                return $this->exportarPDF($tipoReporte, $datos, $titulo);
            case 'html':
            default:
                return view('admin.reportes.resultado', compact('datos', 'titulo', 'tipoReporte', 'formato'));
        }
    }

    /**
     * Obtener datos según tipo de reporte
     */
    private function obtenerDatosReporte($tipo, $fechaInicio = null, $fechaFin = null, $docenteId = null)
    {
        switch ($tipo) {
            case 'asistencias':
                return $this->obtenerDatosAsistencias($fechaInicio, $fechaFin, $docenteId);
            
            case 'bitacora':
                return $this->obtenerDatosBitacora($fechaInicio, $fechaFin);
            
            case 'materias':
                return $this->obtenerDatosMaterias();
            
            case 'aulas':
                return $this->obtenerDatosAulas();
            
            case 'docentes':
                return $this->obtenerDatosDocentes();
            
            case 'grupos':
                return $this->obtenerDatosGrupos();
            
            default:
                return [];
        }
    }

    /**
     * Obtener datos para reporte de asistencias
     */
    private function obtenerDatosAsistencias($fechaInicio, $fechaFin, $docenteId)
    {
        $query = Asistencia::with(['docente', 'horario.grupoMateria.materia', 'horario.grupoMateria.grupo', 'horario.aula']);

        if ($fechaInicio) {
            $query->where('fecha', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha', '<=', $fechaFin);
        }

        if ($docenteId) {
            $query->where('docente_id', $docenteId);
        }

        return $query->latest()->get();
    }

    /**
     * Obtener datos para reporte de bitácora
     */
    private function obtenerDatosBitacora($fechaInicio, $fechaFin)
    {
        $query = Bitacora::with('user');

        if ($fechaInicio) {
            $query->where('fecha_y_hora', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha_y_hora', '<=', $fechaFin . ' 23:59:59');
        }

        return $query->latest()->get();
    }

    /**
     * Obtener datos para reporte de materias
     */
    private function obtenerDatosMaterias()
    {
        return Materia::withCount(['gruposMateria'])->latest()->get();
    }

    /**
     * Obtener datos para reporte de aulas
     */
    private function obtenerDatosAulas()
    {
        return Aula::latest()->get();
    }

    /**
     * Obtener datos para reporte de docentes
     */
    private function obtenerDatosDocentes()
    {
        return User::where('rol', 'docente')
            ->withCount(['materiasAsignadas', 'horarios'])
            ->latest()
            ->get();
    }

    /**
     * Obtener datos para reporte de grupos
     */
    private function obtenerDatosGrupos()
    {
        return Grupo::withCount(['materiasAsignadas'])
            ->with(['materiasAsignadas.materia', 'materiasAsignadas.docente'])
            ->latest()
            ->get();
    }

    /**
     * Obtener título del reporte
     */
    private function obtenerTituloReporte($tipo, $fechaInicio, $fechaFin)
    {
        $titulos = [
            'asistencias' => 'Reporte de Asistencias de Docentes',
            'bitacora' => 'Reporte de Bitácora del Sistema',
            'materias' => 'Reporte de Materias',
            'aulas' => 'Reporte de Aulas',
            'docentes' => 'Reporte de Docentes',
            'grupos' => 'Reporte de Grupos',
        ];

        $titulo = $titulos[$tipo] ?? 'Reporte del Sistema';

        if ($fechaInicio && $fechaFin) {
            $titulo .= " del {$fechaInicio} al {$fechaFin}";
        } elseif ($fechaInicio) {
            $titulo .= " desde {$fechaInicio}";
        } elseif ($fechaFin) {
            $titulo .= " hasta {$fechaFin}";
        }

        return $titulo;
    }

    /**
     * Exportar a Excel
     */
    private function exportarExcel($tipo, $datos, $titulo)
    {
        // Para implementación completa necesitaríamos crear Export classes
        // Por ahora redirigimos a HTML
        return back()->with('info', 'La exportación a Excel estará disponible pronto.');
    }

    /**
     * Exportar a PDF
     */
    private function exportarPDF($tipo, $datos, $titulo)
    {
        // Para implementación completa necesitaríamos crear PDF views
        // Por ahora redirigimos a HTML
        return back()->with('info', 'La exportación a PDF estará disponible pronto.');
    }

    /**
     * Vista previa de reporte para impresión
     */
    public function imprimir(Request $request)
    {
        $datos = $this->obtenerDatosReporte(
            $request->tipo_reporte,
            $request->fecha_inicio,
            $request->fecha_fin,
            $request->docente_id
        );

        $titulo = $this->obtenerTituloReporte(
            $request->tipo_reporte,
            $request->fecha_inicio,
            $request->fecha_fin
        );

        return view('admin.reportes.imprimir', compact('datos', 'titulo', 'request'));
    }
}