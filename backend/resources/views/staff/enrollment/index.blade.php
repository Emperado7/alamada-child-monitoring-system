@extends('layouts.app')
@section('title','Enrollment')
@section('page-title','Enrollment')

@section('content')

{{-- ── Page Title + Button ──────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1">Enrollment</h4>
        <div class="text-muted small">
            Manage and monitor child enrollments in the system.
        </div>
    </div>
    <a href="{{ route('staff.enrollment.create') }}"
       class="btn btn-success rounded-3 px-3 py-2 fw-semibold"
       style="background:#2e7d32;border:none;font-size:.88rem">
        <i class="fas fa-plus me-2"></i>New Enrollment
    </a>
</div>

{{-- ── 3 Stat Cards ─────────────────────────────────────────── --}}
@php
    $staffId       = auth()->id();
    $totalEnrolled = \App\Models\Child::where('staff_id',$staffId)->where('status','active')->count();
    $newThisMonth  = \App\Models\Enrollment::where('status','approved')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at',  now()->year)
                        ->whereHas('child',fn($q)=>$q->where('staff_id',$staffId))->count();
    $totalRecords  = \App\Models\Enrollment::whereHas('child',fn($q)=>$q->where('staff_id',$staffId))->count();
@endphp

<div class="row g-3 mb-4">

    {{-- Total Enrolled Children --}}
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
            <a href="?status=approved"
               class="d-flex align-items-center gap-1"
               style="color:#2e7d32;font-size:.83rem;font-weight:600;text-decoration:none">
                View all enrolled children
                <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
            </a>
        </div>
    </div>

    {{-- New Enrollments This Month --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div style="width:56px;height:56px;border-radius:14px;
                            background:#e8f5e9;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <div style="width:36px;height:36px;border-radius:8px;
                                border:2.5px solid #2e7d32;background:#fff;
                                display:flex;align-items:center;justify-content:center">
                        <i class="fas fa-plus" style="color:#2e7d32;font-size:.9rem"></i>
                    </div>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        New Enrollments (This Month)
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $newThisMonth }}
                    </div>
                    <div class="text-muted small mt-1">
                        New children enrolled this month.
                    </div>
                </div>
            </div>
            <a href="{{ route('staff.enrollment.create') }}"
               class="d-flex align-items-center gap-1"
               style="color:#2e7d32;font-size:.83rem;font-weight:600;text-decoration:none">
                View new enrollments
                <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
            </a>
        </div>
    </div>

    {{-- Total Enrollments --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div style="width:56px;height:56px;border-radius:14px;
                            background:#e8f5e9;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <div style="width:36px;height:36px;border-radius:8px;
                                border:2.5px solid #2e7d32;background:#fff;
                                display:flex;align-items:center;justify-content:center">
                        <i class="fas fa-clipboard-list"
                           style="color:#2e7d32;font-size:.9rem"></i>
                    </div>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        Total Enrollments
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $totalRecords }}
                    </div>
                    <div class="text-muted small mt-1">
                        Total enrollment records.
                    </div>
                </div>
            </div>
            <a href="#enrollTable"
               class="d-flex align-items-center gap-1"
               style="color:#2e7d32;font-size:.83rem;font-weight:600;text-decoration:none">
                View enrollment records
                <i class="fas fa-arrow-right" style="font-size:.7rem"></i>
            </a>
        </div>
    </div>
</div>

{{-- ── Enrolled Children Table ──────────────────────────────── --}}
<div class="dash-card" id="enrollTable">
    <div class="dash-card-header">
        <span class="dash-card-title">Enrolled Children</span>
    </div>

    {{-- Search + Status filter --}}
    <div class="px-3 py-3 border-bottom d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <form method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
            {{-- Search --}}
            <div class="input-group" style="max-width:280px">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted small"></i>
                </span>
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       class="form-control form-control-sm border-start-0 ps-0"
                       placeholder="Search by child name or ID...">
            </div>

            {{-- Status filter --}}
            <select name="status"
                    class="form-select form-select-sm rounded-3 ms-auto"
                    style="width:150px"
                    onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach(['pending','approved','rejected','graduated'] as $s)
                    <option value="{{ $s }}"
                        {{ request('status')==$s?'selected':'' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="btn btn-success btn-sm rounded-3"
                    style="background:#2e7d32;border:none;display:none">
                Search
            </button>
        </form>
    </div>

    {{-- ── Table ──────────────────────────────────────────────── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-2 small fw-bold" style="color:#2e7d32;width:40px">#</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Child ID</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Child Name</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Date of Birth</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Date Enrolled</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Age</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-2 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $i => $e)
                @php $child = $e->child; @endphp
                <tr>
                    {{-- Row number --}}
                    <td class="px-3 text-muted small">
                        {{ $enrollments->firstItem() + $i }}
                    </td>

                    {{-- Child ID --}}
                    <td class="fw-semibold small" style="color:#2e7d32">
                        CHD-{{ str_pad($child->id ?? 0, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- Child Name + Photo --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($child && $child->photo)
                                <img src="{{ asset('storage/'.$child->photo) }}"
                                     class="rounded-circle object-fit-cover"
                                     width="36" height="36"
                                     style="border:2px solid #e8f5e9">
                            @else
                                <div style="width:36px;height:36px;border-radius:50%;
                                            background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                            color:#fff;display:flex;align-items:center;
                                            justify-content:center;font-weight:700;
                                            font-size:.8rem;flex-shrink:0">
                                    {{ strtoupper(substr($child->first_name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold small">
                                    {{ $child ? $child->last_name.', '.$child->first_name.' '.($child->middle_name ? substr($child->middle_name,0,1).'.' : '') : '—' }}
                                </div>
                                @if($child)
                                <div class="text-muted" style="font-size:.72rem">
                                    {{ $child->diagnosis }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Date of Birth --}}
                    <td class="small text-muted">
                        {{ $child ? $child->date_of_birth->format('M d, Y') : '—' }}
                    </td>

                    {{-- Date Enrolled --}}
                    <td class="small text-muted">
                        {{ $e->enrollment_date->format('M d, Y') }}
                    </td>

                    {{-- Age --}}
                    <td class="small fw-semibold">
                        {{ $child ? $child->age : '—' }}
                    </td>

                    {{-- Status --}}
                    <td>
                        @php
                            $sm = match($e->status) {
                                'approved'  => ['bg'=>'#e8f5e9','color'=>'#2e7d32','label'=>'Active'],
                                'pending'   => ['bg'=>'#fff3e0','color'=>'#e65100','label'=>'Pending'],
                                'rejected'  => ['bg'=>'#ffebee','color'=>'#c62828','label'=>'Rejected'],
                                'graduated' => ['bg'=>'#f3e5f5','color'=>'#6a1b9a','label'=>'Graduated'],
                                default     => ['bg'=>'#f5f5f5','color'=>'#9e9e9e','label'=>ucfirst($e->status)],
                            };
                        @endphp
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:{{ $sm['bg'] }};color:{{ $sm['color'] }};font-size:.78rem">
                            {{ $sm['label'] }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            {{-- View button --}}
                            <a href="{{ route('staff.enrollment.show', $e) }}"
                               class="btn btn-sm rounded-3 px-3"
                               style="background:#e8f5e9;color:#2e7d32;border:none;
                                      font-size:.8rem;font-weight:600">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            {{-- More options --}}
                            <div class="dropdown">
                                <button class="btn btn-sm rounded-2 p-1 px-2 dropdown-toggle"
                                        style="background:#f5f5f5;color:#757575;border:none"
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v" style="font-size:.8rem"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-1"
                                    style="min-width:160px">
                                    <li>
                                        <a class="dropdown-item rounded-2 small"
                                           href="{{ route('staff.children.show', $e->child_id) }}">
                                            <i class="fas fa-child me-2 text-muted"></i>
                                            View Child Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-2 small"
                                           href="{{ route('staff.attendance.mark') }}?child_id={{ $e->child_id }}">
                                            <i class="fas fa-calendar-check me-2 text-muted"></i>
                                            Mark Attendance
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div style="color:#bdbdbd">
                            <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
                            <div class="fw-semibold">No enrollment records found.</div>
                            <div class="small text-muted mt-2">
                                <a href="{{ route('staff.enrollment.create') }}"
                                   style="color:#2e7d32">
                                    Submit a new enrollment
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────── --}}
    @if($enrollments->hasPages())
    <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="small text-muted">
            Showing {{ $enrollments->firstItem() }} to {{ $enrollments->lastItem() }}
            of {{ $enrollments->total() }} entries
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">

                {{-- Prev --}}
                <li class="page-item {{ $enrollments->onFirstPage() ? 'disabled':'' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $enrollments->previousPageUrl() ?? '#' }}"
                       style="background:#f5f5f5;color:{{ $enrollments->onFirstPage() ? '#bdbdbd':'#2e7d32' }}">
                        ‹
                    </a>
                </li>

                {{-- Pages with smart window --}}
                @php
                    $cur   = $enrollments->currentPage();
                    $last  = $enrollments->lastPage();
                    $start = max(1, $cur - 2);
                    $end   = min($last, $cur + 2);
                @endphp

                @if($start > 1)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $enrollments->url(1) }}"
                       style="background:#f5f5f5;color:#212121">1</a>
                </li>
                @if($start > 2)
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#9e9e9e">…</span>
                </li>
                @endif
                @endif

                @for($p = $start; $p <= $end; $p++)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $enrollments->url($p) }}"
                       style="{{ $p==$cur
                           ? 'background:#2e7d32;color:#fff'
                           : 'background:#f5f5f5;color:#212121' }}">
                        {{ $p }}
                    </a>
                </li>
                @endfor

                @if($end < $last)
                @if($end < $last - 1)
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#9e9e9e">…</span>
                </li>
                @endif
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $enrollments->url($last) }}"
                       style="background:#f5f5f5;color:#212121">{{ $last }}</a>
                </li>
                @endif

                {{-- Next --}}
                <li class="page-item {{ !$enrollments->hasMorePages() ? 'disabled':'' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $enrollments->nextPageUrl() ?? '#' }}"
                       style="background:#f5f5f5;color:{{ !$enrollments->hasMorePages() ? '#bdbdbd':'#2e7d32' }}">
                        ›
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    @else
    <div class="px-3 py-3 border-top small text-muted">
        Showing {{ $enrollments->count() }} entries
    </div>
    @endif
</div>

@endsection
