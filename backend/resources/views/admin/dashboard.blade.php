@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')

{{-- ── Page title row ────────────────────────────────────────── --}}
<div class="page-title-row d-flex justify-content-between align-items-start">
    <div>
        <h4>Dashboard</h4>
        <p>Welcome back, <strong>{{ auth()->user()->name }}</strong>!
           Here's what's happening today.</p>
    </div>
    <div class="date-badge">
        <i class="fas fa-calendar-alt" style="color:#2e7d32"></i>
        {{ now()->format('M d, Y') }} | {{ now()->format('l') }}
    </div>
</div>

{{-- ── 4 Stat Cards ───────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-child" style="color:#2e7d32"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Children</div>
                <div class="stat-value" style="color:#2e7d32">{{ $totalChildren }}</div>
                <a href="{{ route('admin.children.index') }}" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-clipboard-check" style="color:#1565c0"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Active Enrolments</div>
                <div class="stat-value" style="color:#1565c0">{{ $activeEnrollments }}</div>
                <a href="{{ route('admin.enrollment.index') }}" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-user-check" style="color:#e65100"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Present Today</div>
                <div class="stat-value" style="color:#e65100">{{ $presentToday }}</div>
                <a href="{{ route('admin.attendance.index') }}" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-file-alt" style="color:#6a1b9a"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Reports Generated</div>
                <div class="stat-value" style="color:#6a1b9a">
                    {{ \App\Models\Report::count() }}
                </div>
                <a href="{{ route('admin.reports.index') }}" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 2: Attendance Chart + Recent Activities ─────────────── --}}
