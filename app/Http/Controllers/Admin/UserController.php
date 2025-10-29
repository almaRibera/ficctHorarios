<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Docente;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('docente')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => 'required|in:admin,docente',
            'codigo_docente' => 'required_if:rol,docente|nullable|string|unique:docentes',
            'profesion' => 'required_if:rol,docente|nullable|string|max:255',
        ]);

        // Crear Usuarios
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);

        // Si es Docente, crear registro en tabla Docentes
        if ($request->rol == 'docente') {
            Docente::create([
                'user_id' => $user->id,
                'codigo_docente' => $request->codigo_docente,
                'profesion' => $request->profesion,
            ]);
        }

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Creó usuario: ' . $user->email,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
            'rol' => 'required|in:admin,docente',
            'codigo_docente' => 'required_if:rol,docente|nullable|string|unique:docentes,codigo_docente,' . ($user->docente ? $user->docente->id : 'NULL'),
            'profesion' => 'required_if:rol,docente|nullable|string|max:255',
        ]);

        // Actualizar usuario
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'rol' => $request->rol,
        ]);

        // Actualizar contraseña si se proporcionó
        if ($request->password) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Manejar registro en tabla docentes
        if ($request->rol == 'docente') {
            if ($user->docente) {
                // Actualizar docente existente
                $user->docente->update([
                    'codigo_docente' => $request->codigo_docente,
                    'profesion' => $request->profesion,
                ]);
            } else {
                // Crear nuevo registro docente
                Docente::create([
                    'user_id' => $user->id,
                    'codigo_docente' => $request->codigo_docente,
                    'profesion' => $request->profesion,
                ]);
            }
        } else {
            // Si cambia de docente a admin, eliminar registro docente
            if ($user->docente) {
                $user->docente->delete();
            }
        }

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Actualizó usuario: ' . $user->email,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $email = $user->email;
        $user->delete();

        // Registrar en bitácora
        Bitacora::create([
            'user_id' => auth()->id(),
            'accion_realizada' => 'Eliminó usuario: ' . $email,
            'fecha_y_hora' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
