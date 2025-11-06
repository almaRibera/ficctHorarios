@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Grupo</h1>
            <p class="text-gray-600">Registre la información básica del grupo</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.grupos.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <!-- Sigla Grupo -->
                    <div>
                        <label for="sigla_grupo" class="block text-sm font-medium text-gray-700 mb-1">
                            Sigla del Grupo *
                        </label>
                        <input type="text" name="sigla_grupo" id="sigla_grupo" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('sigla_grupo') }}"
                               placeholder="Ej: G1, G2, MAT-1"
                               maxlength="10">
                        @error('sigla_grupo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código Grupo -->
                    <div>
                        <label for="codigo_grupo" class="block text-sm font-medium text-gray-700 mb-1">
                            Código del Grupo *
                        </label>
                        <input type="text" name="codigo_grupo" id="codigo_grupo" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('codigo_grupo') }}"
                               placeholder="Ej: INF-2024-G1, MAT-2024-A"
                               maxlength="20">
                        @error('codigo_grupo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="mt-8 flex gap-4">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                        💾 Guardar Grupo
                    </button>
                    <a href="{{ route('admin.grupos.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        ↩️ Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Información -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">💡 Información</h3>
            <p class="text-sm text-blue-700">
                Después de crear el grupo, podrá registrar los horarios para 6 materias con sus respectivos docentes y aulas.
            </p>
        </div>
    </div>
</div>
@endsection