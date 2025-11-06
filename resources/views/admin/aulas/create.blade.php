@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Crear Nueva Aula</h1>
            <p class="text-gray-600">Complete la información de la nueva aula</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.aulas.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre del Aula *
                        </label>
                        <input type="text" name="nombre" id="nombre" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: A-101, Lab-201">
                        @error('nombre')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Piso -->
                    <div>
                        <label for="piso" class="block text-sm font-medium text-gray-700 mb-1">
                            Piso *
                        </label>
                        <select name="piso" id="piso" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione el piso</option>
                            <option value="1" {{ old('piso') == '1' ? 'selected' : '' }}>1er Piso</option>
                            <option value="2" {{ old('piso') == '2' ? 'selected' : '' }}>2do Piso</option>
                            <option value="3" {{ old('piso') == '3' ? 'selected' : '' }}>3er Piso</option>
                            <option value="4" {{ old('piso') == '4' ? 'selected' : '' }}>4to Piso</option>
                        </select>
                        @error('piso')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Aula *
                        </label>
                        <select name="tipo" id="tipo" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione el tipo</option>
                            <option value="teorica" {{ old('tipo') == 'teorica' ? 'selected' : '' }}>Teórica</option>
                            <option value="laboratorio" {{ old('tipo') == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                        </select>
                        @error('tipo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                            Estado *
                        </label>
                        <select name="estado" id="estado" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="ocupada" {{ old('estado') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                            <option value="mantenimiento" {{ old('estado') == 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                        </select>
                        @error('estado')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacidad -->
                    <div>
                        <label for="capacidad" class="block text-sm font-medium text-gray-700 mb-1">
                            Capacidad
                        </label>
                        <input type="number" name="capacidad" id="capacidad" min="1" max="200"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('capacidad') }}"
                               placeholder="Ej: 30">
                        @error('capacidad')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Equipamiento -->
                    <div class="md:col-span-2">
                        <label for="equipamiento" class="block text-sm font-medium text-gray-700 mb-1">
                            Equipamiento
                        </label>
                        <textarea name="equipamiento" id="equipamiento" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Describa el equipamiento disponible...">{{ old('equipamiento') }}</textarea>
                        @error('equipamiento')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Observaciones -->
                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                            Observaciones
                        </label>
                        <textarea name="observaciones" id="observaciones" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="mt-8 flex gap-4">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                        💾 Guardar Aula
                    </button>
                    <a href="{{ route('admin.aulas.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        ↩️ Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection