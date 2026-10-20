@extends('layouts.app')
@section('title','Attendance')
@section('page-title','Attendance')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="mb-4">
    <h4 class="fw-bold mb-1">Attendance</h4>
    <div class="text-muted small">Record and manage daily attendance of children.</div>
</div>

{{-- ── 3 Stat Cards ─────────────────────────────────────────── --}}
@php
    $totalEnrolled = \App\Models\Child::where('status','active')->count();
    $today         = $date ?? now()->toDateString();
    $presentToday  = \App\Models\Attendance::where('attendance_date', $today)
                         ->where('status','present')->count();
    $absentToday   = \App\Models\Attendance::where('attendance_date', $today)
                         ->where('status','absent')->count();
    $presentPct    = $totalEnrolled > 0 ? round($presentToday / $totalEnrolled * 100) : 0;
    $absentPct     = $totalEnrolled > 0 ? round($absentToday  / $totalEnrolled * 100) : 0;
@endphp

<div class="row g-3 mb-4">

    {{-- Total Enrolled --}}
    <div class="col-12 col-md-4">
        <div class="att-stat-card h-100">
            <div class="att-stat-icon" style="background:#e8f5e9">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                    <circle cx="9"  cy="7"  r="3.4" fill="#2e7d32"/>
                    <circle cx="15" cy="7"  r="3.4" fill="#66bb6a"/>
                    <path d="M2 19.5c0-3.31 3.13-6 7-6s7 2.69 7 6"
                          stroke="#2e7d32" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                    <path d="M15 13.5c2.4.8 3.8 2.6 4 5"
                          stroke="#66bb6a" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
            <div class="att-stat-body">
                <div class="att-stat-label">Total Enrolled Children</div>
                <div class="att-stat-value">{{ $totalEnrolled }}</div>
                <div class="att-stat-sub">All enrolled children in the system.</div>
            </div>
        </div>
    </div>

    {{-- Present Today --}}
    <div class="col-12 col-md-4">
        <div class="att-stat-card h-100">
            <div class="att-stat-icon" style="background:#e8f5e9">
                <div style="width:42px;height:42px;border-radius:50%;
                            background:#2e7d32;display:flex;align-items:center;
                            justify-content:center">
                    <i class="fas fa-check" style="color:#fff;font-size:1.2rem"></i>
                </div>
            </div>
            <div class="att-stat-body">
                <div class="att-stat-label">Present Today</div>
                <div class="att-stat-value">{{ $presentToday }}</div>
                <div class="att-stat-sub">{{ $presentPct }}% of total enrolled children</div>
            </div>
        </div>
    </div>

    {{-- Absent Today --}}
    <div class="col-12 col-md-4">
        <div class="att-stat-card h-100">
            <div class="att-stat-icon" style="background:#ffebee">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                    <circle cx="10" cy="7.5" r="3.5" fill="#e53935" opacity=".2"/>
                    <circle cx="10" cy="7.5" r="3.5" stroke="#e53935" stroke-width="1.6"/>
                    <path d="M3 20c0-3.31 3.13-6 7-6s7 2.69 7 6"
                          stroke="#e53935" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                    <line x1="17.5" y1="11" x2="21.5" y2="15" stroke="#e53935" stroke-width="2" stroke-linecap="round"/>
                    <line x1="21.5" y1="11" x2="17.5" y2="15" stroke="#e53935" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="att-stat-body">
                <div class="att-stat-label">Absent Today</div>
                <div class="att-stat-value">{{ $absentToday }}</div>
                <div class="att-stat-sub">{{ $absentPct }}% of total enrolled children</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Main Form Card ───────────────────────────────────────── --}}
