@extends('layouts.app')
@section('title','Enrollment Detail')
@section('page-title','Enrollment Detail')

@section('content')
<div class="d-flex mb-3">
    <a href="{{ route('staff.enrollment.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card p-3">
            <div class="fw-semibold mb-3 border-bottom pb-2">Enrollment Information</div>
            <table class="table table-sm">
                <tr><th class="text-muted fw-normal">School Year</th><td>{{ $enrollment->school_year }}</td></tr>
                <tr><th class="text-muted fw-normal">Enrollment Date</th><td>{{ $enrollment->enrollment_date->format('F d, Y') }}</td></tr>
                <tr><th class="text-muted fw-normal">Status</th>
                    <td><span class="badge badge-{{ $enrollment->status }}">{{ ucfirst($enrollment->status) }}</span></td></tr>
                <tr><th class="text-muted fw-normal">Approved By</th><td>{{ $enrollment->approver->name ?? 'Pending' }}</td></tr>
                <tr><th class="text-muted fw-normal">Remarks</th><td>{{ $enrollment->remarks ?? '—' }}</td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <div class="fw-semibold mb-3 border-bottom pb-2">Child</div>
            <table class="table table-sm">
                <tr><th class="text-muted fw-normal">Name</th><td>{{ $enrollment->child->first_name }} {{ $enrollment->child->last_name }}</td></tr>
                <tr><th class="text-muted fw-normal">Age</th><td>{{ $enrollment->child->age }}</td></tr>
                <tr><th class="text-muted fw-normal">Diagnosis</th><td>{{ $enrollment->child->diagnosis }}</td></tr>
                <tr><th class="text-muted fw-normal">Guardian</th><td>{{ $enrollment->child->guardian_name }}</td></tr>
            </table>
            <a href="{{ route('staff.children.show',$enrollment->child) }}" class="btn btn-sm btn-outline-primary mt-2">
                <i class="fas fa-user me-1"></i>View Child Profile
            </a>
        </div>
    </div>
</div>
@endsection
