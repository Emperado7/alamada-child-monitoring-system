@extends('layouts.app')
@section('title','Child Monitoring')
@section('page-title','Child Monitoring')

@section('content')

{{-- ── Page Title + Refresh ─────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1">Child Monitoring</h4>
        <div class="text-muted small">
            View and manage child information in the system.
        </div>
    </div>
    <a href="{{ route('staff.children.index') }}"
       class="btn btn-outline-secondary rounded-3 px-3 py-2"
       style="font-size:.88rem;border-color:#e0e0e0">
        <i class="fas fa-sync-alt me-2" style="color:#2e7d32"></i>
        <span style="color:#2e7d32;font-weight:600">Refresh</span>
    </a>
</div>

{{-- ── 3 Stat Cards ─────────────────────────────────────────── --}}
@php
    $staffId      = auth()->id();
    $totalAll     = \App\Models\Child::where('staff_id',$staffId)->count();
    $totalMale    = \App\Models\Child::where('staff_id',$staffId)->where('gender','male')->count();
    $totalFemale  = \App\Models\Child::where('staff_id',$staffId)->where('gender','female')->count();
    $malePct      = $totalAll > 0 ? round($totalMale  / $totalAll * 100) : 0;
    $femalePct    = $totalAll > 0 ? round($totalFemale / $totalAll * 100) : 0;
@endphp

<div class="row g-3 mb-4">

    {{-- Total Children --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-center gap-4">
                <div style="width:64px;height:64px;border-radius:16px;
                            background:#e8f5e9;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
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
                        Total Children
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $totalAll }}
                    </div>
                    <div class="text-muted small mt-1">
                        All enrolled children in the system.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Boys --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-center gap-4">
                <div style="width:64px;height:64px;border-radius:16px;
                            background:#e3f2fd;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4.5" fill="#1565c0"/>
                        <path d="M4 20c0-3.31 3.58-6 8-6s8 2.69 8 6"
                              stroke="#1565c0" stroke-width="1.6" fill="none"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        Boys
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $totalMale }}
                    </div>
                    <div class="text-muted small mt-1">
                        {{ $malePct }}% of total children
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Girls --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-center gap-4">
                <div style="width:64px;height:64px;border-radius:16px;
                            background:#fce4ec;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="7" r="4.5" fill="#c2185b"/>
                        <path d="M4 19c0-3.31 3.58-6 8-6s8 2.69 8 6"
                              stroke="#c2185b" stroke-width="1.6" fill="none"/>
                        <line x1="12" y1="17" x2="12" y2="22"
                              stroke="#c2185b" stroke-width="1.5"/>
                        <line x1="9"  y1="20" x2="15" y2="20"
                              stroke="#c2185b" stroke-width="1.5"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        Girls
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $totalFemale }}
                    </div>
                    <div class="text-muted small mt-1">
                        {{ $femalePct }}% of total children
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Children Table ───────────────────────────────────────── --}}
<div class="dash-card mb-4">

    {{-- Search + Filters --}}
    <div class="px-3 py-3 border-bottom d-flex align-items-center gap-3 flex-wrap">
        <form method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
            {{-- Search --}}
            <div class="input-group" style="max-width:300px">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted small"></i>
                </span>
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       class="form-control form-control-sm border-start-0 ps-0"
                       placeholder="Search by child name or ID...">
            </div>

            {{-- Gender filter --}}
            <select name="gender"
                    class="form-select form-select-sm rounded-3"
                    style="width:150px"
                    onchange="this.form.submit()">
                <option value="">All Gender</option>
                <option value="male"   {{ request('gender')=='male'  ?'selected':'' }}>Male</option>
                <option value="female" {{ request('gender')=='female'?'selected':'' }}>Female</option>
            </select>

            {{-- Status filter --}}
            <select name="status"
                    class="form-select form-select-sm rounded-3"
                    style="width:150px"
                    onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active"    {{ request('status')=='active'   ?'selected':'' }}>Active</option>
                <option value="inactive"  {{ request('status')=='inactive' ?'selected':'' }}>Inactive</option>
                <option value="graduated" {{ request('status')=='graduated'?'selected':'' }}>Graduated</option>
            </select>

            <button type="submit" style="display:none">Search</button>
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
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Age</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Gender</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-2 small fw-bold text-end pe-3" style="color:#2e7d32">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($children as $i => $child)
                <tr>
                    {{-- Row # --}}
                    <td class="px-3 text-muted small">
                        {{ $children->firstItem() + $i }}
                    </td>

                    {{-- Child ID --}}
                    <td class="fw-semibold small" style="color:#2e7d32">
                        CHD-{{ str_pad($child->id, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- Child Name + Photo --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($child->photo)
                                <img src="{{ asset('storage/'.$child->photo) }}"
                                     class="rounded-circle object-fit-cover"
                                     width="38" height="38"
                                     style="border:2px solid #e8f5e9">
                            @else
                                <div style="width:38px;height:38px;border-radius:50%;
                                            background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                            color:#fff;display:flex;align-items:center;
                                            justify-content:center;font-weight:700;
                                            font-size:.85rem;flex-shrink:0">
                                    {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="fw-semibold small">
                                {{-- Last, First Middle format --}}
                                {{ $child->last_name }},
                                {{ $child->first_name }}
                                {{ $child->middle_name ? $child->middle_name : '' }}
                            </div>
                        </div>
                    </td>

                    {{-- Age --}}
                    <td class="small fw-semibold">{{ $child->age }}</td>

                    {{-- Gender with icon --}}
                    <td>
                        @if($child->gender === 'male')
                        <div class="d-flex align-items-center gap-1">
                            <i class="fas fa-mars" style="color:#1565c0;font-size:.9rem"></i>
                            <span class="small" style="color:#1565c0;font-weight:600">Male</span>
                        </div>
                        @else
                        <div class="d-flex align-items-center gap-1">
                            <i class="fas fa-venus" style="color:#c2185b;font-size:.9rem"></i>
                            <span class="small" style="color:#c2185b;font-weight:600">Female</span>
                        </div>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        @php
                            $sc = match($child->status) {
                                'active'    => ['bg'=>'#e8f5e9','color'=>'#2e7d32','label'=>'Active'],
                                'inactive'  => ['bg'=>'#f5f5f5','color'=>'#616161','label'=>'Inactive'],
                                'graduated' => ['bg'=>'#f3e5f5','color'=>'#6a1b9a','label'=>'Graduated'],
                                default     => ['bg'=>'#f5f5f5','color'=>'#9e9e9e','label'=>ucfirst($child->status)],
                            };
                        @endphp
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:.78rem">
                            {{ $sc['label'] }}
                        </span>
                    </td>

                    {{-- Action --}}
                    <td class="text-end pe-3">
                        <a href="{{ route('staff.children.show', $child) }}"
                           class="btn btn-sm rounded-3 px-3"
                           style="background:#e8f5e9;color:#2e7d32;border:none;
                                  font-size:.8rem;font-weight:600">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="color:#bdbdbd">
                            <i class="fas fa-child fa-3x mb-3 d-block"></i>
                            <div class="fw-semibold">No children found.</div>
                            <div class="small text-muted mt-1">
                                Children will appear here after enrollment.
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
                {{-- Prev --}}
                <li class="page-item {{ $children->onFirstPage() ? 'disabled':'' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $children->previousPageUrl() ?? '#' }}"
                       style="background:#f5f5f5;color:{{ $children->onFirstPage() ? '#bdbdbd':'#2e7d32' }}">‹</a>
                </li>

                @php
                    $cur   = $children->currentPage();
                    $last  = $children->lastPage();
                    $start = max(1, $cur - 2);
                    $end   = min($last, $cur + 2);
                @endphp

                @if($start > 1)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0" href="{{ $children->url(1) }}"
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
                    <a class="page-link rounded-2 border-0" href="{{ $children->url($p) }}"
                       style="{{ $p==$cur ? 'background:#2e7d32;color:#fff' : 'background:#f5f5f5;color:#212121' }}">
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
                    <a class="page-link rounded-2 border-0" href="{{ $children->url($last) }}"
                       style="background:#f5f5f5;color:#212121">{{ $last }}</a>
                </li>
                @endif

                {{-- Next --}}
                <li class="page-item {{ !$children->hasMorePages() ? 'disabled':'' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $children->nextPageUrl() ?? '#' }}"
                       style="background:#f5f5f5;color:{{ !$children->hasMorePages() ? '#bdbdbd':'#2e7d32' }}">›</a>
                </li>
            </ul>
        </nav>
    </div>
    @else
    <div class="px-3 py-3 border-top small text-muted">
        Showing {{ $children->count() }} entries
    </div>
    @endif
</div>

{{-- ── Bottom Quick Links ───────────────────────────────────── --}}
<div class="row g-3">

    {{-- View Child List --}}
    <div class="col-md-6">
        <a href="{{ route('staff.children.index') }}"
           class="dash-card p-4 d-flex align-items-center gap-4 text-decoration-none"
           style="transition:box-shadow .2s,transform .15s">
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
            <div class="flex-grow-1">
                <div class="fw-bold" style="color:#2e7d32;font-size:.95rem">
                    View Child List
                </div>
                <div class="text-muted small">
                    See the list of all enrolled children.
                </div>
            </div>
            {{-- Illustration placeholder --}}
            <div style="width:80px;height:60px;background:#f5f5f5;border-radius:10px;
                        display:flex;align-items:center;justify-content:center;
                        opacity:.5;flex-shrink:0">
                <i class="fas fa-list-ul" style="font-size:1.5rem;color:#bdbdbd"></i>
            </div>
            <i class="fas fa-chevron-right" style="color:#2e7d32;font-size:.9rem;flex-shrink:0"></i>
        </a>
    </div>

    {{-- View Child Details --}}
    <div class="col-md-6">
        <a href="{{ $children->first() ? route('staff.children.show', $children->first()) : route('staff.children.index') }}"
           class="dash-card p-4 d-flex align-items-center gap-4 text-decoration-none"
           style="transition:box-shadow .2s,transform .15s">
            <div style="width:56px;height:56px;border-radius:14px;
                        background:#e3f2fd;display:flex;align-items:center;
                        justify-content:center;flex-shrink:0">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="3" width="16" height="18" rx="3"
                          fill="#1565c0" opacity=".15"/>
                    <rect x="4" y="3" width="16" height="18" rx="3"
                          stroke="#1565c0" stroke-width="1.5"/>
                    <line x1="8" y1="8"  x2="16" y2="8"
                          stroke="#1565c0" stroke-width="1.3" stroke-linecap="round"/>
                    <line x1="8" y1="12" x2="16" y2="12"
                          stroke="#1565c0" stroke-width="1.3" stroke-linecap="round"/>
                    <line x1="8" y1="16" x2="13" y2="16"
                          stroke="#1565c0" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="color:#1565c0;font-size:.95rem">
                    View Child Details
                </div>
                <div class="text-muted small">
                    View detailed information of a selected child.
                </div>
            </div>
            {{-- Illustration placeholder --}}
            <div style="width:80px;height:60px;background:#f5f5f5;border-radius:10px;
                        display:flex;align-items:center;justify-content:center;
                        opacity:.5;flex-shrink:0">
                <i class="fas fa-user-circle" style="font-size:1.5rem;color:#bdbdbd"></i>
            </div>
            <i class="fas fa-chevron-right" style="color:#1565c0;font-size:.9rem;flex-shrink:0"></i>
        </a>
    </div>

</div>

@endsection

@push('styles')
<style>
.dash-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.1) !important;
    transform: translateY(-1px);
}
</style>
@endpush
