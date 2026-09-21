@extends('layouts.app')
@section('title','Manage Users')
@section('page-title','Manage Users')

@section('content')

{{-- ── Breadcrumb + Title ───────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="fw-bold mb-1">Manage Users</h4>
        <nav style="font-size:.83rem">
            <a href="{{ route('admin.dashboard') }}"
               style="color:#2e7d32;text-decoration:none">Dashboard</a>
            <span class="text-muted mx-1">›</span>
            <span class="text-muted">Manage Users</span>
        </nav>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="btn btn-success rounded-3 px-3 py-2 fw-semibold"
       style="background:#2e7d32;border:none;font-size:.88rem">
        <i class="fas fa-plus me-2"></i>Add New User
    </a>
</div>

{{-- ── 2 Stat Cards (Staff + Parents) ──────────────────────── --}}
@php
    $staffCount   = \App\Models\User::where('role','staff')->count();
    $parentCount  = \App\Models\User::where('role','parent')->count();
@endphp

<div class="row g-3 mb-4">

    {{-- Staff Card --}}
    <div class="col-md-6">
        <div class="dash-card p-4">
            <div class="d-flex align-items-center gap-4">
                <div style="width:60px;height:60px;border-radius:14px;
                            background:#f5f5f5;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <i class="fas fa-user-tie" style="font-size:1.8rem;color:#424242"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-muted mb-1"
                         style="font-size:.8rem;letter-spacing:.08em;text-transform:uppercase">
                        STAFF
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#212121;line-height:1">
                        {{ $staffCount }}
                    </div>
                    <div class="text-muted small mt-1">All Staff</div>
                </div>
                <div class="text-end">
                    <a href="?role=staff"
                       class="btn btn-sm rounded-3 px-3 py-2"
                       style="background:#e8f5e9;color:#2e7d32;border:none;font-weight:600">
                        View Details
                        <i class="fas fa-arrow-right ms-1" style="font-size:.75rem"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Parents Card --}}
    <div class="col-md-6">
        <div class="dash-card p-4">
            <div class="d-flex align-items-center gap-4">
                <div style="width:60px;height:60px;border-radius:14px;
                            background:#f3e5f5;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    <i class="fas fa-users" style="font-size:1.8rem;color:#6a1b9a"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-muted mb-1"
                         style="font-size:.8rem;letter-spacing:.08em;text-transform:uppercase">
                        PARENTS
                    </div>
                    <div style="font-size:2.8rem;font-weight:800;color:#6a1b9a;line-height:1">
                        {{ $parentCount }}
                    </div>
                    <div class="text-muted small mt-1">All Parents</div>
                </div>
                <div class="text-end">
                    <a href="?role=parent"
                       class="btn btn-sm rounded-3 px-3 py-2"
                       style="background:#f3e5f5;color:#6a1b9a;border:none;font-weight:600">
                        View Details
                        <i class="fas fa-arrow-right ms-1" style="font-size:.75rem"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Table Card ───────────────────────────────────────────── --}}
