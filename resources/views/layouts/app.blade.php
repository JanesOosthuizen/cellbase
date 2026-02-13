<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @stack('styles')
    <style>
        :root {
            --nav-bg: #0f172a;
            --nav-text: #94a3b8;
            --nav-text-hover: #f1f5f9;
            --nav-active: #38bdf8;
            --nav-border: rgba(148, 163, 184, 0.12);
            --app-bg: #f8fafc;
            --card-bg: #ffffff;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.06);
            --card-radius: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--app-bg);
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
            line-height: 1.5;
        }
        .app-nav {
            background: var(--nav-bg);
            border-bottom: 1px solid var(--nav-border);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .app-nav__inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            gap: 32px;
        }
        .app-nav__brand {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            letter-spacing: -0.02em;
            white-space: nowrap;
        }
        .app-nav__brand:hover { color: var(--nav-text-hover); }
        .app-nav__menu {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            flex: 1;
        }
        .app-nav__link {
            display: block;
            padding: 8px 14px;
            color: var(--nav-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            transition: color 0.15s, background 0.15s;
        }
        .app-nav__link:hover {
            color: var(--nav-text-hover);
            background: rgba(255,255,255,0.06);
        }
        .app-nav__link--active {
            color: var(--nav-active);
        }
        .app-nav__link--active:hover {
            color: #7dd3fc;
            background: rgba(56, 189, 248, 0.1);
        }
        .app-nav__dropdown-trigger {
            border: none;
            cursor: pointer;
            font-family: inherit;
            background: transparent;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .app-nav__dropdown-trigger::after {
            content: '';
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid currentColor;
            opacity: 0.7;
        }
        .app-nav__dropdown {
            position: relative;
        }
        .app-nav__dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 6px;
            min-width: 200px;
            background: #1e293b;
            border: 1px solid var(--nav-border);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 6px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 0.2s, transform 0.2s, visibility 0.2s;
            z-index: 100;
        }
        .app-nav__dropdown:hover .app-nav__dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .app-nav__dropdown-item {
            display: block;
            padding: 10px 14px;
            color: var(--nav-text);
            text-decoration: none;
            font-size: 14px;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
        }
        .app-nav__dropdown-item:hover {
            background: rgba(255,255,255,0.08);
            color: var(--nav-text-hover);
        }
        .app-nav__user {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-left: 16px;
            border-left: 1px solid var(--nav-border);
        }
        .app-nav__user-name {
            font-size: 13px;
            color: var(--nav-text);
            font-weight: 500;
        }
        .app-nav__logout-form { display: inline; }
        .app-nav__logout {
            font-size: 13px;
            color: var(--nav-text);
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-weight: 500;
            padding: 6px 0;
            text-decoration: none;
            transition: color 0.15s;
        }
        .app-nav__logout:hover { color: var(--nav-text-hover); }
        .app-nav__search {
            position: relative;
            flex: 1;
            max-width: 320px;
            margin-left: 16px;
        }
        .app-nav__search-input {
            width: 100%;
            padding: 8px 14px;
            border: 1px solid var(--nav-border);
            border-radius: 8px;
            background: rgba(30, 41, 59, 0.6);
            color: var(--nav-text-hover);
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.15s, background 0.15s;
        }
        .app-nav__search-input::placeholder { color: var(--nav-text); opacity: 0.8; }
        .app-nav__search-input:focus {
            outline: none;
            border-color: var(--nav-active);
            background: rgba(30, 41, 59, 0.8);
        }
        .app-nav__search-wrap { position: relative; }
        .app-nav__search-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            max-height: 70vh;
            overflow-y: auto;
            background: #1e293b;
            border: 1px solid var(--nav-border);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 8px;
            z-index: 200;
            display: none;
        }
        .app-nav__search-dropdown.is-open { display: block; }
        .app-nav__search-section-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--nav-text);
            padding: 6px 10px 4px;
            margin-top: 4px;
        }
        .app-nav__search-section-title:first-child { margin-top: 0; }
        .app-nav__search-item {
            display: block;
            padding: 10px 12px;
            color: var(--nav-text-hover);
            text-decoration: none;
            font-size: 14px;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
        }
        .app-nav__search-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .app-nav__search-item-label { font-weight: 500; }
        .app-nav__search-item-sub { font-size: 12px; color: var(--nav-text); margin-top: 2px; }
        .app-nav__search-empty { padding: 16px; color: var(--nav-text); font-size: 14px; text-align: center; }
        .app-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 28px 24px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: #0f172a; letter-spacing: -0.02em; }
        .card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            padding: 24px;
            margin-bottom: 24px;
        }
        .card h2 { font-size: 17px; font-weight: 600; color: #0f172a; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: background 0.15s, color 0.15s;
        }
        .btn-primary { background: #0ea5e9; color: #fff; }
        .btn-primary:hover { background: #0284c7; }
        .btn-secondary { background: #64748b; color: #fff; }
        .btn-secondary:hover { background: #475569; }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-danger:hover { background: #b91c1c; }
        .alert-success { padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; background: #ecfdf5; color: #065f46; font-size: 14px; }
        .alert-danger { padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; background: #fef2f2; color: #991b1b; font-size: 14px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 12px 14px; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
        .table tbody tr:hover { background: #f8fafc; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 14px; color: #334155; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.15); }
        .form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .pagination { display: flex; justify-content: center; gap: 6px; margin-top: 24px; flex-wrap: wrap; }
        .pagination a, .pagination span { padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 14px; color: #64748b; }
        .pagination a:hover { background: #f1f5f9; color: #0ea5e9; }
        .pagination .active { background: #0ea5e9; color: #fff; }
        .empty-state { text-align: center; padding: 48px 24px; color: #64748b; }
        .empty-state p { font-size: 15px; margin-bottom: 16px; }
        .table thead { background: #f8fafc; }
        .table thead th { color: #64748b; }
        .table a { color: #0ea5e9; text-decoration: none; }
        .table a:hover { text-decoration: underline; }
        .table tbody tr { cursor: pointer; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        .status-booked_in { background: #dbeafe; color: #1e40af; }
        .status-sent_away { background: #fef3c7; color: #92400e; }
        .status-received { background: #e0e7ff; color: #3730a3; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-collected { background: #d1fae5; color: #047857; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-group .error { color: #ef4444; font-size: 13px; margin-top: 4px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: #fff; border-radius: 12px; padding: 28px; max-width: 560px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .modal-header { margin-bottom: 20px; }
        .modal-header h2 { font-size: 18px; font-weight: 600; color: #0f172a; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        .badge-draft { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .imei-code { font-family: ui-monospace, monospace; font-size: 13px; }
    </style>
</head>
<body>
    @include('layouts.partials.nav')
    <main class="app-main">
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
