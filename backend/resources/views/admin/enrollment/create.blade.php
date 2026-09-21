@extends('layouts.app')
@section('title','New Enrollment')
@section('page-title','New Enrollment')

@section('content')
<div class="d-flex mb-3">
    <a href="{{ route('admin.enrollment.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('admin.enrollment.store') }}">
    @csrf

    <div class="row g-4">

        {{-- ── LEFT: Child Information ─────────────────────────────── --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-child text-primary me-2"></i>Child Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="first_name"
                                   value="{{ old('first_name') }}"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   placeholder="e.g. Juan" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Middle Name</label>
                            <input type="text" name="middle_name"
                                   value="{{ old('middle_name') }}"
                                   class="form-control"
                                   placeholder="e.g. Santos">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="last_name"
                                   value="{{ old('last_name') }}"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   placeholder="e.g. Dela Cruz" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                Date of Birth <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date_of_birth"
                                   value="{{ old('date_of_birth') }}"
                                   class="form-control @error('date_of_birth') is-invalid @enderror"
                                   max="{{ now()->subYears(5)->format('Y-m-d') }}"
                                   onchange="computeAge(this.value)"
                                   required>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="ageDisplay"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                Gender <span class="text-danger">*</span>
                            </label>
                            <select name="gender"
                                    class="form-select @error('gender') is-invalid @enderror"
                                    required>
                                <option value="">Select gender…</option>
                                <option value="male"   {{ old('gender')=='male'   ? 'selected':'' }}>Male</option>
                                <option value="female" {{ old('gender')=='female' ? 'selected':'' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">
                                Address <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="address"
                                   value="{{ old('address') }}"
                                   class="form-control @error('address') is-invalid @enderror"
                                   placeholder="e.g. Alamada, Cotabato" required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                Guardian Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="guardian_name"
                                   value="{{ old('guardian_name') }}"
                                   class="form-control @error('guardian_name') is-invalid @enderror"
                                   placeholder="Full name of parent/guardian" required>
                            @error('guardian_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                Guardian Contact <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="guardian_contact"
                                   value="{{ old('guardian_contact') }}"
                                   class="form-control @error('guardian_contact') is-invalid @enderror"
                                   placeholder="e.g. 09171234567" required>
                            @error('guardian_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                Diagnosis <span class="text-danger">*</span>
                            </label>
                            <select name="diagnosis"
                                    class="form-select @error('diagnosis') is-invalid @enderror"
                                    required>
                                <option value="">Select diagnosis…</option>
                                @foreach(['ADHD','Autism','Mental Disability','Cerebral Palsy','Blindness','Other'] as $d)
                                    <option value="{{ $d }}"
                                        {{ old('diagnosis')==$d ? 'selected':'' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Diagnosis Notes</label>
                            <input type="text" name="diagnosis_notes"
                                   value="{{ old('diagnosis_notes') }}"
                                   class="form-control"
                                   placeholder="Optional additional notes…">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Assigned Staff</label>
                            <select name="staff_id" class="form-select">
                                <option value="">None / Assign later</option>
                                @foreach($staffList as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('staff_id')==$s->id ? 'selected':'' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Enrollment Details ────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-clipboard-list text-primary me-2"></i>Enrollment Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label small fw-semibold">
                                Enrollment Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="enrollment_date"
                                   value="{{ old('enrollment_date', now()->toDateString()) }}"
                                   class="form-control @error('enrollment_date') is-invalid @enderror"
                                   required>
                            @error('enrollment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">
                                School Year <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="school_year"
                                   value="{{ old('school_year', now()->year) }}"
                                   class="form-control @error('school_year') is-invalid @enderror"
                                   min="2000" max="2099" required>
                            @error('school_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Remarks</label>
                            <textarea name="remarks" rows="3"
                                      class="form-control"
                                      placeholder="Optional remarks…">{{ old('remarks') }}</textarea>
                        </div>

                        {{-- Admin can auto-approve --}}
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="auto_approve" value="1" id="autoApprove"
                                       {{ old('auto_approve') ? 'checked' : 'checked' }}>
                                <label class="form-check-label small fw-semibold"
                                       for="autoApprove">
                                    Approve enrollment immediately
                                </label>
                            </div>
                            <div class="form-text">
                                Leave checked to approve right away.
                                Uncheck to save as Pending.
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-primary rounded-3 py-2">
                    <i class="fas fa-user-plus me-2"></i>Enroll Child
                </button>
                <a href="{{ route('admin.enrollment.index') }}"
                   class="btn btn-outline-secondary rounded-3">
                    Cancel
                </a>
            </div>
        </div>

    </div>
</form>
@endsection

@push('scripts')
<script>
function computeAge(dob) {
    if (!dob) return;
    const today = new Date();
    const birth  = new Date(dob);
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    const el = document.getElementById('ageDisplay');
    if (age >= 0) {
        el.textContent = 'Age: ' + age + ' year' + (age !== 1 ? 's' : '') + ' old';
        el.className = 'form-text text-primary fw-semibold';
    }
}
</script>
@endpush
