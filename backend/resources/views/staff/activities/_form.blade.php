{{-- Shared form fields for activity create/edit --}}
<div class="mb-3">
    <label class="form-label">Child <span class="text-danger">*</span></label>
    <select name="child_id" class="form-select @error('child_id') is-invalid @enderror" required
            {{ isset($activity) ? 'disabled' : '' }}>
        <option value="">Select child…</option>
        @foreach($children as $c)
            <option value="{{ $c->id }}"
                {{ old('child_id', $activity->child_id ?? request('child_id')) == $c->id ? 'selected':'' }}>
                {{ $c->first_name }} {{ $c->last_name }}
            </option>
        @endforeach
    </select>
    @if(isset($activity))
        <input type="hidden" name="child_id" value="{{ $activity->child_id }}">
    @endif
    @error('child_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Activity Date <span class="text-danger">*</span></label>
    <input type="date" name="activity_date"
           value="{{ old('activity_date', $activity->activity_date->format('Y-m-d') ?? now()->toDateString()) }}"
           class="form-control @error('activity_date') is-invalid @enderror" required>
    @error('activity_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Activity Type <span class="text-danger">*</span></label>
    <select name="activity_type" class="form-select @error('activity_type') is-invalid @enderror" required>
        <option value="">Select activity…</option>
        @foreach($activityTypes as $t)
            <option value="{{ $t }}"
                {{ old('activity_type', $activity->activity_type ?? '') == $t ? 'selected':'' }}>{{ $t }}</option>
        @endforeach
    </select>
    @error('activity_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Completion Status <span class="text-danger">*</span></label>
    <select name="completion_status" class="form-select @error('completion_status') is-invalid @enderror" required>
        @foreach(['completed'=>'Completed','in_progress'=>'In Progress','not_started'=>'Not Started'] as $val=>$label)
            <option value="{{ $val }}"
                {{ old('completion_status', $activity->completion_status ?? 'completed') == $val ? 'selected':'' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('completion_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" rows="3" class="form-control"
              placeholder="Observations, progress notes…">{{ old('notes', $activity->notes ?? '') }}</textarea>
</div>
