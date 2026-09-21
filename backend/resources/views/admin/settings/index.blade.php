@extends('layouts.app')
@section('title','Settings')
@section('page-title','Settings')

@section('content')

{{-- ── Breadcrumb + Title ───────────────────────────────────── --}}
<div class="mb-4">
    <h4 class="fw-bold mb-1">Settings</h4>
    <nav style="font-size:.83rem">
        <a href="{{ route('admin.dashboard') }}"
           style="color:#2e7d32;text-decoration:none">Dashboard</a>
        <span class="text-muted mx-1">›</span>
        <span class="text-muted">Settings</span>
    </nav>
</div>

<div class="row g-4">

    {{-- ══════════════════════════════════════════════════════
         LEFT: Settings Navigation Tabs
    ══════════════════════════════════════════════════════ --}}
    <div class="col-lg-3 col-md-4">
        <div class="dash-card p-0 overflow-hidden">
            <nav class="settings-nav">
                @php
                    $tab = request('tab','general');
                    $tabs = [
                        'general'       => ['icon'=>'fa-cog',          'label'=>'General Settings'],
                        'school'        => ['icon'=>'fa-school',        'label'=>'School Information'],
                        'notifications' => ['icon'=>'fa-bell',          'label'=>'Notification Settings'],
                        'preferences'   => ['icon'=>'fa-sliders-h',     'label'=>'System Preferences'],
                        'account'       => ['icon'=>'fa-user-circle',   'label'=>'Account Settings'],
                        'security'      => ['icon'=>'fa-shield-alt',    'label'=>'Security'],
                        'backup'        => ['icon'=>'fa-database',      'label'=>'Backup & Restore'],
                        'logs'          => ['icon'=>'fa-list-alt',      'label'=>'System Logs'],
                    ];
                @endphp

                @foreach($tabs as $key => $t)
                <a href="?tab={{ $key }}"
                   class="settings-nav-item {{ $tab === $key ? 'active' : '' }}">
                    <i class="fas {{ $t['icon'] }}"></i>
                    <span>{{ $t['label'] }}</span>
                </a>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         RIGHT: Settings Content Panel
    ══════════════════════════════════════════════════════ --}}
    <div class="col-lg-9 col-md-8">

        {{-- ── GENERAL SETTINGS ──────────────────────────────── --}}
        @if($tab === 'general')
        <div class="dash-card p-4">
            <h5 class="fw-bold mb-4">General Settings</h5>
            <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
                @csrf

                {{-- System Name --}}
                <div class="settings-row">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-globe" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">System Name</div>
                        <div class="text-muted" style="font-size:.78rem">
                            The name of the system displayed in the header.
                        </div>
                    </div>
                    <div class="settings-control">
                        <input type="text" name="system_name"
                               value="Alamada Learning Center Monitoring System"
                               class="form-control form-control-sm rounded-3">
                    </div>
                </div>

                {{-- Theme --}}
                <div class="settings-row">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-palette" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">Theme</div>
                        <div class="text-muted" style="font-size:.78rem">
                            Select the color theme for the system.
                        </div>
                    </div>
                    <div class="settings-control">
                        <select name="theme" class="form-select form-select-sm rounded-3">
                            <option selected>Green (Default)</option>
                            <option>Blue</option>
                            <option>Dark</option>
                        </select>
                    </div>
                </div>

                {{-- Date Format --}}
                <div class="settings-row">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-calendar-alt" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">Date Format</div>
                        <div class="text-muted" style="font-size:.78rem">
                            Choose the date format for the system.
                        </div>
                    </div>
                    <div class="settings-control">
                        <select name="date_format" class="form-select form-select-sm rounded-3">
                            <option selected>{{ now()->format('M d, Y') }} (MM/DD/YYYY)</option>
                            <option>{{ now()->format('Y-m-d') }} (YYYY-MM-DD)</option>
                            <option>{{ now()->format('d/m/Y') }} (DD/MM/YYYY)</option>
                        </select>
                    </div>
                </div>

                {{-- Time Format --}}
                <div class="settings-row">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-clock" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">Time Format</div>
                        <div class="text-muted" style="font-size:.78rem">
                            Choose the time format for the system.
                        </div>
                    </div>
                    <div class="settings-control">
                        <select name="time_format" class="form-select form-select-sm rounded-3">
                            <option selected>12-Hour (AM/PM)</option>
                            <option>24-Hour</option>
                        </select>
                    </div>
                </div>

                {{-- Timezone --}}
                <div class="settings-row">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-globe-asia" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">Timezone</div>
                        <div class="text-muted" style="font-size:.78rem">
                            Select the timezone for the system.
                        </div>
                    </div>
                    <div class="settings-control">
                        <select name="timezone" class="form-select form-select-sm rounded-3">
                            <option selected>(GMT+08:00) Asia/Manila</option>
                            <option>(GMT+00:00) UTC</option>
                            <option>(GMT+08:00) Asia/Singapore</option>
                        </select>
                    </div>
                </div>

                {{-- Items Per Page --}}
                <div class="settings-row" style="border-bottom:none">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-list" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">Items Per Page</div>
                        <div class="text-muted" style="font-size:.78rem">
                            Set the number of items to display per page in tables.
                        </div>
                    </div>
                    <div class="settings-control">
                        <select name="per_page" class="form-select form-select-sm rounded-3">
                            @foreach([10,15,25,50] as $p)
                                <option value="{{ $p }}" {{ $p==10?'selected':'' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit"
                            class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                            style="background:#2e7d32;border:none">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ── SCHOOL INFORMATION ─────────────────────────────── --}}
        @elseif($tab === 'school')
        <div class="dash-card p-4">
            <h5 class="fw-bold mb-4">School Information</h5>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @foreach([
                    ['Center Name','center_name','Alamada LGU Learning Center','text'],
                    ['Municipality','municipality','Alamada, Cotabato','text'],
                    ['Province','province','North Cotabato','text'],
                    ['Region','region','Region XII (SOCCSKSARGEN)','text'],
                    ['Contact Email','contact_email','admin@alamada-lgu.gov.ph','email'],
                    ['Contact Phone','contact_phone','09171234567','tel'],
                ] as [$label,$name,$val,$type])
                <div class="settings-row {{ $loop->last ? '' : '' }}">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-{{ $type=='email' ? 'envelope' : ($type=='tel' ? 'phone' : 'info-circle') }}"
                           style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">{{ $label }}</div>
                    </div>
                    <div class="settings-control">
                        <input type="{{ $type }}" name="{{ $name }}"
                               value="{{ $val }}"
                               class="form-control form-control-sm rounded-3">
                    </div>
                </div>
                @endforeach
                <div class="settings-row" style="border-bottom:none">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-calendar" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">Current School Year</div>
                    </div>
                    <div class="settings-control">
                        <input type="text" name="school_year"
                               value="{{ now()->year }} - {{ now()->year + 1 }}"
                               class="form-control form-control-sm rounded-3">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit"
                            class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                            style="background:#2e7d32;border:none">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ── NOTIFICATION SETTINGS ──────────────────────────── --}}
        @elseif($tab === 'notifications')
        <div class="dash-card p-4">
            <h5 class="fw-bold mb-4">Notification Settings</h5>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @foreach([
                    ['Enable Email Notifications','email_notifications','Send system notifications via email'],
                    ['Enrollment Alerts','enrollment_alerts','Alert admin on new enrollment requests'],
                    ['Attendance Alerts','attendance_alerts','Daily attendance summary notifications'],
                    ['Parent Notifications','parent_notifications','Automatically notify parents of updates'],
                    ['System Announcements','system_announcements','Broadcast announcements to all users'],
                ] as [$label,$name,$hint])
                <div class="settings-row {{ $loop->last ? '' : '' }}"
                     style="{{ $loop->last ? 'border-bottom:none' : '' }}">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-bell" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label flex-grow-1">
                        <div class="fw-semibold small">{{ $label }}</div>
                        <div class="text-muted" style="font-size:.78rem">{{ $hint }}</div>
                    </div>
                    <div class="settings-control" style="min-width:auto">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox"
                                   name="{{ $name }}" checked
                                   style="width:2.8rem;height:1.4rem;cursor:pointer">
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit"
                            class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                            style="background:#2e7d32;border:none">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ── ACCOUNT SETTINGS ───────────────────────────────── --}}
        @elseif($tab === 'account')
        <div class="dash-card p-4">
            <h5 class="fw-bold mb-4">Account Settings</h5>

            {{-- Profile card --}}
            <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-3"
                 style="background:#f8f9fa">
                <div style="width:64px;height:64px;border-radius:50%;
                            background:linear-gradient(135deg,#2e7d32,#66bb6a);
                            color:#fff;display:flex;align-items:center;
                            justify-content:center;font-size:1.8rem;font-weight:700">
                    {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                </div>
                <div>
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <div class="text-muted small">{{ auth()->user()->email }}</div>
                    <span class="badge rounded-pill mt-1 px-3"
                          style="background:#e8f5e9;color:#2e7d32;font-size:.78rem">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control rounded-3"
                               value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-3"
                               value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control rounded-3"
                               value="{{ old('phone', auth()->user()->phone) }}"
                               placeholder="e.g. 09171234567">
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit"
                            class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                            style="background:#2e7d32;border:none">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ── SECURITY ────────────────────────────────────────── --}}
        @elseif($tab === 'security')
        <div class="dash-card p-4">
            <h5 class="fw-bold mb-4">Security</h5>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="cp"
                                   class="form-control rounded-start-3
                                   @error('current_password') is-invalid @enderror">
                            <button type="button" class="btn btn-outline-secondary rounded-end-3"
                                    onclick="toggleField('cp')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" id="np"
                                   class="form-control rounded-start-3
                                   @error('new_password') is-invalid @enderror"
                                   minlength="8">
                            <button type="button" class="btn btn-outline-secondary rounded-end-3"
                                    onclick="toggleField('np')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="text-muted" style="font-size:.75rem">Minimum 8 characters</div>
                        @error('new_password')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password_confirmation" id="cnp"
                                   class="form-control rounded-start-3">
                            <button type="button" class="btn btn-outline-secondary rounded-end-3"
                                    onclick="toggleField('cnp')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit"
                            class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                            style="background:#2e7d32;border:none">
                        <i class="fas fa-key me-2"></i>Update Password
                    </button>
                </div>
            </form>
        </div>

        {{-- ── BACKUP & RESTORE ───────────────────────────────── --}}
        @elseif($tab === 'backup')
        <div class="dash-card p-4">
            <h5 class="fw-bold mb-4">Backup &amp; Restore</h5>

            <div class="row g-3">
                <div class="col-12">
                    <div class="p-3 rounded-3 border d-flex align-items-center gap-3">
                        <div style="width:48px;height:48px;border-radius:12px;
                                    background:#e8f5e9;display:flex;align-items:center;
                                    justify-content:center;flex-shrink:0">
                            <i class="fas fa-download" style="color:#2e7d32;font-size:1.2rem"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Download Backup</div>
                            <div class="text-muted small">
                                Download a full backup of the system database.
                            </div>
                        </div>
                        <button class="btn btn-success rounded-3 px-3"
                                style="background:#2e7d32;border:none;font-size:.88rem"
                                onclick="alert('Backup feature: Export your database from phpMyAdmin at http://localhost/phpmyadmin')">
                            <i class="fas fa-download me-1"></i>Download
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 rounded-3 border d-flex align-items-center gap-3">
                        <div style="width:48px;height:48px;border-radius:12px;
                                    background:#e3f2fd;display:flex;align-items:center;
                                    justify-content:center;flex-shrink:0">
                            <i class="fas fa-upload" style="color:#1565c0;font-size:1.2rem"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Restore from Backup</div>
                            <div class="text-muted small">
                                Restore the system from a previous backup file.
                            </div>
                        </div>
                        <button class="btn btn-outline-primary rounded-3 px-3"
                                style="font-size:.88rem"
                                onclick="alert('Restore feature: Import your database from phpMyAdmin at http://localhost/phpmyadmin')">
                            <i class="fas fa-upload me-1"></i>Restore
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SYSTEM LOGS ─────────────────────────────────────── --}}
        @elseif($tab === 'logs')
        <div class="dash-card p-4">
            <h5 class="fw-bold mb-3">System Logs</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead style="background:#f8f9fa">
                        <tr>
                            <th class="small fw-semibold" style="color:#2e7d32">Time</th>
                            <th class="small fw-semibold" style="color:#2e7d32">Level</th>
                            <th class="small fw-semibold" style="color:#2e7d32">Event</th>
                            <th class="small fw-semibold" style="color:#2e7d32">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="small text-muted">{{ now()->format('M d, Y h:i A') }}</td>
                            <td><span class="badge rounded-2 px-2" style="background:#e8f5e9;color:#2e7d32;font-size:.72rem">INFO</span></td>
                            <td class="small">System started successfully</td>
                            <td class="small">System</td>
                        </tr>
                        <tr>
                            <td class="small text-muted">{{ now()->subMinutes(5)->format('M d, Y h:i A') }}</td>
                            <td><span class="badge rounded-2 px-2" style="background:#e3f2fd;color:#1565c0;font-size:.72rem">AUTH</span></td>
                            <td class="small">Admin logged in</td>
                            <td class="small">{{ auth()->user()->name }}</td>
                        </tr>
                        <tr>
                            <td class="small text-muted">{{ now()->subMinutes(10)->format('M d, Y h:i A') }}</td>
                            <td><span class="badge rounded-2 px-2" style="background:#e8f5e9;color:#2e7d32;font-size:.72rem">INFO</span></td>
                            <td class="small">Database connected — alamada_cms</td>
                            <td class="small">System</td>
                        </tr>
                        <tr>
                            <td class="small text-muted">{{ now()->subMinutes(20)->format('M d, Y h:i A') }}</td>
                            <td><span class="badge rounded-2 px-2" style="background:#e8f5e9;color:#2e7d32;font-size:.72rem">INFO</span></td>
                            <td class="small">PHP Version: {{ PHP_VERSION }}</td>
                            <td class="small">System</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── SYSTEM PREFERENCES ─────────────────────────────── --}}
        @elseif($tab === 'preferences')
        <div class="dash-card p-4">
            <h5 class="fw-bold mb-4">System Preferences</h5>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @foreach([
                    ['Language','language','English','select',['English','Filipino']],
                    ['Default Page','default_page','Dashboard','select',['Dashboard','Child Information','Enrollment','Attendance']],
                    ['Sidebar Style','sidebar','Expanded','select',['Expanded','Collapsed']],
                    ['Table Density','table_density','Default','select',['Default','Compact','Comfortable']],
                ] as [$label,$name,$default,$type,$options])
                <div class="settings-row">
                    <div class="settings-icon-wrap">
                        <i class="fas fa-sliders-h" style="color:#2e7d32"></i>
                    </div>
                    <div class="settings-label">
                        <div class="fw-semibold small">{{ $label }}</div>
                    </div>
                    <div class="settings-control">
                        <select name="{{ $name }}" class="form-select form-select-sm rounded-3">
                            @foreach($options as $opt)
                                <option {{ $opt==$default?'selected':'' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endforeach
                <div class="settings-row" style="border-bottom:none"></div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit"
                            class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                            style="background:#2e7d32;border:none">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ── DEFAULT: General ────────────────────────────────── --}}
        @else
        <div class="dash-card p-4">
            <h5 class="fw-bold">General Settings</h5>
            <p class="text-muted small">Select a settings section from the left.</p>
        </div>
        @endif

    </div>{{-- end right col --}}
