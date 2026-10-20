@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@push('styles')
<style>
/* ── Dashboard-specific overrides ───────────────────────── */

/* Page header */
.dash-page-header { margin-bottom: 22px; }
.dash-page-header h4 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 3px;
}
.dash-page-header p { color: #757575; font-size: .88rem; margin: 0; }
.dash-page-header p strong { color: #2e7d32; }
.dash-date-badge {
    background: #fff;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    padding: 7px 14px;
    font-size: .83rem;
    color: #555;
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}

/* ── Stat cards ──────────────────────────────────────────── */
.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    display: flex;
    align-items: center;
    gap: 18px;
    height: 100%;
    transition: transform .2s, box-shadow .2s;
    border: none;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.11); }
.stat-icon {
    width: 58px; height: 58px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.55rem;
    flex-shrink: 0;
}
.stat-icon.green  { background: #e8f5e9; }
.stat-icon.blue   { background: #e3f2fd; }
.stat-icon.orange { background: #fff3e0; }
.stat-icon.purple { background: #f3e5f5; }
.stat-body { flex-grow: 1; min-width: 0; }
.stat-label {
    font-size: .72rem; font-weight: 700;
    color: #9e9e9e; letter-spacing: .08em;
    text-transform: uppercase; margin-bottom: 3px;
}
.stat-value {
    font-size: 2.2rem; font-weight: 800;
    line-height: 1.1; margin-bottom: 5px;
}
.stat-link {
    font-size: .78rem; color: #9e9e9e;
    text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
    transition: color .15s;
}
.stat-link:hover { color: var(--green-main); }

/* ── Dash cards (chart / activities / enrollment / events) ── */
.dash-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    overflow: hidden;
    height: 100%;
}
.dash-card-header {
    padding: 16px 20px 12px;
    border-bottom: 1px solid #f0f0f0;
    display: flex; align-items: center; justify-content: space-between;
}
.dash-card-title {
    font-weight: 700; font-size: 1rem; color: #1a1a1a;
}
.dash-card-body { padding: 18px 20px; }

/* ── Activity list ──────────────────────────────────────── */
.activity-item {
    display: flex; align-items: flex-start;
    gap: 12px; padding: 11px 0;
    border-bottom: 1px solid #f5f5f5;
}
.activity-item:last-child { border-bottom: none; }
.activity-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.activity-text { flex: 1; min-width: 0; }
.activity-text .at { font-size: .88rem; font-weight: 600; color: #1a1a1a; }
.activity-text .as { font-size: .78rem; color: #757575; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.activity-time { margin-left: auto; font-size: .73rem; color: #bdbdbd; white-space: nowrap; padding-left: 8px; }

/* ── Enrollment donut legend ─────────────────────────────── */
.enroll-legend-item {
    display: flex; align-items: center;
    gap: 8px; margin-bottom: 9px; font-size: .86rem;
}
.legend-dot { width: 13px; height: 13px; border-radius: 50%; flex-shrink: 0; }
.legend-name { flex: 1; color: #555; }
.legend-val { font-weight: 700; color: #1a1a1a; }
.legend-pct { color: #9e9e9e; font-size: .78rem; }

.total-enroll-box {
    background: var(--green-pale);
    border-radius: 14px;
    padding: 16px 18px;
    text-align: center;
    border: 1.5px solid #c8e6c9;
    min-width: 110px;
}
.total-enroll-box .te-label { font-size: .75rem; color: #757575; margin-bottom: 2px; }
.total-enroll-box .te-value { font-size: 2rem; font-weight: 800; color: var(--green-dark); }

/* ── Event cards ─────────────────────────────────────────── */
.event-card {
    background: #f9f9f9;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex; align-items: center; gap: 14px;
}
.event-icon-box {
    width: 46px; height: 46px;
    background: var(--green-pale);
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; color: var(--green-main);
    flex-shrink: 0;
}
.event-title { font-weight: 700; font-size: .9rem; color: #1a1a1a; }
.event-sub   { font-size: .78rem; color: #757575; margin-top: 3px; }
.btn-view-cal {
    margin-left: auto;
    background: var(--green-main);
    color: #fff; border: none;
    border-radius: 9px;
    padding: 8px 16px;
    font-size: .8rem; font-weight: 700;
    cursor: pointer; white-space: nowrap;
    text-decoration: none;
    transition: background .15s;
    flex-shrink: 0;
}
.btn-view-cal:hover { background: var(--green-dark); color: #fff; }

/* ── Attendance chart month selector ────────────────────── */
.month-select {
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: .82rem;
    color: #555;
    background: #fff;
    outline: none;
    cursor: pointer;
}

/* ── Footer ─────────────────────────────────────────────── */
.dash-footer {
    text-align: center;
    padding: 20px 0 0;
    font-size: .78rem;
    color: #bdbdbd;
}
.dash-footer a { color: var(--green-main); text-decoration: none; }
</style>
@endpush

@section('content')

{{-- ══ Page header ══════════════════════════════════════════════ --}}
<div class="dash-page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h4>Dashboard</h4>
        <p>Welcome back, <strong>{{ auth()->user()->name }}</strong>!
           Here's what's happening today.</p>
    </div>
    <div class="dash-date-badge">
        <i class="fas fa-calendar-alt" style="color:#2e7d32"></i>
        {{ now()->format('M d, Y') }} &nbsp;|&nbsp; {{ now()->format('l') }}
    </div>
</div>

{{-- ══ Stat cards ══════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Total Children --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-child" style="color:#2e7d32"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Children</div>
                <div class="stat-value" style="color:#2e7d32">{{ $totalChildren }}</div>
                <a href="{{ route('admin.children.index') }}" class="stat-link">
                    View Details &nbsp;<i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Active Enrolments --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-clipboard-check" style="color:#1565c0"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Active Enrolments</div>
                <div class="stat-value" style="color:#1565c0">{{ $activeEnrollments }}</div>
                <a href="{{ route('admin.enrollment.index') }}" class="stat-link">
                    View Details &nbsp;<i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Present Today --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-user-check" style="color:#e65100"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Present Today</div>
                <div class="stat-value" style="color:#e65100">{{ $presentToday }}</div>
                <a href="{{ route('admin.attendance.index') }}" class="stat-link">
                    View Details &nbsp;<i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Reports Generated --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-file-alt" style="color:#6a1b9a"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Reports Generated</div>
                <div class="stat-value" style="color:#6a1b9a">{{ $totalReports }}</div>
                <a href="{{ route('admin.reports.index') }}" class="stat-link">
                    View Details &nbsp;<i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ══ Row 2 : Attendance Chart + Recent Activities ════════════ --}}
<div class="row g-3 mb-4">

    {{-- Attendance Overview --}}
    <div class="col-lg-7">
        <div class="dash-card">
            <div class="dash-card-header">
                <div>
                    <span class="dash-card-title">Attendance Overview</span>
                    <span class="text-muted small ms-2">(This Month)</span>
                </div>
                <select class="month-select">
                    <option>{{ now()->format('M Y') }}</option>
                </select>
            </div>
            <div class="dash-card-body">

                {{-- Line chart --}}
                <canvas id="attendanceChart" height="130"></canvas>

                {{-- Legend row --}}
                <div class="row text-center mt-3 pt-3 border-top g-0">
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#2e7d32;display:inline-block"></span>
                            <span class="small text-muted">Present</span>
                        </div>
                        <div class="fw-bold fs-5" style="color:#2e7d32">{{ $presMonth }}</div>
                        <div class="small text-muted">children</div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#f9a825;display:inline-block"></span>
                            <span class="small text-muted">Absent</span>
                        </div>
                        <div class="fw-bold fs-5" style="color:#f9a825">{{ $absMonth }}</div>
                        <div class="small text-muted">children</div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#1565c0;display:inline-block"></span>
                            <span class="small text-muted">Late</span>
                        </div>
                        <div class="fw-bold fs-5" style="color:#1565c0">{{ $lateMonth }}</div>
                        <div class="small text-muted">children</div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#9e9e9e;display:inline-block"></span>
                            <span class="small text-muted">Excused</span>
                        </div>
                        <div class="fw-bold fs-5" style="color:#9e9e9e">{{ $excusedMonth }}</div>
                        <div class="small text-muted">children</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="col-lg-5">
        <div class="dash-card">
            <div class="dash-card-header">
                <span class="dash-card-title">Recent Activities</span>
                <a href="{{ route('admin.enrollment.index') }}"
                   style="color:#2e7d32;font-size:.82rem;font-weight:600;text-decoration:none">
                   View All
                </a>
            </div>
            <div class="dash-card-body p-0">
                <div style="padding: 6px 20px">
                    @forelse($recentActivities->take(5) as $act)
                    <div class="activity-item">
                        <div class="activity-icon"
                             style="background:{{ $act['ibg'] }};color:{{ $act['ic'] }}">
                            <i class="{{ $act['icon'] }}"></i>
                        </div>
                        <div class="activity-text">
                            <div class="at">{{ $act['title'] }}</div>
                            <div class="as">{{ $act['sub'] }}</div>
                        </div>
                        <div class="activity-time">{{ $act['time'] }}</div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4 small">No recent activities.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Row 3 : Enrollment Status + Upcoming Events ════════════ --}}
<div class="row g-3">

    {{-- Enrollment Status --}}
    <div class="col-lg-5">
        <div class="dash-card">
            <div class="dash-card-header">
                <span class="dash-card-title">Enrollment Status</span>
            </div>
            <div class="dash-card-body">
                <div class="d-flex align-items-center gap-4 flex-wrap">

                    {{-- Donut --}}
                    <div style="width:130px;height:130px;flex-shrink:0">
                        <canvas id="enrollDonut"></canvas>
                    </div>

                    {{-- Legend --}}
                    <div class="flex-grow-1">
                        <div class="enroll-legend-item">
                            <span class="legend-dot" style="background:#2e7d32"></span>
                            <span class="legend-name">Active</span>
                            <span class="legend-val">{{ $enActive }}</span>
                            <span class="legend-pct">&nbsp;({{ $enActivePct }}%)</span>
                        </div>
                        <div class="enroll-legend-item">
                            <span class="legend-dot" style="background:#f9a825"></span>
                            <span class="legend-name">Pending</span>
                            <span class="legend-val">{{ $enPending }}</span>
                            <span class="legend-pct">&nbsp;({{ $enPendingPct }}%)</span>
                        </div>
                        <div class="enroll-legend-item">
                            <span class="legend-dot" style="background:#ef5350"></span>
                            <span class="legend-name">Withdrawn</span>
                            <span class="legend-val">{{ $enRejected }}</span>
                            <span class="legend-pct">&nbsp;({{ $enRejPct }}%)</span>
                        </div>
                    </div>

                    {{-- Total box --}}
                    <div class="total-enroll-box">
                        <div class="te-label">Total Enrollments</div>
                        <div class="te-value">{{ $enTotal }}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Upcoming Events --}}
    <div class="col-lg-7">
        <div class="dash-card">
            <div class="dash-card-header">
                <span class="dash-card-title">Upcoming Events</span>
            </div>
            <div class="dash-card-body d-flex flex-column gap-3">

                <div class="event-card">
                    <div class="event-icon-box">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="event-title">Parent-Teacher Meeting</div>
                        <div class="event-sub">
                            {{ now()->addDays(5)->format('M d, Y (l)') }}
                            &nbsp;&middot;&nbsp; 1:00 PM – 4:00 PM
                        </div>
                    </div>
                    <a href="#" class="btn-view-cal">View Calendar</a>
                </div>

                <div class="event-card">
                    <div class="event-icon-box">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="event-title">Monthly Activity Day</div>
                        <div class="event-sub">
                            {{ now()->endOfMonth()->format('M d, Y (l)') }}
                            &nbsp;&middot;&nbsp; 8:00 AM – 12:00 PM
                        </div>
                    </div>
                    <a href="#" class="btn-view-cal">View Calendar</a>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Attendance Line Chart ─────────────────────────────────────
(function () {
    const labels = @json($chartLabels);
    const data   = @json($chartData);

    new Chart(document.getElementById('attendanceChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Present',
                data,
                borderColor: '#2e7d32',
                backgroundColor: 'rgba(46,125,50,.10)',
                borderWidth: 2.5,
                tension: 0.45,
                fill: true,
                pointBackgroundColor: '#2e7d32',
                pointBorderColor: '#fff',
                pointBorderWidth: 1.5,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1b5e20',
                    titleColor: '#fff',
                    bodyColor: '#c8e6c9',
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxTicksLimit: 9,
                        font: { size: 10 },
                        color: '#9e9e9e',
                    },
                    grid: { display: false },
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 10, font: { size: 10 }, color: '#9e9e9e' },
                    grid: { color: '#f5f5f5', drawBorder: false },
                }
            }
        }
    });
})();

// ── Enrollment Donut ──────────────────────────────────────────
new Chart(document.getElementById('enrollDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Pending', 'Withdrawn'],
        datasets: [{
            data: [{{ $enActive }}, {{ $enPending }}, {{ $enRejected }}],
            backgroundColor: ['#2e7d32', '#f9a825', '#ef5350'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 4,
            cutout: '72%',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1b5e20',
                titleColor: '#fff',
                bodyColor: '#c8e6c9',
                padding: 10,
                cornerRadius: 8,
            }
        },
        animation: { duration: 900 }
    }
});
</script>
@endpush
