<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            padding: 18px 14px 14px;
            border-bottom: 1px solid rgba(255,255,255,.12);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .sidebar-logo {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
        }
        .sidebar-brand-text .st { font-size: .75rem; color: rgba(255,255,255,.7); }
        .sidebar-brand-text .sb { font-size: .95rem; font-weight: 700; color: #fff; line-height: 1.2; }

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

        /* ═══════════════════════════════════════════
           TOP NAVBAR
        ═══════════════════════════════════════════ */
        #topnav {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--green-main);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 100;
            box-shadow: 0 2px 6px rgba(0,0,0,.18);
        }
        .topnav-left { display: flex; align-items: center; gap: 14px; }
        .topnav-toggle {
            background: none; border: none; color: #fff;
            font-size: 1.2rem; cursor: pointer; padding: 4px 8px;
        }
        .topnav-title { color: #fff; font-weight: 600; font-size: 1rem; }

        .topnav-right { display: flex; align-items: center; gap: 14px; }

        /* Notification bell */
        .notif-btn {
            position: relative;
            background: none; border: none;
            color: #fff; font-size: 1.15rem; cursor: pointer; padding: 6px;
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
            border: 2px solid var(--green-main);
        }

        /* Admin dropdown */
        .admin-pill {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.15);
            border: none;
            color: #fff;
            border-radius: 24px;
            padding: 5px 12px 5px 6px;
            cursor: pointer;
            font-size: .88rem;
            font-weight: 600;
            transition: background .15s;
        }
        .admin-pill:hover { background: rgba(255,255,255,.25); }
        .admin-avatar {
            width: 32px; height: 32px;
            background: rgba(255,255,255,.25);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .88rem; color: #fff;
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
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #topnav { left: 0; }
            #main { margin-left: 0; }
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
             style="width:44px;height:44px;object-fit:contain;flex-shrink:0;border-radius:8px"
             onerror="this.src='{{ asset('images/logo.svg') }}'">
        <div class="sidebar-brand-text">
            <div class="st">Monitoring System</div>
            <div class="sb">Alamada Learning<br>Center</div>
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
        <button class="topnav-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <span class="topnav-title">@yield('page-title','Dashboard')</span>
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
}
// Auto DataTables
$(function() {
    $('.datatable').DataTable({
        pageLength: 15,
        language: { search: '' }
    });
});
</script>
@stack('scripts')
</body>
</html>
