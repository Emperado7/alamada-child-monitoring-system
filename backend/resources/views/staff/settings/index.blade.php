@extends('layouts.app')
@section('title','Settings')
@section('page-title','Settings')

@section('content')

{{-- ── Page Title ───────────────────────────────────────────── --}}
<div class="mb-3">
    <h4 class="fw-bold mb-1">Settings</h4>
    <div class="text-muted small">Manage your account settings and system preferences.</div>
</div>

{{-- ── Tab Navigation ───────────────────────────────────────── --}}
@php $tab = request('tab','profile'); @endphp

<div class="settings-tabs-row mb-4">
    @foreach([
        'profile'     => ['icon'=>'fa-user',        'label'=>'Profile'],
        'account'     => ['icon'=>'fa-id-card',     'label'=>'Account'],
        'preferences' => ['icon'=>'fa-sliders-h',   'label'=>'Preferences'],
        'security'    => ['icon'=>'fa-shield-alt',  'label'=>'Security'],
    ] as $key => $t)
    <a href="?tab={{ $key }}"
       class="settings-tab {{ $tab === $key ? 'active' : '' }}">
        <i class="fas {{ $t['icon'] }}"></i>
        {{ $t['label'] }}
    </a>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════════
     PROFILE TAB (default — matches screenshot exactly)
═══════════════════════════════════════════════════════════ --}}
@if($tab === 'profile')

