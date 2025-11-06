@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Administrador</h1>
        <p class="text-gray-600">Bienvenido, {{ auth()->user()->name }}</p>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\User::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Docentes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Docente::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Registros Bitácora</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Bitacora::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Acciones Rápidas</h3>
        <div class="flex flex-wrap gap-4">
            <!-- Gestión de Usuarios -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 min-w-[200px]">
                <h4 class="text-lg font-semibold mb-2 text-blue-800">Gestión de Usuarios</h4>
                <p class="text-blue-600 text-sm mb-3">Administrar docentes y roles del sistema</p>
                <a href="{{ route('admin.users.index') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg inline-block text-sm">
                    Gestionar Usuarios
                </a>
            </div>

            <!-- Gestión de Materias -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 min-w-[200px]">
                <h4 class="text-lg font-semibold mb-2 text-green-800">Gestión de Materias</h4>
                <p class="text-green-600 text-sm mb-3">Administrar materias y planes de estudio</p>
                <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                    <span class="mr-2">📚</span> Gestionar Materias
                </button>
            </div>

            <!-- Generar Horarios -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 min-w-[200px]">
                <h4 class="text-lg font-semibold mb-2 text-purple-800">Generar Horarios</h4>
                <p class="text-purple-600 text-sm mb-3">Crear y optimizar horarios automáticamente</p>
                <button class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                    <span class="mr-2">🕐</span> Generar Horarios
                </button>
            </div>

            <!-- Reportes -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 min-w-[200px]">
                <h4 class="text-lg font-semibold mb-2 text-red-800">Reportes</h4>
                <p class="text-red-600 text-sm mb-3">Generar reportes y estadísticas</p>
                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                    <span class="mr-2">📊</span> Ver Reportes
                </button>
            </div>
        </div>
    </div>

        <!-- Bitácora Reciente Actualizada -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Actividad Reciente</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(\App\Models\Bitacora::with('user')->latest()->take(5)->get() as $bitacora)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $bitacora->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $bitacora->accion_realizada }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($bitacora->fecha_y_hora instanceof \Illuminate\Support\Carbon)
                                {{ $bitacora->fecha_y_hora->format('d/m/Y H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($bitacora->fecha_y_hora)->format('d/m/Y H:i') }}
                            @endif
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay registros en la bitácora
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
