@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Gestión de Materias</h1>
                <p class="text-gray-600">Administre las materias de la facultad</p>
            </div>
            <a href="{{ route('admin.materias.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                <span class="mr-2">➕</span> Nueva Materia
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    📚
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Materias</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $materias->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    🔷
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Troncales</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $materias->where('tipo', 'truncal')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    🔶
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Electivas</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $materias->where('tipo', 'electiva')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-lg">
                    #️⃣
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Niveles</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $materias->pluck('nivel')->unique()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Filtrar Materias</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select id="filtro-nivel" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los niveles</option>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">Nivel {{ $i }}</option>
                @endfor
            </select>

            <select id="filtro-tipo" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los tipos</option>
                <option value="truncal">Troncal</option>
                <option value="electiva">Electiva</option>
            </select>

            <input type="text" id="filtro-busqueda" placeholder="Buscar por sigla o nombre..." 
                   class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button onclick="aplicarFiltros()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                🔍 Aplicar Filtros
            </button>
        </div>
    </div>

    <!-- Tabla de Materias -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sigla</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tabla-materias">
                    @forelse($materias as $materia)
                    <tr class="hover:bg-gray-50" data-nivel="{{ $materia->nivel }}" data-tipo="{{ $materia->tipo }}" data-sigla="{{ $materia->sigla }}" data-nombre="{{ $materia->nombre }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $materia->sigla }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $materia->nombre }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Nivel {{ $materia->nivel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $materia->tipo == 'truncal' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $materia->tipo_completo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.materias.show', $materia) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    👁️
                                </a>
                                <a href="{{ route('admin.materias.edit', $materia) }}" 
                                   class="text-green-600 hover:text-green-900" title="Editar">
                                    ✏️
                                </a>
                                <form action="{{ route('admin.materias.destroy', $materia) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar esta materia?')">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No hay materias registradas
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
    const nivel = document.getElementById('filtro-nivel').value;
    const tipo = document.getElementById('filtro-tipo').value;
    const busqueda = document.getElementById('filtro-busqueda').value.toLowerCase();
    
    const filas = document.querySelectorAll('#tabla-materias tr');
    
    filas.forEach(fila => {
        let mostrar = true;
        
        if (nivel && fila.dataset.nivel !== nivel) {
            mostrar = false;
        }
        
        if (tipo && fila.dataset.tipo !== tipo) {
            mostrar = false;
        }
        
        if (busqueda && !fila.dataset.sigla.toLowerCase().includes(busqueda) && 
            !fila.dataset.nombre.toLowerCase().includes(busqueda)) {
            mostrar = false;
        }
        
        fila.style.display = mostrar ? '' : 'none';
    });
}
</script>
@endsection