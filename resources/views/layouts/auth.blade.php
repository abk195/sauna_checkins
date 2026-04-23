<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Log in') — {{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --border: #334155;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --accent: #22d3ee;
            --accent-dim: #0891b2;
            --danger: #f87171;
            --success: #34d399;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(165deg, var(--bg) 0%, #172554 100%);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
        }
        a { color: var(--accent); }
        .auth-wrap { width: 100%; max-width: 28rem; }
        .auth-wrap h1 { margin: 0 0 1.25rem; font-size: 1.25rem; font-weight: 700; text-align: center; }
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
        .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .card {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        label { display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 0.35rem; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 0.6rem 0.75rem;
            border-radius: 0.5rem; border: 1px solid var(--border);
            background: var(--bg); color: var(--text); font-size: 1rem;
        }
        .field { margin-bottom: 1rem; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-wrap">
        <h1>@yield('heading', 'Admin login')</h1>

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
    @stack('scripts')
</body>
</html>
