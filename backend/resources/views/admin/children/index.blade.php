@extends('layouts.app')
@section('title','Child Information')
@section('page-title','Child Information')

@section('content')

{{-- ── Breadcrumb ────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="fw-bold mb-1">Child Information</h4>
        <nav style="font-size:.83rem">
            <a href="{{ route('admin.dashboard') }}"
               style="color:#2e7d32;text-decoration:none">Dashboard</a>
            <span class="text-muted mx-1">›</span>
            <span class="text-muted">Child Information</span>
        </nav>
    </div>
    <a href="{{ route('admin.children.create') }}"
       class="btn btn-success rounded-3 px-3 py-2 fw-semibold"
       style="background:#2e7d32;border:none;font-size:.88rem">
        <i class="fas fa-plus me-2"></i>Add New Child
    </a>
</div>

{{-- ── 4 Stat Cards ─────────────────────────────────────────── --}}
@php
    $totalAll    = \App\Models\Child::count();
    $totalMale   = \App\Models\Child::where('gender','male')->count();
    $totalFemale = \App\Models\Child::where('gender','female')->count();
    $avgAge      = round(\App\Models\Child::selectRaw(
        'AVG(TIMESTAMPDIFF(YEAR,date_of_birth,CURDATE())) as a'
    )->value('a'), 1);
@endphp

<div class="row g-3 mb-4">

    {{-- Total Children --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <circle cx="9"  cy="7"  r="3.5" fill="#2e7d32"/>
                    <circle cx="15" cy="7"  r="3.5" fill="#66bb6a"/>
                    <path d="M2 19c0-3.31 3.13-6 7-6s7 2.69 7 6" stroke="#2e7d32" stroke-width="1.5" fill="none"/>
                    <path d="M15 13c2.5.6 4 2.3 4 5" stroke="#66bb6a" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Children</div>
                <div class="stat-value" style="color:#2e7d32">{{ $totalAll }}</div>
                <a href="#childTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Male --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" fill="#1565c0"/>
                    <path d="M4 20c0-3.31 3.58-6 8-6s8 2.69 8 6" stroke="#1565c0" stroke-width="1.6" fill="none"/>
                </svg>
            </div>
            <div class="stat-body">
                <div class="stat-label">Male</div>
                <div class="stat-value" style="color:#1565c0">{{ $totalMale }}</div>
                <a href="#childTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Female --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce4ec">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" fill="#c2185b"/>
                    <path d="M4 20c0-3.31 3.58-6 8-6s8 2.69 8 6" stroke="#c2185b" stroke-width="1.6" fill="none"/>
                    <line x1="12" y1="16" x2="12" y2="21" stroke="#c2185b" stroke-width="1.4"/>
                    <line x1="9" y1="19" x2="15" y2="19" stroke="#c2185b" stroke-width="1.4"/>
                </svg>
            </div>
            <div class="stat-body">
                <div class="stat-label">Female</div>
                <div class="stat-value" style="color:#c2185b">{{ $totalFemale }}</div>
                <a href="#childTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Average Age --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#e65100" opacity=".2"/>
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="#e65100" stroke-width="1.5"/>
                    <circle cx="12" cy="9" r="2.5" fill="#e65100"/>
                </svg>
            </div>
            <div class="stat-body">
                <div class="stat-label">Average Age</div>
                <div class="stat-value" style="color:#e65100">
                    {{ $avgAge }}
                    <span style="font-size:1rem;font-weight:500;color:#9e9e9e">years</span>
                </div>
                <a href="#childTable" class="stat-link">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Search + Filter row ───────────────────────────────────── --}}
<div class="dash-card" id="childTable">
    <div class="dash-card-header flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted">Show</span>
            <select id="pageSizeSelect"
                    class="form-select form-select-sm rounded-2"
                    style="width:70px"
                    onchange="changePageSize(this.value)">
                <option value="10" {{ request('per_page',10)==10?'selected':'' }}>10</option>
                <option value="25" {{ request('per_page',10)==25?'selected':'' }}>25</option>
                <option value="50" {{ request('per_page',10)==50?'selected':'' }}>50</option>
            </select>
            <span class="small text-muted">entries</span>
        </div>

        <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
            <form method="GET" id="searchForm" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="per_page" value="{{ request('per_page',10) }}">
                <div class="input-group" style="max-width:240px">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search child..."
                           style="border-right:none">
                    <button class="btn btn-success btn-sm"
                            style="background:#2e7d32;border-color:#2e7d32">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                {{-- Filters button --}}
                <button type="button"
                        class="btn btn-sm btn-outline-secondary rounded-2"
                        data-bs-toggle="collapse"
                        data-bs-target="#filterPanel">
                    <i class="fas fa-filter me-1"></i>Filters
                </button>
            </form>
        </div>
    </div>

    {{-- Filter panel --}}
    <div class="collapse {{ request('gender') || request('diagnosis') || request('status') ? 'show' : '' }}"
         id="filterPanel">
        <div class="px-3 pb-3 pt-1 border-bottom">
            <form method="GET" class="row g-2 align-items-end" id="filterForm">
                <input type="hidden" name="per_page" value="{{ request('per_page',10) }}">
                <input type="hidden" name="search"   value="{{ request('search') }}">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Gender</label>
                    <select name="gender" class="form-select form-select-sm">
                        <option value="">All Genders</option>
                        <option value="male"   {{ request('gender')=='male'   ?'selected':'' }}>Male</option>
                        <option value="female" {{ request('gender')=='female' ?'selected':'' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Diagnosis</label>
                    <select name="diagnosis" class="form-select form-select-sm">
                        <option value="">All Diagnoses</option>
                        @foreach(['ADHD','Autism','Mental Disability','Cerebral Palsy','Blindness','Other'] as $d)
                            <option value="{{ $d }}" {{ request('diagnosis')==$d?'selected':'' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active"    {{ request('status')=='active'   ?'selected':'' }}>Active</option>
                        <option value="inactive"  {{ request('status')=='inactive' ?'selected':'' }}>Inactive</option>
                        <option value="graduated" {{ request('status')=='graduated'?'selected':'' }}>Graduated</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button class="btn btn-success btn-sm w-100 rounded-2"
                            style="background:#2e7d32;border:none">Apply</button>
                    <a href="{{ route('admin.children.index') }}"
                       class="btn btn-outline-secondary btn-sm w-100 rounded-2">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Table ──────────────────────────────────────────────── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width:900px">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-2 small fw-bold" style="color:#2e7d32">ID</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Photo</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Full Name</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Date of Birth</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Age</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Gender</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Guardian</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Contact Number</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Diagnosis</th>
                    <th class="py-2 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($children as $i => $child)
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
                                        justify-content:center;font-weight:700;
                                        font-size:.88rem;border:2px solid #e8f5e9">
                                {{ strtoupper(substr($child->first_name,0,1)) }}
                            </div>
                        @endif
                    </td>

                    {{-- Full Name --}}
                    <td>
                        <div class="fw-semibold small">
                            {{ $child->first_name }}
                            {{ $child->middle_name ? $child->middle_name.' ' : '' }}{{ $child->last_name }}
                        </div>
                    </td>

                    {{-- Date of Birth --}}
                    <td class="small text-muted">
                        {{ $child->date_of_birth->format('F d, Y') }}
                    </td>

                    {{-- Age --}}
                    <td class="small fw-semibold">{{ $child->age }}</td>

                    {{-- Gender badge --}}
                    <td>
                        @if($child->gender === 'male')
                        <span class="badge rounded-pill px-3 py-1 fw-semibold"
                              style="background:#e3f2fd;color:#1565c0;font-size:.78rem">
                            Male
                        </span>
                        @else
                        <span class="badge rounded-pill px-3 py-1 fw-semibold"
                              style="background:#fce4ec;color:#c2185b;font-size:.78rem">
                            Female
                        </span>
                        @endif
                    </td>

                    {{-- Guardian --}}
                    <td>
                        <div class="small fw-semibold">{{ $child->guardian_name }}</div>
                        <div class="text-muted" style="font-size:.72rem">({{ ucfirst($child->gender === 'male' ? 'Father' : 'Mother') }})</div>
                    </td>

                    {{-- Contact --}}
                    <td class="small text-muted">{{ $child->guardian_contact }}</td>

                    {{-- Diagnosis --}}
                    <td style="max-width:160px">
                        @php
                            $dColors = [
                                'ADHD'             => '#ef5350',
                                'Autism'           => '#1565c0',
                                'Mental Disability'=> '#6a1b9a',
                                'Cerebral Palsy'   => '#e65100',
                                'Blindness'        => '#37474f',
                                'Other'            => '#2e7d32',
                            ];
                            $dc = $dColors[$child->diagnosis] ?? '#2e7d32';
                            $diagLabels = [
                                'ADHD'             => ['ADHD','Attention Deficit / Hyperactivity Disorder'],
                                'Autism'           => ['Autism','Autism Spectrum Disorder'],
                                'Mental Disability'=> ['Mental Disability','Intellectual / Developmental Disability'],
                                'Cerebral Palsy'   => ['Cerebral Palsy','Motor Disability'],
                                'Blindness'        => ['Blind','Visual Impairment'],
                                'Other'            => ['Other','Other Condition'],
                            ];
                            $dl = $diagLabels[$child->diagnosis] ?? [$child->diagnosis,''];
                        @endphp
                        <div class="d-flex align-items-start gap-1">
                            <span style="width:8px;height:8px;border-radius:50%;
                                         background:{{ $dc }};
                                         flex-shrink:0;margin-top:4px"></span>
                            <div>
                                <div class="fw-semibold small">{{ $dl[0] }}</div>
                                <div class="text-muted" style="font-size:.72rem">{{ $dl[1] }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.children.show',$child) }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e3f2fd;color:#1565c0;border:none"
                               title="View">
                                <i class="fas fa-eye" style="font-size:.8rem"></i>
                            </a>
                            <a href="{{ route('admin.children.edit',$child) }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e8f5e9;color:#2e7d32;border:none"
                               title="Edit">
                                <i class="fas fa-pencil-alt" style="font-size:.8rem"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.children.destroy',$child) }}"
                                  onsubmit="return confirm('Delete {{ $child->first_name }} {{ $child->last_name }}?')">
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
                    <td colspan="10" class="text-center py-5">
                        <div style="color:#bdbdbd">
                            <i class="fas fa-child fa-3x mb-3 d-block"></i>
                            <div class="fw-semibold">No children found.</div>
                            <div class="small text-muted mt-1">
                                <a href="{{ route('admin.children.create') }}"
                                   style="color:#2e7d32">Add the first child</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination row ─────────────────────────────────────── --}}
    @if($children->hasPages())
    <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            Showing {{ $children->firstItem() }} to {{ $children->lastItem() }}
            of {{ $children->total() }} entries
        </div>
        {{-- Custom green pagination --}}
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

                {{-- Page numbers --}}
                @foreach($children->getUrlRange(1,$children->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $children->currentPage() ? 'active' : '' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $url }}"
                       style="{{ $page == $children->currentPage()
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
    <div class="px-3 py-3 border-top">
        <div class="small text-muted">
            Showing {{ $children->count() }} entries
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function changePageSize(size) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', size);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}
</script>
@endpush