<form method="POST" action="{{ route('staff.attendance.save') }}" id="attendanceForm">
    @csrf
    <input type="hidden" name="date" id="hiddenDate" value="{{ $date }}">

    <div class="dash-card mb-3">

        {{-- ── Step row: date picker + child search ────────── --}}
        <div class="row g-0 border-bottom">

            {{-- 1. Select Date --}}
            <div class="col-12 col-md-5 p-4" style="border-right:1px solid #f0f0f0">
                <div class="fw-bold mb-1" style="font-size:.95rem">1. Select Date</div>
                <div class="text-muted small mb-3">Choose the date for attendance.</div>

                <div class="input-group" style="max-width:300px">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-calendar-alt" style="color:#9e9e9e;font-size:.85rem"></i>
                    </span>
                    <input type="date" name="date_select" id="dateInput"
                           value="{{ $date }}"
                           class="form-control form-control-sm border-start-0"
                           onchange="updateDate(this.value)">
                    <span class="input-group-text bg-white border-start-0"
                          style="font-size:.82rem;color:#555;cursor:pointer"
                          onclick="document.getElementById('dateInput').showPicker()">
                        ▼
                    </span>
                </div>
                <div class="text-muted small mt-2" id="dateFriendly">
                    {{ \Carbon\Carbon::parse($date)->format('M d, Y (l)') }}
                </div>
            </div>

            {{-- 2. Select Child --}}
            <div class="col-12 col-md-7 p-4">
                <div class="fw-bold mb-1" style="font-size:.95rem">2. Select Child</div>
                <div class="text-muted small mb-3">Search and select a child to mark attendance.</div>

                <div class="d-flex gap-2">
                    <div class="input-group" style="max-width:340px">
                        <span class="input-group-text bg-white border-end-0 pe-1">
                            <i class="fas fa-search" style="color:#9e9e9e;font-size:.82rem"></i>
                        </span>
                        <input type="text" id="childSearch"
                               class="form-control form-control-sm border-start-0 ps-1"
                               placeholder="Search by child name or ID..."
                               oninput="filterRows(this.value)">
                    </div>

                    <select id="statusFilter"
                            class="form-select form-select-sm rounded-3"
                            style="width:150px"
                            onchange="filterRows()">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ── Table ────────────────────────────────────────── --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width:680px">
                <thead>
                    <tr style="background:#f1f8f2">
                        <th class="px-3 py-3 small fw-bold" style="color:#2e7d32;width:44px">#</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Child ID</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Child Name</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Age</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Status</th>
                        <th class="py-3 small fw-bold text-center" style="color:#2e7d32">Attendance</th>
                    </tr>
                </thead>
                <tbody id="childTableBody">
                    @forelse($children as $i => $child)
                    @php $cur = $existing[$child->id] ?? null; @endphp
                    <tr class="child-row"
                        data-name="{{ strtolower($child->first_name.' '.$child->last_name) }}"
                        data-id="CHD-{{ str_pad($child->id, 3, '0', STR_PAD_LEFT) }}"
                        data-status="{{ strtolower($child->status ?? 'active') }}">

                        {{-- # --}}
                        <td class="px-3 text-muted small">{{ $i + 1 }}</td>

                        {{-- Child ID --}}
                        <td class="small fw-semibold" style="color:#555">
                            CHD-{{ str_pad($child->id, 3, '0', STR_PAD_LEFT) }}
                        </td>

                        {{-- Photo + Name --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($child->photo)
                                    <img src="{{ asset('storage/'.$child->photo) }}"
                                         class="rounded-circle object-fit-cover"
                                         width="36" height="36"
                                         style="border:2px solid #e8f5e9;flex-shrink:0">
                                @else
                                    <div style="width:36px;height:36px;border-radius:50%;
                                                background:linear-gradient(135deg,#1565c0,#90caf9);
                                                color:#fff;display:flex;align-items:center;
                                                justify-content:center;font-weight:700;
                                                font-size:.82rem;flex-shrink:0">
                                        {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="small fw-semibold">
                                    {{ $child->last_name }}, {{ $child->first_name }}
                                    {{ $child->middle_name ?? '' }}
                                </span>
                            </div>
                        </td>

                        {{-- Age --}}
                        <td class="small fw-semibold text-center">
                            {{ $child->age ?? (floor(now()->diffInMonths(\Carbon\Carbon::parse($child->birthdate)) / 12)) }}
                        </td>

                        {{-- Status badge --}}
                        <td>
                            @php
                                $sc = match(strtolower($child->status ?? 'active')) {
                                    'active'   => ['bg'=>'#e8f5e9','c'=>'#2e7d32','l'=>'Active'],
                                    'inactive' => ['bg'=>'#f5f5f5','c'=>'#616161','l'=>'Inactive'],
                                    default    => ['bg'=>'#f5f5f5','c'=>'#9e9e9e','l'=>ucfirst($child->status ?? '')],
                                };
                            @endphp
                            <span class="badge rounded-2 px-3 py-2 fw-semibold"
                                  style="background:{{ $sc['bg'] }};color:{{ $sc['c'] }};font-size:.78rem">
                                {{ $sc['l'] }}
                            </span>
                        </td>

                        {{-- Attendance: Present / Absent buttons --}}
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <label class="mb-0">
                                    <input type="radio"
                                           name="attendance[{{ $child->id }}]"
                                           value="present"
                                           class="d-none"
                                           {{ $cur === 'present' ? 'checked' : '' }}
                                           onchange="highlightRow(this, {{ $child->id }})">
                                    <div class="att-btn att-present {{ $cur === 'present' ? 'att-active-present' : '' }}"
                                         id="present-btn-{{ $child->id }}"
                                         onclick="pickStatus({{ $child->id }}, 'present')">
                                        <i class="fas fa-check-circle me-1" style="font-size:.78rem"></i>Present
                                    </div>
                                </label>
                                <label class="mb-0">
                                    <input type="radio"
                                           name="attendance[{{ $child->id }}]"
                                           value="absent"
                                           class="d-none"
                                           {{ $cur === 'absent' ? 'checked' : '' }}
                                           onchange="highlightRow(this, {{ $child->id }})">
                                    <div class="att-btn att-absent {{ $cur === 'absent' ? 'att-active-absent' : '' }}"
                                         id="absent-btn-{{ $child->id }}"
                                         onclick="pickStatus({{ $child->id }}, 'absent')">
                                        <i class="fas fa-times-circle me-1" style="font-size:.78rem"></i>Absent
                                    </div>
                                </label>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div style="color:#bdbdbd">
                                <i class="fas fa-child fa-3x mb-3 d-block"></i>
                                <div class="fw-semibold">No children found.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Action row ───────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div></div>{{-- spacer --}}
        <div class="d-flex align-items-center gap-3">
            <button type="submit"
                    class="btn rounded-3 px-4 py-2 fw-semibold d-flex align-items-center gap-2"
                    style="background:#2e7d32;color:#fff;border:none;font-size:.88rem">
                <i class="fas fa-save"></i> Save Attendance
            </button>
            <button type="button"
                    class="btn btn-outline-secondary rounded-3 px-3 py-2 d-flex align-items-center gap-2"
                    style="font-size:.88rem"
                    onclick="clearAll()">
                <i class="fas fa-undo"></i> Clear Selections
            </button>
        </div>
    </div>

    {{-- ── Info bar ─────────────────────────────────────────── --}}
    <div class="rounded-3 px-4 py-3 d-flex align-items-center gap-2"
         style="background:#e3f2fd;border:1px solid #90caf9">
        <i class="fas fa-info-circle" style="color:#1565c0;font-size:1rem;flex-shrink:0"></i>
        <span class="small" style="color:#1565c0">
            Please make sure to select the correct date and mark attendance for all children.
        </span>
    </div>

</form>

@endsection

@push('styles')
<style>
/* ── Stat cards ──────────────────────────────────────────── */
.att-stat-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: box-shadow .2s, transform .15s;
}
.att-stat-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.12);
    transform: translateY(-2px);
}
.att-stat-icon {
    width: 66px; height: 66px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.att-stat-body { flex: 1; min-width: 0; }
.att-stat-label {
    font-size: .73rem; font-weight: 700;
    color: #9e9e9e; text-transform: uppercase;
    letter-spacing: .07em; margin-bottom: 2px;
}
.att-stat-value {
    font-size: 2.5rem; font-weight: 800;
    color: #212121; line-height: 1.1; margin-bottom: 3px;
}
.att-stat-sub { font-size: .8rem; color: #9e9e9e; }

/* ── Present / Absent inline buttons ────────────────────── */
.att-btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    border: 1.5px solid #e0e0e0;
    background: #f5f5f5;
    color: #757575;
    user-select: none;
    transition: background .12s, border-color .12s, color .12s;
    white-space: nowrap;
}
.att-btn:hover { opacity: .85; }

/* Active present */
.att-active-present,
.att-present:hover {
    background: #e8f5e9 !important;
    border-color: #2e7d32 !important;
    color: #2e7d32 !important;
}
/* Active absent */
.att-active-absent,
.att-absent:hover {
    background: #ffebee !important;
    border-color: #c62828 !important;
    color: #c62828 !important;
}
</style>
@endpush

@push('scripts')
<script>
// Pick present or absent for a child
function pickStatus(childId, status) {
    const radios = document.querySelectorAll(`input[name="attendance[${childId}]"]`);
    radios.forEach(r => { r.checked = (r.value === status); });

    const pBtn = document.getElementById(`present-btn-${childId}`);
    const aBtn = document.getElementById(`absent-btn-${childId}`);
    if (!pBtn || !aBtn) return;

    pBtn.classList.remove('att-active-present');
    aBtn.classList.remove('att-active-absent');

    if (status === 'present') pBtn.classList.add('att-active-present');
    if (status === 'absent')  aBtn.classList.add('att-active-absent');
}

// Clear all selections
function clearAll() {
    document.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);
    document.querySelectorAll('.att-btn').forEach(b => {
        b.classList.remove('att-active-present', 'att-active-absent');
    });
}

// Filter table rows by search text and status
function filterRows(searchVal) {
    const search = (searchVal ?? document.getElementById('childSearch').value).toLowerCase();
    const status = document.getElementById('statusFilter').value.toLowerCase();
    document.querySelectorAll('.child-row').forEach(row => {
        const name    = row.dataset.name  ?? '';
        const id      = row.dataset.id    ?? '';
        const rowStat = row.dataset.status ?? '';
        const matchSearch = name.includes(search) || id.toLowerCase().includes(search);
        const matchStatus = !status || rowStat === status;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

// Update date display and hidden input
function updateDate(val) {
    document.getElementById('hiddenDate').value = val;
    // Refresh the page with the new date
    const url = new URL(window.location.href);
    url.searchParams.set('date', val);
    window.location.href = url.toString();
}
</script>
@endpush
