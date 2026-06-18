<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Solo lectura — misma lógica que el pagosonline anterior.
     *
     * @return array{user: ?array, reason: ?string}
     */
    public function attemptDetailed(string $code, string $password): array
    {
        $code = $this->formatCode($code);
        $user = $this->findUserByCode($code);

        if (!$user) {
            return ['user' => null, 'reason' => 'not_found'];
        }

        if (empty($user->password)) {
            return ['user' => null, 'reason' => 'no_password'];
        }

        if (!$this->verifyPassword($password, $user->password)) {
            return ['user' => null, 'reason' => 'invalid_password'];
        }

        return [
            'user'   => $this->mapUser($user),
            'reason' => null,
        ];
    }

    public function attempt(string $code, string $password): ?array
    {
        return $this->attemptDetailed($code, $password)['user'];
    }

    public function formatCode(string $code): string
    {
        $digits = preg_replace('/\D/', '', trim($code));

        return str_pad($digits, 10, '0', STR_PAD_LEFT);
    }

    private function findUserByCode(string $code): ?object
    {
        return DB::connection('satmunxp')
            ->table('pagosonline.users_original')
            ->where('code', $code)
            ->first();
    }

    private function verifyPassword(string $plain, string $hash): bool
    {
        $hash = trim($hash);

        if ($hash === '' || $plain === '') {
            return false;
        }

        if (Hash::check($plain, $hash)) {
            return true;
        }

        if (password_verify($plain, $hash)) {
            return true;
        }

        $alternate = strpos($hash, '$2y$') === 0
            ? '$2a$' . substr($hash, 4)
            : (strpos($hash, '$2a$') === 0 ? '$2y$' . substr($hash, 4) : null);

        if ($alternate) {
            return password_verify($plain, $alternate);
        }

        return false;
    }

    private function mapUser(object $user): array
    {
        $codper = null;

        // 1. Intentar buscar por DNI en V_RELA_DOCU
        if (!empty($user->dni)) {
            $relaDocu = DB::connection('satmunxp')
                ->table('SATMUNXP.dbo.V_RELA_DOCU')
                ->where('REDO_chNUMRED', trim($user->dni))
                ->select('PERS_P_inCODPER')
                ->first();

            $codper = $relaDocu->PERS_P_inCODPER ?? null;
        }

        // 2. Fallback a M_CTAC si no se encuentra en V_RELA_DOCU
        if (!$codper) {
            $ctac = DB::connection('satmunxp')
                ->table('SATMUNXP.dbo.M_CTAC')
                ->where('CTAC_P_inCODCTA', (int) trim($user->code))
                ->select('PERS_P_inCODPER')
                ->first();

            $codper = $ctac->PERS_P_inCODPER ?? null;
        }

        return [
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'dni'     => $user->dni,
            'code'    => trim($user->code),
            'codper'  => $codper, // PERS_P_inCODPER — percod para el EXEC
            'address' => $user->address,
            'phone'   => $user->phone,
            'phone2'  => $user->phone2,
        ];
    }
}