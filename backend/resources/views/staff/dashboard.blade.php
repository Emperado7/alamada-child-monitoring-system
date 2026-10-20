@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')

{{-- ── Page Title + Date ─────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <div style="color:#2e7d32;font-weight:700;font-size:.95rem">
            Welcome, {{ auth()->user()->name }}! 👋
        </div>
        <div class="text-muted small mt-1">
            Here's an overview of today at Alamada Learning Center.
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 rounded-3 px-3 py-2"
         style="background:#fff;border:1.5px solid #e0e0e0;font-size:.83rem;color:#555;
                box-shadow:0 1px 4px rgba(0,0,0,.06)">
        <i class="fas fa-calendar-alt" style="color:#2e7d32"></i>
        {{ now()->format('M d, Y (l)') }}
    </div>
</div>

{{-- ── 3 Separate Stat Cards ────────────────────────────────── --}}
@php
    $totalEnrolled = \App\Models\Child::where('status','active')->count();
    $today         = now()->toDateString();
    $presentToday  = \App\Models\Attendance::where('attendance_date',$today)
                         ->where('status','present')->count();
    $absentToday   = \App\Models\Attendance::where('attendance_date',$today)
                         ->where('status','absent')->count();
    $presentPct    = $totalEnrolled > 0 ? round($presentToday / $totalEnrolled * 100) : 0;
    $absentPct     = $totalEnrolled > 0 ? round($absentToday  / $totalEnrolled * 100) : 0;
@endphp

<div class="row g-3 mb-4">

    {{-- Total Enrolled Children --}}
    <div class="col-12 col-md-4">
        <div class="scard scard-green h-100">
            <div class="scard-icon-wrap scard-icon-green">
                {{-- Two-person silhouette --}}
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                    <circle cx="9"  cy="7"  r="3.4" fill="#2e7d32"/>
                    <circle cx="15" cy="7"  r="3.4" fill="#66bb6a"/>
                    <path d="M2 20c0-3.31 3.13-6 7-6s7 2.69 7 6"
                          stroke="#2e7d32" stroke-width="1.7" stroke-linecap="round" fill="none"/>
                    <path d="M15 14c2.3.9 3.6 2.6 4 5"
                          stroke="#66bb6a" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
            <div class="scard-body">
                <div class="scard-label">Total Enrolled Children</div>
                <div class="scard-value">{{ $totalEnrolled }}</div>
                <div class="scard-sub">All enrolled children in the system.</div>
                <a href="{{ route('staff.children.index') }}" class="scard-link">
                    View all children <i class="fas fa-arrow-right" style="font-size:.65rem;margin-left:3px"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Present Today --}}
    <div class="col-12 col-md-4">
        <div class="scard scard-green h-100">
            <div class="scard-icon-wrap scard-icon-green">
                {{-- Green solid circle with white check --}}
                <div style="width:40px;height:40px;border-radius:50%;
                            background:#2e7d32;display:flex;align-items:center;
                            justify-content:center">
                    <i class="fas fa-check" style="color:#fff;font-size:1.15rem"></i>
                </div>
            </div>
            <div class="scard-body">
                <div class="scard-label">Present Today</div>
                <div class="scard-value">{{ $presentToday }}</div>
                <div class="scard-sub">{{ $presentPct }}% of total enrolled children</div>
                <a href="{{ route('staff.attendance.index') }}" class="scard-link">
                    View attendance <i class="fas fa-arrow-right" style="font-size:.65rem;margin-left:3px"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Absent Today --}}
    <div class="col-12 col-md-4">
        <div class="scard scard-red h-100">
            <div class="scard-icon-wrap scard-icon-red">
                {{-- Red person with X --}}
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                    <circle cx="10" cy="7.5" r="3.5" fill="#e53935" opacity=".2"/>
                    <circle cx="10" cy="7.5" r="3.5" stroke="#e53935" stroke-width="1.6"/>
                    <path d="M3 20c0-3.31 3.13-6 7-6s7 2.69 7 6"
                          stroke="#e53935" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                    <line x1="17.5" y1="11" x2="21.5" y2="15" stroke="#e53935" stroke-width="2" stroke-linecap="round"/>
                    <line x1="21.5" y1="11" x2="17.5" y2="15" stroke="#e53935" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="scard-body">
                <div class="scard-label">Absent Today</div>
                <div class="scard-value">{{ $absentToday }}</div>
                <div class="scard-sub">{{ $absentPct }}% of total enrolled children</div>
                <a href="{{ route('staff.attendance.index') }}" class="scard-link">
                    View attendance <i class="fas fa-arrow-right" style="font-size:.65rem;margin-left:3px"></i>
                </a>
            </div>
        </div>
    </div>

</div>

