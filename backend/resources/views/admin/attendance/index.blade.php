@extends('layouts.app')
@section('title','Attendance')
@section('page-title','Attendance')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="fw-bold mb-1">Attendance Dashboard</h4>
        <nav style="font-size:.83rem">
            <a href="{{ route('admin.dashboard') }}"
               style="color:#2e7d32;text-decoration:none">Dashboard</a>
            <span class="text-muted mx-1">›</span>
            <span class="text-muted">Attendance</span>
        </nav>
    </div>
</div>

{{-- ── Stat Cards ───────────────────────────────────────────── --}}
@php
    $totalActive  = \App\Models\Child::where('status','active')->count();
    $presentCount = $summary['present'];
    $absentCount  = $summary['absent'];
    $todayPct     = $totalActive > 0 ? round($presentCount / $totalActive * 100) : 0;
@endphp

<div class="row g-3 mb-4">

    {{-- Today's Attendance --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-calendar-check" style="color:#2e7d32;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Today's Attendance</div>
                <div class="stat-value" style="color:#2e7d32">
                    {{ $presentCount }} / {{ $totalActive }}
                </div>
                <div class="text-muted" style="font-size:.78rem;margin-bottom:3px">
                    {{ $todayPct }}% Present
                </div>
                <a href="#attendanceTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Present Today --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-user-check" style="color:#1565c0;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Present Today</div>
                <div class="stat-value" style="color:#1565c0">{{ $presentCount }}</div>
                <a href="#attendanceTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Absent Today --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ffebee">
                <i class="fas fa-user-times" style="color:#c62828;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Absent Today</div>
                <div class="stat-value" style="color:#c62828">{{ $absentCount }}</div>
                <a href="#attendanceTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Attendance Rate --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-shopping-bag" style="color:#e65100;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Attendance Rate</div>
                <div class="stat-value" style="color:#e65100">{{ $monthRate }}%</div>
                <div class="text-muted" style="font-size:.78rem;margin-bottom:3px">This Month</div>
                <a href="#attendanceTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Table Card ───────────────────────────────────────────── --}}
<div class="dash-card" id="attendanceTable">

    {{-- Toolbar --}}
    <div class="dash-card-header flex-wrap gap-2">
        <form method="GET" id="filterForm"
              class="d-flex align-items-center gap-2 flex-wrap w-100">

            {{-- School Year --}}
            <label class="small text-muted mb-0 fw-semibold">School Year</label>
            <select name="school_year"
                    class="form-select form-select-sm rounded-2"
                    style="width:135px"
                    onchange="document.getElementById('filterForm').submit()">
                @foreach(range(now()->year, now()->year - 3) as $y)
                    <option value="{{ $y }}"
                        {{ request('school_year', now()->year) == $y ? 'selected' : '' }}>
                        {{ $y }} - {{ $y+1 }}
                    </option>
                @endforeach
            </select>

            {{-- Date --}}
            <label class="small text-muted mb-0 fw-semibold">Date</label>
            <div class="input-group" style="width:175px">
                <input type="date" name="date" value="{{ $date }}"
                       class="form-control form-control-sm"
                       onchange="document.getElementById('filterForm').submit()">
                <span class="input-group-text bg-white border-start-0"
                      style="font-size:.8rem;color:#9e9e9e;cursor:pointer"
                      onclick="this.previousElementSibling.showPicker()">
                    <i class="fas fa-calendar-alt" style="color:#2e7d32"></i>
                </span>
            </div>

            {{-- Class / Group --}}
            <label class="small text-muted mb-0 fw-semibold">Class / Group</label>
            <select name="group"
                    class="form-select form-select-sm rounded-2"
                    style="width:115px"
                    onchange="document.getElementById('filterForm').submit()">
                <option value="">All</option>
                <option value="Pre-School A" {{ request('group')=='Pre-School A'?'selected':'' }}>
                    Pre-School A
                </option>
                <option value="Pre-School B" {{ request('group')=='Pre-School B'?'selected':'' }}>
                    Pre-School B
                </option>
            </select>

            {{-- Search + Filters (pushed right) --}}
            <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
                <div class="input-group" style="max-width:230px">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search child...">
                    <button class="btn btn-success btn-sm"
                            style="background:#2e7d32;border-color:#2e7d32"
                            type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                {{-- Status Filters --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary rounded-2 dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>Filters
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2"
                        style="min-width:160px">
                        @foreach([
                            ''        => ['All Status',  '#9e9e9e'],
                            'present' => ['Present',     '#2e7d32'],
                            'absent'  => ['Absent',      '#c62828'],
                            'late'    => ['Late',        '#e65100'],
                            'excused' => ['Excused',     '#1565c0'],
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
        <table class="table table-hover align-middle mb-0" style="min-width:780px">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-3 small fw-bold" style="color:#2e7d32">ID</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Photo</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Child Name</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Class / Group</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Time In</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-3 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($children as $child)
                @php
                    $att    = $child->attendances->first();
                    $status = $att ? $att->status : null;
                    // Assign group based on id for display (placeholder — replace with real group field)
                    $group  = ($child->id % 2 === 0) ? 'Pre-School B' : 'Pre-School A';
                    // Apply group filter
                    $groupFilter = request('group');
                    if ($groupFilter && $group !== $groupFilter) continue;
                @endphp
                <tr>
                    {{-- ID --}}
                    <td class="px-3 fw-semibold small" style="color:#2e7d32">
                        CH-{{ str_pad($child->id, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- Photo --}}
                    <td class="py-2">
                        @if($child->photo)
                            <img src="{{ asset('storage/'.$child->photo) }}"
                                 class="rounded-circle object-fit-cover"
                                 width="40" height="40"
                                 style="border:2px solid #e8f5e9">
                        @else
                            <div style="width:40px;height:40px;border-radius:50%;
                                        background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                        color:#fff;display:flex;align-items:center;
                                        justify-content:center;font-weight:700;font-size:.88rem">
                                {{ strtoupper(substr($child->first_name,0,1)) }}
                            </div>
                        @endif
                    </td>

                    {{-- Child Name --}}
                    <td>
                        <div class="fw-semibold small">
                            {{ $child->first_name }} {{ $child->last_name }}
                        </div>
                        <div class="text-muted" style="font-size:.72rem">
                            {{ $child->diagnosis }}
                        </div>
                    </td>

                    {{-- Class / Group --}}
                    <td class="small text-muted">{{ $group }}</td>

                    {{-- Time In --}}
                    <td class="small">
                        @if($att && in_array($status, ['present','late']))
                            <span class="fw-semibold">
                                {{ \Carbon\Carbon::parse($att->updated_at)->format('g:i A') }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Status badge --}}
                    <td>
                        @php
                            $badgeMap = [
                                'present' => ['#e8f5e9','#2e7d32','Present'],
                                'absent'  => ['#ffebee','#c62828','Absent'],
                                'late'    => ['#fff3e0','#e65100','Late'],
                                'excused' => ['#e3f2fd','#1565c0','Excused'],
                            ];
                            $b = $badgeMap[$status] ?? ['#f5f5f5','#9e9e9e','Not Recorded'];
                        @endphp
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:{{ $b[0] }};color:{{ $b[1] }};font-size:.78rem">
                            {{ $b[2] }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            {{-- View --}}
                            <a href="{{ route('admin.attendance.show',$child) }}?month={{ $month }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e3f2fd;color:#1565c0;border:none"
                               title="View">
                                <i class="fas fa-eye" style="font-size:.8rem"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('admin.attendance.mark') }}?date={{ $date }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e8f5e9;color:#2e7d32;border:none"
                               title="Mark Attendance">
                                <i class="fas fa-pencil-alt" style="font-size:.8rem"></i>
                            </a>

                            {{-- Delete --}}
                            @php
                                $attRecord = \App\Models\Attendance::where('child_id',$child->id)
                                    ->where('attendance_date',$date)->first();
                            @endphp
                            @if($attRecord)
                            <form method="POST"
                                  action="{{ route('admin.attendance.destroy',$attRecord) }}"
                                  onsubmit="return confirm('Delete this attendance record?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm rounded-2 p-1 px-2"
                                        style="background:#ffebee;color:#c62828;border:none"
                                        title="Delete">
                                    <i class="fas fa-trash" style="font-size:.8rem"></i>
                                </button>
                            </form>
                            @else
                            <button class="btn btn-sm rounded-2 p-1 px-2"
                                    style="background:#f5f5f5;color:#bdbdbd;border:none"
                                    disabled title="No record to delete">
                                <i class="fas fa-trash" style="font-size:.8rem"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="color:#bdbdbd">
                            <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                            <div class="fw-semibold">No children found.</div>
                            <div class="small text-muted mt-1">
                                Enroll children first to track attendance.
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────── --}}
    @if($children->hasPages())
    <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="small text-muted">
            Showing {{ $children->firstItem() }} to {{ $children->lastItem() }}
            of {{ $children->total() }} entries
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">
                {{-- Previous --}}
                @if($children->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#bdbdbd">«</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $children->previousPageUrl() }}"
                       style="background:#f5f5f5;color:#2e7d32">«</a>
                </li>
                @endif

                {{-- Pages --}}
                @foreach($children->getUrlRange(1,$children->lastPage()) as $page => $url)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0" href="{{ $url }}"
                       style="{{ $page==$children->currentPage()
                           ? 'background:#2e7d32;color:#fff'
                           : 'background:#f5f5f5;color:#212121' }}">
                        {{ $page }}
                    </a>
                </li>
                @endforeach

                {{-- Next --}}
                @if($children->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $children->nextPageUrl() }}"
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
        Showing {{ $children->count() }} entries
    </div>
    @endif
</div>

@endsection
