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
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
                return collect();
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

        return $query->orderBy('fecha', 'desc')->get();
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

        return $query->orderBy('fecha_y_hora', 'desc')->get();
    }

    /**
     * Obtener datos para reporte de materias
     */
    private function obtenerDatosMaterias()
    {
        return Materia::withCount(['gruposMateria'])
            ->with(['gruposMateria.grupo', 'gruposMateria.docente'])
            ->orderBy('sigla')
            ->get();
    }

    /**
     * Obtener datos para reporte de aulas
     */
    private function obtenerDatosAulas()
    {
        return Aula::orderBy('nombre')->get();
    }

    /**
     * Obtener datos para reporte de docentes
     */
    private function obtenerDatosDocentes()
    {
        return User::where('rol', 'docente')
            ->withCount(['materiasAsignadas'])
            ->with(['materiasAsignadas.grupo', 'materiasAsignadas.materia'])
            ->orderBy('name')
            ->get()
            ->map(function ($docente) {
                $horariosCount = 0;
                foreach ($docente->materiasAsignadas as $materiaAsignada) {
                    $horariosCount += $materiaAsignada->horarios->count();
                }
                
                $docente->horarios_count = $horariosCount;
                return $docente;
            });
    }

    /**
     * Obtener datos para reporte de grupos
     */
    private function obtenerDatosGrupos()
    {
        return Grupo::withCount(['materiasAsignadas'])
            ->with(['materiasAsignadas.materia', 'materiasAsignadas.docente'])
            ->orderBy('sigla_grupo')
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
        try {
            return $this->exportarExcelSimple($tipo, $datos, $titulo);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar Excel: ' . $e->getMessage());
        }
    }

    /**
     * Exportación simple a Excel
     */
    private function exportarExcelSimple($tipo, $datos, $titulo)
    {
        $fileName = $this->generarNombreArchivo($tipo, 'xlsx');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'w');
        
        // Escribir encabezados
        fputcsv($fp, [$titulo], "\t");
        fputcsv($fp, ['Generado el: ' . now()->format('d/m/Y H:i')], "\t");
        fputcsv($fp, ['Generado por: ' . auth()->user()->name], "\t");
        fputcsv($fp, [], "\t"); // Línea vacía

        // Escribir headers de columnas
        $headers = $this->obtenerHeadersExcel($tipo);
        fputcsv($fp, $headers, "\t");

        // Escribir datos
        foreach ($datos as $item) {
            $row = $this->formatearFilaExcel($tipo, $item);
            fputcsv($fp, $row, "\t");
        }

        fclose($fp);
        exit;
    }

    /**
     * Exportar a PDF
     */
    private function exportarPDF($tipo, $datos, $titulo)
    {
        try {
            $pdf = PDF::loadView('admin.reportes.pdf', [
                'datos' => $datos,
                'titulo' => $titulo,
                'tipoReporte' => $tipo
            ])->setPaper('a4', 'landscape');

            $fileName = $this->generarNombreArchivo($tipo, 'pdf');
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generar nombre de archivo
     */
    private function generarNombreArchivo($tipo, $extension)
    {
        $nombres = [
            'asistencias' => 'reporte_asistencias',
            'bitacora' => 'reporte_bitacora',
            'materias' => 'reporte_materias',
            'aulas' => 'reporte_aulas',
            'docentes' => 'reporte_docentes',
            'grupos' => 'reporte_grupos',
        ];

        $nombreBase = $nombres[$tipo] ?? 'reporte';
        $fecha = now()->format('Y-m-d_H-i');
        
        return "{$nombreBase}_{$fecha}.{$extension}";
    }

    /**
     * Obtener headers para Excel
     */
    private function obtenerHeadersExcel($tipo)
    {
        switch ($tipo) {
            case 'asistencias':
                return ['Fecha', 'Docente', 'Email Docente', 'Materia', 'Grupo', 'Aula', 'Horario', 'Registro', 'Estado'];
            
            case 'bitacora':
                return ['Fecha/Hora', 'Usuario', 'Email Usuario', 'Acción Realizada'];
            
            case 'materias':
                return ['Sigla', 'Nombre', 'Nivel', 'Tipo', 'Grupos Asignados', 'Docentes'];
            
            case 'aulas':
                return ['Nombre', 'Piso', 'Tipo', 'Estado', 'Capacidad'];
            
            case 'docentes':
                return ['Nombre', 'Email', 'Materias Asignadas', 'Horarios', 'Fecha Registro'];
            
            case 'grupos':
                return ['Sigla', 'Código', 'Materias', 'Docentes', 'Fecha Registro'];
            
            default:
                return ['Datos'];
        }
    }

    /**
     * Formatear fila para Excel
     */
    private function formatearFilaExcel($tipo, $item)
    {
        switch ($tipo) {
            case 'asistencias':
                return [
                    $item->fecha->format('d/m/Y'),
                    $item->docente->name,
                    $item->docente->email,
                    $item->horario->grupoMateria->materia->sigla,
                    $item->horario->grupoMateria->grupo->sigla_grupo,
                    $item->horario->aula->nombre,
                    $item->horario->hora_inicio->format('H:i') . '-' . $item->horario->hora_fin->format('H:i'),
                    $item->hora_registro,
                    $item->estado == 'presente' ? 'Presente' : 'Tardanza'
                ];
            
            case 'bitacora':
                return [
                    $item->fecha_y_hora->format('d/m/Y H:i'),
                    $item->user->name,
                    $item->user->email,
                    $item->accion_realizada
                ];
            
            case 'materias':
                return [
                    $item->sigla,
                    $item->nombre,
                    'Nivel ' . $item->nivel,
                    $item->tipo_completo,
                    $item->grupos_materia_count,
                    $item->gruposMateria->unique('docente_id')->count()
                ];
            
            case 'aulas':
                return [
                    $item->nombre,
                    'Piso ' . $item->piso,
                    $item->tipo_completo,
                    $item->estado_texto,
                    $item->capacidad ?? 'N/A'
                ];
            
            case 'docentes':
                return [
                    $item->name,
                    $item->email,
                    $item->materias_asignadas_count,
                    $item->horarios_count,
                    $item->created_at->format('d/m/Y')
                ];
            
            case 'grupos':
                return [
                    $item->sigla_grupo,
                    $item->codigo_grupo,
                    $item->materias_asignadas_count,
                    $item->materiasAsignadas->unique('docente_id')->count(),
                    $item->created_at->format('d/m/Y')
                ];
            
            default:
                return [json_encode($item)];
        }
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