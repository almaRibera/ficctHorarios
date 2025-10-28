@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Docente</h1>
        <p class="text-gray-600">Bienvenido al sistema de gestión académica</p>
    </div>
    
    <!-- Información del docente -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Mi Información</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><strong class="text-gray-700">Nombre:</strong> {{ auth()->user()->name }}</p>
                <p><strong class="text-gray-700">Email:</strong> {{ auth()->user()->email }}</p>
                <p><strong class="text-gray-700">Rol:</strong> 
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm capitalize">{{ auth()->user()->rol }}</span>
                </p>
            </div>
            <div>
                <p><strong class="text-gray-700">Materias asignadas:</strong> 4</p>
                <p><strong class="text-gray-700">Horas semanales:</strong> 24</p>
                <p><strong class="text-gray-700">Grupos:</strong> 3</p>
            </div>
        </div>
    </div>

    <!-- Acciones del docente -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3 text-blue-600">📅 Mi Horario Semanal</h3>
            <p class="text-gray-600 mb-4">Consulta tu horario de clases</p>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full">
                Ver Horario Completo
            </button>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3 text-green-600">✅ Registrar Asistencia</h3>
            <p class="text-gray-600 mb-4">Marca tu asistencia diaria</p>
            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded w-full">
                Registrar Hoy
            </button>
        </div>
    </div>

    <!-- Próximas clases -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Próximas Clases Hoy</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                <div>
                    <p class="font-medium">Sistemas de Información I</p>
                    <p class="text-sm text-gray-600">Grupo: SA - Aula: 304</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">10:30 - 12:00</p>
                    <p class="text-sm text-gray-600">En 45 minutos</p>
                </div>
            </div>
            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                <div>
                    <p class="font-medium">Base de Datos II</p>
                    <p class="text-sm text-gray-600">Grupo: SB - Aula: 205</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">14:00 - 15:30</p>
                    <p class="text-sm text-gray-600">En 4 horas</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection