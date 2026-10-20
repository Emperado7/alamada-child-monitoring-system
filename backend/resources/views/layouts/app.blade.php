<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- iPhone / iOS PWA meta --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Alamada CMS">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2e7d32">
    <title>@yield('title','Dashboard') — Alamada Learning Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --green-dark:   #1b5e20;
            --green-main:   #2e7d32;
            --green-mid:    #388e3c;
            --green-light:  #43a047;
            --green-pale:   #e8f5e9;
            --sidebar-w:    220px;
            --topbar-h:     60px;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            color: #212121;
        }

        /* ═══════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════ */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, var(--green-dark) 0%, var(--green-main) 100%);
            display: flex;
            flex-direction: column;
            z-index: 200;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform .25s ease;
        }

        /* Brand / logo area */
        .sidebar-brand {
            padding: 14px 14px 12px;
            border-bottom: 1px solid rgba(255,255,255,.12);
            display: flex;
            flex-direction: row;
            align-items: center;
            text-align: left;
            text-decoration: none;
            gap: 10px;
        }
        .sidebar-brand img.sidebar-logo-img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 10px;
            flex-shrink: 0;
        }
        .sidebar-brand-text .st {
            font-size: .72rem;
            color: rgba(255,255,255,.65);
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .sidebar-brand-text .sb {
            font-size: .95rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
        }
        .sidebar-brand-text .sc {
            font-size: .7rem;
            color: rgba(255,255,255,.6);
            letter-spacing: .3px;
        }

        /* Nav links */
        .sidebar-nav { padding: 10px 0; flex-grow: 1; }
        .nav-item-s { list-style: none; }
        .nav-link-s {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 18px;
            color: rgba(255,255,255,.82);
            text-decoration: none;
            font-size: .88rem;
            font-weight: 500;
            transition: background .15s, color .15s;
            border-radius: 0;
            position: relative;
        }
        .nav-link-s i { width: 18px; text-align: center; font-size: .95rem; }
        .nav-link-s:hover {
            background: rgba(255,255,255,.12);
            color: #fff;
        }
        .nav-link-s.active {
            background: rgba(255,255,255,.18);
            color: #fff;
            font-weight: 600;
        }
        .nav-link-s.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: #a5d6a7;
            border-radius: 0 3px 3px 0;
        }
        /* Badge on nav */
        .nav-badge {
            margin-left: auto;
            background: #ef5350;
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
        }

        /* Bottom logout */
        .sidebar-bottom {
            padding: 10px 0 16px;
            border-top: 1px solid rgba(255,255,255,.12);
        }

        /* School building illustration */
        .sidebar-illustration {
            margin-top: auto;
            padding: 0 0 4px;
            flex-shrink: 0;
            pointer-events: none;
            user-select: none;
        }

        /* ═══════════════════════════════════════════
           TOP NAVBAR
        ═══════════════════════════════════════════ */
        #topnav {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #ffffff;
            border-bottom: 1px solid #e8f0e8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }
        .topnav-left { display: flex; align-items: center; gap: 14px; }
        .topnav-toggle {
            background: none; border: none; color: #555;
            font-size: 1.2rem; cursor: pointer; padding: 4px 8px;
        }
        .topnav-title {
            color: #212121; font-weight: 700; font-size: 1.05rem; display:block;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .topnav-right { display: flex; align-items: center; gap: 14px; }

        /* Notification bell */
        .notif-btn {
            position: relative;
            background: none; border: none;
            color: #555; font-size: 1.2rem; cursor: pointer; padding: 6px;
        }
        .notif-badge {
            position: absolute;
            top: 0; right: 0;
            background: #ef5350;
            color: #fff;
            font-size: .62rem; font-weight: 700;
            width: 17px; height: 17px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }

        /* Admin dropdown */
        .admin-pill {
            display: flex; align-items: center; gap: 8px;
            background: transparent;
            border: none;
            color: #212121;
            border-radius: 24px;
            padding: 5px 10px 5px 6px;
            cursor: pointer;
            font-size: .88rem;
            font-weight: 600;
            transition: background .15s;
        }
        .admin-pill:hover { background: #f5f5f5; }
        .admin-avatar {
            width: 34px; height: 34px;
            background: var(--green-main);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .9rem; color: #fff;
        }

        /* ═══════════════════════════════════════════
           MAIN CONTENT
        ═══════════════════════════════════════════ */
        #main {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 24px 26px 40px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ═══════════════════════════════════════════
           STAT CARDS
        ═══════════════════════════════════════════ */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            border: none;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
            transition: box-shadow .2s, transform .15s;
        }
        .stat-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.12); transform: translateY(-2px); }
        .stat-icon {
            width: 54px; height: 54px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .stat-icon.green  { background: #e8f5e9; }
        .stat-icon.blue   { background: #e3f2fd; }
        .stat-icon.orange { background: #fff3e0; }
        .stat-icon.purple { background: #f3e5f5; }
        .stat-body { flex-grow: 1; }
        .stat-label {
            font-size: .72rem; font-weight: 700;
            color: #9e9e9e; letter-spacing: .07em;
            text-transform: uppercase; margin-bottom: 2px;
        }
        .stat-value {
            font-size: 2rem; font-weight: 800;
            line-height: 1.1; margin-bottom: 4px;
        }
        .stat-link {
            font-size: .78rem; color: #757575;
            text-decoration: none; display: flex; align-items: center; gap: 4px;
        }
        .stat-link:hover { color: var(--green-main); }

        /* ═══════════════════════════════════════════
           CARDS
        ═══════════════════════════════════════════ */
        .dash-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            border: none;
            overflow: hidden;
        }
        .dash-card-header {
            padding: 14px 18px 10px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dash-card-title {
            font-weight: 700; font-size: .95rem; color: #212121;
        }
        .dash-card-body { padding: 16px 18px; }

        /* ═══════════════════════════════════════════
           ACTIVITY LIST
        ═══════════════════════════════════════════ */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
            flex-shrink: 0;
        }
        .activity-text .at { font-size: .88rem; font-weight: 600; color: #212121; }
        .activity-text .as { font-size: .78rem; color: #757575; margin-top: 1px; }
        .activity-time { margin-left: auto; font-size: .75rem; color: #9e9e9e; white-space: nowrap; }

        /* ═══════════════════════════════════════════
           ENROLLMENT STATUS
        ═══════════════════════════════════════════ */
        .enroll-legend-item {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 6px; font-size: .85rem;
        }
        .legend-dot {
            width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0;
        }
        .legend-val { margin-left: auto; font-weight: 600; color: #212121; }
        .legend-pct { color: #9e9e9e; font-size: .78rem; }

        /* Total enrollments badge */
        .total-enroll-box {
            background: var(--green-pale);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            border: 1px solid #c8e6c9;
        }
        .total-enroll-box .te-label { font-size: .78rem; color: #757575; }
        .total-enroll-box .te-value { font-size: 2rem; font-weight: 800; color: var(--green-dark); }

        /* ═══════════════════════════════════════════
           UPCOMING EVENTS
        ═══════════════════════════════════════════ */
        .event-card {
            background: #fff;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,.07);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .event-icon-box {
            width: 44px; height: 44px;
            background: var(--green-pale);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: var(--green-main);
            flex-shrink: 0;
        }
        .event-title { font-weight: 600; font-size: .88rem; color: #212121; }
        .event-sub   { font-size: .78rem; color: #757575; margin-top: 2px; }
        .btn-view-cal {
            margin-left: auto;
            background: var(--green-main);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            text-decoration: none;
            transition: background .15s;
        }
        .btn-view-cal:hover { background: var(--green-dark); color: #fff; }

        /* ═══════════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════════ */
        .dash-footer {
            text-align: center;
            padding: 16px 0 0;
            font-size: .78rem;
            color: #9e9e9e;
        }
        .dash-footer a { color: var(--green-main); text-decoration: none; }

        /* ═══════════════════════════════════════════
           MISC
        ═══════════════════════════════════════════ */
        .badge-green  { background: #e8f5e9; color: #2e7d32; }
        .badge-orange { background: #fff3e0; color: #e65100; }
        .badge-red    { background: #ffebee; color: #c62828; }
        .badge-blue   { background: #e3f2fd; color: #1565c0; }
        .table thead th { background: #f8f9fa; font-weight: 600; border: none; color: #555; font-size: .83rem; }
        .table td { vertical-align: middle; font-size: .87rem; }

        /* Page title row */
        .page-title-row { margin-bottom: 20px; }
        .page-title-row h4 { font-weight: 700; font-size: 1.3rem; margin-bottom: 2px; }
        .page-title-row p  { color: #757575; font-size: .85rem; margin: 0; }
        .page-title-row .date-badge {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: .83rem;
            color: #555;
            display: flex; align-items: center; gap: 6px;
        }

        /* Mobile */
        @media (max-width: 991px) {
            /* Sidebar slides off-screen on all phones & tablets */
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            /* Topnav spans full width */
            #topnav { left: 0; }
            /* Main content fills full width */
            #main { margin-left: 0; padding: 16px 14px 100px; }
        }

        /* ── iPhone / Small phone fixes ── */
        @media (max-width: 576px) {
            /* Stat cards: 2 per row, smaller font */
            .stat-card { padding: 12px 14px; gap: 10px; }
            .stat-icon { width: 42px; height: 42px; font-size: 1.1rem; }
            .stat-value { font-size: 1.5rem; }
            .stat-label { font-size: .68rem; }

            /* Page title row stacks vertically */
            .page-title-row {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px;
            }
            .page-title-row h4 { font-size: 1.1rem; }
            .page-title-row .date-badge { font-size: .78rem; padding: 4px 10px; }

            /* Tables scroll horizontally */
            .table-responsive-mobile {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .table { min-width: 600px; }

            /* Prevent overflow on event cards */
            .event-card { flex-wrap: wrap; }
            .btn-view-cal { margin-left: 0; margin-top: 8px; width: 100%; text-align: center; }

            /* Admin pill in topnav — hide name, show initial only */
            .admin-pill-name { display: none; }

            /* Charts — remove fixed height attribute effect */
            canvas { max-width: 100% !important; }

            /* Dash card padding */
            .dash-card-body { padding: 10px 12px; }
            .dash-card-header { padding: 10px 12px 8px; }

            /* iOS: prevent auto-zoom on form inputs — must be >= 16px */
            input, select, textarea, .form-control, .form-select {
                font-size: 16px !important;
            }

            /* Larger tap targets for buttons and links */
            .btn, .nav-link-s, .dropdown-item {
                min-height: 44px;
                display: flex;
                align-items: center;
            }

            /* Topnav admin name hidden on small screens */
            .admin-pill { padding: 5px 8px 5px 6px; }
        }

        /* ── Safe area insets for iPhone notch / home bar ── */
        #topnav {
            padding-left: calc(24px + env(safe-area-inset-left));
            padding-right: calc(24px + env(safe-area-inset-right));
        }
        #main {
            padding-bottom: calc(40px + env(safe-area-inset-bottom));
        }
        #sidebar {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* ── iOS touch improvements ── */
        a, button, .nav-link-s, .stat-card, .list-group-item {
            -webkit-tap-highlight-color: rgba(0,0,0,0.05);
        }
        #sidebar {
            -webkit-overflow-scrolling: touch;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ════════════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════════════ --}}
<nav id="sidebar">

    {{-- Brand --}}
    <a href="#" class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}"
             alt="Alamada Learning Center"
             class="sidebar-logo-img"
             onerror="this.src='{{ asset('images/logo.svg') }}'">
        <div class="sidebar-brand-text">
            <div class="sb">Alamada Learning Center</div>
            <div class="sc">Monitoring System</div>
        </div>
    </a>

    {{-- Nav --}}
    <ul class="sidebar-nav list-unstyled mb-0">
        @auth

        @if(auth()->user()->isAdmin())
        {{-- ADMIN MENU --}}
        <li class="nav-item-s">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link-s {{ request()->routeIs('admin.dashboard') ? 'active':'' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('admin.children.index') }}"
               class="nav-link-s {{ request()->routeIs('admin.children.*') ? 'active':'' }}">
                <i class="fas fa-child"></i> Child Information
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('admin.enrollment.index') }}"
               class="nav-link-s {{ request()->routeIs('admin.enrollment.*') ? 'active':'' }}">
                <i class="fas fa-clipboard-list"></i> Enrollment
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('admin.attendance.index') }}"
               class="nav-link-s {{ request()->routeIs('admin.attendance.*') ? 'active':'' }}">
                <i class="fas fa-calendar-check"></i> Attendance
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('admin.reports.index') }}"
               class="nav-link-s {{ request()->routeIs('admin.reports.*') ? 'active':'' }}">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('admin.notifications.index') }}"
               class="nav-link-s {{ request()->routeIs('admin.notifications.*') ? 'active':'' }}">
                <i class="fas fa-bell"></i> Notifications
                @php
                    $unread = \App\Models\Notification::count();
                @endphp
                @if($unread > 0)
                <span class="nav-badge">{{ $unread > 9 ? '9+' : $unread }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('admin.users.index') }}"
               class="nav-link-s {{ request()->routeIs('admin.users.*') ? 'active':'' }}">
                <i class="fas fa-users-cog"></i> Manage Users
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('admin.settings.index') }}"
               class="nav-link-s {{ request()->routeIs('admin.settings.*') ? 'active':'' }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>

        @elseif(auth()->user()->isStaff())
        {{-- STAFF MENU --}}
        <li class="nav-item-s">
            <a href="{{ route('staff.dashboard') }}"
               class="nav-link-s {{ request()->routeIs('staff.dashboard') ? 'active':'' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('staff.children.index') }}"
               class="nav-link-s {{ request()->routeIs('staff.children.*') ? 'active':'' }}">
                <i class="fas fa-child"></i> Child Monitoring
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('staff.enrollment.index') }}"
               class="nav-link-s {{ request()->routeIs('staff.enrollment.*') ? 'active':'' }}">
                <i class="fas fa-clipboard-list"></i> Enrollment
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('staff.attendance.index') }}"
               class="nav-link-s {{ request()->routeIs('staff.attendance.*') ? 'active':'' }}">
                <i class="fas fa-calendar-check"></i> Attendance
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('staff.activities.index') }}"
               class="nav-link-s {{ request()->routeIs('staff.activities.*') ? 'active':'' }}">
                <i class="fas fa-puzzle-piece"></i> Activities
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('staff.notifications.index') }}"
               class="nav-link-s {{ request()->routeIs('staff.notifications.*') ? 'active':'' }}">
                <i class="fas fa-bell"></i> Notifications
            </a>
        </li>
        <li class="nav-item-s">
            <a href="{{ route('staff.settings.index') }}"
               class="nav-link-s {{ request()->routeIs('staff.settings.*') ? 'active':'' }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
        @endif

        @endauth
    </ul>

    {{-- School building illustration --}}
    <div class="sidebar-illustration" aria-hidden="true">
        <svg viewBox="0 0 220 140" xmlns="http://www.w3.org/2000/svg"
             style="width:100%;display:block">
            <!-- Sky / background -->
            <rect width="220" height="140" fill="none"/>

            <!-- Ground hill -->
            <ellipse cx="110" cy="148" rx="130" ry="38" fill="#1b5e20" opacity=".55"/>

            <!-- Trees left -->
            <rect x="18" y="82" width="7" height="20" rx="3" fill="#388e3c" opacity=".7"/>
            <ellipse cx="21" cy="78" rx="12" ry="14" fill="#43a047" opacity=".8"/>

            <!-- Trees right -->
            <rect x="192" y="85" width="7" height="18" rx="3" fill="#388e3c" opacity=".7"/>
            <ellipse cx="195" cy="81" rx="11" ry="13" fill="#43a047" opacity=".8"/>

            <!-- Building body -->
            <rect x="62" y="58" width="96" height="72" rx="3" fill="#fff" opacity=".92"/>
            <rect x="62" y="58" width="96" height="72" rx="3"
                  stroke="rgba(255,255,255,.3)" stroke-width="1" fill="none"/>

            <!-- Roof -->
            <polygon points="55,60 110,28 165,60" fill="#e8f5e9" opacity=".95"/>
            <polygon points="55,60 110,28 165,60"
                     stroke="rgba(255,255,255,.4)" stroke-width="1" fill="none"/>

            <!-- Flag pole -->
            <line x1="110" y1="28" x2="110" y2="10"
                  stroke="#fff" stroke-width="1.8" stroke-linecap="round" opacity=".9"/>
            <!-- Flag -->
            <path d="M110 10 L128 15 L110 20 Z" fill="#a5d6a7" opacity=".95"/>

            <!-- Door -->
            <rect x="98" y="98" width="24" height="32" rx="3" fill="#a5d6a7" opacity=".85"/>
            <circle cx="119" cy="114" r="2" fill="#2e7d32" opacity=".7"/>

            <!-- Windows -->
            <rect x="70" y="70" width="22" height="18" rx="2" fill="#b3e5fc" opacity=".8"/>
            <line x1="81" y1="70" x2="81" y2="88" stroke="rgba(255,255,255,.6)" stroke-width="1"/>
            <line x1="70" y1="79" x2="92" y2="79" stroke="rgba(255,255,255,.6)" stroke-width="1"/>

            <rect x="128" y="70" width="22" height="18" rx="2" fill="#b3e5fc" opacity=".8"/>
            <line x1="139" y1="70" x2="139" y2="88" stroke="rgba(255,255,255,.6)" stroke-width="1"/>
            <line x1="128" y1="79" x2="150" y2="79" stroke="rgba(255,255,255,.6)" stroke-width="1"/>

            <!-- Small bushes -->
            <ellipse cx="74"  cy="130" rx="10" ry="7" fill="#388e3c" opacity=".7"/>
            <ellipse cx="147" cy="130" rx="10" ry="7" fill="#388e3c" opacity=".7"/>
        </svg>
    </div>

    {{-- Logout at bottom --}}
    @auth
    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link-s w-100 text-start border-0 bg-transparent">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
    @endauth