<div class="row g-3 mb-4">

    {{-- Attendance Overview chart --}}
    <div class="col-lg-7">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <div>
                    <span class="dash-card-title">Attendance Overview</span>
                    <span class="text-muted small ms-2">(This Month)</span>
                </div>
                <select class="form-select form-select-sm rounded-3"
                        style="width:130px;border-color:#e0e0e0">
                    <option>{{ now()->format('M Y') }}</option>
                </select>
            </div>
            <div class="dash-card-body">
                <canvas id="attendanceChart" height="130"></canvas>

                {{-- Legend row --}}
                @php
                    $today = now()->toDateString();
                    $staffId = null;
                    $pres = \App\Models\Attendance::whereMonth('attendance_date', now()->month)
                        ->where('status','present')->count();
                    $abs  = \App\Models\Attendance::whereMonth('attendance_date', now()->month)
                        ->where('status','absent')->count();
                    $late = \App\Models\Attendance::whereMonth('attendance_date', now()->month)
                        ->where('status','late')->count();
                    $exc  = \App\Models\Attendance::whereMonth('attendance_date', now()->month)
                        ->where('status','excused')->count();
                @endphp
                <div class="row text-center mt-3 pt-2 border-top g-0">
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#2e7d32;display:inline-block"></span>
                            <span class="small text-muted">Present</span>
                        </div>
                        <div class="fw-bold" style="color:#2e7d32">{{ $pres }}</div>
                        <div class="small text-muted">children</div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#f9a825;display:inline-block"></span>
                            <span class="small text-muted">Absent</span>
                        </div>
                        <div class="fw-bold" style="color:#f9a825">{{ $abs }}</div>
                        <div class="small text-muted">children</div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#1565c0;display:inline-block"></span>
                            <span class="small text-muted">Late</span>
                        </div>
                        <div class="fw-bold" style="color:#1565c0">{{ $late }}</div>
                        <div class="small text-muted">children</div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#9e9e9e;display:inline-block"></span>
                            <span class="small text-muted">Excused</span>
                        </div>
                        <div class="fw-bold" style="color:#9e9e9e">{{ $exc }}</div>
                        <div class="small text-muted">children</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="col-lg-5">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <span class="dash-card-title">Recent Activities</span>
                <a href="{{ route('admin.enrollment.index') }}"
                   style="color:#2e7d32;font-size:.82rem;font-weight:600;text-decoration:none">
                   View All
                </a>
            </div>
            <div class="dash-card-body p-0">
                @php
                    $recentActivities = collect();

                    // Recent enrollments
                    $recentEnrolls = \App\Models\Enrollment::with('child')
                        ->orderBy('created_at','desc')->take(2)->get();
                    foreach($recentEnrolls as $e) {
                        $recentActivities->push([
                            'icon'  => 'fas fa-user-plus',
                            'ibg'   => '#e8f5e9',
                            'ic'    => '#2e7d32',
                            'title' => 'New enrollment',
                            'sub'   => ($e->child->first_name ?? 'Child') . ' ' . ($e->child->last_name ?? '') . ' has been enrolled.',
                            'time'  => $e->created_at->diffForHumans(null, true, true),
                        ]);
                    }

                    // Attendance recorded today
                    $attCount = \App\Models\Attendance::whereDate('attendance_date', now()->toDateString())->count();
                    if($attCount > 0) {
                        $recentActivities->push([
                            'icon'  => 'fas fa-calendar-check',
                            'ibg'   => '#fff3e0',
                            'ic'    => '#e65100',
                            'title' => 'Attendance recorded',
                            'sub'   => $attCount . ' children marked present today.',
                            'time'  => 'Today',
                        ]);
                    }

                    // Latest report
                    $latestReport = \App\Models\Report::latest()->first();
                    if($latestReport) {
                        $recentActivities->push([
                            'icon'  => 'fas fa-file-alt',
                            'ibg'   => '#e3f2fd',
                            'ic'    => '#1565c0',
                            'title' => 'Report generated',
                            'sub'   => $latestReport->title,
                            'time'  => $latestReport->generated_at->diffForHumans(null, true, true),
                        ]);
                    }

                    // Latest notification
                    $latestNotif = \App\Models\Notification::latest()->first();
                    if($latestNotif) {
                        $recentActivities->push([
                            'icon'  => 'fas fa-bell',
                            'ibg'   => '#f3e5f5',
                            'ic'    => '#6a1b9a',
                            'title' => 'New notification sent',
                            'sub'   => \Str::limit($latestNotif->message, 55),
                            'time'  => $latestNotif->created_at->diffForHumans(null, true, true),
                        ]);
                    }

                    // Latest user
                    $latestUser = \App\Models\User::latest()->first();
                    if($latestUser) {
                        $recentActivities->push([
                            'icon'  => 'fas fa-user',
                            'ibg'   => '#e8f5e9',
                            'ic'    => '#388e3c',
                            'title' => 'New user added',
                            'sub'   => ucfirst($latestUser->role) . ' "' . $latestUser->name . '" has been added.',
                            'time'  => $latestUser->created_at->diffForHumans(null, true, true),
                        ]);
                    }
                @endphp

                <div style="padding: 4px 18px">
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
                    <div class="text-center text-muted py-4 small">
                        No recent activities.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 3: Enrollment Status + Upcoming Events ───────────────── --}}
