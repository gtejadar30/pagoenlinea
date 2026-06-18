@extends('layouts.dashboard')

@section('title', 'Estado de Cuenta — Pago en Línea')
@section('page-title', 'Estado de cuenta')

@push('styles')
<style>
    .ec-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%);
        color: #fff;
        border-radius: 12px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(30, 58, 95, 0.25);
    }

    .ec-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .ec-header h1 {
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: .25rem;
    }

    .ec-header p {
        opacity: .85;
        font-size: .9rem;
    }

    .badge {
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        padding: .35rem .75rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 500;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .card {
        background: #fff;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        border: 1px solid #e2e8f0;
    }

    .card-label {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        font-weight: 600;
        margin-bottom: .35rem;
    }

    .card-value {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1e3a5f;
    }

    .card-value.saldo {
        color: #dc2626;
        font-size: 1.6rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-box {
        background: #fff;
        border-radius: 10px;
        padding: 1.25rem 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .info-box h2 {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #64748b;
        margin-bottom: .75rem;
        font-weight: 600;
    }

    .info-row {
        display: flex;
        gap: .5rem;
        margin-bottom: .4rem;
        font-size: .9rem;
    }

    .info-row strong {
        min-width: 110px;
        color: #64748b;
        font-weight: 500;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: .9rem;
    }

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .alert-info {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .table-wrap {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }

    .table-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .table-header h2 {
        font-size: 1rem;
        font-weight: 600;
    }

    .table-scroll {
        overflow-x: auto;
    }

    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: .8rem;
    }

    .table-wrap thead {
        background: #f8fafc;
    }

    .table-wrap th {
        padding: .75rem .65rem;
        text-align: left;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        font-size: .68rem;
        letter-spacing: .03em;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    .table-wrap td {
        padding: .65rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: top;
    }

    .table-wrap tbody tr:hover {
        background: #f8fafc;
    }

    .text-right { text-align: right; }
    .mono { font-variant-numeric: tabular-nums; }
    .nowrap { white-space: nowrap; }

    .table-wrap tfoot td {
        background: #f1f5f9;
        font-weight: 700;
        border-top: 2px solid #e2e8f0;
    }

    .empty {
        padding: 3rem;
        text-align: center;
        color: #64748b;
    }
</style>
@endpush

@section('content')
    @php
        $formatFecha = function ($fecha) {
            if (!$fecha || strlen($fecha) !== 8) {
                return $fecha ?: '-';
            }
            return substr($fecha, 6, 2) . '/' . substr($fecha, 4, 2) . '/' . substr($fecha, 0, 4);
        };

        $formatMonto = function ($valor) {
            return number_format((float) $valor, 2, '.', ',');
        };
    @endphp

    <div class="ec-header">
        <div class="ec-header-top">
            <div>
                <h1>Estado de Cuenta</h1>
                <p>Consulta de deudas pendientes del contribuyente</p>
            </div>
            <span class="badge">Modo prueba — Cta. {{ $codCtaContri }}</span>
        </div>
    </div>

    @if ($error)
        <div class="alert alert-error">
            <strong>Error:</strong> {{ $error }}
        </div>
    @else
        <div class="alert alert-info">
            Código de cuenta fijado en el sistema: <strong>{{ $codCtaContri }}</strong>
            — Persona: <strong>{{ $contribuyente->PERS_P_inCODPER ?? '-' }}</strong>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h2>Datos del contribuyente</h2>
                <div class="info-row"><strong>Cód. persona:</strong> <span>{{ $contribuyente->PERS_P_inCODPER }}</span></div>
                <div class="info-row"><strong>Cód. cuenta:</strong> <span>{{ $contribuyente->CTAC_P_inCODCTA }}</span></div>
                <div class="info-row"><strong>Nombre:</strong> <span>{{ $contribuyente->PERS_chNOMCOM }}</span></div>
                <div class="info-row"><strong>Dirección:</strong> <span>{{ $contribuyente->PERS_chDESDIR ?: '-' }}</span></div>
            </div>

            <div class="info-box">
                <h2>Resumen</h2>
                <div class="info-row"><strong>Registros:</strong> <span>{{ count($lineas) }}</span></div>
                <div class="info-row"><strong>Fecha consulta:</strong> <span>{{ now()->format('d/m/Y H:i') }}</span></div>
                <div class="info-row"><strong>Estado:</strong> <span>Cuentas activas (CTAEST = A)</span></div>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="card-label">Total insoluto</div>
                <div class="card-value mono">S/ {{ $formatMonto($totales['insoluto'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-label">Total intereses</div>
                <div class="card-value mono">S/ {{ $formatMonto(($totales['interes'] ?? 0) + ($totales['inteacum'] ?? 0)) }}</div>
            </div>
            <div class="card">
                <div class="card-label">Total pagos</div>
                <div class="card-value mono">S/ {{ $formatMonto($totales['pago'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-label">Saldo total</div>
                <div class="card-value saldo mono">S/ {{ $formatMonto($totales['saldo'] ?? 0) }}</div>
            </div>
        </div>

        <div class="table-wrap">
            <div class="table-header">
                <h2>Detalle de deudas</h2>
                <span style="color: #64748b; font-size: .85rem;">{{ count($lineas) }} línea(s)</span>
            </div>

            @if (count($lineas))
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Cta.</th>
                                <th>Tributo</th>
                                <th>Periodo</th>
                                <th>Descripción</th>
                                <th>Vencimiento</th>
                                <th class="text-right">Insoluto</th>
                                <th class="text-right">Emisión</th>
                                <th class="text-right">Reajuste</th>
                                <th class="text-right">Int. acum.</th>
                                <th class="text-right">Interés</th>
                                <th class="text-right">Otros</th>
                                <th class="text-right">Dscto.</th>
                                <th class="text-right">Saldo</th>
                                <th class="text-right">Pago</th>
                                <th>Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lineas as $linea)
                                <tr>
                                    <td class="mono nowrap">{{ $linea->CTACOD }}</td>
                                    <td>
                                        <strong>{{ $linea->TRIDES }}</strong>
                                        <br><span style="color: #64748b;">{{ $linea->TRIABR }}</span>
                                    </td>
                                    <td class="nowrap">{{ $linea->CTAANO }}-{{ $linea->CTAPER }}</td>
                                    <td>{{ $linea->CTADES }}</td>
                                    <td class="nowrap">{{ $formatFecha($linea->CTAVEN) }}</td>
                                    <td class="text-right mono">{{ $formatMonto($linea->INSOLUTO) }}</td>
                                    <td class="text-right mono">{{ $formatMonto($linea->EMISION) }}</td>
                                    <td class="text-right mono">{{ $formatMonto($linea->REAJUSTE) }}</td>
                                    <td class="text-right mono">{{ $formatMonto($linea->INTEACUM) }}</td>
                                    <td class="text-right mono">{{ $formatMonto($linea->INTERES) }}</td>
                                    <td class="text-right mono">{{ $formatMonto($linea->OTROS) }}</td>
                                    <td class="text-right mono">{{ $formatMonto($linea->DESCTO) }}</td>
                                    <td class="text-right mono" style="font-weight: 600; color: #dc2626;">{{ $formatMonto($linea->SALDO) }}</td>
                                    <td class="text-right mono">{{ $formatMonto($linea->PAGO) }}</td>
                                    <td style="max-width: 180px; word-break: break-word;">{{ $linea->CTAREF ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"><strong>TOTALES</strong></td>
                                <td class="text-right mono">{{ $formatMonto($totales['insoluto'] ?? 0) }}</td>
                                <td class="text-right mono">{{ $formatMonto($totales['emision'] ?? 0) }}</td>
                                <td class="text-right mono">{{ $formatMonto($totales['reajuste'] ?? 0) }}</td>
                                <td class="text-right mono">{{ $formatMonto($totales['inteacum'] ?? 0) }}</td>
                                <td class="text-right mono">{{ $formatMonto($totales['interes'] ?? 0) }}</td>
                                <td class="text-right mono">{{ $formatMonto($totales['otros'] ?? 0) }}</td>
                                <td class="text-right mono">{{ $formatMonto($totales['descto'] ?? 0) }}</td>
                                <td class="text-right mono" style="color: #dc2626;">{{ $formatMonto($totales['saldo'] ?? 0) }}</td>
                                <td class="text-right mono">{{ $formatMonto($totales['pago'] ?? 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="empty">No se encontraron deudas pendientes para este contribuyente.</div>
            @endif
        </div>
    @endif
@endsection
