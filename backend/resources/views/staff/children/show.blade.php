@extends('layouts.app')
@section('title','Child Detail')
@section('page-title','Child Detail')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('staff.children.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.attendance.mark') }}?child_id={{ $child->id }}" class="btn btn-sm btn-success"><i class="fas fa-calendar-check me-1"></i>Mark Attendance</a>
        <a href="{{ route('staff.activities.create') }}?child_id={{ $child->id }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Record Activity</a>
    </div>
</div>

<div class="row g-3">
    {{-- Profile --}}
    <div class="col-md-4">
        <div class="card p-3 text-center">
            @if($child->photo)
                <img src="{{ asset('storage/'.$child->photo) }}" class="rounded-circle mx-auto mb-3" width="90" height="90" style="object-fit:cover">
            @else
                <div class="avatar-circle mx-auto mb-3" style="width:80px;height:80px;font-size:2rem">
                    {{ strtoupper(substr($child->first_name,0,1)) }}
                </div>
            @endif
            <h5 class="fw-bold mb-0">{{ $child->first_name }} {{ $child->middle_name }} {{ $child->last_name }}</h5>
            <div class="text-muted small mb-1">Age {{ $child->age }} · {{ ucfirst($child->gender) }}</div>
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
                @if($child->parent)
                <tr><th class="text-muted fw-normal">Parent App</th><td>{{ $child->parent->name }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Enrollment history --}}
        <div class="card mb-3">
            <div class="card-header">Enrollment History</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>School Year</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($child->enrollments as $e)
                        <tr>
                            <td>{{ $e->school_year }}</td>
                            <td>{{ $e->enrollment_date->format('M d, Y') }}</td>
                            <td><span class="badge badge-{{ $e->status }}">{{ ucfirst($e->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-muted text-center py-2">No enrollments</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Attendance --}}
        <div class="card mb-3">
            <div class="card-header">Recent Attendance</div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:190px;overflow-y:auto">
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Activities</span>
                <a href="{{ route('staff.activities.create') }}?child_id={{ $child->id }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:190px;overflow-y:auto">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Date</th><th>Activity</th><th>Status</th><th>Notes</th><th></th></tr></thead>
                        <tbody>
                            @forelse($child->activities as $act)
                            <tr>
                                <td>{{ $act->activity_date->format('M d, Y') }}</td>
                                <td class="small">{{ $act->activity_type }}</td>
                                <td class="small">{{ ucfirst(str_replace('_',' ',$act->completion_status)) }}</td>
                                <td class="text-muted small">{{ Str::limit($act->notes,40) ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('staff.activities.edit',$act) }}" class="btn btn-sm btn-outline-primary p-1"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-muted text-center py-2">No activities</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
