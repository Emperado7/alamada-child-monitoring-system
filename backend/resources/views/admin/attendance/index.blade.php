@extends('layouts.app')
@section('title','Attendance')
@section('page-title','Attendance')

@section('content')

{{-- ── Breadcrumb + Title ───────────────────────────────────── --}}
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

{{-- ── 4 Stat Cards ─────────────────────────────────────────── --}}
@php
    $today         = \Carbon\Carbon::parse($date);
    $totalActive   = \App\Models\Child::where('status','active')->count();
    $presentCount  = \App\Models\Attendance::where('attendance_date',$date)
                        ->where('status','present')->count();
    $absentCount   = \App\Models\Attendance::where('attendance_date',$date)
                        ->where('status','absent')->count();

    // Monthly attendance rate
    [$yr, $mo] = explode('-', $month);
    $monthRecs = \App\Models\Attendance::whereYear('attendance_date',$yr)
                    ->whereMonth('attendance_date',$mo)->get();
    $monthRate = $monthRecs->count()
        ? round($monthRecs->where('status','present')->count() / $monthRecs->count() * 100)
        : 0;
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
                <div class="text-muted" style="font-size:.78rem">
                    {{ $totalActive > 0 ? round($presentCount/$totalActive*100) : 0 }}% Present
                </div>
                <a href="#attendanceTable" class="stat-link mt-1">
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
                <i class="fas fa-chart-pie" style="color:#e65100;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Attendance Rate</div>
                <div class="stat-value" style="color:#e65100">{{ $monthRate }}%</div>
                <div class="text-muted" style="font-size:.78rem">This Month</div>
                <a href="#attendanceTable" class="stat-link mt-1">
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
              class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">

            {{-- School Year --}}
            <label class="small text-muted mb-0 fw-semibold">School Year</label>
            <select name="school_year"
                    class="form-select form-select-sm rounded-2"
                    style="width:130px"
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
            <div class="input-group" style="width:170px">
                <input type="date" name="date" value="{{ $date }}"
                       class="form-control form-control-sm"
                       onchange="document.getElementById('filterForm').submit()">
            </div>

            {{-- Class/Group --}}
            <label class="small text-muted mb-0 fw-semibold">Class / Group</label>
            <select name="group"
                    class="form-select form-select-sm rounded-2"
                    style="width:110px"
                    onchange="document.getElementById('filterForm').submit()">
                <option value="">All</option>
                <option value="Pre-School A" {{ request('group')=='Pre-School A'?'selected':'' }}>Pre-School A</option>
                <option value="Pre-School B" {{ request('group')=='Pre-School B'?'selected':'' }}>Pre-School B</option>
            </select>

            {{-- Search --}}
            <div class="d-flex align-items-center gap-2 ms-auto">
                <div class="input-group" style="max-width:220px">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search child...">
                    <button class="btn btn-success btn-sm"
                            style="background:#2e7d32;border-color:#2e7d32">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                {{-- Filters dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary rounded-2 dropdown-toggle"
                            data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>Filters
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2">
                        @foreach([''=>'All Status','present'=>'Present','absent'=>'Absent','late'=>'Late','excused'=>'Excused'] as $val=>$label)
                        <li>
                            <a class="dropdown-item rounded-2 small {{ request('status')==$val?'fw-bold':'' }}"
                               href="{{ request()->fullUrlWithQuery(['status'=>$val,'page'=>1]) }}">
                                @if($val=='present') <span class="me-2" style="color:#2e7d32">●</span>
                                @elseif($val=='absent') <span class="me-2" style="color:#c62828">●</span>
                                @elseif($val=='late') <span class="me-2" style="color:#e65100">●</span>
                                @elseif($val=='excused') <span class="me-2" style="color:#1565c0">●</span>
                                @else <span class="me-2" style="color:#9e9e9e">●</span>
                                @endif
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
        <table class="table table-hover align-middle mb-0" style="min-width:800px">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-2 small fw-bold" style="color:#2e7d32">ID</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Photo</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Child Name</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Class / Group</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Time In</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-2 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($children as $child)
                @php
                    $att = $child->attendances->first();
                    $status = $att ? $att->status : null;
                    $groups = ['Pre-School A','Pre-School B'];
                    $group  = $groups[$child->id % 2];
                @endphp
                <tr>
                    {{-- ID --}}
                    <td class="px-3 fw-semibold small" style="color:#2e7d32">
                        CH-{{ str_pad($child->id, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- Photo --}}
                    <td>
                        @if($child->photo)
                            <img src="{{ asset('storage/'.$child->photo) }}"
                                 class="rounded-circle object-fit-cover"
                                 width="38" height="38"
                                 style="border:2px solid #e8f5e9">
                        @else
                            <div style="width:38px;height:38px;border-radius:50%;
                                        background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                        color:#fff;display:flex;align-items:center;
                                        justify-content:center;font-weight:700;font-size:.85rem">
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

                    {{-- Class/Group --}}
                    <td class="small text-muted">{{ $group }}</td>

                    {{-- Time In --}}
                    <td class="small">
                        @if($att && $status === 'present')
                            <span class="fw-semibold">
                                {{ \Carbon\Carbon::parse($att->updated_at)->format('g:i A') }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($status === 'present')
                            <span class="badge rounded-2 px-3 py-2 fw-semibold"
                                  style="background:#e8f5e9;color:#2e7d32;font-size:.78rem">
                                Present
                            </span>
                        @elseif($status === 'absent')
                            <span class="badge rounded-2 px-3 py-2 fw-semibold"
                                  style="background:#ffebee;color:#c62828;font-size:.78rem">
                                Absent
                            </span>
                        @elseif($status === 'late')
                            <span class="badge rounded-2 px-3 py-2 fw-semibold"
                                  style="background:#fff3e0;color:#e65100;font-size:.78rem">
                                Late
                            </span>
                        @elseif($status === 'excused')
                            <span class="badge rounded-2 px-3 py-2 fw-semibold"
                                  style="background:#e3f2fd;color:#1565c0;font-size:.78rem">
                                Excused
                            </span>
                        @else
                            <span class="badge rounded-2 px-3 py-2 fw-semibold"
                                  style="background:#f5f5f5;color:#9e9e9e;font-size:.78rem">
                                Not Recorded
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.attendance.show',$child) }}?month={{ $month }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e3f2fd;color:#1565c0;border:none"
                               title="View">
                                <i class="fas fa-eye" style="font-size:.8rem"></i>
                            </a>
                            <a href="{{ route('staff.attendance.mark') }}?date={{ $date }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e8f5e9;color:#2e7d32;border:none"
                               title="Edit Attendance">
                                <i class="fas fa-pencil-alt" style="font-size:.8rem"></i>
                            </a>
                            @php
                                $attRecord = \App\Models\Attendance::where('child_id',$child->id)
                                    ->where('attendance_date',$date)->first();
                            @endphp
                            @if($attRecord)
                            <form method="POST"
                                  action="/admin/attendance/{{ $attRecord->id }}"
                                  onsubmit="return confirm('Delete this attendance record?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm rounded-2 p-1 px-2"
                                        style="background:#ffebee;color:#c62828;border:none"
                                        title="Delete">
                                    <i class="fas fa-trash" style="font-size:.8rem"></i>
                                </button>
                            </form>
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
