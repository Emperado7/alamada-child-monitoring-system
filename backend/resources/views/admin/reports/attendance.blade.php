@extends('layouts.app')
@section('title','Attendance Report')
@section('page-title','Attendance Report')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <div class="d-flex gap-2 align-items-center">
        <form method="GET" class="d-flex gap-2">
            <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm" style="width:160px">
            <button class="btn btn-sm btn-primary">Apply</button>
        </form>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print me-1"></i>Print</button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Attendance Summary — {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}
        <span class="ms-2 badge bg-primary">{{ $summary->count() }} children</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Gender</th><th>Diagnosis</th>
                        <th>Present</th><th>Absent</th><th>Late</th><th>Excused</th>
                        <th>Total</th><th>Rate</th></tr>
                </thead>
                <tbody>
                    @forelse($summary as $i => $row)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td class="fw-semibold small">{{ $row['child']->first_name }} {{ $row['child']->last_name }}</td>
                        <td>{{ ucfirst($row['child']->gender) }}</td>
                        <td class="small text-muted">{{ $row['child']->diagnosis }}</td>
                        <td><span class="badge badge-present">{{ $row['present'] }}</span></td>
                        <td><span class="badge badge-absent">{{ $row['absent'] }}</span></td>
                        <td><span class="badge badge-late">{{ $row['late'] }}</span></td>
                        <td><span class="badge badge-excused">{{ $row['excused'] }}</span></td>
                        <td>{{ $row['total'] }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;min-width:50px">
                                    <div class="progress-bar {{ $row['rate'] >= 75 ? 'bg-success' : 'bg-danger' }}" style="width:{{ $row['rate'] }}%"></div>
                                </div>
                                <span class="small fw-semibold">{{ $row['rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">No attendance records for this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
