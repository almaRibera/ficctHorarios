@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Gestión de Aulas</h1>
                <p class="text-gray-600">Administre las aulas de la facultad</p>
            </div>
            <a href="{{ route('admin.aulas.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                <span class="mr-2">➕</span> Nueva Aula
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    🏫
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Aulas</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $aulas->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    ✅
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Disponibles</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $aulas->where('estado', 'disponible')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    🔴
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Ocupadas</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $aulas->where('estado', 'ocupada')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    🔧
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Mantenimiento</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $aulas->where('estado', 'mantenimiento')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Filtrar Aulas</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select id="filtro-piso" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los pisos</option>
                <option value="1">1er Piso</option>
                <option value="2">2do Piso</option>
                <option value="3">3er Piso</option>
                <option value="4">4to Piso</option>
            </select>

            <select id="filtro-tipo" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los tipos</option>
                <option value="teorica">Teórica</option>
                <option value="laboratorio">Laboratorio</option>
            </select>

            <select id="filtro-estado" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                <option value="disponible">Disponible</option>
                <option value="ocupada">Ocupada</option>
                <option value="mantenimiento">Mantenimiento</option>
            </select>

            <button onclick="aplicarFiltros()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                🔍 Aplicar Filtros
            </button>
        </div>
    </div>

    <!-- Tabla de Aulas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Piso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tabla-aulas">
                    @forelse($aulas as $aula)
                    <tr class="hover:bg-gray-50" data-piso="{{ $aula->piso }}" data-tipo="{{ $aula->tipo }}" data-estado="{{ $aula->estado }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $aula->nombre }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Piso {{ $aula->piso }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $aula->tipo == 'teorica' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $aula->tipo_completo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $aula->capacidad ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $aula->estado == 'disponible' ? 'bg-green-100 text-green-800' : 
                                   ($aula->estado == 'ocupada' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $aula->estado_texto }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.aulas.show', $aula) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    👁️
                                </a>
                                <a href="{{ route('admin.aulas.edit', $aula) }}" 
                                   class="text-green-600 hover:text-green-900" title="Editar">
                                    ✏️
                                </a>
                                <form action="{{ route('admin.aulas.destroy', $aula) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar esta aula?')">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No hay aulas registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function aplicarFiltros() {
    const piso = document.getElementById('filtro-piso').value;
    const tipo = document.getElementById('filtro-tipo').value;
    const estado = document.getElementById('filtro-estado').value;
    
    const filas = document.querySelectorAll('#tabla-aulas tr');
    
    filas.forEach(fila => {
        let mostrar = true;
        
        if (piso && fila.dataset.piso !== piso) {
            mostrar = false;
        }
        
        if (tipo && fila.dataset.tipo !== tipo) {
            mostrar = false;
        }
        
        if (estado && fila.dataset.estado !== estado) {
            mostrar = false;
        }
        
        fila.style.display = mostrar ? '' : 'none';
    });
}
</script>
@endsection