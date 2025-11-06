@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Crear Nueva Materia</h1>
            <p class="text-gray-600">Complete la información de la nueva materia</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.materias.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Sigla -->
                    <div>
                        <label for="sigla" class="block text-sm font-medium text-gray-700 mb-1">
                            Sigla *
                        </label>
                        <input type="text" name="sigla" id="sigla" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('sigla') }}"
                               placeholder="Ej: INF-111, MAT-101"
                               maxlength="10">
                        @error('sigla')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de la Materia *
                        </label>
                        <input type="text" name="nombre" id="nombre" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Introducción a la Programación">
                        @error('nombre')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Nivel -->
                        <div>
                            <label for="nivel" class="block text-sm font-medium text-gray-700 mb-1">
                                Nivel *
                            </label>
                            <select name="nivel" id="nivel" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione el nivel</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('nivel') == $i ? 'selected' : '' }}>
                                        Nivel {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            @error('nivel')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo -->
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipo *
                            </label>
                            <select name="tipo" id="tipo" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione el tipo</option>
                                <option value="truncal" {{ old('tipo') == 'truncal' ? 'selected' : '' }}>Troncal</option>
                                <option value="electiva" {{ old('tipo') == 'electiva' ? 'selected' : '' }}>Electiva</option>
                            </select>
                            @error('tipo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="mt-8 flex gap-4">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                        💾 Guardar Materia
                    </button>
                    <a href="{{ route('admin.materias.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        ↩️ Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection