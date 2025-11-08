<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Middleware\CheckRole;  
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\MateriaController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\AsistenciaController;
use App\Http\Controllers\Admin\ReporteController;

use App\Http\Controllers\Docente\HorarioController;
use App\Http\Controllers\Docente\AsistenciaController as DocenteAsistenciaController;


// Rutas de autenticaciónes
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Rutas principales
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->rol == 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('docente.dashboard');
    }
    return redirect()->route('login');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard')->middleware('role:admin');

    // Rutas para docente - horarios-----------------------
    Route::middleware(['auth', 'role:docente'])->prefix('docente')->name('docente.')->group(function () {
        Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
        Route::get('/horarios/{grupoMateria}/create', [HorarioController::class, 'create'])->name('horarios.create');
        Route::post('/horarios/{grupoMateria}', [HorarioController::class, 'store'])->name('horarios.store');
        Route::delete('/horarios/{horario}', [HorarioController::class, 'destroy'])->name('horarios.destroy');
        //assistencias docente
        Route::get('/asistencia', [DocenteAsistenciaController::class, 'index'])->name('asistencia.index');
        Route::post('/asistencia/{horario}', [DocenteAsistenciaController::class, 'store'])->name('asistencia.store');
 
 
 
 
    });

    // Dashboard docente
    Route::get('/docente/dashboard', function () {
        return view('docente.dashboard');
    })->name('docente.dashboard')->middleware('role:docente');

 // Rutas de gestión de usuarios (solo admin)-----------------------------------------
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
           // Reportes
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::post('/reportes/generar', [ReporteController::class, 'generar'])->name('reportes.generar');
        Route::post('/reportes/imprimir', [ReporteController::class, 'imprimir'])->name('reportes.imprimir');

        // Rutas de grupos
        Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos.index');
        Route::get('/grupos/create', [GrupoController::class, 'create'])->name('grupos.create');
        Route::post('/grupos', [GrupoController::class, 'store'])->name('grupos.store');
        Route::get('/grupos/{grupo}', [GrupoController::class, 'show'])->name('grupos.show');
        Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.destroy');
        Route::post('/grupos/{grupo}/asignar-materia', [GrupoController::class, 'asignarMateria'])->name('grupos.asignar-materia');
        Route::delete('/grupo-materia/{grupoMateria}', [GrupoController::class, 'eliminarMateria'])->name('grupos.eliminar-materia');
        //asistencias
          Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
          Route::get('/asistencias/por-docente', [AsistenciaController::class, 'porDocente'])->name('asistencias.por-docente');
          Route::get('/asistencias/{asistencia}', [AsistenciaController::class, 'show'])->name('asistencias.show');
      // Rutas de materias
        Route::get('/materias', [MateriaController::class, 'index'])->name('materias.index');
        Route::get('/materias/create', [MateriaController::class, 'create'])->name('materias.create');
        Route::post('/materias', [MateriaController::class, 'store'])->name('materias.store');
        Route::get('/materias/{materia}', [MateriaController::class, 'show'])->name('materias.show');
        Route::get('/materias/{materia}/edit', [MateriaController::class, 'edit'])->name('materias.edit');
        Route::put('/materias/{materia}', [MateriaController::class, 'update'])->name('materias.update');
        Route::delete('/materias/{materia}', [MateriaController::class, 'destroy'])->name('materias.destroy');
        Route::post('/materias/{materia}/cambiar-estado', [MateriaController::class, 'cambiarEstado'])->name('materias.cambiar-estado');
            // Rutas de aulas
        Route::get('/aulas', [AulaController::class, 'index'])->name('aulas.index');
        Route::get('/aulas/create', [AulaController::class, 'create'])->name('aulas.create');
        Route::post('/aulas', [AulaController::class, 'store'])->name('aulas.store');
        Route::get('/aulas/{aula}', [AulaController::class, 'show'])->name('aulas.show');
        Route::get('/aulas/{aula}/edit', [AulaController::class, 'edit'])->name('aulas.edit');
        Route::put('/aulas/{aula}', [AulaController::class, 'update'])->name('aulas.update');
        Route::delete('/aulas/{aula}', [AulaController::class, 'destroy'])->name('aulas.destroy');
        Route::post('/aulas/{aula}/cambiar-estado', [AulaController::class, 'cambiarEstado'])->name('aulas.cambiar-estado');
            // Rutas de bitácora
        Route::get('/bitacoras', [BitacoraController::class, 'index'])->name('bitacoras.index');
        Route::get('/bitacoras/filtrar', [BitacoraController::class, 'filtrar'])->name('bitacoras.filtrar');
        Route::get('/bitacoras/{bitacora}', [BitacoraController::class, 'show'])->name('bitacoras.show');
    });
});
