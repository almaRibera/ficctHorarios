@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Registrar Asistencia</h1>
            <p class="text-gray-600">
                {{ $horario->grupoMateria->materia->sigla }} - {{ $horario->grupoMateria->grupo->sigla_grupo }} | 
                {{ $horario->hora_inicio->format('H:i') }} - {{ $horario->hora_fin->format('H:i') }}
            </p>
        </div>

        <!-- Cámara y Formulario -->
        <div class="bg-white rounded-lg shadow p-6">
            <form id="asistencia-form" action="{{ route('docente.asistencia.store', $horario) }}" method="POST">
                @csrf
                
                <!-- Vista previa de cámara -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tome una foto del aula:</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <div id="camera-preview" class="mb-4">
                            <video id="video" width="100%" height="300" autoplay playsinline class="rounded-lg bg-gray-100"></video>
                            <canvas id="canvas" class="hidden"></canvas>
                        </div>
                        <div id="photo-preview" class="hidden mb-4">
                            <img id="photo" src="" alt="Foto capturada" class="rounded-lg mx-auto max-h-64">
                        </div>
                        
                        <div class="flex gap-2 justify-center">
                            <button type="button" id="capture-btn" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                📷 Capturar Foto
                            </button>
                            <button type="button" id="retake-btn" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg hidden">
                    🔄 Volver a Tomar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Campo oculto para la foto -->
                <input type="hidden" name="foto" id="foto-input">

                <!-- Observaciones -->
                <div class="mb-6">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones (opcional)
                    </label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Alguna observación sobre la clase..."></textarea>
                </div>

                <!-- Información -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">💡 Información importante</h4>
                    <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                        <li>La foto debe mostrar claramente el aula y los estudiantes</li>
                        <li>Solo puede registrar asistencia 15 minutos antes o después de su horario</li>
                        <li>El sistema registrará automáticamente si llegó a tiempo o con tardanza</li>
                    </ul>
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <button type="submit" id="submit-btn" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium flex items-center disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        ✅ Registrar Asistencia
                    </button>
                    <a href="{{ route('docente.asistencia.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        ↩️ Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let stream = null;
let photoCaptured = false;

// Elementos DOM
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const photo = document.getElementById('photo');
const captureBtn = document.getElementById('capture-btn');
const retakeBtn = document.getElementById('retake-btn');
const submitBtn = document.getElementById('submit-btn');
const fotoInput = document.getElementById('foto-input');
const cameraPreview = document.getElementById('camera-preview');
const photoPreview = document.getElementById('photo-preview');

// Inicializar cámara
async function initCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: 'environment' 
            } 
        });
        video.srcObject = stream;
    } catch (err) {
        console.error('Error al acceder a la cámara:', err);
        alert('No se pudo acceder a la cámara. Por favor, permita el acceso a la cámara.');
    }
}

// Capturar foto
captureBtn.addEventListener('click', function() {
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convertir a base64
    const photoData = canvas.toDataURL('image/png');
    fotoInput.value = photoData;
    photo.src = photoData;
    
    // Mostrar vista previa
    cameraPreview.classList.add('hidden');
    photoPreview.classList.remove('hidden');
    captureBtn.classList.add('hidden');
    retakeBtn.classList.remove('hidden');
    submitBtn.disabled = false;
    
    photoCaptured = true;
    
    // Detener cámara
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});

// Volver a tomar foto
retakeBtn.addEventListener('click', function() {
    photoPreview.classList.add('hidden');
    cameraPreview.classList.remove('hidden');
    captureBtn.classList.remove('hidden');
    retakeBtn.classList.add('hidden');
    submitBtn.disabled = true;
    fotoInput.value = '';
    photoCaptured = false;
    
    // Reiniciar cámara
    initCamera();
});

// Validar envío del formulario
document.getElementById('asistencia-form').addEventListener('submit', function(e) {
    if (!photoCaptured) {
        e.preventDefault();
        alert('Por favor, capture una foto antes de registrar la asistencia.');
        return false;
    }
});

// Inicializar cámara al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    initCamera();
});

// Limpiar al salir de la página
window.addEventListener('beforeunload', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});
</script>

<style>
#video, #photo {
    max-width: 100%;
    height: auto;
}
</style>
@endsection