<div class="dash-card">

    {{-- Toolbar --}}
    <div class="dash-card-header flex-wrap gap-2">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">

            {{-- User Type --}}
            <label class="small text-muted mb-0 fw-semibold">User Type</label>
            <select name="role"
                    class="form-select form-select-sm rounded-2"
                    style="width:140px">
                <option value="">All Users</option>
                <option value="admin"  {{ request('role')=='admin'  ?'selected':'' }}>Admin</option>
                <option value="staff"  {{ request('role')=='staff'  ?'selected':'' }}>Staff</option>
                <option value="parent" {{ request('role')=='parent' ?'selected':'' }}>Parent</option>
            </select>

            {{-- Status --}}
            <label class="small text-muted mb-0 fw-semibold">Status</label>
            <select name="status"
                    class="form-select form-select-sm rounded-2"
                    style="width:140px">
                <option value="">All Status</option>
                <option value="active"   {{ request('status')=='active'  ?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
            </select>

            {{-- Search --}}
            <div class="d-flex align-items-center gap-2 ms-auto">
                <div class="input-group" style="max-width:240px">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search user...">
                    <button class="btn btn-success btn-sm"
                            style="background:#2e7d32;border-color:#2e7d32">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary rounded-2 dropdown-toggle"
                            data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>Filters
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2">
                        @foreach([''=>'All Users','admin'=>'Admin','staff'=>'Staff','parent'=>'Parent'] as $v=>$l)
                        <li>
                            <a class="dropdown-item rounded-2 small {{ request('role')==$v?'fw-bold':'' }}"
                               href="{{ request()->fullUrlWithQuery(['role'=>$v,'page'=>1]) }}">
                                @if($v=='admin')     <span class="me-2" style="color:#c62828">●</span>
                                @elseif($v=='staff') <span class="me-2" style="color:#1565c0">●</span>
                                @elseif($v=='parent')<span class="me-2" style="color:#6a1b9a">●</span>
                                @else                <span class="me-2" style="color:#9e9e9e">●</span>
                                @endif
                                {{ $l }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Table ──────────────────────────────────────────────── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-2 small fw-bold" style="color:#2e7d32">ID</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">User</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">User Type</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Email</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Phone</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Last Login</th>
                    <th class="py-2 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                @php
                    // ID prefix by role
                    $prefix = match($u->role) {
                        'admin'  => 'ADM',
                        'staff'  => 'USR',
                        'parent' => 'PRT',
                        default  => 'USR',
                    };
                    // Role badge
                    $roleBadge = match($u->role) {
                        'admin'  => ['bg'=>'#ffebee','color'=>'#c62828','label'=>'Admin'],
                        'staff'  => ['bg'=>'#e3f2fd','color'=>'#1565c0','label'=>'Staff'],
                        'parent' => ['bg'=>'#f3e5f5','color'=>'#6a1b9a','label'=>'Parent'],
                        default  => ['bg'=>'#f5f5f5','color'=>'#757575','label'=>ucfirst($u->role)],
                    };
                @endphp
                <tr>
                    {{-- ID --}}
                    <td class="px-3 fw-semibold small" style="color:#2e7d32">
                        {{ $prefix }}-{{ str_pad($u->id, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- User avatar + name --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:38px;height:38px;border-radius:50%;
                                        background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                        color:#fff;display:flex;align-items:center;
                                        justify-content:center;font-weight:700;
                                        font-size:.88rem;flex-shrink:0">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $u->name }}</div>
                                <div class="text-muted" style="font-size:.72rem">
                                    {{ ucfirst($u->role) }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- User Type badge --}}
                    <td>
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:{{ $roleBadge['bg'] }};
                                     color:{{ $roleBadge['color'] }};
                                     font-size:.78rem">
                            {{ $roleBadge['label'] }}
                        </span>
                    </td>

                    {{-- Email --}}
                    <td class="small text-muted">{{ $u->email }}</td>

                    {{-- Phone --}}
                    <td class="small">{{ $u->phone ?? '—' }}</td>

                    {{-- Status --}}
                    <td>
                        @if($u->status === 'active')
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:#e8f5e9;color:#2e7d32;font-size:.78rem">
                            Active
                        </span>
                        @else
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:#ffebee;color:#c62828;font-size:.78rem">
                            Inactive
                        </span>
                        @endif
                    </td>

                    {{-- Last Login (using updated_at as proxy) --}}
                    <td class="small text-muted">
                        {{ $u->updated_at->format('M d, Y') }}<br>
                        <span style="font-size:.72rem">
                            {{ $u->updated_at->format('h:i A') }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            {{-- View --}}
                            <button class="btn btn-sm rounded-2 p-1 px-2"
                                    style="background:#e3f2fd;color:#1565c0;border:none"
                                    title="View"
                                    onclick="viewUser(
                                        '{{ addslashes($u->name) }}',
                                        '{{ $u->email }}',
                                        '{{ $u->phone ?? '—' }}',
                                        '{{ ucfirst($u->role) }}',
                                        '{{ ucfirst($u->status) }}',
                                        '{{ $u->created_at->format('M d, Y') }}'
                                    )">
                                <i class="fas fa-eye" style="font-size:.8rem"></i>
                            </button>

                            {{-- Edit --}}
                            <a href="{{ route('admin.users.edit', $u) }}"
                               class="btn btn-sm rounded-2 p-1 px-2"
                               style="background:#e8f5e9;color:#2e7d32;border:none"
                               title="Edit">
                                <i class="fas fa-pencil-alt" style="font-size:.8rem"></i>
                            </a>

                            {{-- Delete --}}
                            @if($u->id !== auth()->id())
                            <form method="POST"
                                  action="{{ route('admin.users.destroy', $u) }}"
                                  onsubmit="return confirm('Delete user {{ addslashes($u->name) }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm rounded-2 p-1 px-2"
                                        style="background:#ffebee;color:#c62828;border:none"
                                        title="Delete">
                                    <i class="fas fa-trash" style="font-size:.8rem"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div style="color:#bdbdbd">
                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                            <div class="fw-semibold">No users found.</div>
                            <div class="small text-muted mt-2">
                                <a href="{{ route('admin.users.create') }}"
                                   style="color:#2e7d32">Add a new user</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────── --}}
    @if($users->hasPages())
    <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="small text-muted">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }}
            of {{ $users->total() }} entries
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">

                {{-- First --}}
                <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $users->url(1) }}"
                       style="background:#f5f5f5;color:{{ $users->onFirstPage() ? '#bdbdbd' : '#2e7d32' }}">
                        «
                    </a>
                </li>

                {{-- Pages --}}
                @php
                    $current   = $users->currentPage();
                    $last      = $users->lastPage();
                    $window    = 3;
                    $start     = max(1, $current - $window);
                    $end       = min($last, $current + $window);
                @endphp

                @if($start > 1)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $users->url(1) }}"
                       style="background:#f5f5f5;color:#212121">1</a>
                </li>
                @if($start > 2)
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#9e9e9e">…</span>
                </li>
                @endif
                @endif

                @for($p = $start; $p <= $end; $p++)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $users->url($p) }}"
                       style="{{ $p == $current
                           ? 'background:#2e7d32;color:#fff'
                           : 'background:#f5f5f5;color:#212121' }}">
                        {{ $p }}
                    </a>
                </li>
                @endfor

                @if($end < $last)
                @if($end < $last - 1)
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#9e9e9e">…</span>
                </li>
                @endif
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $users->url($last) }}"
                       style="background:#f5f5f5;color:#212121">
                        {{ $last }}
                    </a>
                </li>
                @endif

                {{-- Last --}}
                <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $users->url($last) }}"
                       style="background:#f5f5f5;color:{{ !$users->hasMorePages() ? '#bdbdbd' : '#2e7d32' }}">
                        »
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    @else
    <div class="px-3 py-3 border-top small text-muted">
        Showing {{ $users->count() }} entries
    </div>
    @endif
