<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pago en Línea')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-w: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #1d4ed8;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --topbar-h: 64px;
            --bg: #f1f5f9;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #1e40af;
            --danger: #dc2626;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 100;
            transition: transform .25s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .sidebar-brand h1 {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .sidebar-brand p {
            font-size: .75rem;
            margin-top: .25rem;
            opacity: .7;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem .75rem;
            overflow-y: auto;
        }

        .nav-label {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            padding: .75rem .75rem .5rem;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1rem;
            border-radius: 10px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            margin-bottom: .25rem;
            transition: background .15s, color .15s;
        }

        .nav-item:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .nav-item.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            opacity: .85;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.06);
        }

        .user-box {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: .75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: .85rem;
        }

        .user-info strong {
            display: block;
            color: #fff;
            font-size: .85rem;
        }

        .user-info span {
            font-size: .75rem;
            opacity: .65;
        }

        .btn-logout {
            width: 100%;
            padding: .55rem;
            border: 1px solid rgba(255,255,255,.12);
            background: transparent;
            color: var(--sidebar-text);
            border-radius: 8px;
            font-size: .8rem;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,.06);
            color: #fff;
        }

        .main {
            flex: 1;
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: var(--topbar-h);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar h2 {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .topbar-meta {
            font-size: .8rem;
            color: var(--muted);
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: .25rem;
        }

        .content {
            flex: 1;
            padding: 1.5rem;
        }

        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 90;
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .menu-toggle { display: block; }
            .overlay.open { display: block; }
        }

        @yield('styles')
    </style>
    @stack('styles')
</head>
<body>
    <div class="overlay" id="overlay"></div>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <h1>Pago en Línea</h1>
                <p>Municipalidad</p>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menú principal</div>

                <a href="{{ route('inicio') }}"
                   class="nav-item {{ request()->routeIs('inicio') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Inicio
                </a>

                <a href="{{ route('estado-cuenta') }}"
                   class="nav-item {{ request()->routeIs('estado-cuenta') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Estado de cuenta
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-box">
                    @php $authUser = session('auth_user', []); @endphp
                    <div class="user-avatar">{{ strtoupper(substr($authUser['name'] ?? 'U', 0, 1)) }}</div>
                    <div class="user-info">
                        <strong>{{ $authUser['name'] ?? 'Usuario' }}</strong>
                        <span>Cód. {{ $authUser['code'] ?? '-' }}</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <button class="menu-toggle" id="menuToggle" aria-label="Menú">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h2>@yield('page-title', 'Panel')</h2>
                </div>
                <span class="topbar-meta">{{ now()->format('d/m/Y') }}</span>
            </header>

            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuToggle = document.getElementById('menuToggle');

        menuToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
    </script>
    @stack('scripts')
</body>
</html>