</nav>

{{-- ════════════════════════════════════════════════
     TOP NAVBAR
════════════════════════════════════════════════ --}}
<header id="topnav">
    <div class="topnav-left">
        <button class="topnav-toggle" onclick="toggleSidebar()" style="min-width:44px;min-height:44px">
            <i class="fas fa-bars"></i>
        </button>
        <span class="topnav-title">Alamada Learning Center Monitoring System</span>
    </div>
    <div class="topnav-right">
        @auth
        {{-- Notification bell --}}
        <a href="{{ auth()->user()->isAdmin() ? route('admin.notifications.index') : route('staff.notifications.index') }}"
           class="notif-btn text-decoration-none">
            <i class="fas fa-bell"></i>
            @php $nb = \App\Models\Notification::count(); @endphp
            @if($nb > 0)
            <span class="notif-badge">{{ $nb > 9 ? '9+' : $nb }}</span>
            @endif
        </a>

        {{-- Admin pill dropdown --}}
        <div class="dropdown">
            <button class="admin-pill dropdown-toggle" data-bs-toggle="dropdown">
                <div class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                {{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                <li>
                    <a class="dropdown-item" href="{{ auth()->user()->isAdmin() ? route('admin.settings.index') : route('staff.settings.index') }}">
                        <i class="fas fa-cog me-2 text-muted"></i>Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        @endauth
    </div>
</header>

{{-- ════════════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════════════ --}}
<main id="main">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Please fix the following:</strong>
        <ul class="mb-0 mt-1 small">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')

    {{-- Footer --}}
    <div class="dash-footer mt-4">
        &copy; {{ date('Y') }}
        <a href="#">Alamada Learning Center Monitoring System</a>.
        All rights reserved.
    </div>
</main>

{{-- Mobile sidebar overlay --}}
<div id="sidebarOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:199"
     onclick="toggleSidebar()"></div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('sidebarOverlay');
    const isOpen = sb.classList.toggle('open');
    ov.style.display = isOpen ? 'block' : 'none';
    // Prevent body scroll when sidebar is open on iOS
    document.body.style.overflow = isOpen ? 'hidden' : '';
}

// Close sidebar when any nav link is tapped (mobile)
document.querySelectorAll('.nav-link-s').forEach(function(link) {
    link.addEventListener('click', function() {
        if (window.innerWidth < 992) {
            const sb = document.getElementById('sidebar');
            const ov = document.getElementById('sidebarOverlay');
            sb.classList.remove('open');
            ov.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
});

// Auto DataTables — with responsive wrapper
$(function() {
    // Wrap all tables in responsive div if not already wrapped
    $('table.datatable').each(function() {
        if (!$(this).parent().hasClass('table-responsive')) {
            $(this).wrap('<div class="table-responsive"></div>');
        }
    });
    $('.datatable').DataTable({
        pageLength: 15,
        language: { search: '' },
        scrollX: false,
    });
});
</script>
@stack('scripts')

{{-- ── PWA: Offline / Online banner ─────────────────────── --}}
<div id="offlineBanner" style="
    display:none;position:fixed;bottom:0;left:0;right:0;z-index:9999;
    background:#e65100;color:#fff;text-align:center;
    padding:10px 16px;font-size:.88rem;font-weight:600;
    box-shadow:0 -2px 8px rgba(0,0,0,.2);
    display:none;align-items:center;justify-content:center;gap:8px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
        <path d="M1 1l22 22" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
        <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
        <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.8" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
        <circle cx="12" cy="20" r="1" fill="#fff"/>
    </svg>
    You are offline — showing cached pages. Some features may not work.
</div>

<div id="onlineBanner" style="
    display:none;position:fixed;bottom:0;left:0;right:0;z-index:9999;
    background:#2e7d32;color:#fff;text-align:center;
    padding:10px 16px;font-size:.88rem;font-weight:600;
    box-shadow:0 -2px 8px rgba(0,0,0,.2);
    align-items:center;justify-content:center;gap:8px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
        <polyline points="20 6 9 17 4 12" stroke="#fff" stroke-width="2.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Back online!
</div>

<script>
// ── Service Worker registration ───────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js')
            .then(function (reg) {
                console.log('[SW] Registered, scope:', reg.scope);
            })
            .catch(function (err) {
                console.warn('[SW] Registration failed:', err);
            });
    });
}

// ── Save last-online timestamp ────────────────────────────────
localStorage.setItem('alamada_last_online', new Date().toISOString());

// ── Offline / Online banners ──────────────────────────────────
var offlineBanner = document.getElementById('offlineBanner');
var onlineBanner  = document.getElementById('onlineBanner');

function showOffline() {
    offlineBanner.style.display = 'flex';
    onlineBanner.style.display  = 'none';
}
function showOnline() {
    onlineBanner.style.display  = 'flex';
    offlineBanner.style.display = 'none';
    setTimeout(function () {
        onlineBanner.style.display = 'none';
    }, 3000); // hide "Back online!" after 3 s
}

if (!navigator.onLine) showOffline();

window.addEventListener('offline', showOffline);
window.addEventListener('online',  showOnline);
</script>
</body>
</html>
