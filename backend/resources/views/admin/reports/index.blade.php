@extends('layouts.app')
@section('title','Reports')
@section('page-title','Reports')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Reports Dashboard</h4>
        <nav style="font-size:.83rem">
            <a href="{{ route('admin.dashboard') }}"
               style="color:#2e7d32;text-decoration:none">Dashboard</a>
            <span class="text-muted mx-1">›</span>
            <span class="text-muted">Reports</span>
        </nav>
    </div>
    <a href="{{ route('admin.reports.enrollment') }}"
       class="btn rounded-3 px-4 py-2 fw-semibold d-flex align-items-center gap-2"
       style="background:#2e7d32;color:#fff;border:none;font-size:.88rem">
        <i class="fas fa-download"></i> Export Reports
    </a>
</div>

{{-- ── 4 Stat Cards ─────────────────────────────────────────── --}}
@php
    $totalReports      = \App\Models\Report::count();
    $enrollmentReports = \App\Models\Report::where('report_type','enrollment')->count();
    $attendanceReports = \App\Models\Report::where('report_type','attendance')->count();
    $otherReports      = \App\Models\Report::whereNotIn('report_type',['enrollment','attendance'])->count();
@endphp

<div class="row g-3 mb-4">

    {{-- Total Reports Generated --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-file-alt" style="color:#2e7d32;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Reports Generated</div>
                <div class="stat-value" style="color:#2e7d32">{{ $totalReports }}</div>
                <div class="text-muted" style="font-size:.78rem;margin-bottom:3px">This Month</div>
                <a href="#reportsTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Enrollment Reports --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-user-friends" style="color:#1565c0;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Enrollment Reports</div>
                <div class="stat-value" style="color:#1565c0">{{ $enrollmentReports }}</div>
                <div class="text-muted" style="font-size:.78rem;margin-bottom:3px">This Month</div>
                <a href="{{ route('admin.reports.enrollment') }}" class="stat-link">
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
                <div class="text-muted" style="font-size:.78rem;margin-bottom:3px">This Month</div>
                <a href="{{ route('admin.reports.attendance') }}" class="stat-link">
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
                <div class="text-muted" style="font-size:.78rem;margin-bottom:3px">This Month</div>
                <a href="#reportsTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Reports Table ────────────────────────────────────────── --}}
<div class="dash-card" id="reportsTable">

    {{-- Toolbar --}}
    <div class="dash-card-header flex-wrap gap-2">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap w-100">

            {{-- Date Range --}}
            <label class="small text-muted mb-0 fw-semibold">Date Range</label>
            <div class="input-group" style="width:240px">
                <input type="date" name="date_from"
                       value="{{ request('date_from', now()->startOfMonth()->toDateString()) }}"
                       class="form-control form-control-sm">
                <span class="input-group-text bg-white px-1 border-start-0 border-end-0"
                      style="font-size:.75rem;color:#9e9e9e">–</span>
                <input type="date" name="date_to"
                       value="{{ request('date_to', now()->toDateString()) }}"
                       class="form-control form-control-sm border-start-0">
                <span class="input-group-text bg-white border-start-0"
                      style="cursor:pointer">
                    <i class="fas fa-calendar-alt" style="color:#9e9e9e;font-size:.8rem"></i>
                </span>
            </div>

            {{-- Report Type --}}
            <label class="small text-muted mb-0 fw-semibold">Report Type</label>
            <select name="report_type"
                    class="form-select form-select-sm rounded-2"
                    style="width:140px">
                <option value="">All Types</option>
                @foreach(['enrollment'=>'Enrollment','attendance'=>'Attendance','activity'=>'Activity','other'=>'Other'] as $v=>$l)
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

            {{-- Search + Filters --}}
            <div class="d-flex align-items-center gap-2 ms-auto">
                <div class="input-group" style="max-width:230px">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search reports...">
                    <button class="btn btn-success btn-sm"
                            style="background:#2e7d32;border-color:#2e7d32"
                            type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary rounded-2 dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>Filters
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2"
                        style="min-width:160px">
                        @foreach([
                            ''           => ['All Status',  '#9e9e9e'],
                            'generated'  => ['Completed',   '#2e7d32'],
                            'pending'    => ['Pending',     '#e65100'],
                            'failed'     => ['Failed',      '#c62828'],
                        ] as $val => [$label, $dot])
                        <li>
                            <a class="dropdown-item rounded-2 small py-2
                                      {{ request('status')==$val ? 'fw-bold' : '' }}"
                               href="{{ request()->fullUrlWithQuery(['status'=>$val,'page'=>1]) }}">
                                <span class="me-2" style="color:{{ $dot }}">●</span>
                                {{ $label }}
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
        <table class="table table-hover align-middle mb-0" style="min-width:860px">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-3 small fw-bold" style="color:#2e7d32">Report Name</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Report Type</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Date Generated</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Generated By</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Format</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-3 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                @php
                    // Icon + colors per type
                    $typeMap = [
                        'enrollment' => ['icon'=>'fa-file-alt',      'bg'=>'#e8f5e9','color'=>'#2e7d32'],
                        'attendance' => ['icon'=>'fa-calendar-check', 'bg'=>'#fff3e0','color'=>'#e65100'],
                        'activity'   => ['icon'=>'fa-puzzle-piece',   'bg'=>'#f3e5f5','color'=>'#6a1b9a'],
                        'summary'    => ['icon'=>'fa-chart-bar',      'bg'=>'#e3f2fd','color'=>'#1565c0'],
                        'other'      => ['icon'=>'fa-file-alt',       'bg'=>'#f3e5f5','color'=>'#6a1b9a'],
                    ];
                    $ti = $typeMap[$r->report_type] ?? ['icon'=>'fa-file-alt','bg'=>'#f5f5f5','color'=>'#757575'];

                    // Friendly report name
                    $nameMap = [
                        'enrollment' => 'Enrollment Summary Report',
                        'attendance' => 'Attendance Summary Report',
                        'activity'   => 'Activity Report',
                        'summary'    => 'Summary Statistics Report',
                        'other'      => 'Child Information Report',
                    ];
                    $reportName = $r->title ?? ($nameMap[$r->report_type] ?? 'Report');

                    // Format badge
                    $fmt = strtolower($r->format ?? 'pdf');
                    $fmtBadge = $fmt === 'excel'
                        ? ['bg'=>'#e8f5e9','c'=>'#2e7d32','label'=>'Excel']
                        : ['bg'=>'#ffebee','c'=>'#c62828','label'=>'PDF'];
                @endphp
                <tr>
                    {{-- Report Name with icon --}}
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:8px;
                                        background:{{ $ti['bg'] }};color:{{ $ti['color'] }};
                                        display:flex;align-items:center;justify-content:center;
                                        flex-shrink:0">
                                <i class="fas {{ $ti['icon'] }}" style="font-size:.85rem"></i>
                            </div>
                            <span class="fw-semibold small">{{ $reportName }}</span>
                        </div>
                    </td>

                    {{-- Report Type (plain text) --}}
                    <td class="small text-muted">{{ ucfirst($r->report_type) }}</td>

                    {{-- Date Generated --}}
                    <td class="small text-muted">
                        {{ \Carbon\Carbon::parse($r->generated_at ?? $r->created_at)->format('M d, Y h:i A') }}
                    </td>

                    {{-- Generated By --}}
                    <td class="small">{{ optional($r->generatedBy)->name ?? 'Admin' }}</td>

                    {{-- Format badge --}}
                    <td>
                        <span class="badge rounded-2 px-3 py-2 fw-bold"
                              style="background:{{ $fmtBadge['bg'] }};color:{{ $fmtBadge['c'] }};font-size:.78rem">
                            {{ $fmtBadge['label'] }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:#e8f5e9;color:#2e7d32;font-size:.78rem">
                            Completed
                        </span>
                    </td>

                    {{-- Actions: View + Download --}}
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
                            {{-- Download --}}
                            <a href="{{ $r->report_type === 'enrollment'
                                ? route('admin.reports.enrollment')
                                : route('admin.reports.attendance') }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e8f5e9;color:#2e7d32;border:none"
                               title="Download">
                                <i class="fas fa-download" style="font-size:.8rem"></i>
                            </a>
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
                                Click <strong>Export Reports</strong> above to create your first report.
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
                {{-- Previous --}}
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

                {{-- Pages --}}
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

                {{-- Next --}}
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
