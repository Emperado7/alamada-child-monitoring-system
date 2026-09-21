@extends('layouts.app')
@section('title','Record Activity')
@section('page-title','Record Activity')

@section('content')
<div class="d-flex mb-3">
    <a href="{{ route('staff.activities.index') }}"
       class="btn btn-sm btn-outline-secondary rounded-3">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-puzzle-piece text-primary me-2"></i>Record Activity
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('staff.activities.store') }}">
                    @csrf

                    {{-- Child selector --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Child <span class="text-danger">*</span>
                        </label>
                        <select name="child_id"
                                class="form-select @error('child_id') is-invalid @enderror"
                                required>
                            <option value="">Select child…</option>
                            @foreach($children as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('child_id', request('child_id'))==$c->id ? 'selected':'' }}>
                                    {{ $c->first_name }} {{ $c->last_name }}
                                    — Age {{ $c->age }} ({{ $c->diagnosis }})
                                </option>
                            @endforeach
                        </select>
                        @error('child_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($children->isEmpty())
                            <div class="text-warning small mt-1">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No active children assigned to you yet.
                            </div>
                        @endif
                    </div>

                    {{-- Activity Date --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Activity Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="activity_date"
                               value="{{ old('activity_date', today()->toDateString()) }}"
                               class="form-control @error('activity_date') is-invalid @enderror"
                               required>
                        @error('activity_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Activity Type --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Activity Type <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            @foreach($activityTypes as $type)
                            <div class="col-6 col-md-4">
                                <label class="w-100" style="cursor:pointer">
                                    <input type="radio" name="activity_type"
                                           value="{{ $type }}"
                                           class="d-none activity-radio"
                                           {{ old('activity_type')==$type ? 'checked':'' }}
                                           required>
                                    <div class="activity-btn border rounded-3 text-center py-2 px-1
                                                {{ old('activity_type')==$type ? 'active':'' }}"
                                         style="font-size:.82rem;transition:all .15s;cursor:pointer"
                                         onclick="selectActivity('{{ $type }}')">
                                        <div class="mb-1">{{ activityIcon($type ?? '') }}</div>
                                        {{ $type }}
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('activity_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Completion Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Completion Status <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            @foreach([
                                'completed'   => ['label'=>'Completed',   'icon'=>'fa-check-circle',  'bg'=>'#E8F5E9','color'=>'#2E7D32'],
                                'in_progress' => ['label'=>'In Progress', 'icon'=>'fa-spinner',       'bg'=>'#FFF8E1','color'=>'#E65100'],
                                'not_started' => ['label'=>'Not Started', 'icon'=>'fa-circle',        'bg'=>'#F5F5F5','color'=>'#616161'],
                            ] as $val => $cfg)
                            <div class="col-4">
                                <label class="w-100" style="cursor:pointer">
                                    <input type="radio" name="completion_status"
                                           value="{{ $val }}"
                                           class="d-none status-radio"
                                           {{ old('completion_status', 'completed')==$val ? 'checked':'' }}
                                           required>
                                    <div class="status-btn border rounded-3 text-center py-3
                                                {{ old('completion_status','completed')==$val ? 'active':'' }}"
                                         style="cursor:pointer;transition:all .15s;
                                                background:{{ old('completion_status','completed')==$val ? $cfg['bg'] : '#fff' }};
                                                border-color:{{ old('completion_status','completed')==$val ? $cfg['color'] : '#dee2e6' }} !important;
                                                color:{{ old('completion_status','completed')==$val ? $cfg['color'] : '#6c757d' }}"
                                         onclick="selectStatus('{{ $val }}','{{ $cfg['bg'] }}','{{ $cfg['color'] }}')">
                                        <i class="fas {{ $cfg['icon'] }} d-block mb-1 fs-5"></i>
                                        <span style="font-size:.82rem;font-weight:600">{{ $cfg['label'] }}</span>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('completion_status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notes / Observations</label>
                        <textarea name="notes" rows="4"
                                  class="form-control"
                                  placeholder="Describe the child's performance, behavior, or progress during the activity…">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-3 px-4">
                            <i class="fas fa-save me-1"></i>Save Activity
                        </button>
                        <a href="{{ route('staff.activities.index') }}"
                           class="btn btn-outline-secondary rounded-3">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Activity Guide ──────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-info-circle text-primary me-2"></i>Activity Guide
                </h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    @foreach([
                        ['Building Blocks',       '🧱', 'Motor skills, spatial awareness, problem solving'],
                        ['Logico Piccolo',         '🎯', 'Logic, pattern recognition, cognitive development'],
                        ['Drawing and Coloring',   '🎨', 'Creativity, fine motor skills, color recognition'],
                        ['Writing Improvement',    '✏️', 'Handwriting, letter formation, pen control'],
                        ['Socialization',          '🤝', 'Communication, sharing, interaction with peers'],
                        ['Counting Numbers',       '🔢', 'Numeracy, number recognition, counting skills'],
                        ['Identifying Alphabets',  '🔤', 'Letter recognition, phonics awareness'],
                        ['Identifying Shapes',     '🔷', 'Shape recognition, geometry basics'],
                        ['Addition',               '➕', 'Basic math, number bonds, addition skills'],
                        ['Subtraction',            '➖', 'Basic math, number bonds, subtraction skills'],
                        ['Maze Tracing',           '🌀', 'Focus, fine motor skills, problem solving'],
                        ['Puzzles',                '🧩', 'Problem solving, patience, cognitive skills'],
                    ] as [$name, $icon, $desc])
                    <li class="list-group-item px-3 py-2 d-flex align-items-center gap-2">
                        <span style="font-size:1.2rem;width:28px;text-align:center">{{ $icon }}</span>
                        <div>
                            <div class="fw-semibold" style="font-size:.85rem">{{ $name }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ $desc }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.activity-btn:hover,
.activity-btn.active {
    background: #E3F2FD !important;
    border-color: #1565C0 !important;
    color: #1565C0 !important;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
// Select activity type card
function selectActivity(type) {
    document.querySelectorAll('.activity-radio').forEach(r => {
        r.checked = (r.value === type);
    });
    document.querySelectorAll('.activity-btn').forEach(btn => {
        const radio = btn.closest('label').querySelector('.activity-radio');
        btn.classList.toggle('active', radio && radio.checked);
    });
}

// Select completion status card
function selectStatus(val, bg, color) {
    document.querySelectorAll('.status-radio').forEach(r => {
        r.checked = (r.value === val);
    });
    document.querySelectorAll('.status-btn').forEach(btn => {
        const radio = btn.closest('label').querySelector('.status-radio');
        const isActive = radio && radio.checked;
        btn.style.background   = isActive ? bg    : '#fff';
        btn.style.borderColor  = isActive ? color : '#dee2e6';
        btn.style.color        = isActive ? color : '#6c757d';
        btn.classList.toggle('active', isActive);
    });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    const checkedActivity = document.querySelector('.activity-radio:checked');
    if (checkedActivity) selectActivity(checkedActivity.value);

    const checkedStatus = document.querySelector('.status-radio:checked');
    // status buttons are already initialized via inline styles
});
</script>
@endpush

@php
function activityIcon($type) {
    $icons = [
        'Building Blocks'      => '🧱',
        'Logico Piccolo'       => '🎯',
        'Drawing and Coloring' => '🎨',
        'Writing Improvement'  => '✏️',
        'Socialization'        => '🤝',
        'Counting Numbers'     => '🔢',
        'Identifying Alphabets'=> '🔤',
        'Identifying Shapes'   => '🔷',
        'Addition'             => '➕',
        'Subtraction'          => '➖',
        'Maze Tracing'         => '🌀',
        'Puzzles'              => '🧩',
        'Other'                => '📝',
    ];
    return $icons[$type] ?? '📝';
}
@endphp
