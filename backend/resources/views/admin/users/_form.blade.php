{{-- Shared form for user create/edit --}}
<div class="mb-3">
    <label class="form-label">Full Name <span class="text-danger">*</span></label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Email Address <span class="text-danger">*</span></label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
           class="form-control @error('email') is-invalid @enderror" required>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
           class="form-control" placeholder="e.g. 09171234567">
</div>

<div class="mb-3">
    <label class="form-label">Role <span class="text-danger">*</span></label>
    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
        @foreach(['admin','staff','parent'] as $r)
            <option value="{{ $r }}" {{ old('role', $user->role ?? '') == $r ? 'selected':'' }}>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@if(isset($user))
<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
        <option value="active"   {{ old('status',$user->status)=='active'   ? 'selected':'' }}>Active</option>
        <option value="inactive" {{ old('status',$user->status)=='inactive' ? 'selected':'' }}>Inactive</option>
    </select>
</div>
@endif

<hr class="my-3">
<p class="text-muted small mb-2">{{ isset($user) ? 'Leave password blank to keep unchanged.' : 'Set a strong password.' }}</p>

<div class="mb-3">
    <label class="form-label">Password {{ isset($user) ? '' : '<span class="text-danger">*</span>' }}</label>
    <input type="password" name="password"
           class="form-control @error('password') is-invalid @enderror"
           {{ isset($user) ? '' : 'required' }} minlength="8">
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Confirm Password</label>
    <input type="password" name="password_confirmation" class="form-control">
</div>
