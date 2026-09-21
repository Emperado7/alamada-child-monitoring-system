@extends('layouts.app')
@section('title','Enrollment')
@section('page-title','Enrollment')

@section('content')

{{-- ── Breadcrumb + Title ───────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="fw-bold mb-1">Enrollment Dashboard</h4>
        <nav style="font-size:.83rem">
            <a href="{{ route('admin.dashboard') }}"
               style="color:#2e7d32;text-decoration:none">Dashboard</a>
            <span class="text-muted mx-1">›</span>
            <span class="text-muted">Enrollment</span>
        </nav>
    </div>
    <a href="{{ route('admin.enrollment.create') }}"
       class="btn btn-success rounded-3 px-3 py-2 fw-semibold"
       style="background:#2e7d32;border:none;font-size:.88rem">
        <i class="fas fa-plus me-2"></i>New Enrollment
    </a>
</div>

{{-- ── 4 Stat Cards ─────────────────────────────────────────── --}}
@php
    $currentYear   = now()->year;
    $totalEnrolled = \App\Models\Enrollment::where('status','approved')->count();
    $newThisMonth  = \App\Models\Enrollment::where('status','approved')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at',  now()->year)->count();
    $pendingCount  = \App\Models\Enrollment::where('status','pending')->count();
    $graduated     = \App\Models\Enrollment::where('status','graduated')->count();
@endphp

<div class="row g-3 mb-4">

    {{-- Total Enrolled --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <circle cx="9"  cy="7"  r="3.5" fill="#2e7d32"/>
                    <circle cx="15" cy="7"  r="3.5" fill="#66bb6a"/>
                    <path d="M2 19c0-3.31 3.13-6 7-6s7 2.69 7 6"
                          stroke="#2e7d32" stroke-width="1.5" fill="none"/>
                    <path d="M15 13c2.5.6 4 2.3 4 5"
                          stroke="#66bb6a" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Enrolled</div>
                <div class="stat-value" style="color:#2e7d32">{{ $totalEnrolled }}</div>
                <a href="?status=approved" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- New Enrollments --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <circle cx="10" cy="8" r="4" fill="#1565c0"/>
                    <path d="M3 20c0-3.31 3.13-6 7-6"
                          stroke="#1565c0" stroke-width="1.6" fill="none"/>
                    <line x1="18" y1="14" x2="18" y2="20" stroke="#1565c0" stroke-width="1.8" stroke-linecap="round"/>
                    <line x1="15" y1="17" x2="21" y2="17" stroke="#1565c0" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="stat-body">
                <div class="stat-label">New Enrollments</div>
                <div class="stat-value" style="color:#1565c0">{{ $newThisMonth }}</div>
                <span class="stat-link text-muted" style="font-size:.75rem;cursor:default">
                    This Month
                </span>
            </div>
        </div>
    </div>

    {{-- Pending Enrollments --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="4" width="16" height="16" rx="3"
                          fill="#e65100" opacity=".15"/>
                    <rect x="4" y="4" width="16" height="16" rx="3"
                          stroke="#e65100" stroke-width="1.5"/>
                    <line x1="8"  y1="9"  x2="16" y2="9"  stroke="#e65100" stroke-width="1.4" stroke-linecap="round"/>
                    <line x1="8"  y1="12" x2="16" y2="12" stroke="#e65100" stroke-width="1.4" stroke-linecap="round"/>
                    <line x1="8"  y1="15" x2="12" y2="15" stroke="#e65100" stroke-width="1.4" stroke-linecap="round"/>
                    <circle cx="17" cy="15" r="3.5" fill="#e65100"/>
                    <line x1="17" y1="13.5" x2="17" y2="15" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/>
                    <circle cx="17" cy="16.2" r=".5" fill="#fff"/>
                </svg>
            </div>
            <div class="stat-body">
                <div class="stat-label">Pending Enrollments</div>
                <div class="stat-value" style="color:#e65100">{{ $pendingCount }}</div>
                <a href="?status=pending" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Total Graduated --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3L2 8l10 5 10-5-10-5z" fill="#6a1b9a" opacity=".2"/>
                    <path d="M12 3L2 8l10 5 10-5-10-5z" stroke="#6a1b9a" stroke-width="1.4" stroke-linejoin="round"/>
                    <path d="M6 10.5v4c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4"
                          stroke="#6a1b9a" stroke-width="1.4" stroke-linecap="round"/>
                    <line x1="20" y1="8" x2="20" y2="14" stroke="#6a1b9a" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Graduated</div>
                <div class="stat-value" style="color:#6a1b9a">{{ $graduated }}</div>
                <a href="?status=graduated" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Table Card ───────────────────────────────────────────── --}}
<div class="dash-card">

    {{-- Toolbar --}}
    <div class="dash-card-header flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- School year filter --}}
            <label class="small text-muted mb-0 fw-semibold">School Year</label>
            <form method="GET" id="yearForm" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="per_page"  value="{{ request('per_page',10) }}">
                <input type="hidden" name="search"    value="{{ request('search') }}">
                <input type="hidden" name="status"    value="{{ request('status') }}">
                <select name="school_year"
                        class="form-select form-select-sm rounded-2"
                        style="width:130px"
                        onchange="document.getElementById('yearForm').submit()">
                    <option value="">All Years</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('school_year')==$y?'selected':'' }}>
                            {{ $y }} - {{ $y+1 }}
                        </option>
                    @endforeach
                </select>
            </form>

            <span class="text-muted small">Show</span>
            <select class="form-select form-select-sm rounded-2"
                    style="width:70px"
                    onchange="changePageSize(this.value)">
                @foreach([10,25,50] as $ps)
                    <option value="{{ $ps }}"
                        {{ request('per_page',10)==$ps?'selected':'' }}>
                        {{ $ps }}
                    </option>
                @endforeach
            </select>
            <span class="text-muted small">entries</span>
        </div>

        <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
            <form method="GET" class="d-flex gap-2">
                <input type="hidden" name="per_page"    value="{{ request('per_page',10) }}">
                <input type="hidden" name="school_year" value="{{ request('school_year') }}">
                <input type="hidden" name="status"      value="{{ request('status') }}">
                <div class="input-group" style="max-width:240px">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search child or guardian...">
                    <button class="btn btn-success btn-sm"
                            style="background:#2e7d32;border-color:#2e7d32">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            {{-- Status filter dropdown --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary rounded-2 dropdown-toggle"
                        data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i>Filters
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2"
                    style="min-width:160px">
                    @foreach([''=>'All Status','approved'=>'Enrolled','pending'=>'Pending','rejected'=>'Rejected','graduated'=>'Graduated'] as $val=>$label)
                    <li>
                        <a class="dropdown-item rounded-2 small {{ request('status')==$val?'fw-bold':'' }}"
                           href="{{ request()->fullUrlWithQuery(['status'=>$val,'page'=>1]) }}">
                            @if($val=='approved')
                            <span class="me-2" style="color:#2e7d32">●</span>
                            @elseif($val=='pending')
                            <span class="me-2" style="color:#e65100">●</span>
                            @elseif($val=='rejected')
                            <span class="me-2" style="color:#c62828">●</span>
                            @elseif($val=='graduated')
                            <span class="me-2" style="color:#6a1b9a">●</span>
                            @else
                            <span class="me-2" style="color:#9e9e9e">●</span>
                            @endif
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ── Table ──────────────────────────────────────────────── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width:900px">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-2 small fw-bold" style="color:#2e7d32">ID</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Photo</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Child Name</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Date of Birth</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Age</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Gender</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Guardian</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Date Enrolled</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">School Year</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-2 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $e)
                @php $child = $e->child; @endphp
                <tr>
                    {{-- ID --}}
                    <td class="px-3 fw-semibold small" style="color:#2e7d32">
                        ENR-{{ str_pad($e->id, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- Photo --}}
                    <td>
                        @if($child && $child->photo)
                            <img src="{{ asset('storage/'.$child->photo) }}"
                                 class="rounded-circle object-fit-cover"
                                 width="40" height="40"
                                 style="border:2px solid #e8f5e9">
                        @else
                            <div style="width:40px;height:40px;border-radius:50%;
                                        background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                        color:#fff;display:flex;align-items:center;
                                        justify-content:center;font-weight:700;
                                        font-size:.88rem">
                                {{ strtoupper(substr($child->first_name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                    </td>

                    {{-- Child Name --}}
                    <td>
                        <div class="fw-semibold small">
                            {{ $child->first_name ?? '—' }}
                            {{ $child->last_name  ?? '' }}
                        </div>
                        <div class="text-muted" style="font-size:.72rem">
                            {{ $child->diagnosis ?? '' }}
                        </div>
                    </td>

                    {{-- Date of Birth --}}
                    <td class="small text-muted">
                        {{ $child ? $child->date_of_birth->format('F d, Y') : '—' }}
                    </td>

                    {{-- Age --}}
                    <td class="small fw-semibold">
                        {{ $child ? $child->age : '—' }}
                    </td>

                    {{-- Gender --}}
                    <td>
                        @if($child && $child->gender === 'male')
                        <span class="badge rounded-pill px-3 py-1 fw-semibold"
                              style="background:#e3f2fd;color:#1565c0;font-size:.78rem">Male</span>
                        @elseif($child)
                        <span class="badge rounded-pill px-3 py-1 fw-semibold"
                              style="background:#fce4ec;color:#c2185b;font-size:.78rem">Female</span>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>

                    {{-- Guardian --}}
                    <td>
                        @if($child)
                        <div class="small fw-semibold">{{ $child->guardian_name }}</div>
                        <div class="text-muted" style="font-size:.72rem">
                            ({{ $child->gender === 'female' ? 'Mother' : 'Father' }})
                        </div>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>

                    {{-- Date Enrolled --}}
                    <td class="small text-muted">
                        {{ $e->enrollment_date->format('F d, Y') }}
                    </td>

                    {{-- School Year --}}
                    <td class="small">
                        {{ $e->school_year }} - {{ $e->school_year + 1 }}
                    </td>

                    {{-- Status --}}
                    <td>
                        @php
                            $statusMap = [
                                'approved'  => ['bg'=>'#e8f5e9','color'=>'#2e7d32','label'=>'Enrolled'],
                                'pending'   => ['bg'=>'#fff3e0','color'=>'#e65100','label'=>'Pending'],
                                'rejected'  => ['bg'=>'#ffebee','color'=>'#c62828','label'=>'Rejected'],
                                'graduated' => ['bg'=>'#f3e5f5','color'=>'#6a1b9a','label'=>'Graduated'],
                            ];
                            $sm = $statusMap[$e->status] ?? ['bg'=>'#f5f5f5','color'=>'#9e9e9e','label'=>ucfirst($e->status)];
                        @endphp
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:{{ $sm['bg'] }};color:{{ $sm['color'] }};font-size:.78rem">
                            {{ $sm['label'] }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.enrollment.show',$e) }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e3f2fd;color:#1565c0;border:none"
                               title="View">
                                <i class="fas fa-eye" style="font-size:.8rem"></i>
                            </a>
                            @if($e->status === 'pending')
                            <form method="POST" action="{{ route('admin.enrollment.approve',$e) }}">
                                @csrf
                                <button class="btn btn-sm rounded-2 p-1 px-2"
                                        style="background:#e8f5e9;color:#2e7d32;border:none"
                                        title="Approve">
                                    <i class="fas fa-check" style="font-size:.8rem"></i>
                                </button>
                            </form>
                            <button class="btn btn-sm rounded-2 p-1 px-2"
                                    style="background:#fff3e0;color:#e65100;border:none"
                                    title="Reject"
                                    onclick="openRejectModal({{ $e->id }})">
                                <i class="fas fa-times" style="font-size:.8rem"></i>
                            </button>
                            @else
                            <a href="{{ route('admin.children.edit', $e->child_id) }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e8f5e9;color:#2e7d32;border:none"
                               title="Edit">
                                <i class="fas fa-pencil-alt" style="font-size:.8rem"></i>
                            </a>
                            @endif
                            <form method="POST"
                                  action="{{ route('admin.enrollment.destroy',$e) }}"
                                  onsubmit="return confirm('Delete this enrollment?')">
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
                    <td colspan="11" class="text-center py-5">
                        <div style="color:#bdbdbd">
                            <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
                            <div class="fw-semibold">No enrollment records found.</div>
                            <div class="small text-muted mt-1">
                                <a href="{{ route('admin.enrollment.create') }}"
                                   style="color:#2e7d32">Enroll a child now</a>
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
                @if($enrollments->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#bdbdbd">«</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $enrollments->previousPageUrl() }}"
                       style="background:#f5f5f5;color:#2e7d32">«</a>
                </li>
                @endif

                {{-- Pages --}}
                @foreach($enrollments->getUrlRange(1, $enrollments->lastPage()) as $page => $url)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $url }}"
                       style="{{ $page == $enrollments->currentPage()
                           ? 'background:#2e7d32;color:#fff'
                           : 'background:#f5f5f5;color:#212121' }}">
                        {{ $page }}
                    </a>
                </li>
                @endforeach

                {{-- Next --}}
                @if($enrollments->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $enrollments->nextPageUrl() }}"
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
        Showing {{ $enrollments->count() }} entries
    </div>
    @endif
</div>

{{-- ── Reject Modal ─────────────────────────────────────────── --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-header border-0 pb-1">
                    <h6 class="modal-title fw-bold">Reject Enrollment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-1">
                    <label class="form-label small fw-semibold">Reason (optional)</label>
                    <textarea name="remarks" class="form-control form-control-sm rounded-3"
                              rows="3" placeholder="Enter rejection reason…"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0 gap-2">
                    <button type="button"
                            class="btn btn-sm btn-secondary rounded-3"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit"
                            class="btn btn-sm btn-danger rounded-3">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openRejectModal(id) {
    document.getElementById('rejectForm').action = `/admin/enrollment/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function changePageSize(size) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', size);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}
</script>
@endpush