</div>

{{-- ── View User Modal ─────────────────────────────────────── --}}
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">User Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div id="viewAvatar"
                         style="width:64px;height:64px;border-radius:50%;
                                background:linear-gradient(135deg,#2e7d32,#66bb6a);
                                color:#fff;display:flex;align-items:center;
                                justify-content:center;font-size:1.8rem;
                                font-weight:700;margin:0 auto 10px"></div>
                    <div class="fw-bold fs-6" id="viewName"></div>
                </div>
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted small fw-semibold">Email</td>
                        <td class="small" id="viewEmail"></td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Phone</td>
                        <td class="small" id="viewPhone"></td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Role</td>
                        <td class="small" id="viewRole"></td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Status</td>
                        <td class="small" id="viewStatus"></td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Joined</td>
                        <td class="small" id="viewJoined"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button"
                        class="btn btn-success rounded-3 w-100"
                        style="background:#2e7d32;border:none"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewUser(name, email, phone, role, status, joined) {
    document.getElementById('viewAvatar').textContent  = name.charAt(0).toUpperCase();
    document.getElementById('viewName').textContent    = name;
    document.getElementById('viewEmail').textContent   = email;
    document.getElementById('viewPhone').textContent   = phone;
    document.getElementById('viewRole').textContent    = role;
    document.getElementById('viewStatus').textContent  = status;
    document.getElementById('viewJoined').textContent  = joined;
    new bootstrap.Modal(document.getElementById('viewUserModal')).show();
}
</script>
@endpush
