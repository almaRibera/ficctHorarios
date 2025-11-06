@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detalle de Registro de Bitácora</h1>
                <p class="text-gray-600">Información detallada de la actividad registrada</p>
            </div>
            <a href="{{ route('admin.bitacoras.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                ← Volver a la bitácora
            </a>
        </div>
    </div>

    <!-- Información del Registro -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Información de la Actividad</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Acción Realizada</label>
                        <p class="mt-1 text-lg text-gray-900 bg-gray-50 p-3 rounded border">{{ $bitacora->accion_realizada }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID del Registro</label>
                            <p class="mt-1 text-gray-900">#{{ $bitacora->id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                            <p class="mt-1 text-gray-900">{{ $bitacora->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha y Hora de la Acción</label>
                        <p class="mt-1 text-gray-900">{{ $bitacora->fecha_y_hora->format('d/m/Y H:i:s') }}</p>
                        <p class="text-sm text-gray-500">({{ $bitacora->fecha_y_hora->diffForHumans() }})</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Usuario -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Información del Usuario</h3>
                
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ substr($bitacora->user->name, 0, 1) }}
                    </div>
                    <div class="ml-4">
                        <div class="text-lg font-medium text-gray-900">{{ $bitacora->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $bitacora->user->email }}</div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                            {{ $bitacora->user->rol }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Usuario desde</label>
                        <p class="text-sm text-gray-900">{{ $bitacora->user->created_at->format('d/m/Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID de Usuario</label>
                        <p class="text-sm text-gray-900">#{{ $bitacora->user->id }}</p>
                    </div>
                </div>

                @if(auth()->user()->id !== $bitacora->user_id)
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.users.show', $bitacora->user) }}" 
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white text-center px-4 py-2 rounded-lg block">
                        Ver perfil del usuario
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Acciones Adicionales -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Acciones</h3>
        <div class="flex gap-4">
            <a href="{{ route('admin.bitacoras.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                ← Volver a la lista
            </a>
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                🖨️ Imprimir
            </button>
        </div>
    </div>
</div>
@endsection