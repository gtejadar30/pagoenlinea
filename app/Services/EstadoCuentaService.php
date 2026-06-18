<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class EstadoCuentaService
{
    /**
     * $codper = PERS_P_inCODPER guardado en sesión al hacer login.
     * Va directo al EXEC s_i_M_CTAC_CTACTE_3G_DEUDAS_PEND como percod.
     */
    public function obtener(string $codper): array
    {
        $percod = preg_replace('/[^0-9]/', '', $codper);

        try {
            // 1. Obtener datos del contribuyente para mostrar en cabecera
            $contribuyente = $this->obtenerContribuyente($percod);

            if (!$contribuyente) {
                return $this->respuestaError('No se encontró el contribuyente con código de persona ' . $percod);
            }

            // 2. Consultar estado de cuenta — percod va directo al EXEC
            $lineas = $this->consultarEstadoCuenta($percod);

            // 3. La dirección viene de las líneas del estado de cuenta
            $contribuyente->PERS_chDESDIR = !empty($lineas) ? ($lineas[0]->PERDIR ?? null) : null;

            return [
                'error'         => null,
                'codCtaContri'  => $contribuyente->CTAC_P_inCODCTA,
                'contribuyente' => $contribuyente,
                'lineas'        => $lineas,
                'totales'       => $this->calcularTotales($lineas),
            ];
        } catch (Throwable $e) {
            return $this->respuestaError($e->getMessage());
        }
    }

    /**
     * Trae datos del contribuyente desde M_CTAC + M_PERS usando PERS_P_inCODPER.
     * PERS_chDESDIR se inicializa en null y se sobreescribe con PERDIR de las líneas.
     */
    private function obtenerContribuyente(string $percod): ?object
    {
        $rows = DB::select(
            'SELECT TOP 1
                c.CTAC_P_inCODCTA,
                c.PERS_P_inCODPER,
                p.PERS_chNOMCOM,
                NULL AS PERS_chDESDIR
             FROM SATMUNXP.dbo.M_CTAC c
             INNER JOIN SATMUNXP.dbo.M_PERS p ON p.PERS_P_inCODPER = c.PERS_P_inCODPER
             WHERE c.PERS_P_inCODPER = ?',
            [$percod]
        );

        return $rows[0] ?? null;
    }

    private function consultarEstadoCuenta(string $percod): array
    {
        $sql = "
            IF OBJECT_ID('tempdb..#CTACTE_3G_DEUDAS')  IS NOT NULL DROP TABLE #CTACTE_3G_DEUDAS;
            IF OBJECT_ID('tempdb..#CTACTE_3G_PAGOS')   IS NOT NULL DROP TABLE #CTACTE_3G_PAGOS;
            IF OBJECT_ID('tempdb..#CTACTE_3G_CUENTAS') IS NOT NULL DROP TABLE #CTACTE_3G_CUENTAS;

            SELECT * INTO #CTACTE_3G_DEUDAS  FROM SATMUNXP.dbo.ESTRUCTURA_CTACTE_3G_DEUDAS();
            SELECT * INTO #CTACTE_3G_PAGOS   FROM SATMUNXP.dbo.ESTRUCTURA_CTACTE_3G_PAGOS();
            SELECT * INTO #CTACTE_3G_CUENTAS FROM SATMUNXP.dbo.ESTRUCTURA_CTACTE_3G_CUENTAS();

            EXEC SATMUNXP.dbo.s_i_M_CTAC_CTACTE_3G_DEUDAS_PEND '{$percod}';
            EXEC SATMUNXP.dbo.s_i_V_CTAD_CTACTE_3G_PAGOS;
            DECLARE @FECHA CHAR(8) = CONVERT(CHAR(8), GETDATE(), 112);
            EXEC SATMUNXP.dbo.s_i_M_CTAC_CTACTE_3G_CUENTAS @FECHA;

            SELECT
                CTAC_P_inCODCTA  AS CTACOD,
                PERS_P_inCODPER  AS PERCOD,
                PERS_chNOMCOM    AS PERNOM,
                PERS_chDESDIR    AS PERDIR,
                CCOR_P_inCODORI  AS ORGCOD,
                CCOR_chDESAUX    AS ORGDES,
                CCOR_chDETORI    AS ORGDET,
                CTAC_chESTCTA    AS CTAEST,
                ANIO_P_chCODANO  AS CTAANO,
                CTAC_chPERCTA    AS CTAPER,
                TRIB_P_inCODTRI  AS TRICOD,
                TRIB_chDESTRI    AS TRIDES,
                TRIB_chABRTRI    AS TRIABR,
                TRIB_chAGRTRI    AS TRIAGR,
                TRIB_chAGRORD    AS TRIORD,
                CTAC_chDESCTA    AS CTADES,
                CTAC_chFECVEN    AS CTAVEN,
                ROUND(ISNULL(CTAC_MONINS, 0), 2) AS INSOLUTO,
                ROUND(ISNULL(CTAC_MONEMI, 0), 2) AS EMISION,
                ROUND(ISNULL(CTAC_MONREA, 0), 2) AS REAJUSTE,
                ROUND(ISNULL(CTAC_MONIAC, 0), 2) AS INTEACUM,
                ROUND(ISNULL(CTAC_MONINT, 0), 2) AS INTERES,
                ROUND(ISNULL(CTAC_MONOTR, 0), 2) AS OTROS,
                ROUND(ISNULL(CTAC_MONDSC, 0), 2) AS DESCTO,
                ROUND(
                    ISNULL(CTAC_MONINS,0) + ISNULL(CTAC_MONEMI,0) + ISNULL(CTAC_MONREA,0)
                  + ISNULL(CTAC_MONIAC,0) + ISNULL(CTAC_MONINT,0) + ISNULL(CTAC_MONOTR,0)
                  - ISNULL(CTAC_MONDSC,0),
                2) AS SALDO,
                ISNULL(CTAC_MONTOT, 0) AS PAGO,
                CTAC_chDOCREF    AS CTAREF
            FROM #CTACTE_3G_CUENTAS
            WHERE CTAC_chESTCTA = 'A'
              AND TRIB_P_inCODTRI <> 8
            ORDER BY TRIB_chAGRORD, CCOR_P_inCODORI, ANIO_P_chCODANO, CTAC_chPERCTA, TRIB_P_inCODTRI DESC;
        ";

        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->query($sql);

        do {
            if ($stmt->columnCount() > 0) {
                $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
                if (!empty($rows) && isset($rows[0]->CTACOD)) {
                    return $rows;
                }
            }
        } while ($stmt->nextRowset());

        return [];
    }

    private function calcularTotales(array $lineas): array
    {
        $campos  = ['INSOLUTO', 'EMISION', 'REAJUSTE', 'INTEACUM', 'INTERES', 'OTROS', 'DESCTO', 'SALDO', 'PAGO'];
        $totales = array_fill_keys(array_map('strtolower', $campos), 0.0);

        foreach ($lineas as $linea) {
            foreach ($campos as $campo) {
                $totales[strtolower($campo)] += (float) ($linea->{$campo} ?? 0);
            }
        }

        return $totales;
    }

    private function respuestaError(string $mensaje): array
    {
        return [
            'error'         => $mensaje,
            'codCtaContri'  => null,
            'contribuyente' => null,
            'lineas'        => [],
            'totales'       => [],
        ];
    }
}