{{-- ── Quick Access ─────────────────────────────────────────── --}}
<div class="dash-card p-4">
    <h5 class="fw-bold text-center mb-4">Quick Access</h5>

    <div class="row g-3">

        {{-- Enrollment --}}
        <div class="col-6 col-md-3">
            <div class="qcard h-100">
                <div class="qcard-icon" style="background:#e8f5e9">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                        <rect x="5" y="3" width="14" height="18" rx="2"
                              fill="#2e7d32" opacity=".15"/>
                        <rect x="5" y="3" width="14" height="18" rx="2"
                              stroke="#2e7d32" stroke-width="1.5"/>
                        <line x1="9"  y1="8"  x2="15" y2="8"  stroke="#2e7d32" stroke-width="1.4" stroke-linecap="round"/>
                        <line x1="9"  y1="11" x2="15" y2="11" stroke="#2e7d32" stroke-width="1.4" stroke-linecap="round"/>
                        <line x1="9"  y1="14" x2="13" y2="14" stroke="#2e7d32" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="qcard-title" style="color:#2e7d32">Enrollment</div>
                <div class="qcard-desc">
                    Manage child enrollments<br>and view enrolled children.
                </div>
                <div class="qcard-sub">
                    <div class="qcard-sub-icon" style="background:#e8f5e9;color:#2e7d32">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <div class="qcard-sub-title">New Enrollment</div>
                        <div class="qcard-sub-desc">Register a new child</div>
                    </div>
                </div>
                <div class="qcard-sub">
                    <div class="qcard-sub-icon" style="background:#e8f5e9;color:#2e7d32">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="qcard-sub-title">Enrolled Children</div>
                        <div class="qcard-sub-desc">View all enrolled children</div>
                    </div>
                </div>
                <a href="{{ route('staff.enrollment.index') }}" class="qcard-link" style="color:#2e7d32">
                    Go to Enrollment <i class="fas fa-arrow-right ms-1" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>

        {{-- Child Monitoring --}}
        <div class="col-6 col-md-3">
            <div class="qcard h-100">
                <div class="qcard-icon" style="background:#e3f2fd">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                        <circle cx="9"  cy="8"  r="3.5" fill="#1565c0" opacity=".25"/>
                        <circle cx="9"  cy="8"  r="3.5" stroke="#1565c0" stroke-width="1.5"/>
                        <circle cx="16" cy="8"  r="2.8" fill="#1565c0" opacity=".15"/>
                        <circle cx="16" cy="8"  r="2.8" stroke="#1565c0" stroke-width="1.3"/>
                        <path d="M2 20c0-3.31 3.13-6 7-6s7 2.69 7 6"
                              stroke="#1565c0" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                        <path d="M16 13.5c2 .8 3.2 2.4 3.5 5"
                              stroke="#1565c0" stroke-width="1.3" stroke-linecap="round" fill="none"/>
                    </svg>
                </div>
                <div class="qcard-title" style="color:#1565c0">Child Monitoring</div>
                <div class="qcard-desc">
                    View and manage child<br>information.
                </div>
                <div class="qcard-sub">
                    <div class="qcard-sub-icon" style="background:#e3f2fd;color:#1565c0">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div>
                        <div class="qcard-sub-title">View Child List</div>
                        <div class="qcard-sub-desc">See the list of all children</div>
                    </div>
                </div>
                <div class="qcard-sub">
                    <div class="qcard-sub-icon" style="background:#e3f2fd;color:#1565c0">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="qcard-sub-title">View Child Details</div>
                        <div class="qcard-sub-desc">View detailed information<br>of a child</div>
                    </div>
                </div>
                <a href="{{ route('staff.children.index') }}" class="qcard-link" style="color:#1565c0">
                    Go to Child Monitoring <i class="fas fa-arrow-right ms-1" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>

        {{-- Attendance --}}
        <div class="col-6 col-md-3">
            <div class="qcard h-100">
                <div class="qcard-icon" style="background:#fff3e0">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="17" rx="3"
                              fill="#e65100" opacity=".12"/>
                        <rect x="3" y="4" width="18" height="17" rx="3"
                              stroke="#e65100" stroke-width="1.5"/>
                        <line x1="3" y1="9" x2="21" y2="9" stroke="#e65100" stroke-width="1.3"/>
                        <line x1="8"  y1="2" x2="8"  y2="6" stroke="#e65100" stroke-width="1.8" stroke-linecap="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="#e65100" stroke-width="1.8" stroke-linecap="round"/>
                        <polyline points="8,14 11,17 16,12" stroke="#e65100" stroke-width="1.8"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="qcard-title" style="color:#e65100">Attendance</div>
                <div class="qcard-desc">
                    Record and manage<br>attendance of children.
                </div>
                <div class="qcard-sub">
                    <div class="qcard-sub-icon" style="background:#fff3e0;color:#e65100">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <div class="qcard-sub-title">Select Child</div>
                        <div class="qcard-sub-desc">Choose a child</div>
                    </div>
                </div>
                <div class="qcard-sub">
                    <div class="qcard-sub-icon" style="background:#fff3e0;color:#e65100">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    <div>
                        <div class="qcard-sub-title">Mark Attendance</div>
                        <div class="qcard-sub-desc">Mark attendance for<br>the selected child</div>
                    </div>
                </div>
                <a href="{{ route('staff.attendance.mark') }}" class="qcard-link" style="color:#e65100">
                    Go to Attendance <i class="fas fa-arrow-right ms-1" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>

        {{-- Settings --}}
        <div class="col-6 col-md-3">
            <div class="qcard h-100">
                <div class="qcard-icon" style="background:#f3e5f5">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="7" stroke="#6a1b9a" stroke-width="1.4" fill="none"/>
                        <circle cx="12" cy="12" r="2.8" fill="#6a1b9a" opacity=".3"/>
                        <circle cx="12" cy="12" r="2.8" stroke="#6a1b9a" stroke-width="1.4"/>
                        <line x1="12" y1="2"  x2="12" y2="5"  stroke="#6a1b9a" stroke-width="1.6" stroke-linecap="round"/>
                        <line x1="12" y1="19" x2="12" y2="22" stroke="#6a1b9a" stroke-width="1.6" stroke-linecap="round"/>
                        <line x1="2"  y1="12" x2="5"  y2="12" stroke="#6a1b9a" stroke-width="1.6" stroke-linecap="round"/>
                        <line x1="19" y1="12" x2="22" y2="12" stroke="#6a1b9a" stroke-width="1.6" stroke-linecap="round"/>
                        <line x1="4.9" y1="4.9"  x2="7"  y2="7"  stroke="#6a1b9a" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="17" y1="17"  x2="19.1" y2="19.1" stroke="#6a1b9a" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="19.1" y1="4.9" x2="17" y2="7"  stroke="#6a1b9a" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="7"  y1="17"  x2="4.9" y2="19.1" stroke="#6a1b9a" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="qcard-title" style="color:#6a1b9a">Settings</div>
                <div class="qcard-desc">
                    Manage your account<br>and preferences.
                </div>
                <div class="qcard-sub">
                    <div class="qcard-sub-icon" style="background:#f3e5f5;color:#6a1b9a">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <div class="qcard-sub-title">Account Settings</div>
                        <div class="qcard-sub-desc">Update your account<br>information</div>
                    </div>
                </div>
                <div class="qcard-sub">
                    <div class="qcard-sub-icon" style="background:#f3e5f5;color:#6a1b9a">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <div class="qcard-sub-title">Preferences</div>
                        <div class="qcard-sub-desc">Customize your<br>preferences</div>
                    </div>
                </div>
                <a href="{{ route('staff.settings.index') }}" class="qcard-link" style="color:#6a1b9a">
                    Go to Settings <i class="fas fa-arrow-right ms-1" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>

    </div>{{-- /row --}}
