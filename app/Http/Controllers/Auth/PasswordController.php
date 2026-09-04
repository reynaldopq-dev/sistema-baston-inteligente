<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    // Pantalla de cambio de contraseña (obligatoria si debe_cambiar_password,
    // pero también accesible libremente para cualquier usuario autenticado
    // que quiera cambiar su contraseña por su cuenta).
    public function edit()
    {
        return view('auth.cambiar-password', [
            'obligatorio' => (bool) Auth::user()->debe_cambiar_password,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_actual' => 'required|string',
            'password'        => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_actual, $user->password)) {
            return back()->withErrors([
                'password_actual' => 'La contraseña actual no es correcta.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->debe_cambiar_password = false;
        $user->save();

        return redirect('/dashboard')->with('success', 'Contraseña actualizada correctamente.');
    }
}
