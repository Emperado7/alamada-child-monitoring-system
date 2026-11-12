@extends('layouts.app')
@section('title','Attendance')
@section('page-title','Attendance')

@section('content')

{{-- ── Page Title ───────────────────────────────────────────── --}}
<div class="mb-4">
    <h4 class="fw-bold mb-1">Attendance</h4>
    <div class="text-muted small">Record and manage daily attendance of children.</div>
</div>

{{-- ── 3 Stat Cards ─────────────────────────────────────────── --}}
@php
    $staffId       = auth()->id();
    $selectedDate  = request('date', \Carbon\Carbon::today()->toDateString());
    $selectedMonth = request('month', \Carbon\Carbon::now()->format('Y-m'));

    // Only approved-enrollment children (10)
    $totalEnrolled = \App\Models\Enrollment::where('status','approved')->distinct('child_id')->count('child_id');
    $presentToday  = \App\Models\Attendance::where('attendance_date',$selectedDate)
                        ->where('status','present')->count();
    $absentToday   = \App\Models\Attendance::where('attendance_date',$selectedDate)
                        ->where('status','absent')->count();
    $presentPct    = $totalEnrolled > 0 ? round($presentToday/$totalEnrolled*100) : 0;
    $absentPct     = $totalEnrolled > 0 ? round($absentToday/$totalEnrolled*100)  : 0;
@endphp

