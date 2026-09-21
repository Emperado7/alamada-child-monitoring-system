@extends('layouts.app')
@section('title','Send Notification')
@section('page-title','Send Notification')

@section('content')
<div class="d-flex mb-3">
    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="card" style="max-width:700px">
    <div class="card-header">New Notification</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.notifications.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="form-control @error('title') is-invalid @enderror"
                       placeholder="Notification title" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Message <span class="text-danger">*</span></label>
                <textarea name="message" rows="5"
                          class="form-control @error('message') is-invalid @enderror"
                          placeholder="Write your message here…" required>{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Send To <span class="text-danger">*</span></label>
                <select name="recipient_group" id="recipientGroup"
                        class="form-select @error('recipient_group') is-invalid @enderror"
                        onchange="toggleSpecific(this.value)" required>
                    <option value="all"      {{ old('recipient_group')=='all'      ? 'selected':'' }}>Everyone (Admin, Staff, Parents)</option>
                    <option value="parents"  {{ old('recipient_group')=='parents'  ? 'selected':'' }}>All Parents</option>
                    <option value="staff"    {{ old('recipient_group')=='staff'    ? 'selected':'' }}>All Staff</option>
                    <option value="specific" {{ old('recipient_group')=='specific' ? 'selected':'' }}>Specific User</option>
                </select>
                @error('recipient_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 d-none" id="specificUser">
                <label class="form-label">Select User</label>
                <select name="recipient_id" class="form-select">
                    <option value="">Choose a user…</option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $u)
                        <option value="{{ $u->id }}" {{ old('recipient_id')==$u->id ? 'selected':'' }}>
                            {{ $u->name }} ({{ ucfirst($u->role) }})
                        </option>
                    @endforeach
                </select>
                @error('recipient_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Send</button>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSpecific(v) {
    document.getElementById('specificUser').classList.toggle('d-none', v !== 'specific');
}
toggleSpecific(document.getElementById('recipientGroup').value);
</script>
@endpush
