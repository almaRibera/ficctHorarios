@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Asignar Horarios - {{ $grupoMateria->materia->sigla }}</h1>
                <p class="text-gray-600">Grupo: {{ $grupoMateria->grupo->sigla_grupo }}</p>
                <p class="text-gray-600">Horas semanales: {{ $grupoMateria->horas_semanales }}h | 
                   Asignadas: {{ $grupoMateria->horasAsignadas() }}h | 
                   Pendientes: {{ $grupoMateria->horasPendientes() }}h</p>
            </div>
            <a href="{{ route('docente.horarios.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                ← Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Formulario para asignar horario -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Asignar Nuevo Horario</h3>
            
            @if($grupoMateria->horasPendientes() > 0)
            <form action="{{ route('docente.horarios.store', $grupoMateria) }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-4">
                    <!-- Modalidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad de Clase *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="modalidad" value="presencial" class="text-blue-500 focus:ring-blue-500" checked>
                                <span class="ml-2 text-sm font-medium text-gray-700">
                                    🏫 Presencial
                                </span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="modalidad" value="virtual" class="text-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">
                                    💻 Virtual
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Aula (solo para presencial) -->
                    <div id="aula-field">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aula *</label>
                        <select name="aula_id" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione aula</option>
                            @foreach($aulas as $aula)
                                <option value="{{ $aula->id }}">{{ $aula->nombre }} ({{ $aula->tipo_completo }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Solo para clases presenciales</p>
                    </div>

                    <!-- Enlace Virtual (solo para virtual) -->
                    <div id="enlace-field" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Enlace de la Clase Virtual *</label>
                        <input type="url" name="enlace_virtual" 
                               placeholder="https://meet.google.com/xxx-xxxx-xxx"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Ej: Google Meet, Zoom, Teams, etc.</p>
                    </div>

                    <!-- Día -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Día *</label>
                        <select name="dia" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione día</option>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sábado">Sábado</option>
                        </select>
                    </div>

                    <!-- Hora Inicio y Fin -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora Inicio *</label>
                            <input type="time" name="hora_inicio" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora Fin *</label>
                            <input type="time" name="hora_fin" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mt-6">
                    💾 Asignar Horario
                </button>
            </form>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                <div class="text-green-600 text-2xl mb-2">✅</div>
                <p class="text-green-800 font-semibold">Todas las horas han sido asignadas</p>
            </div>
            @endif
        </div>

        <!-- Horarios Existentes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Horarios Asignados</h3>
            
            @if($horariosExistentes->count() > 0)
            <div class="space-y-3">
                @foreach($horariosExistentes as $horario)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold">{{ $horario->dia }}</div>
                            <div class="text-sm text-gray-600">{{ $horario->hora_inicio->format('H:i') }} - {{ $horario->hora_fin->format('H:i') }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                @if($horario->esPresencial())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        🏫 Presencial
                                    </span>
                                    <span class="text-sm text-blue-600">{{ $horario->aula->nombre ?? 'Sin aula' }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        💻 Virtual
                                    </span>
                                    @if($horario->enlace_virtual)
                                    <a href="{{ $horario->enlace_virtual }}" target="_blank" 
                                       class="text-sm text-green-600 hover:underline">
                                        Enlace
                                    </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <form action="{{ route('docente.horarios.destroy', $horario) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-500 hover:text-red-700"
                                    onclick="return confirm('¿Está seguro de eliminar este horario?')">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-4xl mb-4">⏰</div>
                <p class="text-gray-500">No hay horarios asignados aún.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalidadRadios = document.querySelectorAll('input[name="modalidad"]');
    const aulaField = document.getElementById('aula-field');
    const enlaceField = document.getElementById('enlace-field');
    const aulaSelect = document.querySelector('select[name="aula_id"]');
    const enlaceInput = document.querySelector('input[name="enlace_virtual"]');

    function toggleFields() {
        const modalidad = document.querySelector('input[name="modalidad"]:checked').value;
        
        if (modalidad === 'presencial') {
            aulaField.classList.remove('hidden');
            enlaceField.classList.add('hidden');
            aulaSelect.required = true;
            enlaceInput.required = false;
        } else {
            aulaField.classList.add('hidden');
            enlaceField.classList.remove('hidden');
            aulaSelect.required = false;
            enlaceInput.required = true;
        }
    }

    // Inicializar
    toggleFields();

    // Escuchar cambios en la modalidad
    modalidadRadios.forEach(radio => {
        radio.addEventListener('change', toggleFields);
    });
});
</script>
@endsection