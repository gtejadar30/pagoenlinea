<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->get('app_logged_in')) {
            return redirect()->route('inicio');
        }

        return view('login');
    }

    public function login(Request $request, AuthService $authService): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string',
            'password' => 'required|string',
        ], [
            'code.required' => 'El código de contribuyente es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $result = $authService->attemptDetailed(
            $request->input('code'),
            $request->input('password')
        );

        if ($result['reason'] === 'not_found') {
            return back()
                ->withInput($request->only('code'))
                ->withErrors(['code' => 'El código de contribuyente no existe en nuestros registros.']);
        }

        if ($result['reason'] === 'no_password') {
            return back()
                ->withInput($request->only('code'))
                ->withErrors(['code' => 'Este usuario no tiene contraseña configurada.']);
        }

        if ($result['reason'] === 'invalid_password') {
            return back()
                ->withInput($request->only('code'))
                ->withErrors(['password' => 'La contraseña es incorrecta.']);
        }

        $request->session()->put('app_logged_in', true);
        $request->session()->put('auth_user', $result['user']);
        $request->session()->regenerate();

        return redirect()->route('inicio');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['app_logged_in', 'auth_user', 'app_user']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
