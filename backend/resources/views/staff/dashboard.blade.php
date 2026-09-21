@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')

{{-- ── Page Title + Date ─────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <div style="color:#2e7d32;font-weight:600;font-size:1rem">
            Welcome, {{ auth()->user()->name }}! 👋
        </div>
        <div class="text-muted small mt-1">
            Here's an overview of today at Alamada Learning Center.
        </div>
    </div>
    <div class="date-badge">
        <i class="fas fa-calendar-alt" style="color:#2e7d32"></i>
        {{ now()->format('M d, Y (l)') }}
    </div>
</div>

{{-- ── 3 Stat Cards ─────────────────────────────────────────── --}}
@php
    $staffId      = auth()->id();
    $totalEnrolled = \App\Models\Child::where('staff_id',$staffId)->where('status','active')->count();
    $today        = now()->toDateString();
    $presentToday = \App\Models\Attendance::where('attendance_date',$today)
                        ->where('status','present')
                        ->whereHas('child',fn($q)=>$q->where('staff_id',$staffId))->count();
    $absentToday  = \App\Models\Attendance::where('attendance_date',$today)
                        ->where('status','absent')
                        ->whereHas('child',fn($q)=>$q->where('staff_id',$staffId))->count();
    $presentPct   = $totalEnrolled > 0 ? round($presentToday/$totalEnrolled*100) : 0;
    $absentPct    = $totalEnrolled > 0 ? round($absentToday/$totalEnrolled*100)  : 0;
@endphp

<div class="row g-3 mb-4">

    {{-- Total Enrolled --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div style="width:56px;height:56px;border-radius:14px;
                            background:#e8f5e9;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                        <circle cx="9"  cy="7"  r="3.5" fill="#2e7d32"/>
                        <circle cx="15" cy="7"  r="3.5" fill="#66bb6a"/>
                        <path d="M2 19c0-3.31 3.13-6 7-6s7 2.69 7 6"
                              stroke="#2e7d32" stroke-width="1.5" fill="none"/>
                        <path d="M15 13c2.5.6 4 2.3 4 5"
                              stroke="#66bb6a" stroke-width="1.5" fill="none"
                              stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        Total Enrolled Children
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $totalEnrolled }}
                    </div>
                    <div class="text-muted small mt-1">
                        All enrolled children in the system.
                    </div>
                </div>
            </div>
            <a href="{{ route('staff.children.index') }}"
               class="d-flex align-items-center gap-1"
               style="color:#2e7d32;font-size:.83rem;font-weight:600;text-decoration:none">
                View all children
                <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
            </a>
        </div>
    </div>

    {{-- Present Today --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div style="width:56px;height:56px;border-radius:14px;
                            background:#e8f5e9;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <div style="width:36px;height:36px;border-radius:50%;
                                background:#2e7d32;display:flex;align-items:center;
                                justify-content:center">
                        <i class="fas fa-check" style="color:#fff;font-size:1rem"></i>
                    </div>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        Present Today
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $presentToday }}
                    </div>
                    <div class="text-muted small mt-1">
                        {{ $presentPct }}% of total enrolled children
                    </div>
                </div>
            </div>
            <a href="{{ route('staff.attendance.index') }}"
               class="d-flex align-items-center gap-1"
               style="color:#2e7d32;font-size:.83rem;font-weight:600;text-decoration:none">
                View attendance
                <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
            </a>
        </div>
    </div>

    {{-- Absent Today --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div style="width:56px;height:56px;border-radius:14px;
                            background:#ffebee;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <div style="width:36px;height:36px;border-radius:50%;
                                background:#ffebee;border:3px solid #c62828;
                                display:flex;align-items:center;justify-content:center">
                        <i class="fas fa-times" style="color:#c62828;font-size:.9rem"></i>
                    </div>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        Absent Today
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $absentToday }}
                    </div>
                    <div class="text-muted small mt-1">
                        {{ $absentPct }}% of total enrolled children
                    </div>
                </div>
            </div>
            <a href="{{ route('staff.attendance.index') }}"
               class="d-flex align-items-center gap-1"
               style="color:#2e7d32;font-size:.83rem;font-weight:600;text-decoration:none">
                View attendance
                <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
            </a>
        </div>
    </div>
</div>

{{-- ── Quick Access Section ─────────────────────────────────── --}}
<div class="dash-card p-4">
    <h5 class="fw-bold text-center mb-4">Quick Access</h5>

    <div class="row g-3">

        {{-- Enrollment --}}
        <div class="col-md-3">
            <div class="quick-card h-100">
                <div class="qc-icon" style="background:#e8f5e9;color:#2e7d32">
                    <i class="fas fa-clipboard-list fs-3"></i>
                </div>
                <div class="qc-title" style="color:#2e7d32">Enrollment</div>
                <div class="qc-desc">
                    Manage child enrollments and view enrolled children.
                </div>

                <div class="qc-sub-item">
                    <div class="qc-sub-icon" style="background:#e8f5e9;color:#2e7d32">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <div class="qc-sub-title">New Enrollment</div>
                        <div class="qc-sub-desc">Register a new child</div>
                    </div>
                </div>

                <div class="qc-sub-item">
                    <div class="qc-sub-icon" style="background:#e8f5e9;color:#2e7d32">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="qc-sub-title">Enrolled Children</div>
                        <div class="qc-sub-desc">View all enrolled children</div>
                    </div>
                </div>

                <a href="{{ route('staff.enrollment.index') }}"
                   class="qc-link" style="color:#2e7d32">
                    Go to Enrollment
                    <i class="fas fa-arrow-right ms-1" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>

        {{-- Child Monitoring --}}
        <div class="col-md-3">
            <div class="quick-card h-100">
                <div class="qc-icon" style="background:#e3f2fd;color:#1565c0">
                    <i class="fas fa-child fs-3"></i>
                </div>
                <div class="qc-title" style="color:#1565c0">Child Monitoring</div>
                <div class="qc-desc">
                    View and manage child information.
                </div>

                <div class="qc-sub-item">
                    <div class="qc-sub-icon" style="background:#e3f2fd;color:#1565c0">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div>
                        <div class="qc-sub-title">View Child List</div>
                        <div class="qc-sub-desc">See the list of all children</div>
                    </div>
                </div>

                <div class="qc-sub-item">
                    <div class="qc-sub-icon" style="background:#e3f2fd;color:#1565c0">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <div class="qc-sub-title">View Child Details</div>
                        <div class="qc-sub-desc">View detailed information of a child</div>
                    </div>
                </div>

                <a href="{{ route('staff.children.index') }}"
                   class="qc-link" style="color:#1565c0">
                    Go to Child Monitoring
                    <i class="fas fa-arrow-right ms-1" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>

        {{-- Attendance --}}
        <div class="col-md-3">
            <div class="quick-card h-100">
                <div class="qc-icon" style="background:#fff3e0;color:#e65100">
                    <i class="fas fa-calendar-check fs-3"></i>
                </div>
                <div class="qc-title" style="color:#e65100">Attendance</div>
                <div class="qc-desc">
                    Record and manage attendance of children.
                </div>

                <div class="qc-sub-item">
                    <div class="qc-sub-icon" style="background:#fff3e0;color:#e65100">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <div class="qc-sub-title">Select Child</div>
                        <div class="qc-sub-desc">Choose a child</div>
                    </div>
                </div>

                <div class="qc-sub-item">
                    <div class="qc-sub-icon" style="background:#fff3e0;color:#e65100">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    <div>
                        <div class="qc-sub-title">Mark Attendance</div>
                        <div class="qc-sub-desc">Mark attendance for the selected child</div>
                    </div>
                </div>

                <a href="{{ route('staff.attendance.mark') }}"
                   class="qc-link" style="color:#e65100">
                    Go to Attendance
                    <i class="fas fa-arrow-right ms-1" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>

        {{-- Settings --}}
        <div class="col-md-3">
            <div class="quick-card h-100">
                <div class="qc-icon" style="background:#f3e5f5;color:#6a1b9a">
                    <i class="fas fa-cog fs-3"></i>
                </div>
                <div class="qc-title" style="color:#6a1b9a">Settings</div>
                <div class="qc-desc">
                    Manage your account and preferences.
                </div>

                <div class="qc-sub-item">
                    <div class="qc-sub-icon" style="background:#f3e5f5;color:#6a1b9a">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <div class="qc-sub-title">Account Settings</div>
                        <div class="qc-sub-desc">Update your account information</div>
                    </div>
                </div>

                <div class="qc-sub-item">
                    <div class="qc-sub-icon" style="background:#f3e5f5;color:#6a1b9a">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <div class="qc-sub-title">Preferences</div>
                        <div class="qc-sub-desc">Customize your preferences</div>
                    </div>
                </div>

                <a href="{{ route('staff.settings.index') }}"
                   class="qc-link" style="color:#6a1b9a">
                    Go to Settings
                    <i class="fas fa-arrow-right ms-1" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
/* ── Quick Access Cards ───────────────────────────────── */
.quick-card {
    background: #ffffff;
    border: 1.5px solid #f0f0f0;
    border-radius: 16px;
    padding: 20px;
    transition: box-shadow .2s, transform .15s;
    display: flex;
    flex-direction: column;
}
.quick-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.qc-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
.qc-title {
    font-weight: 700;
    font-size: 1rem;
    text-align: center;
    margin-bottom: 6px;
}
.qc-desc {
    color: #757575;
    font-size: .82rem;
    text-align: center;
    margin-bottom: 16px;
    line-height: 1.4;
}
.qc-sub-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 8px 0;
    border-top: 1px solid #f5f5f5;
}
.qc-sub-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
    margin-top: 2px;
}
.qc-sub-title {
    font-weight: 600;
    font-size: .84rem;
    color: #212121;
}
.qc-sub-desc {
    font-size: .75rem;
    color: #9e9e9e;
    margin-top: 1px;
}
.qc-link {
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: .83rem;
    text-decoration: none;
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid #f5f5f5;
}
.qc-link:hover { text-decoration: underline; }
</style>
@endpush