</div>{{-- end row --}}

@endsection

@push('styles')
<style>
/* ── Settings nav ──────────────────────────────────────── */
.settings-nav {
    display: flex;
    flex-direction: column;
}
.settings-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    color: #424242;
    text-decoration: none;
    font-size: .88rem;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: background .15s, color .15s;
}
.settings-nav-item i {
    width: 18px;
    text-align: center;
    color: #9e9e9e;
}
.settings-nav-item:hover {
    background: #f5f5f5;
    color: #2e7d32;
}
.settings-nav-item:hover i { color: #2e7d32; }
.settings-nav-item.active {
    background: #e8f5e9;
    color: #2e7d32;
    font-weight: 600;
    border-left-color: #2e7d32;
}
.settings-nav-item.active i { color: #2e7d32; }

/* ── Settings rows ─────────────────────────────────────── */
.settings-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #f0f0f0;
}
.settings-icon-wrap {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.settings-label {
    flex: 0 0 260px;
    max-width: 260px;
}
.settings-control {
    flex-grow: 1;
    max-width: 320px;
}
@media (max-width: 768px) {
    .settings-row { flex-wrap: wrap; }
    .settings-label { flex: 0 0 100%; max-width: 100%; }
    .settings-control { max-width: 100%; width: 100%; }
}
</style>
@endpush

@push('scripts')
<script>
function toggleField(id) {
    const f = document.getElementById(id);
    f.type = f.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
