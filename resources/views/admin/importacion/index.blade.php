@extends('layouts.app')

@section('title', 'Importar Docentes y Horarios - CSV')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">

        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3">
                Importar Docentes y Horarios
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Sube un archivo CSV con docentes, grupos, materias y horarios. El sistema validará todo automáticamente.
            </p>
        </div>

        <!-- Alertas de Error -->
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-5 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Resultado de Importación -->
        @if(session('resultado'))
            @php $resultado = session('resultado'); @endphp
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Resultado de la Importación
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-5 text-center transform transition hover:scale-105">
                        <div class="text-3xl font-bold text-green-700">{{ $resultado['importados'] }}</div>
                        <div class="text-sm font-medium text-green-800 mt-1">Importados</div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-5 text-center transform transition hover:scale-105">
                        <div class="text-3xl font-bold text-amber-700">{{ $resultado['omitidos'] }}</div>
                        <div class="text-sm font-medium text-amber-800 mt-1">Omitidos</div>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-5 text-center transform transition hover:scale-105">
                        <div class="text-3xl font-bold text-red-700">{{ count($resultado['errores']) }}</div>
                        <div class="text-sm font-medium text-red-800 mt-1">Errores</div>
                    </div>
                </div>

                @if(count($resultado['errores']) > 0)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                        <h4 class="font-semibold text-red-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Errores Encontrados
                        </h4>
                        <div class="max-h-64 overflow-y-auto bg-white rounded-lg p-3 space-y-2 border">
                            @foreach($resultado['errores'] as $error)
                                <div class="text-sm text-red-700 py-1.5 px-2 bg-red-50 rounded border-l-2 border-red-400">
                                    {{ $error }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Formulario + Acciones -->
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Formulario -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Subir Archivo CSV</h3>
                    
                    <form action="{{ route('admin.importacion.importar') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf

                        <div class="mb-6">
                            <label for="archivo" class="block text-sm font-semibold text-gray-700 mb-3">
                                Selecciona tu archivo
                            </label>
                            <div class="relative">
                                <input type="file" name="archivo" id="archivo" accept=".csv,.txt" required
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-4 bg-gray-50 hover:bg-gray-100 transition">
                                <p class="mt-2 text-xs text-gray-500">Archivos permitidos: .csv, .txt | Máximo: 10MB</p>
                            </div>
                            @error('archivo')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" id="submitBtn"
                                class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3.5 px-6 rounded-xl transition duration-300 shadow-md hover:shadow-lg flex items-center justify-center text-lg">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                            Importar Datos
                        </button>
                    </form>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="space-y-4">
                <a href="{{ route('admin.importacion.descargar-plantilla') }}"
                   class="block bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Plantilla CSV
                </a>

                <a href="{{ route('admin.importacion.ver-ejemplo') }}"
                   class="block bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-bold py-4 px-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d Sized="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ver Ejemplo
                </a>
            </div>
        </div>

        <!-- Instrucciones -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 sm:p-8">
            <h3 class="text-xl font-bold text-indigo-800 mb-5 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Instrucciones Importantes
            </h3>
            <ul class="grid sm:grid-cols-2 gap-3 text-sm text-indigo-700 font-medium">
                <li class="flex items-start">
                    <span class="text-indigo-600 mr-2">•</span> Usa la <strong>plantilla oficial</strong>
                </li>
                <li class="flex items-start">
                    <span class="text-indigo-600 mr-2">•</span> No modifiques los <strong>encabezados</strong>
                </li>
                <li class="flex items-start">
                    <span class="text-indigo-600 mr-2">•</span> Días: <code class="bg-white px-1 rounded text-xs">Lunes</code> a <code class="bg-white px-1 rounded text-xs">Sábado</code>
                </li>
                <li class="flex items-start">
                    <span class="text-indigo-600 mr-2">•</span> Horas en formato <strong>24h</strong>: <code class="bg-white px-1 rounded text-xs">08:30</code>
                </li>
                <li class="flex items-start">
                    <span class="text-indigo-600 mr-2">•</span> Hora fin > hora inicio
                </li>
                <li class="flex items-start">
                    <span class="text-indigo-600 mr-2">•</span> Se detectan <strong>conflictos</strong> automáticamente
                </li>
                <li class="flex items-start">
                    <span class="text-indigo-600 mr-2">•</span> CI duplicado = no se crea
                </li>
            </ul>
        </div>

    </div>
</div>

<!-- Loading Modal Mejorado -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full text-center transform transition-all scale-95">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
            <svg class="animate-spin h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Procesando...</h3>
        <p class="text-gray-600 mt-2">Analizando y validando tu archivo CSV</p>
        <div class="mt-4 flex justify-center space-x-1">
            <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce"></div>
            <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce delay-100"></div>
            <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce delay-200"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('importForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingModal = document.getElementById('loadingModal');

    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Procesando Archivo...
        `;
        loadingModal.classList.remove('hidden');
    });
});
</script>
@endsection