</div>{{-- /Quick Access --}}

@endsection

@push('styles')
<style>
/* ── Individual Stat Cards ───────────────────────────────── */
.scard {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    padding: 22px 22px 18px;
    display: flex;
    align-items: flex-start;
    gap: 18px;
    transition: box-shadow .2s, transform .15s;
    border: none;
}
.scard:hover { box-shadow: 0 6px 20px rgba(0,0,0,.12); transform: translateY(-2px); }

.scard-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.scard-icon-green { background: #e8f5e9; }
.scard-icon-red   { background: #ffebee; }

.scard-body { flex: 1; min-width: 0; }

.scard-label {
    font-size: .75rem;
    font-weight: 700;
    color: #9e9e9e;
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 2px;
}
.scard-value {
    font-size: 2.6rem;
    font-weight: 800;
    color: #212121;
    line-height: 1.1;
    margin-bottom: 3px;
}
.scard-sub {
    font-size: .8rem;
    color: #9e9e9e;
    margin-bottom: 10px;
}
.scard-link {
    font-size: .82rem;
    font-weight: 600;
    color: #2e7d32;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.scard-link:hover { text-decoration: underline; color: #1b5e20; }

/* ── Quick Access Cards ──────────────────────────────────── */
.qcard {
    background: #fff;
    border: 1.5px solid #f0f0f0;
    border-radius: 16px;
    padding: 20px 18px;
    display: flex;
    flex-direction: column;
    transition: box-shadow .2s, transform .15s;
}
.qcard:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.qcard-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
.qcard-title {
    font-weight: 700;
    font-size: 1rem;
    text-align: center;
    margin-bottom: 6px;
}
.qcard-desc {
    color: #757575;
    font-size: .82rem;
    text-align: center;
    margin-bottom: 14px;
    line-height: 1.45;
}
.qcard-sub {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 8px 0;
    border-top: 1px solid #f5f5f5;
}
.qcard-sub-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .82rem;
    flex-shrink: 0;
    margin-top: 1px;
}
.qcard-sub-title {
    font-weight: 600;
    font-size: .84rem;
    color: #212121;
}
.qcard-sub-desc {
    font-size: .74rem;
    color: #9e9e9e;
    margin-top: 1px;
    line-height: 1.35;
}
.qcard-link {
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    font-size: .83rem;
    text-decoration: none;
    margin-top: auto;
    padding-top: 12px;
    border-top: 1px solid #f5f5f5;
}
.qcard-link:hover { text-decoration: underline; opacity: .85; }
</style>
@endpush
