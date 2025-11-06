@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Grupo: {{ $grupo->sigla_grupo }}</h1>
                <p class="text-gray-600">Código: {{ $grupo->codigo_grupo }}</p>
            </div>
            <a href="{{ route('admin.grupos.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Asignar Materia -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Asignar Materia al Grupo</h3>
        
        <form action="{{ route('admin.grupos.asignar-materia', $grupo) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Materia -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Materia *</label>
                    <select name="materia_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione materia</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}">{{ $materia->sigla }} - {{ $materia->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Docente -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Docente *</label>
                    <select name="docente_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione docente</option>
                        @foreach($docentes as $docente)
                            <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Horas Semanales -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horas Semanales *</label>
                    <input type="number" name="horas_semanales" required min="1" max="20"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ej: 4">
                </div>

                <!-- Botón -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
                        ➕ Asignar Materia
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Materias Asignadas -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Materias Asignadas</h3>
        
        @if($grupo->materiasAsignadas->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horas Semanales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horas Asignadas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($grupo->materiasAsignadas as $materiaAsignada)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $materiaAsignada->materia->sigla }}</div>
                            <div class="text-sm text-gray-500">{{ $materiaAsignada->materia->nombre }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $materiaAsignada->docente->name }}</div>
                            <div class="text-sm text-gray-500">{{ $materiaAsignada->docente->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $materiaAsignada->horas_semanales }} horas
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $materiaAsignada->horasAsignadas() }} horas
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $materiaAsignada->horasAsignadas() >= $materiaAsignada->horas_semanales ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $materiaAsignada->horasAsignadas() >= $materiaAsignada->horas_semanales ? 'Completo' : 'Pendiente' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <form action="{{ route('admin.grupos.eliminar-materia', $materiaAsignada) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('¿Está seguro de eliminar esta materia del grupo?')">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <div class="text-gray-400 text-4xl mb-4">📚</div>
            <p class="text-gray-500">No hay materias asignadas a este grupo.</p>
        </div>
        @endif
    </div>
</div>
@endsection