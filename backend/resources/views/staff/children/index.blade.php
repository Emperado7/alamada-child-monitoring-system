@extends('layouts.app')
@section('title','Child Monitoring')
@section('page-title','Child Monitoring')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Child Monitoring</h4>
        <div class="text-muted small">
            View and manage child information in the system.
        </div>
    </div>
    <a href="{{ route('staff.children.index') }}"
       class="btn rounded-3 px-4 py-2 d-flex align-items-center gap-2"
       style="border:1.5px solid #e0e0e0;background:#fff;font-size:.88rem;color:#2e7d32;font-weight:600">
        <i class="fas fa-sync-alt"></i> Refresh
    </a>
</div>

{{-- ── 3 Stat Cards ─────────────────────────────────────────── --}}
@php
    $totalAll    = \App\Models\Child::where('status','active')->count();
    $totalMale   = \App\Models\Child::where('status','active')->where('gender','male')->count();
    $totalFemale = \App\Models\Child::where('status','active')->where('gender','female')->count();
    $malePct     = $totalAll > 0 ? round($totalMale   / $totalAll * 100) : 0;
    $femalePct   = $totalAll > 0 ? round($totalFemale / $totalAll * 100) : 0;
@endphp

<div class="row g-3 mb-4">

    {{-- Total Children --}}
    <div class="col-12 col-md-4">
        <div class="cm-stat-card h-100">
            <div class="cm-stat-icon" style="background:#e8f5e9">
                {{-- Two-person green silhouette --}}
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                    <circle cx="9"  cy="7"  r="3.4" fill="#2e7d32"/>
                    <circle cx="15" cy="7"  r="3.4" fill="#66bb6a"/>
                    <path d="M2 19.5c0-3.31 3.13-6 7-6s7 2.69 7 6"
                          stroke="#2e7d32" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                    <path d="M15 13.5c2.4.8 3.8 2.6 4 5"
                          stroke="#66bb6a" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
            <div class="cm-stat-body">
                <div class="cm-stat-label">Total Children</div>
                <div class="cm-stat-value">{{ $totalAll }}</div>
                <div class="cm-stat-sub">All enrolled children in the system.</div>
            </div>
        </div>
    </div>

    {{-- Boys --}}
    <div class="col-12 col-md-4">
        <div class="cm-stat-card h-100">
            <div class="cm-stat-icon" style="background:#e3f2fd">
                {{-- Single blue person --}}
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8"  r="4" fill="#1565c0" opacity=".3"/>
                    <circle cx="12" cy="8"  r="4" stroke="#1565c0" stroke-width="1.5"/>
                    <path d="M4 20c0-3.31 3.58-6 8-6s8 2.69 8 6"
                          stroke="#1565c0" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
            <div class="cm-stat-body">
                <div class="cm-stat-label">Boys</div>
                <div class="cm-stat-value">{{ $totalMale }}</div>
                <div class="cm-stat-sub">{{ $malePct }}% of total children</div>
            </div>
        </div>
    </div>

    {{-- Girls --}}
    <div class="col-12 col-md-4">
        <div class="cm-stat-card h-100">
            <div class="cm-stat-icon" style="background:#fce4ec">
                {{-- Single pink person (with dress hint) --}}
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="7.5" r="4" fill="#c2185b" opacity=".3"/>
                    <circle cx="12" cy="7.5" r="4" stroke="#c2185b" stroke-width="1.5"/>
                    <path d="M5 20c0-3.31 3.13-5.5 7-5.5s7 2.19 7 5.5"
                          stroke="#c2185b" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                    <line x1="12" y1="17.5" x2="12" y2="21"
                          stroke="#c2185b" stroke-width="1.4" stroke-linecap="round"/>
                    <line x1="9.5" y1="19.5" x2="14.5" y2="19.5"
                          stroke="#c2185b" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="cm-stat-body">
                <div class="cm-stat-label">Girls</div>
                <div class="cm-stat-value">{{ $totalFemale }}</div>
                <div class="cm-stat-sub">{{ $femalePct }}% of total children</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Children Table Card ──────────────────────────────────── --}}
