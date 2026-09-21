@extends('layouts.app')
@section('title','Reports')
@section('page-title','Reports')

@section('content')

{{-- ── Breadcrumb + Title ───────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="fw-bold mb-1">Reports Dashboard</h4>
        <nav style="font-size:.83rem">
            <a href="{{ route('admin.dashboard') }}"
               style="color:#2e7d32;text-decoration:none">Dashboard</a>
            <span class="text-muted mx-1">›</span>
            <span class="text-muted">Reports</span>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.enrollment') }}"
           class="btn btn-success rounded-3 px-3 py-2 fw-semibold"
           style="background:#2e7d32;border:none;font-size:.88rem">
            <i class="fas fa-download me-2"></i>Export Reports
        </a>
    </div>
</div>

{{-- ── 4 Stat Cards ─────────────────────────────────────────── --}}
@php
    $totalReports      = \App\Models\Report::count();
    $enrollmentReports = \App\Models\Report::where('report_type','enrollment')->count();
    $attendanceReports = \App\Models\Report::where('report_type','attendance')->count();
    $otherReports      = \App\Models\Report::whereNotIn('report_type',['enrollment','attendance'])->count();
    $thisMonth         = now()->format('M Y');
@endphp

<div class="row g-3 mb-4">

    {{-- Total Reports --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-file-alt" style="color:#2e7d32;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Reports Generated</div>
                <div class="stat-value" style="color:#2e7d32">{{ $totalReports }}</div>
                <div class="text-muted" style="font-size:.78rem">This Month</div>
                <a href="#reportsTable" class="stat-link mt-1">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Enrollment Reports --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-user-plus" style="color:#1565c0;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Enrollment Reports</div>
                <div class="stat-value" style="color:#1565c0">{{ $enrollmentReports }}</div>
                <div class="text-muted" style="font-size:.78rem">This Month</div>
                <a href="{{ route('admin.reports.enrollment') }}" class="stat-link mt-1">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Attendance Reports --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-calendar-check" style="color:#e65100;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Attendance Reports</div>
                <div class="stat-value" style="color:#e65100">{{ $attendanceReports }}</div>
                <div class="text-muted" style="font-size:.78rem">This Month</div>
                <a href="{{ route('admin.reports.attendance') }}" class="stat-link mt-1">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Other Reports --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-chart-bar" style="color:#6a1b9a;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Other Reports</div>
                <div class="stat-value" style="color:#6a1b9a">{{ $otherReports }}</div>
                <div class="text-muted" style="font-size:.78rem">This Month</div>
                <a href="#reportsTable" class="stat-link mt-1">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Quick Generate Buttons ───────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="dash-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px;height:46px;border-radius:12px;background:#e3f2fd;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-user-plus" style="color:#1565c0;font-size:1.2rem"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">Enrollment Report</div>
                    <div class="text-muted" style="font-size:.75rem">Total enrolled with filters</div>
                </div>
                <a href="{{ route('admin.reports.enrollment') }}"
                   class="btn btn-sm rounded-3 px-3"
                   style="background:#e3f2fd;color:#1565c0;border:none;font-weight:600;font-size:.8rem">
                    Generate
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dash-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px;height:46px;border-radius:12px;background:#fff3e0;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-calendar-check" style="color:#e65100;font-size:1.2rem"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">Attendance Report</div>
                    <div class="text-muted" style="font-size:.75rem">Monthly attendance summary</div>
                </div>
                <a href="{{ route('admin.reports.attendance') }}"
                   class="btn btn-sm rounded-3 px-3"
                   style="background:#fff3e0;color:#e65100;border:none;font-weight:600;font-size:.8rem">
                    Generate
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dash-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px;height:46px;border-radius:12px;background:#e8f5e9;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-child" style="color:#2e7d32;font-size:1.2rem"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">Child Information Report</div>
                    <div class="text-muted" style="font-size:.75rem">All children records</div>
                </div>
                <a href="{{ route('admin.reports.enrollment') }}?report=children"
                   class="btn btn-sm rounded-3 px-3"
                   style="background:#e8f5e9;color:#2e7d32;border:none;font-weight:600;font-size:.8rem">
                    Generate
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Reports Table ────────────────────────────────────────── --}}
<div class="dash-card" id="reportsTable">

    {{-- Toolbar --}}
    <div class="dash-card-header flex-wrap gap-2">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">

            {{-- Date Range --}}
            <div class="d-flex align-items-center gap-2">
                <label class="small text-muted mb-0 fw-semibold">Date Range</label>
                <div class="input-group" style="width:220px">
                    <input type="date" name="date_from"
                           value="{{ request('date_from', now()->startOfMonth()->toDateString()) }}"
                           class="form-control form-control-sm">
                    <span class="input-group-text bg-white px-1 border-start-0 border-end-0"
                          style="font-size:.75rem;color:#9e9e9e">—</span>
                    <input type="date" name="date_to"
                           value="{{ request('date_to', now()->toDateString()) }}"
                           class="form-control form-control-sm">
                    <span class="input-group-text bg-white border-start-0">
                        <i class="fas fa-calendar-alt text-muted small"></i>
                    </span>
                </div>
            </div>

            {{-- Report Type --}}
            <label class="small text-muted mb-0 fw-semibold">Report Type</label>
            <select name="report_type"
                    class="form-select form-select-sm rounded-2"
                    style="width:140px">
                <option value="">All Types</option>
                @foreach(['enrollment'=>'Enrollment','attendance'=>'Attendance','activity'=>'Activity','summary'=>'Summary'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('report_type')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>

            {{-- Generated By --}}
            <label class="small text-muted mb-0 fw-semibold">Generated By</label>
            <select name="generated_by"
                    class="form-select form-select-sm rounded-2"
                    style="width:130px">
                <option value="">All Users</option>
                @foreach(\App\Models\User::whereIn('role',['admin','staff'])->orderBy('name')->get() as $u)
                    <option value="{{ $u->id }}" {{ request('generated_by')==$u->id?'selected':'' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>

            {{-- Search --}}
            <div class="d-flex align-items-center gap-2 ms-auto">
                <div class="input-group" style="max-width:220px">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search reports...">
                    <button class="btn btn-success btn-sm"
                            style="background:#2e7d32;border-color:#2e7d32">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary rounded-2 dropdown-toggle"
                            data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>Filters
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2">
                        @foreach([''=>'All Status','generated'=>'Completed','pending'=>'Pending','failed'=>'Failed'] as $v=>$l)
                        <li>
                            <a class="dropdown-item rounded-2 small {{ request('status')==$v?'fw-bold':'' }}"
                               href="{{ request()->fullUrlWithQuery(['status'=>$v,'page'=>1]) }}">
                                @if($v=='generated') <span class="me-2" style="color:#2e7d32">●</span>
                                @elseif($v=='pending') <span class="me-2" style="color:#e65100">●</span>
                                @elseif($v=='failed') <span class="me-2" style="color:#c62828">●</span>
                                @else <span class="me-2" style="color:#9e9e9e">●</span>
                                @endif
                                {{ $l }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Table ──────────────────────────────────────────────── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-2 small fw-bold" style="color:#2e7d32">Report Name</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Report Type</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Date Generated</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Generated By</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Format</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-2 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                @php
                    $typeIcons = [
                        'enrollment' => ['icon'=>'fa-user-plus',  'bg'=>'#e3f2fd','color'=>'#1565c0'],
                        'attendance' => ['icon'=>'fa-calendar-check','bg'=>'#fff3e0','color'=>'#e65100'],
                        'activity'   => ['icon'=>'fa-puzzle-piece','bg'=>'#f3e5f5','color'=>'#6a1b9a'],
                        'summary'    => ['icon'=>'fa-chart-bar',  'bg'=>'#e8f5e9','color'=>'#2e7d32'],
                    ];
                    $ti = $typeIcons[$r->report_type] ?? ['icon'=>'fa-file-alt','bg'=>'#f5f5f5','color'=>'#757575'];

                    // Report name label
                    $nameMap = [
                        'enrollment' => 'Enrollment Summary Report',
                        'attendance' => 'Attendance Summary Report',
                        'activity'   => 'Activity Report',
                        'summary'    => 'Summary Statistics Report',
                    ];
                    $reportName = $r->title ?? ($nameMap[$r->report_type] ?? 'Report');
                @endphp
                <tr>
                    {{-- Report Name --}}
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:8px;
                                        background:{{ $ti['bg'] }};color:{{ $ti['color'] }};
                                        display:flex;align-items:center;justify-content:center;
                                        flex-shrink:0">
                                <i class="fas {{ $ti['icon'] }}" style="font-size:.85rem"></i>
                            </div>
                            <div class="fw-semibold small">{{ $reportName }}</div>
                        </div>
                    </td>

                    {{-- Report Type --}}
                    <td>
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:{{ $ti['bg'] }};color:{{ $ti['color'] }};font-size:.78rem">
                            {{ ucfirst($r->report_type) }}
                        </span>
                    </td>

                    {{-- Date Generated --}}
                    <td class="small text-muted">
                        {{ $r->generated_at->format('M d, Y h:i A') }}
                    </td>

                    {{-- Generated By --}}
                    <td class="small">{{ $r->generatedBy->name ?? 'Admin' }}</td>

                    {{-- Format --}}
                    <td>
                        @php
                            $fmtColor = $r->format === 'pdf' ? ['bg'=>'#ffebee','c'=>'#c62828']
                                : ($r->format === 'excel' ? ['bg'=>'#e8f5e9','c'=>'#2e7d32']
                                : ['bg'=>'#f5f5f5','c'=>'#757575']);
                        @endphp
                        <span class="badge rounded-2 px-3 py-2 fw-bold"
                              style="background:{{ $fmtColor['bg'] }};color:{{ $fmtColor['c'] }};font-size:.78rem">
                            {{ strtoupper($r->format ?? 'PDF') }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:#e8f5e9;color:#2e7d32;font-size:.78rem">
                            Completed
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            {{-- View --}}
                            <a href="{{ $r->report_type === 'enrollment'
                                ? route('admin.reports.enrollment')
                                : route('admin.reports.attendance') }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e3f2fd;color:#1565c0;border:none"
                               title="View">
                                <i class="fas fa-eye" style="font-size:.8rem"></i>
                            </a>
                            {{-- Download / Print --}}
                            <button class="btn btn-sm rounded-2 p-1 px-2"
                                    style="background:#e8f5e9;color:#2e7d32;border:none"
                                    title="Download"
                                    onclick="window.print()">
                                <i class="fas fa-download" style="font-size:.8rem"></i>
                            </button>
                            {{-- Delete --}}
                            <form method="POST"
                                  action="{{ route('admin.reports.destroy', $r) }}"
                                  onsubmit="return confirm('Delete this report?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm rounded-2 p-1 px-2"
                                        style="background:#ffebee;color:#c62828;border:none"
                                        title="Delete">
                                    <i class="fas fa-trash" style="font-size:.8rem"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="color:#bdbdbd">
                            <i class="fas fa-file-alt fa-3x mb-3 d-block"></i>
                            <div class="fw-semibold">No reports generated yet.</div>
                            <div class="small text-muted mt-2">
                                Click <strong>Generate</strong> above to create your first report.
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────── --}}
    @if($reports->hasPages())
    <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="small text-muted">
            Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }}
            of {{ $reports->total() }} entries
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">
                @if($reports->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#bdbdbd">«</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $reports->previousPageUrl() }}"
                       style="background:#f5f5f5;color:#2e7d32">«</a>
                </li>
                @endif

                @foreach($reports->getUrlRange(1,$reports->lastPage()) as $page => $url)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0" href="{{ $url }}"
                       style="{{ $page==$reports->currentPage()
                           ? 'background:#2e7d32;color:#fff'
                           : 'background:#f5f5f5;color:#212121' }}">
                        {{ $page }}
                    </a>
                </li>
                @endforeach

                @if($reports->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $reports->nextPageUrl() }}"
                       style="background:#f5f5f5;color:#2e7d32">»</a>
                </li>
                @else
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#bdbdbd">»</span>
                </li>
                @endif
            </ul>
        </nav>
    </div>
    @else
    <div class="px-3 py-3 border-top small text-muted">
        Showing {{ $reports->count() }} entries
    </div>
    @endif
</div>

@endsection
