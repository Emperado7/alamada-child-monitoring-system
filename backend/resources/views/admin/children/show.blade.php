@extends('layouts.app')
@section('title','Child Detail')
@section('page-title','Child Detail')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('admin.children.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <a href="{{ route('admin.children.edit',$child) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit me-1"></i>Edit</a>
</div>

<div class="row g-3">
    {{-- Profile card --}}
    <div class="col-md-4">
        <div class="card p-3 text-center">
            @if($child->photo)
                <img src="{{ asset('storage/'.$child->photo) }}" class="rounded-circle mx-auto mb-3" width="100" height="100" style="object-fit:cover">
            @else
                <div class="avatar-circle mx-auto mb-3" style="width:90px;height:90px;font-size:2.2rem">
                    {{ strtoupper(substr($child->first_name,0,1)) }}
                </div>
            @endif
            <h5 class="fw-bold mb-0">{{ $child->first_name }} {{ $child->middle_name }} {{ $child->last_name }}</h5>
            <div class="text-muted small mb-2">Age {{ $child->age }} · {{ ucfirst($child->gender) }}</div>
            <span class="badge badge-{{ $child->status }} mb-3">{{ ucfirst($child->status) }}</span>

            <table class="table table-sm text-start small">
                <tr><th class="text-muted fw-normal">Date of Birth</th><td>{{ $child->date_of_birth->format('M d, Y') }}</td></tr>
                <tr><th class="text-muted fw-normal">Diagnosis</th><td>{{ $child->diagnosis }}</td></tr>
                @if($child->diagnosis_notes)
                <tr><th class="text-muted fw-normal">Notes</th><td>{{ $child->diagnosis_notes }}</td></tr>
                @endif
                <tr><th class="text-muted fw-normal">Address</th><td>{{ $child->address }}</td></tr>
                <tr><th class="text-muted fw-normal">Guardian</th><td>{{ $child->guardian_name }}</td></tr>
                <tr><th class="text-muted fw-normal">Contact</th><td>{{ $child->guardian_contact }}</td></tr>
                <tr><th class="text-muted fw-normal">Staff</th><td>{{ $child->staff->name ?? '—' }}</td></tr>
                <tr><th class="text-muted fw-normal">Parent</th><td>{{ $child->parent->name ?? '—' }}</td></tr>
            </table>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Enrollments --}}
        <div class="card mb-3">
            <div class="card-header">Enrollment History</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>School Year</th><th>Date</th><th>Status</th><th>Approved By</th></tr></thead>
                    <tbody>
                        @forelse($child->enrollments as $e)
                        <tr>
                            <td>{{ $e->school_year }}</td>
                            <td>{{ $e->enrollment_date->format('M d, Y') }}</td>
                            <td><span class="badge badge-{{ $e->status }}">{{ ucfirst($e->status) }}</span></td>
                            <td>{{ $e->approver->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">No enrollments</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Attendance --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <span>Recent Attendance (last 30 records)</span>
                <a href="{{ route('admin.attendance.show',$child) }}" class="btn btn-sm btn-outline-primary">Full Report</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:200px;overflow-y:auto">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Date</th><th>Status</th><th>Remarks</th></tr></thead>
                        <tbody>
                            @forelse($child->attendances as $a)
                            <tr>
                                <td>{{ $a->attendance_date->format('M d, Y') }}</td>
                                <td><span class="badge badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                                <td class="text-muted small">{{ $a->remarks ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-muted text-center py-2">No records</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="card">
            <div class="card-header">Recent Activities</div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:200px;overflow-y:auto">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Date</th><th>Activity</th><th>Status</th><th>Notes</th></tr></thead>
                        <tbody>
                            @forelse($child->activities as $act)
                            <tr>
                                <td>{{ $act->activity_date->format('M d, Y') }}</td>
                                <td>{{ $act->activity_type }}</td>
                                <td>{{ ucfirst(str_replace('_',' ',$act->completion_status)) }}</td>
                                <td class="text-muted small">{{ Str::limit($act->notes,50) ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-muted text-center py-2">No activities</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