<div class="dash-card mb-4">

    {{-- Toolbar: search left, filters right --}}
    <div class="px-3 py-3 border-bottom">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">

            {{-- Search --}}
            <div class="input-group" style="max-width:310px">
                <span class="input-group-text bg-white border-end-0 pe-1">
                    <i class="fas fa-search" style="color:#9e9e9e;font-size:.82rem"></i>
                </span>
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       class="form-control form-control-sm border-start-0 ps-1"
                       placeholder="Search by child name or ID...">
            </div>

            {{-- Spacer --}}
            <div class="flex-grow-1"></div>

            {{-- Gender filter --}}
            <select name="gender"
                    class="form-select form-select-sm rounded-3"
                    style="width:155px"
                    onchange="this.form.submit()">
                <option value="">All Gender</option>
                <option value="male"   {{ request('gender')=='male'  ?'selected':'' }}>Male</option>
                <option value="female" {{ request('gender')=='female'?'selected':'' }}>Female</option>
            </select>

            {{-- Status filter --}}
            <select name="status"
                    class="form-select form-select-sm rounded-3"
                    style="width:155px"
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
        <table class="table table-hover align-middle mb-0" style="min-width:620px">
            <thead>
                <tr style="background:#f1f8f2">
                    <th class="px-3 py-3 small fw-bold" style="color:#2e7d32;width:44px">#</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Child ID</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Child Name</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Age</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Gender</th>
                    <th class="py-3 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-3 small fw-bold text-end pe-3" style="color:#2e7d32">Action</th>
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
                    <td class="fw-semibold small" style="color:#555">
                        CHD-{{ str_pad($child->id, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- Child Name + Photo --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($child->photo)
                                <img src="{{ asset('storage/'.$child->photo) }}"
                                     class="rounded-circle object-fit-cover"
                                     width="38" height="38"
                                     style="border:2px solid #e8f5e9;flex-shrink:0">
                            @else
                                <div style="width:38px;height:38px;border-radius:50%;
                                            background:linear-gradient(135deg,#1565c0,#90caf9);
                                            color:#fff;display:flex;align-items:center;
                                            justify-content:center;font-weight:700;
                                            font-size:.85rem;flex-shrink:0">
                                    {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="fw-semibold small">
                                {{ $child->last_name }}, {{ $child->first_name }}
                                {{ $child->middle_name ? $child->middle_name : '' }}
                            </span>
                        </div>
                    </td>

                    {{-- Age --}}
                    <td class="small fw-semibold text-center">
                        {{ $child->age ?? (floor((now()->diffInMonths($child->birthdate)) / 12)) }}
                    </td>

                    {{-- Gender with icon --}}
                    <td>
                        @if(strtolower($child->gender) === 'male')
                        <div class="d-flex align-items-center gap-1">
                            <i class="fas fa-mars" style="color:#1565c0;font-size:.9rem"></i>
                            <span class="small fw-semibold" style="color:#1565c0">Male</span>
                        </div>
                        @else
                        <div class="d-flex align-items-center gap-1">
                            <i class="fas fa-venus" style="color:#c2185b;font-size:.9rem"></i>
                            <span class="small fw-semibold" style="color:#c2185b">Female</span>
                        </div>
                        @endif
                    </td>

                    {{-- Status badge --}}
                    <td>
                        @php
                            $sc = match(strtolower($child->status ?? 'active')) {
                                'active'    => ['bg'=>'#e8f5e9','c'=>'#2e7d32','l'=>'Active'],
                                'inactive'  => ['bg'=>'#f5f5f5','c'=>'#616161','l'=>'Inactive'],
                                'graduated' => ['bg'=>'#f3e5f5','c'=>'#6a1b9a','l'=>'Graduated'],
                                default     => ['bg'=>'#f5f5f5','c'=>'#9e9e9e','l'=>ucfirst($child->status ?? '')],
                            };
                        @endphp
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:{{ $sc['bg'] }};color:{{ $sc['c'] }};font-size:.78rem">
                            {{ $sc['l'] }}
                        </span>
                    </td>

                    {{-- Action: View button --}}
                    <td class="text-end pe-3">
                        <a href="{{ route('staff.children.show', $child) }}"
                           class="btn btn-sm rounded-3 px-3 d-inline-flex align-items-center gap-1"
                           style="background:#e8f5e9;color:#2e7d32;border:none;
                                  font-size:.8rem;font-weight:600">
                            <i class="fas fa-eye"></i> View
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
                {{-- Prev « --}}
                <li class="page-item {{ $children->onFirstPage() ? 'disabled':'' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $children->previousPageUrl() ?? '#' }}"
                       style="background:#f5f5f5;
                              color:{{ $children->onFirstPage() ? '#bdbdbd':'#2e7d32' }}">‹</a>
                </li>

                @php
                    $cur   = $children->currentPage();
                    $last  = $children->lastPage();
                    $start = max(1, $cur - 2);
                    $end   = min($last, $cur + 2);
                @endphp

                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link rounded-2 border-0"
                           href="{{ $children->url(1) }}"
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
                           href="{{ $children->url($last) }}"
                           style="background:#f5f5f5;color:#212121">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next › --}}
                <li class="page-item {{ !$children->hasMorePages() ? 'disabled':'' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $children->nextPageUrl() ?? '#' }}"
                       style="background:#f5f5f5;
                              color:{{ !$children->hasMorePages() ? '#bdbdbd':'#2e7d32' }}">›</a>
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

{{-- ── Bottom Quick-Link Cards ─────────────────────────────── --}}
<div class="row g-3">

    {{-- View Child List --}}
    <div class="col-12 col-md-6">
        <a href="{{ route('staff.children.index') }}"
           class="dash-card p-4 d-flex align-items-center gap-3 text-decoration-none quick-link-card">
            <div style="width:52px;height:52px;border-radius:50%;
                        background:#e8f5e9;display:flex;align-items:center;
                        justify-content:center;flex-shrink:0">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <circle cx="9"  cy="7"  r="3.2" fill="#2e7d32"/>
                    <circle cx="15" cy="7"  r="3.2" fill="#66bb6a"/>
                    <path d="M2 19.5c0-3.31 3.13-6 7-6s7 2.69 7 6"
                          stroke="#2e7d32" stroke-width="1.5" fill="none"/>
                    <path d="M15 13.5c2.4.8 3.7 2.5 4 5"
                          stroke="#66bb6a" stroke-width="1.4" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="color:#2e7d32;font-size:.93rem">View Child List</div>
                <div class="text-muted small mt-1">See the list of all enrolled children.</div>
            </div>
            {{-- Mini clipboard illustration --}}
            <div class="quick-link-illustration" style="background:#e8f5e9">
                <svg width="38" height="48" viewBox="0 0 38 48" fill="none">
                    <rect x="3" y="8" width="32" height="38" rx="4" fill="#2e7d32" opacity=".12"/>
                    <rect x="3" y="8" width="32" height="38" rx="4"
                          stroke="#2e7d32" stroke-width="1.5"/>
                    <rect x="13" y="3" width="12" height="9" rx="3"
                          fill="#fff" stroke="#2e7d32" stroke-width="1.3"/>
                    <line x1="10" y1="20" x2="28" y2="20"
                          stroke="#2e7d32" stroke-width="1.4" stroke-linecap="round"/>
                    <line x1="10" y1="26" x2="28" y2="26"
                          stroke="#2e7d32" stroke-width="1.4" stroke-linecap="round"/>
                    <line x1="10" y1="32" x2="22" y2="32"
                          stroke="#2e7d32" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
            </div>
            <i class="fas fa-chevron-right" style="color:#2e7d32;font-size:.85rem;flex-shrink:0"></i>
        </a>
    </div>

    {{-- View Child Details --}}
    <div class="col-12 col-md-6">
        <a href="{{ $children->first() ? route('staff.children.show', $children->first()) : route('staff.children.index') }}"
           class="dash-card p-4 d-flex align-items-center gap-3 text-decoration-none quick-link-card">
            <div style="width:52px;height:52px;border-radius:50%;
                        background:#e3f2fd;display:flex;align-items:center;
                        justify-content:center;flex-shrink:0">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
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
                <div class="fw-bold" style="color:#1565c0;font-size:.93rem">View Child Details</div>
                <div class="text-muted small mt-1">View detailed information of a selected child.</div>
            </div>
            {{-- Mini profile illustration --}}
            <div class="quick-link-illustration" style="background:#e3f2fd">
                <svg width="44" height="48" viewBox="0 0 44 48" fill="none">
                    <circle cx="22" cy="14" r="8" fill="#1565c0" opacity=".2"/>
                    <circle cx="22" cy="14" r="8" stroke="#1565c0" stroke-width="1.5"/>
                    <path d="M8 40c0-7.73 6.27-14 14-14s14 6.27 14 14"
                          stroke="#1565c0" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    <line x1="14" y1="34" x2="30" y2="34"
                          stroke="#1565c0" stroke-width="1.3" stroke-linecap="round" opacity=".5"/>
                    <line x1="16" y1="38" x2="28" y2="38"
                          stroke="#1565c0" stroke-width="1.3" stroke-linecap="round" opacity=".5"/>
                </svg>
            </div>
            <i class="fas fa-chevron-right" style="color:#1565c0;font-size:.85rem;flex-shrink:0"></i>
        </a>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ── Stat cards ───────────────────────────────────────────── */
.cm-stat-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    padding: 22px 22px 18px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: box-shadow .2s, transform .15s;
}
.cm-stat-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.12);
    transform: translateY(-2px);
}
.cm-stat-icon {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cm-stat-body { flex: 1; min-width: 0; }
.cm-stat-label {
    font-size: .74rem;
    font-weight: 700;
    color: #9e9e9e;
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 2px;
}
.cm-stat-value {
    font-size: 2.6rem;
    font-weight: 800;
    color: #212121;
    line-height: 1.1;
    margin-bottom: 3px;
}
.cm-stat-sub {
    font-size: .8rem;
    color: #9e9e9e;
}

/* ── Quick-link cards ────────────────────────────────────── */
.quick-link-card { transition: box-shadow .2s, transform .15s; }
.quick-link-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.1) !important;
    transform: translateY(-2px);
}
.quick-link-illustration {
    width: 88px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
}
</style>
@endpush