<div class="row g-4">

    {{-- LEFT: Profile card + Personal Info + Preferences --}}
    <div class="col-lg-6">

        {{-- Profile Card --}}
        <div class="dash-card p-4 mb-4">
            <div class="d-flex align-items-center gap-4">

                {{-- Avatar --}}
                <div style="position:relative;flex-shrink:0">
                    <div style="width:80px;height:80px;border-radius:50%;
                                background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                color:#fff;display:flex;align-items:center;
                                justify-content:center;font-size:2rem;font-weight:700;
                                border:3px solid #e8f5e9">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div style="position:absolute;bottom:2px;right:2px;
                                width:22px;height:22px;border-radius:50%;
                                background:#2e7d32;display:flex;align-items:center;
                                justify-content:center;border:2px solid #fff">
                        <i class="fas fa-camera" style="color:#fff;font-size:.55rem"></i>
                    </div>
                </div>

                {{-- Name + info --}}
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold" style="font-size:1.1rem">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="badge rounded-2 px-2 py-1"
                              style="background:#e8f5e9;color:#2e7d32;font-size:.72rem;font-weight:700">
                            Staff
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                        <i class="fas fa-envelope" style="color:#9e9e9e;width:14px"></i>
                        {{ auth()->user()->email }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                        <i class="fas fa-phone" style="color:#9e9e9e;width:14px"></i>
                        {{ auth()->user()->phone ?? '+63 912 345 6789' }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="fas fa-calendar-alt" style="color:#9e9e9e;width:14px"></i>
                        Joined {{ auth()->user()->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal Information --}}
        <div class="dash-card p-4 mb-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fas fa-user" style="color:#2e7d32"></i>
                <span class="fw-bold" style="font-size:.95rem">Personal Information</span>
            </div>
            <div class="text-muted small mb-4">Update your personal details.</div>

            <form method="POST" action="{{ route('staff.settings.update') }}">
                @csrf
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label small fw-semibold mb-1">Full Name</label>
                        <input type="text" name="name"
                               value="{{ old('name', auth()->user()->name) }}"
                               class="form-control rounded-3 @error('name') is-invalid @enderror"
                               style="border:1.5px solid #e0e0e0">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold mb-1">Email Address</label>
                        <input type="email" name="email"
                               value="{{ old('email', auth()->user()->email) }}"
                               class="form-control rounded-3 @error('email') is-invalid @enderror"
                               style="border:1.5px solid #e0e0e0">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold mb-1">Phone Number</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', auth()->user()->phone) }}"
                               placeholder="+63 912 345 6789"
                               class="form-control rounded-3"
                               style="border:1.5px solid #e0e0e0">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold mb-1">Date of Birth</label>
                        <div class="input-group">
                            <input type="date" name="date_of_birth"
                                   value="1990-01-01"
                                   class="form-control rounded-3"
                                   style="border:1.5px solid #e0e0e0;border-right:none">
                            <span class="input-group-text bg-white"
                                  style="border:1.5px solid #e0e0e0;border-left:none">
                                <i class="fas fa-calendar-alt text-muted small"></i>
                            </span>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold mb-1">Address</label>
                        <input type="text" name="address"
                               value="Alamada, North Cotabato, Philippines"
                               class="form-control rounded-3"
                               style="border:1.5px solid #e0e0e0">
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit"
                            class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                            style="background:#2e7d32;border:none;font-size:.88rem">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Preferences --}}
        <div class="dash-card p-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fas fa-cog" style="color:#2e7d32"></i>
                <span class="fw-bold" style="font-size:.95rem">Preferences</span>
            </div>
            <div class="text-muted small mb-4">Customize your system preferences.</div>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1 d-flex align-items-center gap-1">
                        <i class="fas fa-globe" style="color:#2e7d32;font-size:.8rem"></i>
                        Language
                    </label>
                    <select class="form-select rounded-3" style="border:1.5px solid #e0e0e0;font-size:.88rem">
                        <option selected>English</option>
                        <option>Filipino</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1 d-flex align-items-center gap-1">
                        <i class="fas fa-calendar-alt" style="color:#2e7d32;font-size:.8rem"></i>
                        Date Format
                    </label>
                    <select class="form-select rounded-3" style="border:1.5px solid #e0e0e0;font-size:.88rem">
                        <option selected>{{ now()->format('M d, Y (l)') }}</option>
                        <option>{{ now()->format('Y-m-d') }}</option>
                        <option>{{ now()->format('d/m/Y') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1 d-flex align-items-center gap-1">
                        <i class="fas fa-clock" style="color:#2e7d32;font-size:.8rem"></i>
                        Time Format
                    </label>
                    <select class="form-select rounded-3" style="border:1.5px solid #e0e0e0;font-size:.88rem">
                        <option selected>12-Hour (AM/PM)</option>
                        <option>24-Hour</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1 d-flex align-items-center gap-1">
                        <i class="fas fa-palette" style="color:#2e7d32;font-size:.8rem"></i>
                        Theme
                    </label>
                    <select class="form-select rounded-3" style="border:1.5px solid #e0e0e0;font-size:.88rem">
                        <option selected>Light</option>
                        <option>Dark</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="button"
                        onclick="alert('Preferences saved.')"
                        class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                        style="background:#2e7d32;border:none;font-size:.88rem">
                    <i class="fas fa-save me-2"></i>Save Preferences
                </button>
            </div>
        </div>

    </div>

    {{-- RIGHT: Account Overview + Change Password --}}
    <div class="col-lg-6">

        {{-- Account Overview --}}
        <div class="dash-card p-4 mb-4">
            <div class="fw-bold mb-3" style="font-size:.95rem">Account Overview</div>
            <table class="table table-sm mb-0">
                <tbody>
                    <tr style="border-color:#f5f5f5">
                        <td class="text-muted small py-2" style="width:120px">
                            <i class="fas fa-user me-2" style="color:#9e9e9e;width:16px"></i>Role
                        </td>
                        <td class="small fw-semibold py-2">Staff</td>
                    </tr>
                    <tr style="border-color:#f5f5f5">
                        <td class="text-muted small py-2">
                            <i class="fas fa-circle me-2" style="color:#9e9e9e;width:16px"></i>Status
                        </td>
                        <td class="small py-2">
                            <span style="color:#2e7d32;font-weight:700">
                                <i class="fas fa-circle me-1" style="font-size:.55rem"></i>Active
                            </span>
                        </td>
                    </tr>
                    <tr style="border-color:#f5f5f5">
                        <td class="text-muted small py-2">
                            <i class="fas fa-clock me-2" style="color:#9e9e9e;width:16px"></i>Last Login
                        </td>
                        <td class="small fw-semibold py-2">
                            {{ auth()->user()->updated_at->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                    <tr style="border:none">
                        <td class="text-muted small py-2" style="border:none">
                            <i class="fas fa-shield-alt me-2" style="color:#9e9e9e;width:16px"></i>Account Type
                        </td>
                        <td class="small fw-semibold py-2" style="border:none">Staff Account</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Change Password --}}
        <div class="dash-card p-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fas fa-lock" style="color:#2e7d32"></i>
                <span class="fw-bold" style="font-size:.95rem">Change Password</span>
            </div>
            <div class="text-muted small mb-4">Update your account password.</div>

            <form method="POST" action="{{ route('staff.settings.password') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold mb-1">Current Password</label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="cp"
                               placeholder="Enter current password"
                               class="form-control rounded-start-3
                               @error('current_password') is-invalid @enderror"
                               style="border:1.5px solid #e0e0e0;border-right:none">
                        <button type="button"
                                class="btn bg-white"
                                style="border:1.5px solid #e0e0e0;border-left:none;border-radius:0 10px 10px 0"
                                onclick="togglePwd('cp','eye-cp')">
                            <i class="fas fa-eye text-muted small" id="eye-cp"></i>
                        </button>
                    </div>
                    @error('current_password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold mb-1">New Password</label>
                    <div class="input-group">
                        <input type="password" name="new_password" id="np"
                               placeholder="Enter new password"
                               class="form-control
                               @error('new_password') is-invalid @enderror"
                               style="border:1.5px solid #e0e0e0;border-right:none"
                               minlength="8">
                        <button type="button"
                                class="btn bg-white"
                                style="border:1.5px solid #e0e0e0;border-left:none;border-radius:0 10px 10px 0"
                                onclick="togglePwd('np','eye-np')">
                            <i class="fas fa-eye text-muted small" id="eye-np"></i>
                        </button>
                    </div>
                    @error('new_password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold mb-1">Confirm New Password</label>
                    <div class="input-group">
                        <input type="password" name="new_password_confirmation" id="cnp"
                               placeholder="Confirm new password"
                               class="form-control"
                               style="border:1.5px solid #e0e0e0;border-right:none">
                        <button type="button"
                                class="btn bg-white"
                                style="border:1.5px solid #e0e0e0;border-left:none;border-radius:0 10px 10px 0"
                                onclick="togglePwd('cnp','eye-cnp')">
                            <i class="fas fa-eye text-muted small" id="eye-cnp"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit"
                            class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                            style="background:#2e7d32;border:none;font-size:.88rem">
                        <i class="fas fa-key me-2"></i>Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ACCOUNT TAB
═══════════════════════════════════════════════════════════ --}}
@elseif($tab === 'account')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="dash-card p-4">
            <div class="fw-bold mb-4">Account Information</div>
            <table class="table table-sm">
                <tbody>
                    <tr><td class="text-muted small">Full Name</td><td class="fw-semibold small">{{ auth()->user()->name }}</td></tr>
                    <tr><td class="text-muted small">Email</td><td class="small">{{ auth()->user()->email }}</td></tr>
                    <tr><td class="text-muted small">Phone</td><td class="small">{{ auth()->user()->phone ?? '—' }}</td></tr>
                    <tr><td class="text-muted small">Role</td><td><span class="badge rounded-2 px-2" style="background:#e8f5e9;color:#2e7d32">Staff</span></td></tr>
                    <tr><td class="text-muted small">Status</td><td><span style="color:#2e7d32;font-weight:700">● Active</span></td></tr>
                    <tr><td class="text-muted small">Member Since</td><td class="small">{{ auth()->user()->created_at->format('M d, Y') }}</td></tr>
                </tbody>
            </table>
            <a href="?tab=profile" class="btn btn-success rounded-3 px-3 py-2"
               style="background:#2e7d32;border:none;font-size:.85rem">
                <i class="fas fa-edit me-1"></i>Edit Profile
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     PREFERENCES TAB
═══════════════════════════════════════════════════════════ --}}
@elseif($tab === 'preferences')
<div class="dash-card p-4">
    <div class="fw-bold mb-4">System Preferences</div>
    <div class="row g-3">
        @foreach([
            ['Language','language','English',['English','Filipino'],'fa-globe'],
            ['Date Format','date_format','M d, Y (l)',['M d, Y (l)','Y-m-d','d/m/Y'],'fa-calendar-alt'],
            ['Time Format','time_format','12-Hour (AM/PM)',['12-Hour (AM/PM)','24-Hour'],'fa-clock'],
            ['Theme','theme','Light',['Light','Dark'],'fa-palette'],
        ] as [$label,$name,$default,$options,$icon])
        <div class="col-md-3">
            <label class="form-label small fw-semibold mb-1">
                <i class="fas {{ $icon }} me-1" style="color:#2e7d32;font-size:.8rem"></i>
                {{ $label }}
            </label>
            <select name="{{ $name }}" class="form-select rounded-3"
                    style="border:1.5px solid #e0e0e0;font-size:.88rem">
                @foreach($options as $opt)
                    <option {{ $opt==$default?'selected':'' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-end mt-4">
        <button onclick="alert('Preferences saved.')"
                class="btn btn-success rounded-3 px-4 py-2"
                style="background:#2e7d32;border:none;font-size:.88rem">
            <i class="fas fa-save me-2"></i>Save Preferences
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECURITY TAB
═══════════════════════════════════════════════════════════ --}}
@elseif($tab === 'security')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="dash-card p-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fas fa-lock" style="color:#2e7d32"></i>
                <span class="fw-bold">Change Password</span>
            </div>
            <div class="text-muted small mb-4">Keep your account secure.</div>
            <form method="POST" action="{{ route('staff.settings.password') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Current Password</label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="sec-cp"
                               class="form-control @error('current_password') is-invalid @enderror"
                               style="border:1.5px solid #e0e0e0;border-right:none">
                        <button type="button" class="btn bg-white"
                                style="border:1.5px solid #e0e0e0;border-left:none"
                                onclick="togglePwd('sec-cp','eye-sec-cp')">
                            <i class="fas fa-eye text-muted small" id="eye-sec-cp"></i>
                        </button>
                    </div>
                    @error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">New Password</label>
                    <div class="input-group">
                        <input type="password" name="new_password" id="sec-np"
                               class="form-control @error('new_password') is-invalid @enderror"
                               style="border:1.5px solid #e0e0e0;border-right:none"
                               minlength="8">
                        <button type="button" class="btn bg-white"
                                style="border:1.5px solid #e0e0e0;border-left:none"
                                onclick="togglePwd('sec-np','eye-sec-np')">
                            <i class="fas fa-eye text-muted small" id="eye-sec-np"></i>
                        </button>
                    </div>
                    @error('new_password')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="new_password_confirmation" id="sec-cnp"
                               class="form-control"
                               style="border:1.5px solid #e0e0e0;border-right:none">
                        <button type="button" class="btn bg-white"
                                style="border:1.5px solid #e0e0e0;border-left:none"
                                onclick="togglePwd('sec-cnp','eye-sec-cnp')">
                            <i class="fas fa-eye text-muted small" id="eye-sec-cnp"></i>
                        </button>
                    </div>
                </div>
                <button type="submit"
                        class="btn btn-success rounded-3 px-4 py-2"
                        style="background:#2e7d32;border:none;font-size:.88rem">
                    <i class="fas fa-key me-2"></i>Update Password
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="dash-card p-4">
            <div class="fw-bold mb-3">Account Security Info</div>
            <table class="table table-sm">
                <tr><td class="text-muted small">Account Status</td>
                    <td><span style="color:#2e7d32;font-weight:700">● Active</span></td></tr>
                <tr><td class="text-muted small">Last Login</td>
                    <td class="small fw-semibold">{{ auth()->user()->updated_at->format('M d, Y h:i A') }}</td></tr>
                <tr><td class="text-muted small">Account Type</td>
                    <td class="small">Staff Account</td></tr>
                <tr><td class="text-muted small">Member Since</td>
                    <td class="small">{{ auth()->user()->created_at->format('M d, Y') }}</td></tr>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
/* ── Settings Tab navigation ────────────────────────────── */
.settings-tabs-row {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #e0e0e0;
}
.settings-tab {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 10px 20px;
    color: #757575;
    font-size: .88rem;
    font-weight: 500;
    text-decoration: none;
    border-bottom: 2.5px solid transparent;
    margin-bottom: -2px;
    transition: color .15s, border-color .15s;
}
.settings-tab i { font-size: .85rem; }
.settings-tab:hover { color: #2e7d32; }
.settings-tab.active {
    color: #2e7d32;
    font-weight: 700;
    border-bottom-color: #2e7d32;
}
</style>
@endpush

@push('scripts')
<script>
function togglePwd(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    if (!f || !i) return;
    if (f.type === 'password') {
        f.type = 'text';
        i.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        f.type = 'password';
        i.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush
