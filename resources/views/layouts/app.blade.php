<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Horarios FICCT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar Integrado -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="font-bold text-xl">Sistema Horarios FICCT</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                    <span class="text-blue-100">Bienvenido, {{ auth()->user()->name }}</span>
                    <span class="bg-blue-500 px-2 py-1 rounded text-sm capitalize">{{ auth()->user()->rol }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 px-3 py-1 rounded text-sm">
                            Cerrar Sesión
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar y contenidos principales -->
    <div class="flex">
        <!-- Sidebar integrado -->
        @auth
        <aside class="bg-white w-64 min-h-screen shadow-md">
            <div class="p-4">
                <h2 class="text-lg font-semibold text-gray-700">Menú Principal</h2>
                <nav class="mt-4 space-y-2">
                    @if(auth()->user()->rol == 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                        📊 Dashboard Admin
                    </a>
                    <a href="{{ route('admin.materias.index') }}" 
                    class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('admin.materias.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        📚 Gestión de Materias
                    </a>

                    <a href="{{ route('admin.users.index') }}"  class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded">
                        👥 Gestión de Usuarios
                    </a>
                    <a href="{{ route('admin.bitacoras.index') }}" class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded">
                        📋 Bitácora
                    </a>
                    <a href="{{ route('admin.reportes.index') }}" 
                    class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('admin.reportes.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        📈 Generar Reportes
                    </a>
                    <a href="{{ route('admin.importacion.index') }}" 
                             class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('admin.aulas.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        Importar Datos
                    </a>
                    <a href="{{ route('admin.aulas.index') }}" 
                             class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('admin.aulas.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        🏫 Gestión de Aulas
                    </a>
                    <a href="{{ route('admin.grupos.index') }}" 
                        class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('admin.grupos.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            👥 Gestión de Grupos
                    </a>
                    <a href="{{ route('admin.asistencias.index') }}" 
                    class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('admin.asistencias.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        📊 Reporte Asistencias
                    </a>
                  
                    @else
                    <a href="{{ route('docente.dashboard') }}"
                       class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('docente.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                        📅 Mi Dashboard
                    </a>
                  <a href="{{ route('docente.horarios.index') }}" 
                    class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('docente.horarios.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        📅 Mis Horarios
                    </a>
                    <a href="{{ route('docente.asistencia.index') }}" 
                    class="block py-2 px-4 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded {{ request()->routeIs('docente.asistencia.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        ✅ Registrar Asistencia
                    </a>

                   
                    @endif
                </nav>
            </div>
        </aside>
        @endauth

        <!-- Contenido principal -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
