@extends('layouts.app')
@section('title','Mark Attendance')
@section('page-title','Mark Attendance')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="fw-bold mb-1">Mark Attendance</h4>
        <nav style="font-size:.83rem">
            <a href="{{ route('admin.dashboard') }}" style="color:#2e7d32;text-decoration:none">Dashboard</a>
            <span class="text-muted mx-1">›</span>
            <a href="{{ route('admin.attendance.index') }}" style="color:#2e7d32;text-decoration:none">Attendance</a>
            <span class="text-muted mx-1">›</span>
            <span class="text-muted">Mark</span>
        </nav>
    </div>
    <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="dash-card">
    {{-- Date selector --}}
    <div class="p-4 border-bottom">
        <form method="GET" id="dateForm" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="fw-semibold small">Select Date:</div>
            <input type="date" name="date" value="{{ $date }}"
                   class="form-control form-control-sm rounded-3"
                   style="max-width:200px"
                   onchange="document.getElementById('dateForm').submit()">
        </form>
    </div>

    {{-- Attendance table --}}
    <form method="POST" action="{{ route('admin.attendance.save') }}" id="attendanceForm">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background:#f8f9fa">
                        <th class="px-3 py-3 small fw-bold" style="color:#2e7d32">#</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Child</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Age</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Diagnosis</th>
                        <th class="py-3 small fw-bold text-center" style="color:#2e7d32">Attendance</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Fetched By</th>
                        <th class="py-3 small fw-bold" style="color:#2e7d32">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($children as $i => $child)
                    @php
                        $att       = $child->attendances->first();
                        $curStatus = $att ? $att->status : null;
                    @endphp
                    <tr>
                        <td class="px-3 text-muted small">{{ $i + 1 }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($child->photo)
                                    <img src="{{ asset('storage/'.$child->photo) }}"
                                         class="rounded-circle" width="36" height="36"
                                         style="object-fit:cover;border:2px solid #e8f5e9">
                                @else
                                    <div style="width:36px;height:36px;border-radius:50%;
                                                background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                                color:#fff;display:flex;align-items:center;
                                                justify-content:center;font-weight:700;font-size:.85rem">
                                        {{ strtoupper(substr($child->first_name,0,1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold small">{{ $child->last_name }}, {{ $child->first_name }}</div>
                                    <div class="text-muted" style="font-size:.72rem">
                                        CH-{{ str_pad($child->id,3,'0',STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="small fw-semibold">{{ $child->age ?? '—' }}</td>
                        <td class="small text-muted">{{ $child->diagnosis }}</td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                @foreach(['present'=>['#2e7d32','fa-check-circle','Present'],'absent'=>['#c62828','fa-times-circle','Absent'],'excused'=>['#1565c0','fa-info-circle','Excused']] as $val=>[$col,$icon,$lbl])
                                <label style="cursor:pointer;margin:0">
                                    <input type="radio"
                                           name="attendance[{{ $child->id }}]"
                                           value="{{ $val }}"
                                           class="d-none"
                                           {{ $curStatus===$val?'checked':'' }}>
                                    <span onclick="this.previousElementSibling.checked=true;this.previousElementSibling.dispatchEvent(new Event('change'))"
                                          style="display:inline-flex;align-items:center;gap:4px;
                                                 padding:5px 12px;border-radius:8px;font-size:.8rem;font-weight:700;
                                                 cursor:pointer;border:1.5px solid {{ $curStatus===$val?$col:'#e0e0e0' }};
                                                 background:{{ $curStatus===$val?$col:'#fff' }};
                                                 color:{{ $curStatus===$val?'#fff':'#9e9e9e' }};">
                                        <i class="fas {{ $icon }}"></i>{{ $lbl }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </td>

                        <td>
                            <select name="fetched_by[{{ $child->id }}]"
                                    class="form-select form-select-sm rounded-2"
                                    style="min-width:130px;font-size:.8rem">
                                <option value="">— Select —</option>
                                @foreach(['Mother','Father','Grandmother','Grandfather','Aunt','Uncle','Older Sibling','Guardian','Other'] as $fb)
                                    <option value="{{ $fb }}" {{ ($att?->fetched_by??'') === $fb ? 'selected':'' }}>
                                        {{ $fb }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input type="text" name="remarks[{{ $child->id }}]"
                                   value="{{ $att?->remarks ?? '' }}"
                                   class="form-control form-control-sm rounded-2"
                                   style="min-width:130px;font-size:.8rem"
                                   placeholder="Optional…">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-child fa-2x mb-2 d-block"></i>
                            No active children found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($children->isNotEmpty())
        <div class="px-4 py-3 border-top d-flex justify-content-end gap-3">
            <a href="{{ route('admin.attendance.index') }}"
               class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
            <button type="submit"
                    class="btn btn-success rounded-3 px-4 fw-bold"
                    style="background:#2e7d32;border:none">
                <i class="fas fa-save me-2"></i>Save Attendance
            </button>
        </div>
        @endif
    </form>
</div>

@endsection

@push('scripts')
<script>
// Highlight radio buttons when selected
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const name = this.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
            const span = r.nextElementSibling;
            if (!span) return;
            if (r.checked) {
                const colors = {'present':'#2e7d32','absent':'#c62828','excused':'#1565c0'};
                const c = colors[r.value] || '#2e7d32';
                span.style.background  = c;
                span.style.borderColor = c;
                span.style.color       = '#fff';
            } else {
                span.style.background  = '#fff';
                span.style.borderColor = '#e0e0e0';
                span.style.color       = '#9e9e9e';
            }
        });
    });
});
</script>
@endpush
