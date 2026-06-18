<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class InicioController extends Controller
{
    public function index(): View
    {
        return view('inicio', [
            'usuario' => session('auth_user.name', 'Usuario'),
        ]);
    }
}
