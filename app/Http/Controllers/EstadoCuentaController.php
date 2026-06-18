<?php

namespace App\Http\Controllers;

use App\Services\EstadoCuentaService;
use Illuminate\View\View;

class EstadoCuentaController extends Controller
{
    public function index(EstadoCuentaService $estadoCuentaService): View
    {
        $codper = (string) session('auth_user.codper'); // PERS_P_inCODPER guardado al hacer login

        $data = $estadoCuentaService->obtener($codper);

        return view('estado-cuenta', $data);
    }
}