<div class="row g-3 mb-4">

    {{-- Total Enrolled --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-center gap-4">
                <div style="width:60px;height:60px;border-radius:16px;
                            background:#e8f5e9;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
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
                    <div class="text-muted small mt-1">All enrolled children in the system.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Present Today --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-center gap-4">
                <div style="width:60px;height:60px;border-radius:16px;
                            background:#e8f5e9;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <div style="width:40px;height:40px;border-radius:50%;
                                background:#2e7d32;display:flex;align-items:center;
                                justify-content:center">
                        <i class="fas fa-check" style="color:#fff;font-size:1.1rem"></i>
                    </div>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        Present Today
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $presentToday }}
                    </div>
                    <div class="text-muted small mt-1">
                        {{ $presentPct }}% of total enrolled children
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Absent Today --}}
    <div class="col-md-4">
        <div class="dash-card p-4 h-100">
            <div class="d-flex align-items-center gap-4">
                <div style="width:60px;height:60px;border-radius:16px;
                            background:#ffebee;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <div style="width:40px;height:40px;border-radius:50%;
                                background:#ffebee;border:3px solid #c62828;
                                display:flex;align-items:center;justify-content:center">
                        <i class="fas fa-times" style="color:#c62828;font-size:1rem"></i>
                    </div>
                </div>
                <div>
                    <div class="text-muted small fw-semibold"
                         style="text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">
                        Absent Today
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1.1">
                        {{ $absentToday }}
                    </div>
                    <div class="text-muted small mt-1">
                        {{ $absentPct }}% of total enrolled children
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Attendance Form ──────────────────────────────────────── --}}
<div class="dash-card">

    {{-- Step 1 + Step 2 row --}}
    <div class="d-flex align-items-start gap-4 p-4 border-bottom flex-wrap">

        {{-- 1. Select Date --}}
        <div style="min-width:240px">
            <div class="fw-bold mb-1" style="font-size:.95rem">
                1. Select Date
            </div>
            <div class="text-muted small mb-2">Choose the date for attendance.</div>
            <form method="GET" id="dateForm">
                {{-- Hidden actual date value submitted --}}
                <input type="hidden" name="date" id="hiddenDateInput" value="{{ $selectedDate }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">

                {{-- Clickable display --}}
                <div class="input-group rounded-3" style="max-width:260px;border:1.5px solid #e0e0e0;border-radius:10px;overflow:hidden;cursor:pointer"
                     onclick="toggleCalendar()">
                    <span class="input-group-text bg-white border-0" style="color:#2e7d32">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <span class="form-control border-0 shadow-none bg-white d-flex align-items-center"
                          id="dateDisplay" style="font-size:.88rem;cursor:pointer">
                        {{ \Carbon\Carbon::parse($selectedDate)->format('m/d/Y') }}
                    </span>
                    <span class="input-group-text bg-white border-0 text-muted">
                        <i class="fas fa-chevron-down" style="font-size:.7rem"></i>
                    </span>
                </div>

                {{-- Custom Calendar Popup --}}
                <div id="customCalendar"
                     style="display:none;position:absolute;z-index:9999;background:#fff;
                            border:1px solid #e0e0e0;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.12);
                            padding:16px;width:280px;margin-top:4px">
                    {{-- Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <button type="button" onclick="changeMonth(-1)"
                                style="background:none;border:none;cursor:pointer;color:#2e7d32;font-size:1rem;padding:4px 8px">
                            ‹
                        </button>
                        <span id="calMonthYear" class="fw-bold" style="font-size:.9rem"></span>
                        <button type="button" onclick="changeMonth(1)"
                                style="background:none;border:none;cursor:pointer;color:#2e7d32;font-size:1rem;padding:4px 8px">
                            ›
                        </button>
                    </div>
                    {{-- Day headers --}}
                    <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;margin-bottom:4px">
                        @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $d)
                        <div style="font-size:.72rem;font-weight:700;color:#9e9e9e;padding:4px 0">{{ $d }}</div>
                        @endforeach
                    </div>
                    {{-- Days grid --}}
                    <div id="calDays" style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;gap:2px"></div>

                    <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                        <button type="button" onclick="selectToday()"
                                style="background:none;border:none;color:#2e7d32;font-size:.8rem;font-weight:600;cursor:pointer">
                            Today
                        </button>
                        <button type="button" onclick="toggleCalendar()"
                                style="background:none;border:none;color:#9e9e9e;font-size:.8rem;cursor:pointer">
                            Close
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 2. Select Child --}}
        <div class="flex-grow-1">
            <div class="fw-bold mb-1" style="font-size:.95rem">
                2. Select Child
            </div>
            <div class="text-muted small mb-2">
                Search and select a child to mark attendance.
            </div>
            <form method="GET" id="searchForm" class="d-flex gap-2">
                <input type="hidden" name="date" value="{{ $selectedDate }}">
                <div class="input-group" style="max-width:340px">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted small"></i>
                    </span>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control border-start-0 ps-0"
                           placeholder="Search by child name or ID...">
                </div>
                <select name="status"
                        class="form-select form-select-sm rounded-3"
                        style="width:140px"
                        onchange="document.getElementById('searchForm').submit()">
                    <option value="">All Status</option>
                    <option value="present" {{ request('status')=='present'?'selected':'' }}>Present</option>
                    <option value="absent"  {{ request('status')=='absent' ?'selected':'' }}>Absent</option>
                    <option value="excused" {{ request('status')=='excused'?'selected':'' }}>Excused</option>
                </select>
            </form>
        </div>
    </div>

    {{-- ── Attendance Table ────────────────────────────────── --}}
    <form method="POST" action="{{ route('staff.attendance.save') }}" id="attendanceForm">
        @csrf
        <input type="hidden" name="date" value="{{ $selectedDate }}">

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background:#f8f9fa">
                        <th class="px-3 py-2 small fw-bold" style="color:#2e7d32;width:40px">#</th>
                        <th class="py-2 small fw-bold" style="color:#2e7d32">Child ID</th>
                        <th class="py-2 small fw-bold" style="color:#2e7d32">Child Name</th>
                        <th class="py-2 small fw-bold" style="color:#2e7d32">Age</th>
                        <th class="py-2 small fw-bold" style="color:#2e7d32">Status</th>
                        <th class="py-2 small fw-bold text-center pe-3" style="color:#2e7d32">Attendance</th>
                        <th class="py-2 small fw-bold text-center" style="color:#2e7d32">Fetched By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($children as $i => $child)
                    @php
                        $existing = $attendanceMap[$child->id] ?? null;
                        $curStatus = $existing ? $existing->status : null;
                    @endphp
                    <tr id="row-{{ $child->id }}">
                        {{-- # --}}
                        <td class="px-3 text-muted small">{{ $i + 1 }}</td>

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
                                         width="36" height="36"
                                         style="border:2px solid #e8f5e9">
                                @else
                                    <div style="width:36px;height:36px;border-radius:50%;
                                                background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                                color:#fff;display:flex;align-items:center;
                                                justify-content:center;font-weight:700;
                                                font-size:.8rem;flex-shrink:0">
                                        {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="fw-semibold small">
                                    {{ $child->last_name }}, {{ $child->first_name }}
                                    {{ $child->middle_name ? substr($child->middle_name,0,1).'.' : '' }}
                                </div>
                            </div>
                        </td>

                        {{-- Age --}}
                        <td class="small fw-semibold">{{ $child->age }}</td>

                        {{-- Status --}}
                        <td>
                            <span class="badge rounded-2 px-3 py-2 fw-semibold"
                                  style="background:#e8f5e9;color:#2e7d32;font-size:.78rem">
                                Active
                            </span>
                        </td>

                        {{-- Present / Absent buttons --}}
                        <td class="text-center pe-3">
                            <div class="d-flex justify-content-center gap-2">

                                {{-- PRESENT --}}
                                <label style="cursor:pointer;margin:0">
                                    <input type="radio"
                                           name="attendance[{{ $child->id }}]"
                                           value="present"
                                           class="d-none att-radio-{{ $child->id }}"
                                           {{ $curStatus === 'present' ? 'checked' : '' }}
                                           onchange="highlightBtn({{ $child->id }}, 'present')">
                                    <div id="btn-present-{{ $child->id }}"
                                         onclick="selectAtt({{ $child->id }}, 'present')"
                                         class="att-btn-present {{ $curStatus === 'present' ? 'selected' : '' }}"
                                         style="border-radius:8px;padding:6px 14px;
                                                font-size:.82rem;font-weight:700;
                                                cursor:pointer;user-select:none;
                                                display:flex;align-items:center;gap:5px;
                                                border:1.5px solid {{ $curStatus === 'present' ? '#2e7d32' : '#e0e0e0' }};
                                                background:{{ $curStatus === 'present' ? '#2e7d32' : '#fff' }};
                                                color:{{ $curStatus === 'present' ? '#fff' : '#757575' }};
                                                transition:all .15s">
                                        <i class="fas fa-check-circle"></i> Present
                                    </div>
                                </label>

                                {{-- ABSENT --}}
                                <label style="cursor:pointer;margin:0">
                                    <input type="radio"
                                           name="attendance[{{ $child->id }}]"
                                           value="absent"
                                           class="d-none att-radio-{{ $child->id }}"
                                           {{ $curStatus === 'absent' ? 'checked' : '' }}
                                           onchange="highlightBtn({{ $child->id }}, 'absent')">
                                    <div id="btn-absent-{{ $child->id }}"
                                         onclick="selectAtt({{ $child->id }}, 'absent')"
                                         class="att-btn-absent {{ $curStatus === 'absent' ? 'selected' : '' }}"
                                         style="border-radius:8px;padding:6px 14px;
                                                font-size:.82rem;font-weight:700;
                                                cursor:pointer;user-select:none;
                                                display:flex;align-items:center;gap:5px;
                                                border:1.5px solid {{ $curStatus === 'absent' ? '#c62828' : '#e0e0e0' }};
                                                background:{{ $curStatus === 'absent' ? '#c62828' : '#fff' }};
                                                color:{{ $curStatus === 'absent' ? '#fff' : '#757575' }};
                                                transition:all .15s">
                                        <i class="fas fa-times-circle"></i> Absent
                                    </div>
                                </label>

                            </div>
                        </td>

                        {{-- Fetched By (guardian relationship) --}}
                        <td>
                            <select name="fetched_by[{{ $child->id }}]"
                                    class="form-select form-select-sm rounded-2"
                                    style="min-width:145px;font-size:.8rem">
                                <option value="">— Select —</option>
                                @foreach([
                                    'Mother'        => 'Mother',
                                    'Father'        => 'Father',
                                    'Grandmother'   => 'Grandmother',
                                    'Grandfather'   => 'Grandfather',
                                    'Aunt'          => 'Aunt',
                                    'Uncle'         => 'Uncle',
                                    'Older Sibling' => 'Older Sibling',
                                    'Guardian'      => 'Guardian',
                                    'Other'         => 'Other',
                                ] as $val => $label)
                                    @php $curFetched = $existing?->fetched_by ?? ''; @endphp
                                    <option value="{{ $val }}"
                                        {{ $curFetched === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div style="color:#bdbdbd">
                                <i class="fas fa-child fa-3x mb-3 d-block"></i>
                                <div class="fw-semibold">No active children found.</div>
                                <div class="small text-muted mt-1">
                                    Enroll children first to mark attendance.
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($children->isNotEmpty())
        {{-- Save + Clear row --}}
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
            <div></div>
            <div class="d-flex gap-3 align-items-center">
                <button type="button"
                        onclick="clearAll()"
                        class="btn btn-sm rounded-3"
                        style="background:none;border:none;color:#757575;font-size:.85rem">
                    <i class="fas fa-sync-alt me-1"></i>Clear Selections
                </button>
                <button type="submit"
                        class="btn btn-success rounded-3 px-4 py-2 fw-bold"
                        style="background:#2e7d32;border:none;font-size:.92rem">
                    <i class="fas fa-save me-2"></i>Save Attendance
                </button>
            </div>
        </div>
        @endif
    </form>

    {{-- Info banner --}}
    <div class="mx-3 mb-3 p-3 rounded-3 d-flex align-items-center gap-2"
         style="background:#e3f2fd;border:1px solid #bbdefb">
        <i class="fas fa-info-circle" style="color:#1565c0;font-size:1rem;flex-shrink:0"></i>
        <span style="font-size:.83rem;color:#1565c0">
            Please make sure to select the correct date and mark attendance for all children.
        </span>
    </div>
</div>

@endsection

@push('styles')
<style>
.att-btn-present:hover {
    background: #e8f5e9 !important;
    border-color: #2e7d32 !important;
    color: #2e7d32 !important;
}
.att-btn-absent:hover {
    background: #ffebee !important;
    border-color: #c62828 !important;
    color: #c62828 !important;
}
</style>
@endpush

@push('scripts')
<script>
function selectAtt(childId, status) {
    // Check the hidden radio
    document.querySelectorAll(`.att-radio-${childId}`)
        .forEach(r => r.checked = (r.value === status));

    updateBtnStyles(childId, status);
}

function highlightBtn(childId, status) {
    updateBtnStyles(childId, status);
}

function updateBtnStyles(childId, status) {
    const pBtn = document.getElementById(`btn-present-${childId}`);
    const aBtn = document.getElementById(`btn-absent-${childId}`);

    if (status === 'present') {
        // Present selected
        pBtn.style.background   = '#2e7d32';
        pBtn.style.borderColor  = '#2e7d32';
        pBtn.style.color        = '#fff';
        // Absent deselected
        aBtn.style.background   = '#fff';
        aBtn.style.borderColor  = '#e0e0e0';
        aBtn.style.color        = '#757575';
    } else {
        // Absent selected
        aBtn.style.background   = '#c62828';
        aBtn.style.borderColor  = '#c62828';
        aBtn.style.color        = '#fff';
        // Present deselected
        pBtn.style.background   = '#fff';
        pBtn.style.borderColor  = '#e0e0e0';
        pBtn.style.color        = '#757575';
    }
}

function clearAll() {
    if (!confirm('Clear all attendance selections?')) return;
    document.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);

    // Reset all button styles
    document.querySelectorAll('[id^="btn-present-"], [id^="btn-absent-"]').forEach(btn => {
        btn.style.background  = '#fff';
        btn.style.borderColor = '#e0e0e0';
        btn.style.color       = '#757575';
    });
}
</script>

{{-- ── Custom Calendar Script ──────────────────────────────── --}}
<script>
(function() {
    // Selected date from server
    var selDate  = new Date('{{ $selectedDate }}');
    var viewYear = selDate.getFullYear();
    var viewMonth= selDate.getMonth(); // 0-indexed

    var today    = new Date();
    today.setHours(0,0,0,0);
    // Min = start of current month
    var minDate  = new Date(today.getFullYear(), today.getMonth(), 1);
    // Max = today
    var maxDate  = new Date(today);

    var months = ['January','February','March','April','May','June',
                  'July','August','September','October','November','December'];

    function pad(n){ return n < 10 ? '0'+n : n; }
    function toISO(d){ return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); }

    function renderCalendar() {
        document.getElementById('calMonthYear').textContent =
            months[viewMonth] + ' ' + viewYear;

        var grid = document.getElementById('calDays');
        grid.innerHTML = '';

        var firstDay = new Date(viewYear, viewMonth, 1).getDay();
        var daysInMonth = new Date(viewYear, viewMonth+1, 0).getDate();

        // Empty cells before first day
        for (var i = 0; i < firstDay; i++) {
            grid.innerHTML += '<div></div>';
        }

        for (var d = 1; d <= daysInMonth; d++) {
            var thisDate = new Date(viewYear, viewMonth, d);
            thisDate.setHours(0,0,0,0);

            var iso      = toISO(thisDate);
            var isToday  = thisDate.getTime() === today.getTime();
            var isSelected = iso === toISO(selDate);
            var isPast   = thisDate < minDate;   // before start of month
            var isFuture = thisDate > maxDate;   // after today
            var disabled = isPast || isFuture;

            var bg    = isSelected ? '#2e7d32' : isToday ? '#e8f5e9' : 'transparent';
            var color = isSelected ? '#fff' : disabled ? '#c0c0c0' : isToday ? '#2e7d32' : '#212121';
            var fw    = isSelected || isToday ? '700' : '400';
            var cursor= disabled ? 'default' : 'pointer';
            var strike= disabled ? 'line-through' : 'none';

            var cell = '<div onclick="' + (disabled ? '' : 'pickDate(\''+iso+'\')') + '"'
                + ' style="border-radius:50%;width:34px;height:34px;display:flex;align-items:center;'
                + 'justify-content:center;margin:auto;font-size:.82rem;font-weight:'+fw+';'
                + 'background:'+bg+';color:'+color+';cursor:'+cursor+';'
                + 'text-decoration:'+strike+';transition:background .1s"'
                + (disabled ? '' : ' onmouseover="if(this.style.background===\'transparent\')this.style.background=\'#f5f5f5\'"'
                             + ' onmouseout="if(this.style.background===\'#f5f5f5\')this.style.background=\'transparent\'"')
                + '>' + d + '</div>';
            grid.innerHTML += cell;
        }
    }

    window.toggleCalendar = function() {
        var cal = document.getElementById('customCalendar');
        cal.style.display = cal.style.display === 'none' ? 'block' : 'none';
        if (cal.style.display === 'block') renderCalendar();
    };

    window.changeMonth = function(dir) {
        viewMonth += dir;
        if (viewMonth < 0)  { viewMonth = 11; viewYear--; }
        if (viewMonth > 11) { viewMonth = 0;  viewYear++; }
        renderCalendar();
    };

    window.pickDate = function(iso) {
        selDate = new Date(iso);
        document.getElementById('hiddenDateInput').value = iso;
        document.getElementById('dateDisplay').textContent =
            (selDate.getMonth()+1) + '/' + pad(selDate.getDate()) + '/' + selDate.getFullYear();
        document.getElementById('customCalendar').style.display = 'none';
        document.getElementById('dateForm').submit();
    };

    window.selectToday = function() {
        pickDate(toISO(today));
    };

    // Close calendar when clicking outside
    document.addEventListener('click', function(e) {
        var cal = document.getElementById('customCalendar');
        var wrap = cal ? cal.closest('div[style*="min-width"]') : null;
        if (cal && cal.style.display === 'block') {
            if (!cal.contains(e.target) && !e.target.closest('.input-group')) {
                cal.style.display = 'none';
            }
        }
    });
})();
</script>
@endpush
