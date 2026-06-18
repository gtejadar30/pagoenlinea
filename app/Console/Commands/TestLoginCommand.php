<?php

namespace App\Console\Commands;

use App\Services\AuthService;
use Illuminate\Console\Command;

class TestLoginCommand extends Command
{
    protected $signature = 'auth:test {code} {password}';

    protected $description = 'Probar login contra pagosonline.users_original (solo lectura)';

    public function handle(AuthService $authService): int
    {
        $code = $authService->formatCode($this->argument('code'));
        $result = $authService->attemptDetailed($this->argument('code'), $this->argument('password'));

        $this->line('Código formateado: ' . $code);

        if ($result['user']) {
            $this->info('LOGIN OK — ' . $result['user']['name']);
            return 0;
        }

        switch ($result['reason']) {
            case 'not_found':
                $this->error('Usuario no encontrado.');
                break;
            case 'no_password':
                $this->error('Usuario sin contraseña en BD.');
                break;
            case 'invalid_password':
                $this->error('Contraseña incorrecta (Hash bcrypt no coincide).');
                break;
            default:
                $this->error('Error desconocido.');
                break;
        }

        return 1;
    }
}
