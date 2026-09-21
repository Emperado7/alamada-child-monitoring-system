{{-- Shared form fields for create/edit --}}
<div class="row g-3">
    {{-- Photo --}}
    <div class="col-12 text-center mb-2">
        @if(isset($child) && $child->photo)
            <img src="{{ asset('storage/'.$child->photo) }}" class="rounded-circle mb-2" width="80" height="80" style="object-fit:cover" id="photoPreview">
        @else
            <div class="avatar-circle mx-auto mb-2" style="width:80px;height:80px;font-size:2rem" id="photoPreview">
                <i class="fas fa-camera"></i>
            </div>
        @endif
        <div>
            <label class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-upload me-1"></i>Upload Photo
                <input type="file" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
            </label>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
               value="{{ old('first_name', $child->first_name ?? '') }}" required>
        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Middle Name</label>
        <input type="text" name="middle_name" class="form-control"
               value="{{ old('middle_name', $child->middle_name ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
               value="{{ old('last_name', $child->last_name ?? '') }}" required>
        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
               value="{{ old('date_of_birth', isset($child) ? $child->date_of_birth->format('Y-m-d') : '') }}" required>
        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Gender <span class="text-danger">*</span></label>
        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
            <option value="">Select Gender</option>
            <option value="male"   {{ old('gender', $child->gender ?? '') == 'male'   ? 'selected':'' }}>Male</option>
            <option value="female" {{ old('gender', $child->gender ?? '') == 'female' ? 'selected':'' }}>Female</option>
        </select>
        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @if(isset($child))
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['active','inactive','graduated'] as $s)
                <option value="{{ $s }}" {{ old('status',$child->status) == $s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="col-12">
        <label class="form-label">Address <span class="text-danger">*</span></label>
        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
               value="{{ old('address', $child->address ?? '') }}" required>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Guardian Name <span class="text-danger">*</span></label>
        <input type="text" name="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror"
               value="{{ old('guardian_name', $child->guardian_name ?? '') }}" required>
        @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Guardian Contact <span class="text-danger">*</span></label>
        <input type="text" name="guardian_contact" class="form-control @error('guardian_contact') is-invalid @enderror"
               value="{{ old('guardian_contact', $child->guardian_contact ?? '') }}" required>
        @error('guardian_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Diagnosis <span class="text-danger">*</span></label>
        <select name="diagnosis" class="form-select @error('diagnosis') is-invalid @enderror" required>
            <option value="">Select Diagnosis</option>
            @foreach(['ADHD','Autism','Mental Disability','Cerebral Palsy','Blindness','Other'] as $d)
                <option value="{{ $d }}" {{ old('diagnosis', $child->diagnosis ?? '') == $d ? 'selected':'' }}>{{ $d }}</option>
            @endforeach
        </select>
        @error('diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Diagnosis Notes</label>
        <input type="text" name="diagnosis_notes" class="form-control"
               value="{{ old('diagnosis_notes', $child->diagnosis_notes ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Assigned Staff</label>
        <select name="staff_id" class="form-select">
            <option value="">None</option>
            @foreach($staffList as $s)
                <option value="{{ $s->id }}" {{ old('staff_id', $child->staff_id ?? '') == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Parent Account</label>
        <select name="parent_id" class="form-select">
            <option value="">None</option>
            @foreach($parentList as $p)
                <option value="{{ $p->id }}" {{ old('parent_id', $child->parent_id ?? '') == $p->id ? 'selected':'' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
</div>

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('photoPreview');
            p.outerHTML = `<img src="${e.target.result}" class="rounded-circle mb-2" width="80" height="80" style="object-fit:cover" id="photoPreview">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
