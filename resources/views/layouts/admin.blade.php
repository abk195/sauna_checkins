<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --sidebar: #0c1222;
            --border: #334155;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --accent: #22d3ee;
            --accent-dim: #0891b2;
            --danger: #f87171;
            --success: #34d399;
            --nav-active: rgba(34, 211, 238, 0.15);
            --nav-active-border: #22d3ee;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        a { color: var(--accent); }
        .admin-shell {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 240px;
            flex-shrink: 0;
            background: linear-gradient(180deg, var(--sidebar) 0%, #0f172a 100%);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 1.25rem 0;
        }
        .admin-sidebar__brand {
            padding: 0 1rem 1.25rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }
        .admin-sidebar__brand a {
            color: var(--text);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.02em;
        }
        .admin-sidebar__brand span {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 0.35rem;
        }
        .admin-nav {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            padding: 0 0.75rem;
            flex: 1;
        }
        .admin-nav a {
            display: block;
            padding: 0.65rem 0.85rem;
            border-radius: 0.5rem;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            border-left: 3px solid transparent;
        }
        .admin-nav a:hover {
            color: var(--text);
            background: rgba(148, 163, 184, 0.08);
        }
        .admin-nav a.is-active {
            color: var(--accent);
            background: var(--nav-active);
            border-left-color: var(--nav-active-border);
        }
        .admin-sidebar__foot {
            padding: 1rem 1rem 0;
            border-top: 1px solid var(--border);
            margin-top: auto;
        }
        .admin-sidebar__foot form { margin: 0; }
        .admin-main {
            flex: 1;
            min-width: 0;
            background: linear-gradient(165deg, var(--bg) 0%, #172554 100%);
            padding: clamp(1rem, 2.5vw, 1.75rem) clamp(1rem, 3vw, 2rem) 3rem;
        }
        .admin-main__head {
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .admin-main__head h1 {
            margin: 0;
            font-size: clamp(1.25rem, 2.5vw, 1.5rem);
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .flash {
            padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;
            background: rgba(52, 211, 153, 0.15); border: 1px solid rgba(52, 211, 153, 0.35);
            color: #a7f3d0; font-size: 0.95rem;
        }
        .errors {
            padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;
            background: rgba(248, 113, 113, 0.12); border: 1px solid rgba(248, 113, 113, 0.4);
            color: #fecaca; font-size: 0.95rem;
        }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.95rem; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer;
            background: var(--accent-dim); color: #fff;
        }
        .btn:hover { filter: brightness(1.08); }
        .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--text); width: 100%; }
        .btn-danger { background: #b91c1c; }
        .card {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        .stat-card {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.15rem 1.25rem;
        }
        .stat-card--wide {
            grid-column: 1 / -1;
        }
        @media (min-width: 640px) {
            .stat-card--wide {
                grid-column: span 2;
            }
        }
        .stat-card__label {
            margin: 0 0 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
        }
        .stat-card__value {
            margin: 0;
            font-size: 1.85rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
            color: var(--text);
        }
        .stat-card__value--success { color: #6ee7b7; }
        .stat-card__value--accent { color: var(--accent); }
        .stat-card__value--muted { color: var(--muted); }
        .stat-card__hint {
            margin: 0.5rem 0 0;
            font-size: 0.8rem;
            color: var(--muted);
        }
        label { display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 0.35rem; }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"], select {
            width: 100%; max-width: 28rem; padding: 0.6rem 0.75rem;
            border-radius: 0.5rem; border: 1px solid var(--border);
            background: var(--bg); color: var(--text); font-size: 1rem;
        }
        select { max-width: none; cursor: pointer; }
        input[type="date"] { max-width: none; }
        .filter-bar__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem 1.25rem;
            margin-bottom: 1rem;
        }
        .filter-bar .field label { margin-bottom: 0.35rem; }
        .filter-bar__actions {
            display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;
        }
        .pagination-nav {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 1rem;
        }
        .pagination-nav__info {
            font-size: 0.9rem; color: var(--muted);
        }
        .pagination-nav__info strong { color: var(--text); font-weight: 600; }
        .pagination-nav__list {
            display: flex; flex-wrap: wrap; align-items: center; gap: 0.35rem;
            list-style: none; margin: 0; padding: 0;
        }
        .pagination-nav__list li { margin: 0; }
        .pagination-nav__list a,
        .pagination-nav__current {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 2.25rem; height: 2.25rem; padding: 0 0.5rem;
            border-radius: 0.45rem; font-size: 0.9rem; font-weight: 600;
            text-decoration: none;
        }
        .pagination-nav__list a {
            color: var(--accent); border: 1px solid var(--border); background: rgba(30, 41, 59, 0.6);
        }
        .pagination-nav__list a:hover { border-color: var(--accent); background: rgba(34, 211, 238, 0.1); }
        .pagination-nav__current {
            color: var(--bg); background: var(--accent); border: 1px solid var(--accent);
        }
        .pagination-nav__ellipsis { padding: 0 0.35rem; color: var(--muted); }
        .pagination-nav__disabled {
            color: var(--muted); padding: 0 0.5rem; font-size: 0.9rem;
        }
        .field { margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
        th, td { text-align: left; padding: 0.65rem 0.5rem; border-bottom: 1px solid var(--border); }
        th { color: var(--muted); font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.85rem; }
        .table-wrap { overflow-x: auto; border-radius: 0.75rem; border: 1px solid var(--border); }
        .table-wrap table { margin: 0; }
        .pagination { margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; font-size: 0.9rem; color: var(--muted); }
        .pagination a { color: var(--accent); }
        .modal-backdrop {
            display: none; position: fixed; inset: 0; z-index: 100;
            background: rgba(15, 23, 42, 0.75); align-items: center; justify-content: center;
            padding: 1rem; overflow-y: auto;
        }
        .modal-backdrop.is-open { display: flex; }
        .modal-panel {
            background: var(--surface); border: 1px solid var(--border); border-radius: 0.75rem;
            padding: 1.25rem; max-width: 640px; width: 100%; max-height: min(90vh, 900px); overflow: hidden;
            display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }
        .modal-panel__head {
            display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
            margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);
        }
        .modal-panel__head h2 { margin: 0; font-size: 1.1rem; }
        .modal-panel__body { overflow-y: auto; flex: 1; min-height: 0; }
        .modal-panel__close {
            background: transparent; border: 1px solid var(--border); color: var(--text);
            border-radius: 0.5rem; width: 2.25rem; height: 2.25rem; cursor: pointer;
            font-size: 1.25rem; line-height: 1; flex-shrink: 0;
        }
        .modal-panel__close:hover { border-color: var(--accent); color: var(--accent); }
        .detail-dl { font-size: 0.9rem; }
        .detail-dl dt { color: var(--muted); font-weight: 600; margin-top: 0.75rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .detail-dl dt:first-child { margin-top: 0; }
        .detail-dl dd { margin: 0.25rem 0 0; word-break: break-word; }
        .detail-dl pre {
            margin: 0.5rem 0 0; padding: 0.75rem; border-radius: 0.5rem;
            background: var(--bg); border: 1px solid var(--border); font-size: 0.75rem;
            overflow-x: auto; white-space: pre-wrap; word-break: break-word;
        }
        @media (max-width: 768px) {
            .admin-shell { flex-direction: column; }
            .admin-sidebar {
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                padding: 0.75rem 1rem;
            }
            .admin-sidebar__brand {
                border-bottom: none;
                margin-bottom: 0;
                padding: 0 0.5rem 0 0;
            }
            .admin-nav {
                flex-direction: row;
                flex-wrap: wrap;
                flex: 1;
                justify-content: flex-start;
            }
            .admin-nav a {
                border-left: none;
                border-bottom: 3px solid transparent;
            }
            .admin-nav a.is-active {
                border-left-color: transparent;
                border-bottom-color: var(--nav-active-border);
            }
            .admin-sidebar__foot {
                border-top: none;
                padding: 0;
                margin-top: 0;
                width: 100%;
            }
            .admin-sidebar__foot .btn-ghost { width: auto; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar" aria-label="Admin navigation">
            <div class="admin-sidebar__brand">
                <a href="{{ route('admin.dashboard') }}">{{ config('app.name') }}</a>
                <span>Admin</span>
            </div>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.manifests.index') }}" class="{{ request()->routeIs('admin.manifests.*') ? 'is-active' : '' }}">Manifests</a>
                <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'is-active' : '' }}">Bookings</a>
                <a href="{{ route('admin.checkin-users.index') }}" class="{{ request()->routeIs('admin.checkin-users.*') ? 'is-active' : '' }}">Checkin Users</a>
            </nav>
            <div class="admin-sidebar__foot">
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-ghost">Log out</button>
                </form>
            </div>
        </aside>
        <div class="admin-main">
            <header class="admin-main__head">
                <h1>@yield('heading', 'Admin')</h1>
            </header>

            @if (session('status'))
                <p class="flash" role="status">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="errors" role="alert">
                    <ul style="margin:0;padding-left:1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>
