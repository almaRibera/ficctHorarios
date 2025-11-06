@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Generador de Reportes</h1>
        <p class="text-gray-600">Genere reportes del sistema en diferentes formatos</p>
    </div>

    <!-- Formulario de Reportes -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.reportes.generar') }}" method="POST" id="reporte-form">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tipo de Reporte -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Reporte *
                    </label>
                    <select name="tipo_reporte" id="tipo_reporte" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione el tipo de reporte</option>
                        <option value="asistencias">📊 Asistencias de Docentes</option>
                        <option value="bitacora">📋 Bitácora del Sistema</option>
                        <option value="materias">📚 Materias</option>
                        <option value="aulas">🏫 Aulas</option>
                        <option value="docentes">👥 Docentes</option>
                        <option value="grupos">👨‍🏫 Grupos</option>
                    </select>
                </div>

                <!-- Formato de Salida -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Formato de Salida *
                    </label>
                    <select name="formato" id="formato" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione formato</option>
                        <option value="html">🌐 HTML (Vista Web)</option>
                        <option value="excel">📊 Excel (.xlsx)</option>
                        <option value="pdf">📄 PDF</option>
                    </select>
                </div>
            </div>

            <!-- Filtros por Fecha -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4" id="filtros-fecha">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <!-- Filtro por Docente (solo para asistencias) -->
            <div class="mt-4 hidden" id="filtro-docente">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Docente</label>
                <select name="docente_id" id="docente_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Todos los docentes</option>
                    @foreach(\App\Models\User::where('rol', 'docente')->get() as $docente)
                        <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Información del Reporte -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">💡 Información</h4>
                <div id="info-reporte" class="text-sm text-blue-700">
                    Seleccione un tipo de reporte para ver más información.
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-8 flex gap-4">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium flex items-center">
                    📊 Generar Reporte
                </button>
                <button type="button" id="btn-imprimir" 
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium flex items-center hidden">
                    🖨️ Vista para Imprimir
                </button>
                <button type="reset" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    🔄 Limpiar
                </button>
            </div>
        </form>
    </div>

    <!-- Ejemplos de Reportes -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Reporte de Asistencias -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">📊 Asistencias</h3>
            <p class="text-gray-600 text-sm mb-4">Reporte detallado de asistencias docentes con filtros por fecha y docente.</p>
            <div class="text-xs text-gray-500">
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Excel</span>
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">PDF</span>
                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">HTML</span>
            </div>
        </div>

        <!-- Reporte de Bitácora -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">📋 Bitácora</h3>
            <p class="text-gray-600 text-sm mb-4">Registro completo de actividades del sistema con filtros temporales.</p>
            <div class="text-xs text-gray-500">
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Excel</span>
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">PDF</span>
                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">HTML</span>
            </div>
        </div>

        <!-- Reporte de Materias -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">📚 Materias</h3>
            <p class="text-gray-600 text-sm mb-4">Listado completo de materias con información de grupos asignados.</p>
            <div class="text-xs text-gray-500">
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Excel</span>
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">PDF</span>
                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">HTML</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoReporte = document.getElementById('tipo_reporte');
    const filtroDocente = document.getElementById('filtro-docente');
    const filtrosFecha = document.getElementById('filtros-fecha');
    const infoReporte = document.getElementById('info-reporte');
    const btnImprimir = document.getElementById('btn-imprimir');
    const reporteForm = document.getElementById('reporte-form');

    const informacionReportes = {
        'asistencias': 'Reporte detallado de asistencias docentes. Incluye información de materia, grupo, aula, horario y estado de asistencia.',
        'bitacora': 'Registro completo de actividades del sistema. Muestra usuario, acción realizada y fecha/hora de cada evento.',
        'materias': 'Listado completo de materias del sistema con información de sigla, nombre, nivel y tipo.',
        'aulas': 'Inventario de aulas disponibles con información de piso, tipo, estado y capacidad.',
        'docentes': 'Directorio de docentes con información de contacto y estadísticas de materias asignadas.',
        'grupos': 'Listado de grupos con información de materias asignadas y docentes responsables.'
    };

    tipoReporte.addEventListener('change', function() {
        const valor = this.value;
        
        // Mostrar/ocultar filtros
        if (valor === 'asistencias') {
            filtroDocente.classList.remove('hidden');
            filtrosFecha.classList.remove('hidden');
            btnImprimir.classList.remove('hidden');
        } else if (['bitacora'].includes(valor)) {
            filtroDocente.classList.add('hidden');
            filtrosFecha.classList.remove('hidden');
            btnImprimir.classList.remove('hidden');
        } else {
            filtroDocente.classList.add('hidden');
            filtrosFecha.classList.add('hidden');
            btnImprimir.classList.remove('hidden');
        }

        // Actualizar información
        infoReporte.innerHTML = informacionReportes[valor] || 'Seleccione un tipo de reporte para ver más información.';
    });

    // Manejar clic en botón de imprimir
    btnImprimir.addEventListener('click', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.reportes.imprimir") }}';
        
        // Agregar CSRF token
        const csrfToken = document.querySelector('input[name="_token"]').value;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        // Agregar campos del formulario
        const campos = ['tipo_reporte', 'formato', 'fecha_inicio', 'fecha_fin', 'docente_id'];
        campos.forEach(campo => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = campo;
            input.value = document.querySelector(`[name="${campo}"]`).value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });

    // Establecer fechas por defecto (últimos 30 días)
    const hoy = new Date().toISOString().split('T')[0];
    const hace30Dias = new Date();
    hace30Dias.setDate(hace30Dias.getDate() - 30);
    const fechaHace30Dias = hace30Dias.toISOString().split('T')[0];

    document.getElementById('fecha_inicio').value = fechaHace30Dias;
    document.getElementById('fecha_fin').value = hoy;
});
</script>
@endsection