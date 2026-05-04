<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Water POS')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #0f1117;
            --surface: #181c27;
            --surface2: #1f2435;
            --border: #2a3047;
            --border2: #374060;
            --text: #e8eaf0;
            --muted: #6b7590;
            --accent: #f5a623;
            --accent2: #e8941a;
            --green: #3ecf8e;
            --red: #f56565;
            --blue: #60a5fa;
            --radius: 10px;
            --font-head: 'Syne', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }

        html,
        body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-mono);
            font-size: 13px;
            line-height: 1.5;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border2);
            border-radius: 99px;
        }

        /* ── Nav ── */
        .nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 52px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-head);
            font-weight: 700;
            font-size: 15px;
            color: var(--text);
            text-decoration: none;
        }

        .nav-brand-dot {
            width: 28px;
            height: 28px;
            background: var(--accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f1117;
            font-size: 13px;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 4px;
        }

        .nav-link {
            padding: 5px 12px;
            border-radius: 6px;
            color: var(--muted);
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.15s;
            letter-spacing: 0.02em;
        }

        .nav-link:hover {
            color: var(--text);
            background: var(--surface2);
        }

        .nav-link.active {
            color: var(--accent);
            background: rgba(245, 166, 35, 0.1);
        }

        /* ── Main layout ── */
        .main {
            padding: 20px 24px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
        }

        .card-title {
            font-family: var(--font-head);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 14px;
        }

        /* ── Option buttons ── */
        .opt-btn {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 10px 12px;
            cursor: pointer;
            text-align: left;
            transition: all 0.12s;
            color: var(--text);
            width: 100%;
            font-family: var(--font-mono);
        }

        .opt-btn:hover {
            border-color: var(--border2);
            background: #252a3d;
        }

        .opt-btn.sel {
            border-color: var(--accent);
            background: rgba(245, 166, 35, 0.08);
            box-shadow: 0 0 0 1px rgba(245, 166, 35, 0.2);
        }

        .opt-btn .opt-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            display: block;
        }

        .opt-btn .opt-sub {
            font-size: 11px;
            color: var(--muted);
            display: block;
            margin-top: 2px;
        }

        .opt-btn .opt-price {
            font-size: 11px;
            font-weight: 500;
            color: var(--accent);
            display: block;
            margin-top: 6px;
            font-family: var(--font-mono);
        }

        .opt-btn.sel .opt-name {
            color: var(--accent);
        }

        /* ── Extra toggle buttons ── */
        .ext-btn {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 9px 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.12s;
            color: var(--text);
            font-family: var(--font-mono);
            width: 100%;
        }

        .ext-btn:hover {
            border-color: var(--border2);
        }

        .ext-btn.sel {
            border-color: var(--green);
            background: rgba(62, 207, 142, 0.07);
        }

        .ext-btn .ext-name {
            font-size: 12px;
            font-weight: 500;
        }

        .ext-btn .ext-price {
            font-size: 11px;
            color: var(--green);
            font-family: var(--font-mono);
        }

        .ext-check {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1.5px solid var(--border2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: transparent;
            flex-shrink: 0;
            margin-right: 8px;
            transition: all 0.12s;
        }

        .ext-btn.sel .ext-check {
            background: var(--green);
            border-color: var(--green);
            color: #0f1117;
        }

        /* ── Buttons ── */
        .btn {
            border: none;
            cursor: pointer;
            font-family: var(--font-mono);
            font-weight: 500;
            border-radius: var(--radius);
            transition: all 0.12s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: var(--accent);
            color: #0f1117;
            padding: 11px 20px;
            font-size: 13px;
        }

        .btn-primary:hover {
            background: var(--accent2);
        }

        .btn-primary:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .btn-ghost {
            background: transparent;
            color: var(--muted);
            padding: 8px 14px;
            font-size: 12px;
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            color: var(--text);
            border-color: var(--border2);
        }

        .btn-danger {
            background: rgba(245, 101, 101, 0.1);
            color: var(--red);
            border: 1px solid rgba(245, 101, 101, 0.2);
            padding: 4px 8px;
            font-size: 11px;
            border-radius: 6px;
        }

        .btn-danger:hover {
            background: rgba(245, 101, 101, 0.2);
        }

        /* ── Tags / badges ── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.04em;
        }

        .badge-green {
            background: rgba(62, 207, 142, 0.12);
            color: var(--green);
        }

        .badge-amber {
            background: rgba(245, 166, 35, 0.12);
            color: var(--accent);
        }

        .badge-red {
            background: rgba(245, 101, 101, 0.12);
            color: var(--red);
        }

        /* ── Qty control ── */
        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1px solid var(--border2);
            background: var(--surface2);
            color: var(--text);
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.12s;
            font-family: var(--font-mono);
        }

        .qty-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .qty-num {
            font-size: 15px;
            font-weight: 500;
            width: 24px;
            text-align: center;
            color: var(--text);
        }

        /* ── Textarea / input ── */
        textarea,
        input {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-family: var(--font-mono);
            font-size: 12px;
            padding: 9px 12px;
            width: 100%;
            outline: none;
            transition: border-color 0.15s;
            resize: none;
        }

        textarea:focus,
        input:focus {
            border-color: var(--accent);
        }

        textarea::placeholder,
        input::placeholder {
            color: var(--muted);
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px dashed var(--border);
            margin: 14px 0;
        }

        /* ── Toast ── */
        #toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            z-index: 9999;
            display: none;
            max-width: 320px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        #toast.show {
            display: block;
            animation: slideUp 0.2s ease;
        }

        #toast.success {
            background: rgba(62, 207, 142, 0.15);
            border: 1px solid rgba(62, 207, 142, 0.3);
            color: var(--green);
        }

        #toast.error {
            background: rgba(245, 101, 101, 0.15);
            border: 1px solid rgba(245, 101, 101, 0.3);
            color: var(--red);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Table ── */
        .tbl {
            width: 100%;
            border-collapse: collapse;
        }

        .tbl th {
            font-family: var(--font-head);
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 10px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        .tbl td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            vertical-align: middle;
        }

        .tbl tr:last-child td {
            border-bottom: none;
        }

        .tbl tr:hover td {
            background: var(--surface2);
        }

        /* ── Pagination ── */
        .pagination {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .pagination a,
        .pagination span {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            border: 1px solid var(--border);
            color: var(--muted);
            text-decoration: none;
            transition: all 0.12s;
        }

        .pagination a:hover {
            color: var(--text);
            border-color: var(--border2);
        }

        .pagination .active span {
            background: rgba(245, 166, 35, 0.1);
            border-color: var(--accent);
            color: var(--accent);
        }

        /* ── Animations ── */
        .fade-in {
            animation: fadeIn 0.25s ease both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <nav class="nav">
        <a href="{{ route('pos.index') }}" class="nav-brand">
            <span class="nav-brand-dot">W</span>
            Water POS
        </a>
        <div class="nav-links">
            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.index')  ? 'active' : '' }}">Terminal</a>
            <a href="{{ route('pos.orders') }}" class="nav-link {{ request()->routeIs('pos.orders','pos.show') ? 'active' : '' }}">Orders</a>
        </div>
    </nav>

    <div class="main">
        @yield('content')
    </div>

    <div id="toast"></div>

    @stack('scripts')

    <script>
        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = 'show ' + type;
            clearTimeout(window._toastTimer);
            window._toastTimer = setTimeout(() => {
                t.className = '';
            }, 4000);
        }
    </script>
</body>

</html>