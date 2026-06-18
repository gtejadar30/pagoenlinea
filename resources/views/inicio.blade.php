@extends('layouts.dashboard')

@section('title', 'Inicio — Pago en Línea')
@section('page-title', 'Inicio')

@section('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #fff;
        border-radius: 14px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 30px rgba(30, 64, 175, .25);
    }

    .welcome-banner h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: .35rem;
    }

    .welcome-banner p {
        opacity: .9;
        font-size: .95rem;
    }

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .quick-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        text-decoration: none;
        color: inherit;
        transition: transform .15s, box-shadow .15s, border-color .15s;
        display: block;
    }

    .quick-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,.08);
        border-color: #93c5fd;
    }

    .quick-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .quick-card h4 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: .35rem;
    }

    .quick-card p {
        font-size: .85rem;
        color: #64748b;
        line-height: 1.5;
    }

    .section-title {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
    <div class="welcome-banner">
        <h3>Hola, {{ $usuario }}</h3>
        <p>Bienvenido al portal de Pago en Línea. Selecciona una opción del menú para comenzar.</p>
    </div>

    <p class="section-title">Accesos rápidos</p>

    <div class="quick-grid">
        <a href="{{ route('estado-cuenta') }}" class="quick-card">
            <div class="quick-icon">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h4>Estado de cuenta</h4>
            <p>Consulta tus deudas pendientes, tributos y saldos por pagar.</p>
        </a>
    </div>
@endsection
