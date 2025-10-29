@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Crear Nuevo Usuario</h1>

        <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombres -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Emails -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <!-- Rol -->
                <div>
                    <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                    <select name="rol" id="rol"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="toggleDocenteFields()">
                        <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="docente" {{ old('rol') == 'docente' ? 'selected' : '' }}>Docente</option>
                    </select>
                    @error('rol')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campos específicos para docente -->
                <div id="docente-fields" style="display: none;" class="md:col-span-2 space-y-4">
                    <div>
                        <label for="codigo_docente" class="block text-sm font-medium text-gray-700">Código Docente</label>
                        <input type="text" name="codigo_docente" id="codigo_docente" value="{{ old('codigo_docente') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('codigo_docente')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="profesion" class="block text-sm font-medium text-gray-700">Profesión</label>
                        <input type="text" name="profesion" id="profesion" value="{{ old('profesion') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('profesion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleDocenteFields() {
        const rol = document.getElementById('rol').value;
        const docenteFields = document.getElementById('docente-fields');

        if (rol === 'docente') {
            docenteFields.style.display = 'block';
            // Hacer campos requeridos
            document.getElementById('codigo_docente').required = true;
            document.getElementById('profesion').required = true;
        } else {
            docenteFields.style.display = 'none';
            // Quitar requerido
            document.getElementById('codigo_docente').required = false;
            document.getElementById('profesion').required = false;
        }
    }

    // Ejecutar al cargar la página
    document.addEventListener('DOMContentLoaded', toggleDocenteFields);
</script>
@endsection
