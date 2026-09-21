@extends('layouts.app')
@section('title','Activities')
@section('page-title','Daily Activities')

@section('content')

{{-- ── Stat Cards ────────────────────────────────────────────── --}}
@php
    $staffId      = auth()->id();
    $totalToday   = \App\Models\Activity::whereHas('child', fn($q)=>$q->where('staff_id',$staffId))
                        ->whereDate('activity_date', today())->count();
    $totalCompleted = \App\Models\Activity::whereHas('child', fn($q)=>$q->where('staff_id',$staffId))
                        ->where('completion_status','completed')
                        ->whereMonth('activity_date', now()->month)->count();
    $totalInProgress = \App\Models\Activity::whereHas('child', fn($q)=>$q->where('staff_id',$staffId))
                        ->where('completion_status','in_progress')
                        ->whereMonth('activity_date', now()->month)->count();
    $totalChildren  = \App\Models\Child::where('staff_id',$staffId)->where('status','active')->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:#E3F2FD">
                    <i class="fas fa-puzzle-piece text-primary fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Today's Activities</div>
                    <div class="fw-bold fs-2 lh-1">{{ $totalToday }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:#E8F5E9">
                    <i class="fas fa-check-circle text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Completed (This Month)</div>
                    <div class="fw-bold fs-2 lh-1 text-success">{{ $totalCompleted }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:#FFF8E1">
                    <i class="fas fa-spinner text-warning fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">In Progress</div>
                    <div class="fw-bold fs-2 lh-1 text-warning">{{ $totalInProgress }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:#F3E5F5">
                    <i class="fas fa-users fs-4" style="color:#7B1FA2"></i>
                </div>
                <div>
                    <div class="text-muted small">My Children</div>
                    <div class="fw-bold fs-2 lh-1">{{ $totalChildren }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Filters + Add Button ──────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap flex-grow-1">
                <div class="input-group" style="max-width:260px">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted small"></i>
                    </span>
                    <select name="child_id" class="form-select form-select-sm border-start-0">
                        <option value="">All Children</option>
                        @foreach($children as $c)
                            <option value="{{ $c->id }}"
                                {{ request('child_id')==$c->id ? 'selected':'' }}>
                                {{ $c->first_name }} {{ $c->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <select name="activity_type" class="form-select form-select-sm" style="max-width:200px">
                    <option value="">All Activities</option>
                    @foreach($activityTypes as $t)
                        <option value="{{ $t }}"
                            {{ request('activity_type')==$t ? 'selected':'' }}>{{ $t }}</option>
                    @endforeach
                </select>
                <select name="completion_status" class="form-select form-select-sm" style="max-width:160px">
                    <option value="">All Status</option>
                    <option value="completed"   {{ request('completion_status')=='completed'   ? 'selected':'' }}>Completed</option>
                    <option value="in_progress" {{ request('completion_status')=='in_progress' ? 'selected':'' }}>In Progress</option>
                    <option value="not_started" {{ request('completion_status')=='not_started' ? 'selected':'' }}>Not Started</option>
                </select>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="form-control form-control-sm" style="max-width:160px">
                <button class="btn btn-outline-primary btn-sm rounded-3">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('staff.activities.index') }}"
                   class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="fas fa-times"></i>
                </a>
            </form>
            <a href="{{ route('staff.activities.create') }}"
               class="btn btn-primary btn-sm rounded-3 flex-shrink-0">
                <i class="fas fa-plus me-1"></i>Record Activity
            </a>
        </div>
    </div>
</div>

{{-- ── Activities Table ─────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold">
            <i class="fas fa-list text-primary me-2"></i>Activity Records
            <span class="badge bg-primary ms-2 rounded-pill">{{ $activities->total() }}</span>
        </span>
    </div>
    <div class="card-body p-0">
        @if($activities->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-puzzle-piece fa-3x mb-3 d-block" style="color:#E0E0E0"></i>
            <h5 class="text-muted">No activities recorded yet.</h5>
            <p class="text-muted small">Start recording activities for your children.</p>
            <a href="{{ route('staff.activities.create') }}" class="btn btn-primary rounded-3">
                <i class="fas fa-plus me-1"></i>Record First Activity
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#F8F9FA">
                    <tr>
                        <th class="px-3 py-2 small fw-semibold text-muted">Date</th>
                        <th class="py-2 small fw-semibold text-muted">Child</th>
                        <th class="py-2 small fw-semibold text-muted">Activity</th>
                        <th class="py-2 small fw-semibold text-muted">Status</th>
                        <th class="py-2 small fw-semibold text-muted">Notes</th>
                        <th class="py-2 small fw-semibold text-muted">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $act)
                    <tr>
                        <td class="px-3">
                            <div class="fw-semibold small">
                                {{ $act->activity_date->format('M d, Y') }}
                            </div>
                            <div class="text-muted" style="font-size:.72rem">
                                {{ $act->activity_date->format('l') }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;
                                            background:linear-gradient(135deg,#1565C0,#5e92f3);
                                            color:#fff;display:flex;align-items:center;
                                            justify-content:center;font-weight:700;
                                            font-size:.8rem;flex-shrink:0">
                                    {{ strtoupper(substr($act->child->first_name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small">
                                        {{ $act->child->first_name ?? '—' }}
                                        {{ $act->child->last_name ?? '' }}
                                    </div>
                                    <div class="text-muted" style="font-size:.72rem">
                                        {{ $act->child->diagnosis ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3"
                                  style="background:#E3F2FD;color:#1565C0;font-size:.8rem">
                                {{ $act->activity_type }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'completed'   => ['bg'=>'#E8F5E9','color'=>'#2E7D32','label'=>'Completed'],
                                    'in_progress' => ['bg'=>'#FFF8E1','color'=>'#E65100','label'=>'In Progress'],
                                    'not_started' => ['bg'=>'#F5F5F5','color'=>'#616161','label'=>'Not Started'],
                                ];
                                $sm = $statusMap[$act->completion_status] ?? $statusMap['not_started'];
                            @endphp
                            <span class="badge rounded-pill px-3 py-2"
                                  style="background:{{ $sm['bg'] }};color:{{ $sm['color'] }};font-size:.8rem;font-weight:700">
                                {{ $sm['label'] }}
                            </span>
                        </td>
                        <td class="small text-muted" style="max-width:200px">
                            {{ $act->notes ? \Str::limit($act->notes, 60) : '—' }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('staff.activities.edit', $act) }}"
                                   class="btn btn-sm btn-outline-primary rounded-2" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('staff.activities.destroy', $act) }}"
                                      onsubmit="return confirm('Delete this activity record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-2" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($activities->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">
            Showing {{ $activities->firstItem() }}–{{ $activities->lastItem() }}
            of {{ $activities->total() }}
        </small>
        {{ $activities->links() }}
    </div>
    @endif
</div>
@endsection
