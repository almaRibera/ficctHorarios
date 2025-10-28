<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Registrar en bitácora
            Bitacora::create([
                'user_id' => auth()->id(),
                'accion_realizada' => 'Inicio de sesión',
                'fecha_y_hora' => now(),
            ]);

            // Redirección según rol
            return $this->authenticated($request, auth()->user());
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.',
        ])->onlyInput('email');
    }

    /**
     * Handle response after user authenticated
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        if ($user->rol == 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('docente.dashboard');
    }

    /**
     * Display login view
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Destroy an authenticated session
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Registrar cierre de sesión en bitácora
        if (auth()->check()) {
            Bitacora::create([
                'user_id' => auth()->id(),
                'accion_realizada' => 'Cierre de sesión',
                'fecha_y_hora' => now(),
            ]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}