<div class="row g-3">

    {{-- Enrollment Status --}}
    <div class="col-lg-5">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <span class="dash-card-title">Enrollment Status</span>
            </div>
            <div class="dash-card-body">
                @php
                    $enActive    = \App\Models\Enrollment::where('status','approved')->count();
                    $enPending   = \App\Models\Enrollment::where('status','pending')->count();
                    $enRejected  = \App\Models\Enrollment::where('status','rejected')->count();
                    $enTotal     = $enActive + $enPending + $enRejected;
                    $enActivePct  = $enTotal ? round($enActive  / $enTotal * 100, 1) : 0;
                    $enPendingPct = $enTotal ? round($enPending / $enTotal * 100, 1) : 0;
                    $enRejPct     = $enTotal ? round($enRejected/ $enTotal * 100, 1) : 0;
                @endphp

                <div class="d-flex align-items-center gap-4">
                    {{-- Donut chart --}}
                    <div style="width:120px;height:120px;flex-shrink:0">
                        <canvas id="enrollDonut"></canvas>
                    </div>

                    {{-- Legend --}}
                    <div class="flex-grow-1">
                        <div class="enroll-legend-item">
                            <span class="legend-dot" style="background:#2e7d32"></span>
                            <span class="small">Active</span>
                            <span class="legend-val ms-2">{{ $enActive }}</span>
                            <span class="legend-pct ms-1">({{ $enActivePct }}%)</span>
                        </div>
                        <div class="enroll-legend-item">
                            <span class="legend-dot" style="background:#f9a825"></span>
                            <span class="small">Pending</span>
                            <span class="legend-val ms-2">{{ $enPending }}</span>
                            <span class="legend-pct ms-1">({{ $enPendingPct }}%)</span>
                        </div>
                        <div class="enroll-legend-item">
                            <span class="legend-dot" style="background:#ef5350"></span>
                            <span class="small">Withdrawn</span>
                            <span class="legend-val ms-2">{{ $enRejected }}</span>
                            <span class="legend-pct ms-1">({{ $enRejPct }}%)</span>
                        </div>
                    </div>

                    {{-- Total box --}}
                    <div class="total-enroll-box" style="min-width:100px">
                        <div class="te-label">Total Enrollments</div>
                        <div class="te-value">{{ $enTotal }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Upcoming Events --}}
    <div class="col-lg-7">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <span class="dash-card-title">Upcoming Events</span>
            </div>
            <div class="dash-card-body">
                {{-- You can add real events from DB here. Showing placeholder. --}}
                <div class="event-card mb-3">
                    <div class="event-icon-box">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="event-title">Parent-Teacher Meeting</div>
                        <div class="event-sub">
                            {{ now()->addDays(5)->format('M d, Y (l)') }} &nbsp;·&nbsp;
                            1:00 PM – 4:00 PM
                        </div>
                    </div>
                    <a href="#" class="btn-view-cal">View Calendar</a>
                </div>

                <div class="event-card">
                    <div class="event-icon-box">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <div class="event-title">Monthly Activity Day</div>
                        <div class="event-sub">
                            {{ now()->endOfMonth()->format('M d, Y (l)') }} &nbsp;·&nbsp;
                            8:00 AM – 12:00 PM
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
// ── Attendance Line Chart ──────────────────────────────────────
(function() {
    // Generate labels for each day this month so far
    const daysInMonth = {{ now()->day }};
    const labels = [];
    for (let d = 1; d <= daysInMonth; d += 4) {
        labels.push('{{ now()->format("M") }} ' + d);
    }

    // Placeholder data — replace with real DB data if needed
    const presentData = {!! json_encode(
        \App\Models\Attendance::whereMonth('attendance_date', now()->month)
            ->selectRaw('DAY(attendance_date) as day, count(*) as total')
            ->where('status','present')
            ->groupBy('day')->orderBy('day')
            ->pluck('total','day')->values()
    ) !!};

    const allDays = Array.from({length: daysInMonth}, (_, i) => i + 1);
    const presMap = {!! json_encode(
        \App\Models\Attendance::whereMonth('attendance_date', now()->month)
            ->where('status','present')
            ->selectRaw('DAY(attendance_date) as d, count(*) as c')
            ->groupBy('d')->pluck('c','d')
    ) !!};

    const presArr = allDays.map(d => presMap[d] || 0);
    const dayLabels = allDays.map(d => '{{ now()->format("M") }} ' + d);

    new Chart(document.getElementById('attendanceChart'), {
        type: 'line',
        data: {
            labels: dayLabels,
            datasets: [{
                label: 'Present',
                data: presArr,
                borderColor: '#2e7d32',
                backgroundColor: 'rgba(46,125,50,.1)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#2e7d32',
                pointRadius: 3,
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    ticks: {
                        maxTicksLimit: 8,
                        font: { size: 10 },
                        color: '#9e9e9e'
                    },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 10, font: { size: 10 }, color: '#9e9e9e' },
                    grid: { color: '#f0f0f0' }
                }
            }
        }
    });
})();

// ── Enrollment Donut ──────────────────────────────────────────
new Chart(document.getElementById('enrollDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Active','Pending','Withdrawn'],
        datasets: [{
            data: [{{ $enActive }}, {{ $enPending }}, {{ $enRejected }}],
            backgroundColor: ['#2e7d32','#f9a825','#ef5350'],
            borderWidth: 2,
            borderColor: '#fff',
            cutout: '70%'
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        animation: { duration: 800 }
    }
});
</script>
@endpush
