<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Bloqueo temporal tras varios intentos fallidos (ThrottlesLogins,
        // incluido en AuthenticatesUsers) — antes se saltaba por completo
        // al llamar a Auth::attempt() directo.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);

            // La cuenta existe y la contraseña es correcta, pero está
            // desactivada (ficha de personal marcada "Inactivo", o
            // eliminada). Antes esto no se revisaba en ningún lado y la
            // persona podía seguir entrando igual.
            if (!Auth::user()->activo) {
                $correo = Auth::user()->email;

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                Auditoria::create([
                    'user_id'     => null,
                    'accion'      => 'login_fallido',
                    'modelo'      => 'Auth',
                    'modelo_id'   => null,
                    'descripcion' => "Intento de inicio de sesión con la cuenta desactivada {$correo}",
                    'ip'          => $request->ip(),
                ]);

                return back()->withErrors([
                    'email' => 'Tu cuenta está desactivada. Contacta a un Administrador.',
                ]);
            }

            Auditoria::create([
                'user_id'     => Auth::id(),
                'accion'      => 'login',
                'modelo'      => 'Auth',
                'modelo_id'   => Auth::id(),
                'descripcion' => 'Inicio de sesión de ' . Auth::user()->name,
                'ip'          => $request->ip(),
            ]);

            return redirect()->intended('/dashboard');
        }

        $this->incrementLoginAttempts($request);

        Auditoria::create([
            'user_id'     => null,
            'accion'      => 'login_fallido',
            'modelo'      => 'Auth',
            'modelo_id'   => null,
            'descripcion' => "Intento de inicio de sesión fallido con el correo {$request->email}",
            'ip'          => $request->ip(),
        ]);

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ]);
    }

    // Sobrescribe el mensaje de bloqueo por intentos fallidos (el de
    // ThrottlesLogins usa lang/en/auth.php — no existe lang/es, así que
    // saldría en inglés, inconsistente con el resto del formulario).
    protected function sendLockoutResponse(Request $request)
    {
        $segundos = $this->limiter()->availableIn($this->throttleKey($request));
        $minutos = (int) ceil($segundos / 60);

        Auditoria::create([
            'user_id'     => null,
            'accion'      => 'login_fallido',
            'modelo'      => 'Auth',
            'modelo_id'   => null,
            'descripcion' => "Bloqueado por demasiados intentos fallidos con el correo {$request->email}",
            'ip'          => $request->ip(),
        ]);

        return back()->withErrors([
            'email' => "Demasiados intentos fallidos. Vuelve a intentarlo en {$minutos} minuto(s).",
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auditoria::create([
                'user_id'     => Auth::id(),
                'accion'      => 'logout',
                'modelo'      => 'Auth',
                'modelo_id'   => Auth::id(),
                'descripcion' => 'Cierre de sesión de ' . Auth::user()->name,
                'ip'          => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
