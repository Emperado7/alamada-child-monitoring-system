@extends('layouts.app')
@section('title','Mark Attendance')
@section('page-title','Mark Attendance')

@section('content')

{{-- ── Date selector ─────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex flex-wrap gap-3 align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-calendar-alt text-primary"></i>
                <label class="fw-semibold mb-0">Date:</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="form-control form-control-sm"
                       style="width:170px"
                       onchange="this.form.submit()">
            </div>
            <span class="text-muted small">
                {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}
            </span>
            <a href="{{ route('staff.attendance.mark') }}"
               class="btn btn-sm btn-outline-secondary rounded-3 ms-auto">
                <i class="fas fa-calendar-day me-1"></i>Today
            </a>
            <a href="{{ route('staff.attendance.index') }}"
               class="btn btn-sm btn-outline-primary rounded-3">
                <i class="fas fa-chart-bar me-1"></i>View Summary
            </a>
        </form>
    </div>
</div>

@if($children->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="fas fa-child fa-3x mb-3 d-block" style="color:#E0E0E0"></i>
        <h5 class="text-muted">No children assigned to you yet.</h5>
        <p class="text-muted small">
            Children will appear here after enrollment is approved by the administrator.
        </p>
        <a href="{{ route('staff.enrollment.create') }}" class="btn btn-primary rounded-3">
            <i class="fas fa-plus me-1"></i>Enroll a Child
        </a>
    </div>
</div>
@else

{{-- ── Bulk set row ──────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3 p-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <span class="small fw-semibold text-muted">
            <i class="fas fa-users me-1"></i>
            {{ $children->count() }} children · Set all as:
        </span>
        <button type="button" onclick="setAll('present')"
                class="btn btn-sm btn-success rounded-3 px-3">
            <i class="fas fa-check me-1"></i>All Present
        </button>
        <button type="button" onclick="setAll('absent')"
                class="btn btn-sm btn-danger rounded-3 px-3">
            <i class="fas fa-times me-1"></i>All Absent
        </button>
        <button type="button" onclick="setAll('late')"
                class="btn btn-sm btn-warning rounded-3 px-3">
            <i class="fas fa-clock me-1"></i>All Late
        </button>
        <button type="button" onclick="setAll('excused')"
                class="btn btn-sm btn-info rounded-3 px-3">
            <i class="fas fa-notes-medical me-1"></i>All Excused
        </button>

        {{-- Save button top --}}
        <button type="button" onclick="submitForm()"
                class="btn btn-primary rounded-3 px-4 ms-auto">
            <i class="fas fa-save me-1"></i>Save Attendance
        </button>
    </div>
</div>

{{-- ── Attendance Cards ───────────────────────────────────────── --}}
<form method="POST" action="{{ route('staff.attendance.save') }}" id="attendanceForm">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">

    <div class="row g-3">
        @foreach($children as $child)
        @php $cur = $existing[$child->id] ?? 'present'; @endphp

        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 attendance-card"
                 id="card-{{ $child->id }}"
                 data-status="{{ $cur }}">

                <div class="card-body p-3">

                    {{-- Child info row --}}
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:46px;height:46px;border-radius:50%;
                                    background:linear-gradient(135deg,#1565C0,#5e92f3);
                                    color:#fff;display:flex;align-items:center;
                                    justify-content:center;font-weight:700;
                                    font-size:1.1rem;flex-shrink:0">
                            {{ strtoupper(substr($child->first_name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-truncate">
                                {{ $child->first_name }} {{ $child->last_name }}
                            </div>
                            <div class="text-muted small">
                                Age {{ $child->age }} · {{ ucfirst($child->gender) }}
                            </div>
                            <div class="small" style="color:#1565C0">
                                {{ $child->diagnosis }}
                            </div>
                        </div>
                        {{-- Current status indicator --}}
                        <div class="status-badge-{{ $child->id }} badge rounded-pill px-3 py-2"
                             style="font-size:.8rem;font-weight:700">
                        </div>
                    </div>

                    {{-- Status buttons --}}
                    <div class="row g-2">

                        {{-- PRESENT --}}
                        <div class="col-6">
                            <label class="w-100 cursor-pointer">
                                <input type="radio"
                                       name="attendance[{{ $child->id }}]"
                                       value="present"
                                       class="d-none att-radio-{{ $child->id }}"
                                       {{ $cur === 'present' ? 'checked' : '' }}
                                       onchange="updateCard({{ $child->id }}, 'present')"
                                       required>
                                <div class="btn-att btn-present {{ $cur === 'present' ? 'active' : '' }} w-100 text-center rounded-3 py-2"
                                     onclick="selectStatus({{ $child->id }}, 'present')">
                                    <i class="fas fa-check-circle d-block mb-1" style="font-size:1.3rem"></i>
                                    <span style="font-size:.8rem;font-weight:700">PRESENT</span>
                                </div>
                            </label>
                        </div>

                        {{-- ABSENT --}}
                        <div class="col-6">
                            <label class="w-100 cursor-pointer">
                                <input type="radio"
                                       name="attendance[{{ $child->id }}]"
                                       value="absent"
                                       class="d-none att-radio-{{ $child->id }}"
                                       {{ $cur === 'absent' ? 'checked' : '' }}
                                       onchange="updateCard({{ $child->id }}, 'absent')">
                                <div class="btn-att btn-absent {{ $cur === 'absent' ? 'active' : '' }} w-100 text-center rounded-3 py-2"
                                     onclick="selectStatus({{ $child->id }}, 'absent')">
                                    <i class="fas fa-times-circle d-block mb-1" style="font-size:1.3rem"></i>
                                    <span style="font-size:.8rem;font-weight:700">ABSENT</span>
                                </div>
                            </label>
                        </div>

                        {{-- LATE --}}
                        <div class="col-6">
                            <label class="w-100 cursor-pointer">
                                <input type="radio"
                                       name="attendance[{{ $child->id }}]"
                                       value="late"
                                       class="d-none att-radio-{{ $child->id }}"
                                       {{ $cur === 'late' ? 'checked' : '' }}
                                       onchange="updateCard({{ $child->id }}, 'late')">
                                <div class="btn-att btn-late {{ $cur === 'late' ? 'active' : '' }} w-100 text-center rounded-3 py-2"
                                     onclick="selectStatus({{ $child->id }}, 'late')">
                                    <i class="fas fa-clock d-block mb-1" style="font-size:1.3rem"></i>
                                    <span style="font-size:.8rem;font-weight:700">LATE</span>
                                </div>
                            </label>
                        </div>

                        {{-- EXCUSED --}}
                        <div class="col-6">
                            <label class="w-100 cursor-pointer">
                                <input type="radio"
                                       name="attendance[{{ $child->id }}]"
                                       value="excused"
                                       class="d-none att-radio-{{ $child->id }}"
                                       {{ $cur === 'excused' ? 'checked' : '' }}
                                       onchange="updateCard({{ $child->id }}, 'excused')">
                                <div class="btn-att btn-excused {{ $cur === 'excused' ? 'active' : '' }} w-100 text-center rounded-3 py-2"
                                     onclick="selectStatus({{ $child->id }}, 'excused')">
                                    <i class="fas fa-notes-medical d-block mb-1" style="font-size:1.3rem"></i>
                                    <span style="font-size:.8rem;font-weight:700">EXCUSED</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Remarks field --}}
                    <div class="mt-2">
                        <input type="text"
                               name="remarks[{{ $child->id }}]"
                               value="{{ $remarks[$child->id] ?? '' }}"
                               class="form-control form-control-sm rounded-3"
                               placeholder="Remarks (optional)…">
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Save button bottom ─────────────────────────────────── --}}
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('staff.attendance.index') }}"
           class="btn btn-outline-secondary rounded-3 px-4">
            Cancel
        </a>
        <button type="submit" class="btn btn-primary rounded-3 px-5 py-2">
            <i class="fas fa-save me-2"></i>Save Attendance for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
        </button>
    </div>
</form>
@endif

@endsection

@push('styles')
<style>
/* ── Status buttons ─────────────────────────────────────── */
.btn-att {
    cursor: pointer;
    border: 2px solid #E0E0E0;
    background: #F5F5F5;
    color: #757575;
    transition: all .15s ease;
    user-select: none;
}
.btn-att:hover { transform: scale(1.03); }

/* Present */
.btn-present.active,
.btn-present:hover {
    background: #E8F5E9;
    border-color: #2E7D32;
    color: #2E7D32;
}
/* Absent */
.btn-absent.active,
.btn-absent:hover {
    background: #FFEBEE;
    border-color: #C62828;
    color: #C62828;
}
/* Late */
.btn-late.active,
.btn-late:hover {
    background: #FFF8E1;
    border-color: #E65100;
    color: #E65100;
}
/* Excused */
.btn-excused.active,
.btn-excused:hover {
    background: #E3F2FD;
    border-color: #1565C0;
    color: #1565C0;
}

/* Card border by status */
.card-status-present { border-left: 4px solid #2E7D32 !important; }
.card-status-absent  { border-left: 4px solid #C62828 !important; }
.card-status-late    { border-left: 4px solid #E65100 !important; }
.card-status-excused { border-left: 4px solid #1565C0 !important; }

.cursor-pointer { cursor: pointer; }
</style>
@endpush

@push('scripts')
<script>
// Status config
const statusConfig = {
    present: { label: 'Present', bg: '#E8F5E9', color: '#2E7D32' },
    absent:  { label: 'Absent',  bg: '#FFEBEE', color: '#C62828' },
    late:    { label: 'Late',    bg: '#FFF8E1', color: '#E65100' },
    excused: { label: 'Excused', bg: '#E3F2FD', color: '#1565C0' },
};

// Initialize all cards on load
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[id^="card-"]').forEach(function(card) {
        const id     = card.id.replace('card-', '');
        const status = card.dataset.status || 'present';
        applyCardStyle(id, status);
    });
});

// Called when radio changes
function updateCard(childId, status) {
    applyCardStyle(childId, status);
}

// Called when clicking the button div
function selectStatus(childId, status) {
    // Check the radio
    const radios = document.querySelectorAll(`.att-radio-${childId}`);
    radios.forEach(r => {
        r.checked = (r.value === status);
    });

    // Update button active states
    const statuses = ['present','absent','late','excused'];
    statuses.forEach(s => {
        const btns = document.querySelectorAll(`.btn-${s}`);
        btns.forEach(btn => {
            // only update buttons for this child
            const label = btn.closest('label');
            if (label) {
                const radio = label.querySelector(`input[name="attendance[${childId}]"]`);
                if (radio) {
                    btn.classList.toggle('active', s === status);
                }
            }
        });
    });

    applyCardStyle(childId, status);
}

function applyCardStyle(childId, status) {
    const cfg  = statusConfig[status] || statusConfig.present;
    const card = document.getElementById(`card-${childId}`);
    if (!card) return;

    // Border color
    card.classList.remove(
        'card-status-present','card-status-absent',
        'card-status-late','card-status-excused'
    );
    card.classList.add(`card-status-${status}`);

    // Status badge
    const badge = document.querySelector(`.status-badge-${childId}`);
    if (badge) {
        badge.textContent = cfg.label;
        badge.style.background = cfg.bg;
        badge.style.color       = cfg.color;
    }
}

// Set all children to one status
function setAll(status) {
    document.querySelectorAll('[id^="card-"]').forEach(function(card) {
        const id = card.id.replace('card-', '');
        selectStatus(id, status);
    });
}

// Submit form
function submitForm() {
    document.getElementById('attendanceForm').submit();
}
</script>
@endpush
