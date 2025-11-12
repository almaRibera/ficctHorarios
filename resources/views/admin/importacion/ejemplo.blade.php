@extends('layouts.app')

@section('title', 'Ejemplo de CSV')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Ejemplo de Formato CSV</h1>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Encabezados Requeridos:</h2>
                <div class="bg-gray-100 p-3 rounded">
                    <code class="text-sm">{{ implode(', ', $ejemplo['headers']) }}</code>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Ejemplos de Datos:</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                @foreach($ejemplo['headers'] as $header)
                                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ejemplo['ejemplos'] as $fila)
                            <tr class="hover:bg-gray-50">
                                @foreach($fila as $dato)
                                <td class="py-2 px-4 border-b text-sm text-gray-600">{{ $dato }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('importacion.index') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    ← Volver a Importación
                </a>
                <a href="{{ route('importacion.descargar-plantilla') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Descargar Plantilla Completa
                </a>
            </div>
        </div>
    </div>
</div>
@endsection