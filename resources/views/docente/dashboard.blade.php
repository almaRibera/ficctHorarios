@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Docente</h1>
        <p class="text-gray-600">Bienvenido al sistema de gestión académica</p>
    </div>

    <!-- Información del Docente -->
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
        </div>
    </div>
</div>
@endsection
