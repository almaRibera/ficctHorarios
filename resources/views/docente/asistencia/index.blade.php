@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Registro de Asistencia</h1>
        <p class="text-gray-600">Hoy: {{ $hoy->format('d/m/Y') }}</p>
        <p class="text-gray-600">Hora actual: <span id="hora-actual">{{ \Carbon\Carbon::now()->format('H:i') }}</span></p>
        <div class="mt-2 flex items-center text-sm">
            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
            <span class="text-gray-600 mr-4">Puede registrar</span>
            <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
            <span class="text-gray-600">Fuera de horario</span>
        </div>
    </div>

    @if($horariosHoy->count() > 0)
    <!-- Horarios de Hoy -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($horariosHoy as $horario)
        <div class="bg-white rounded-lg shadow border-l-4 
            {{ $horario->puede_registrar ? 'border-green-500' : 'border-gray-400' }} p-6">
            
            <!-- Información de la Clase -->
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800">{{ $horario->grupoMateria->materia->sigla }}</h3>
                <p class="text-gray-600 text-sm">{{ $horario->grupoMateria->materia->nombre }}</p>
                <p class="text-gray-500 text-sm">Grupo: {{ $horario->grupoMateria->grupo->sigla_grupo }}</p>
                <p class="text-gray-500 text-sm">Aula: {{ $horario->aula->nombre }}</p>
            </div>

            <!-- Horario -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Horario Clase:</span>
                    <span class="text-sm text-gray-900">{{ $horario->hora_inicio->format('H:i') }} - {{ $horario->hora_fin->format('H:i') }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Horario Registro:</span>
                    <span class="text-sm text-gray-900">{{ $horario->hora_inicio_permitido }} - {{ $horario->hora_fin_permitido }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Día:</span>
                    <span class="text-sm text-gray-900">{{ $horario->dia }}</span>
                </div>
            </div>

            <!-- Estado y Botón -->
            <div class="text-center">
                @if($horario->asistencia_registrada)
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="text-green-600 font-semibold mb-1">✅ Asistencia Registrada</div>
                    <div class="text-sm text-green-700">
                        Hora: {{ $horario->asistencia_registrada->hora_registro->format('H:i') }}
                    </div>
                    <div class="text-sm text-green-700 capitalize">
                        Estado: {{ $horario->asistencia_registrada->estado }}
                    </div>
                </div>
                @elseif($horario->puede_registrar)
                <form action="{{ route('docente.asistencia.store', $horario) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg font-semibold text-lg transition duration-200">
                        📝 Registrar Asistencia
                    </button>
                    <p class="text-xs text-gray-500 mt-2">
                        Tiempo restante: <span id="tiempo-restante-{{ $horario->id }}" class="font-semibold"></span>
                    </p>
                </form>
                @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="text-gray-500 font-semibold mb-1">⏰ Fuera de Horario</div>
                    <div class="text-sm text-gray-600">
                        Horario permitido: {{ $horario->hora_inicio_permitido }} - {{ $horario->hora_fin_permitido }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Tiempo Restante -->
            @if(!$horario->asistencia_registrada)
            <div class="mt-3 text-center">
                @php
                    $horaInicioPermitido = \Carbon\Carbon::createFromTimeString($horario->hora_inicio_permitido);
                    $horaFinPermitido = \Carbon\Carbon::createFromTimeString($horario->hora_fin_permitido);
                    $ahora = \Carbon\Carbon::now();
                    
                    if($ahora < $horaInicioPermitido) {
                        $mensaje = "Disponible en: ";
                        $color = 'text-blue-600';
                        $tiempoId = "countdown-{$horario->id}";
                        $segundosRestantes = $ahora->diffInSeconds($horaInicioPermitido, false);
                    } else if($ahora > $horaFinPermitido) {
                        $mensaje = "Tiempo de registro expirado";
                        $color = 'text-red-600';
                        $tiempoId = null;
                        $segundosRestantes = 0;
                    } else {
                        $mensaje = "Disponible - Tiempo restante: ";
                        $color = 'text-green-600';
                        $tiempoId = "countdown-{$horario->id}";
                        $segundosRestantes = $ahora->diffInSeconds($horaFinPermitido, false);
                    }
                @endphp
                <p class="text-xs {{ $color }} font-medium">
                    {{ $mensaje }}
                    @if($tiempoId)
                    <span id="{{ $tiempoId }}"></span>
                    @endif
                </p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <!-- Sin horarios para hoy -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
        <div class="text-blue-500 text-4xl mb-4">📅</div>
        <h3 class="text-xl font-semibold text-blue-800 mb-2">No tiene clases programadas para hoy</h3>
        <p class="text-blue-700">Disfrute su día.</p>
    </div>
    @endif

    <!-- Información -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-yellow-800 mb-2">💡 Información Importante</h4>
        <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
            <li>Puede registrar su asistencia desde 15 minutos antes hasta 15 minutos después del inicio de la clase</li>
            <li>Registro después de 15 minutos se considerará como tardanza</li>
            <li>No se puede registrar asistencia fuera del horario permitido</li>
            <li>Cada clase solo puede registrarse una vez por día</li>
        </ul>
    </div>
</div>

<!-- Script para actualizar automáticamente -->
<script>
function actualizarHoraActual() {
    const ahora = new Date();
    const horas = ahora.getHours().toString().padStart(2, '0');
    const minutos = ahora.getMinutes().toString().padStart(2, '0');
    document.getElementById('hora-actual').textContent = `${horas}:${minutos}`;
}

function actualizarCountdowns() {
    // Actualizar todos los countdowns
    document.querySelectorAll('[id^="countdown-"]').forEach(element => {
        const segundos = parseInt(element.getAttribute('data-segundos')) - 1;
        element.setAttribute('data-segundos', segundos);
        
        if (segundos <= 0) {
            location.reload();
            return;
        }
        
        const horas = Math.floor(segundos / 3600);
        const minutos = Math.floor((segundos % 3600) / 60);
        const segs = segundos % 60;
        
        if (horas > 0) {
            element.textContent = `${horas}h ${minutos}m ${segs}s`;
        } else if (minutos > 0) {
            element.textContent = `${minutos}m ${segs}s`;
        } else {
            element.textContent = `${segs}s`;
        }
    });
}

function actualizarTiempoRestante() {
    // Actualizar tiempo restante para botones activos
    document.querySelectorAll('form').forEach(form => {
        const tiempoElement = form.querySelector('[id^="tiempo-restante-"]');
        if (tiempoElement) {
            const ahora = new Date();
            const horarioId = tiempoElement.id.replace('tiempo-restante-', '');
            const horaFinPermitido = new Date();
            // Asumimos que el horario fin permitido es 15 minutos después del inicio
            horaFinPermitido.setMinutes(ahora.getMinutes() + 15);
            
            const diffMs = horaFinPermitido - ahora;
            const diffMins = Math.floor(diffMs / 60000);
            const diffSecs = Math.floor((diffMs % 60000) / 1000);
            
            if (diffMins > 0) {
                tiempoElement.textContent = `${diffMins}m ${diffSecs}s`;
            } else {
                tiempoElement.textContent = `${diffSecs}s`;
            }
            
            if (diffMs <= 0) {
                location.reload();
            }
        }
    });
}

// Inicializar countdowns
document.addEventListener('DOMContentLoaded', function() {
    // Configurar countdowns existentes
    @foreach($horariosHoy as $horario)
        @if(!$horario->asistencia_registrada)
            @php
                $horaInicioPermitido = \Carbon\Carbon::createFromTimeString($horario->hora_inicio_permitido);
                $horaFinPermitido = \Carbon\Carbon::createFromTimeString($horario->hora_fin_permitido);
                $ahora = \Carbon\Carbon::now();
                $segundosRestantes = 0;
                
                if($ahora < $horaInicioPermitido) {
                    $segundosRestantes = $ahora->diffInSeconds($horaInicioPermitido, false);
                } else if($ahora <= $horaFinPermitido) {
                    $segundosRestantes = $ahora->diffInSeconds($horaFinPermitido, false);
                }
            @endphp
            @if($segundosRestantes > 0)
                document.getElementById('countdown-{{ $horario->id }}').setAttribute('data-segundos', {{ $segundosRestantes }});
            @endif
        @endif
    @endforeach

    // Actualizar cada segundo
    setInterval(() => {
        actualizarHoraActual();
        actualizarCountdowns();
        actualizarTiempoRestante();
    }, 1000);

    // Actualizar la página completa cada 30 segundos para sincronizar estados
    setTimeout(() => {
        location.reload();
    }, 30000);
});
</script>
@endsection