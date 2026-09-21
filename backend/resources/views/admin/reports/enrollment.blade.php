@extends('layouts.app')
@section('title','Enrollment Report')
@section('page-title','Enrollment Report')

@section('content')
<div class="d-flex mb-3">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

{{-- Filter form --}}
<div class="card mb-4 p-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label small mb-1">Gender</label>
            <select name="gender" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="male"   {{ request('gender')=='male'   ? 'selected':'' }}>Male</option>
                <option value="female" {{ request('gender')=='female' ? 'selected':'' }}>Female</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">School Year</label>
            <select name="school_year" class="form-select form-select-sm">
                <option value="">All Years</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('school_year')==$y ? 'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Min Age</label>
            <input type="number" name="age_min" value="{{ request('age_min') }}" min="5" max="16" class="form-control form-control-sm" placeholder="5">
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Max Age</label>
            <input type="number" name="age_max" value="{{ request('age_max') }}" min="5" max="16" class="form-control form-control-sm" placeholder="16">
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
            <a href="{{ route('admin.reports.enrollment') }}" class="btn btn-sm btn-outline-secondary w-100"><i class="fas fa-times"></i></a>
        </div>
        <div class="col-md-2">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary w-100"><i class="fas fa-print me-1"></i>Print</button>
        </div>
    </form>
</div>

{{-- Summary stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card text-center p-3"><div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div><div class="small text-muted">Total Enrolled</div></div></div>
    <div class="col-md-3"><div class="card stat-card text-center p-3"><div class="fs-2 fw-bold text-info">{{ $stats['male'] }}</div><div class="small text-muted">Male</div></div></div>
    <div class="col-md-3"><div class="card stat-card text-center p-3"><div class="fs-2 fw-bold text-pink" style="color:#E91E63">{{ $stats['female'] }}</div><div class="small text-muted">Female</div></div></div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex justify-content-around text-center">
                <div><div class="fw-bold text-primary">{{ $stats['current_year'] }}</div><div class="small text-muted">{{ $currentYear }}</div></div>
                <div class="vr"></div>
                <div><div class="fw-bold text-secondary">{{ $stats['prev_year'] }}</div><div class="small text-muted">{{ $previousYear }}</div></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-5">
        <div class="card p-3">
            <div class="fw-semibold mb-2 small">By Diagnosis</div>
            @foreach($byDiagnosis as $d => $count)
            <div class="d-flex justify-content-between small mb-1">
                <span>{{ $d }}</span><span class="fw-bold">{{ $count }}</span>
            </div>
            <div class="progress mb-2" style="height:5px">
                <div class="progress-bar bg-primary" style="width:{{ $stats['total'] ? round($count/$stats['total']*100) : 0 }}%"></div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-md-7">
        <div class="card p-3 h-100"><canvas id="diagnosisChart"></canvas></div>
    </div>
</div>

{{-- Data table --}}
<div class="card">
    <div class="card-header">Enrolled Children ({{ $data->count() }} records)</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm datatable mb-0">
                <thead><tr><th>#</th><th>Name</th><th>Age</th><th>Gender</th><th>Diagnosis</th><th>School Year</th><th>Enrolled Date</th></tr></thead>
                <tbody>
                    @foreach($data as $i => $row)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->date_of_birth)->age }}</td>
                        <td>{{ ucfirst($row->gender) }}</td>
                        <td>{{ $row->diagnosis }}</td>
                        <td>{{ $row->school_year }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->enrollment_date)->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('diagnosisChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($byDiagnosis->keys()) !!},
        datasets:[{ label:'Children', data: {!! json_encode($byDiagnosis->values()) !!},
            backgroundColor:'#1565C0' }]
    },
    options:{ plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
});
</script>
@endpush
