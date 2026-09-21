@extends('layouts.app')
@section('title','Attendance Detail')
@section('page-title','Attendance — {{ $child->first_name }} {{ $child->last_name }}')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('admin.attendance.index') }}"
       class="btn btn-sm btn-outline-secondary rounded-3">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
    <form method="GET" class="d-flex gap-2 align-items-center">
        <label class="small fw-semibold mb-0">Month:</label>
        <input type="month" name="month" value="{{ $month }}"
               class="form-control form-control-sm" style="width:170px">
        <button class="btn btn-sm btn-primary rounded-3">Apply</button>
    </form>
</div>

{{-- ── Summary stat cards ────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-success">{{ $summary['present'] }}</div>
            <div class="small text-muted">Present</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-danger">{{ $summary['absent'] }}</div>
            <div class="small text-muted">Absent</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-warning">{{ $summary['late'] }}</div>
            <div class="small text-muted">Late</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-primary">{{ $summary['rate'] }}%</div>
            <div class="small text-muted">Rate ({{ $summary['total'] }} days)</div>
        </div>
    </div>
</div>

{{-- ── Daily records ─────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 fw-semibold">
        Daily Records — {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#F8F9FA">
                <tr>
                    <th class="px-3 py-2 small fw-semibold text-muted">Date</th>
                    <th class="py-2 small fw-semibold text-muted">Day</th>
                    <th class="py-2 small fw-semibold text-muted">Status</th>
                    <th class="py-2 small fw-semibold text-muted">Remarks</th>
                    <th class="py-2 small fw-semibold text-muted">Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr>
                    <td class="px-3 fw-semibold small">
                        {{ $r->attendance_date->format('M d, Y') }}
                    </td>
                    <td class="text-muted small">
                        {{ $r->attendance_date->format('l') }}
                    </td>
                    <td>
                        @php
                            $colors = [
                                'present' => ['bg'=>'#E8F5E9','color'=>'#2E7D32'],
                                'absent'  => ['bg'=>'#FFEBEE','color'=>'#C62828'],
                                'late'    => ['bg'=>'#FFF8E1','color'=>'#E65100'],
                                'excused' => ['bg'=>'#E3F2FD','color'=>'#1565C0'],
                            ];
                            $c = $colors[$r->status] ?? ['bg'=>'#F5F5F5','color'=>'#616161'];
                        @endphp
                        <span class="badge rounded-pill px-3 py-2 fw-semibold"
                              style="background:{{ $c['bg'] }};color:{{ $c['color'] }}">
                            {{ ucfirst($r->status) }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $r->remarks ?? '—' }}</td>
                    <td class="small text-muted">{{ $r->recorder->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        No attendance records for this month.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
