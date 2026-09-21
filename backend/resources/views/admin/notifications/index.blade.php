@extends('layouts.app')
@section('title','Notifications')
@section('page-title','Notifications')

@section('content')

{{-- ── Breadcrumb + Title ───────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="fw-bold mb-1">Notification Dashboard</h4>
        <nav style="font-size:.83rem">
            <a href="{{ route('admin.dashboard') }}"
               style="color:#2e7d32;text-decoration:none">Dashboard</a>
            <span class="text-muted mx-1">›</span>
            <span class="text-muted">Notifications</span>
        </nav>
    </div>
    <a href="{{ route('admin.notifications.create') }}"
       class="btn btn-success rounded-3 px-3 py-2 fw-semibold"
       style="background:#2e7d32;border:none;font-size:.88rem">
        <i class="fas fa-plus me-2"></i>New Notification
    </a>
</div>

{{-- ── 4 Stat Cards ─────────────────────────────────────────── --}}
@php
    $totalNotifs     = \App\Models\Notification::count();
    $sentThisMonth   = \App\Models\Notification::whereMonth('created_at', now()->month)
                           ->whereYear('created_at', now()->year)->count();
    $unreadCount     = \DB::table('notification_reads')
                           ->whereNull('read_at')->count();
    $recipientsCount = \App\Models\User::where('role','parent')->count()
                     + \App\Models\User::where('role','staff')->count();
@endphp

<div class="row g-3 mb-4">

    {{-- Total Notifications --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-bell" style="color:#2e7d32;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Notifications</div>
                <div class="stat-value" style="color:#2e7d32">{{ $totalNotifs }}</div>
                <div class="text-muted" style="font-size:.78rem">All Time</div>
                <a href="#notifTable" class="stat-link mt-1">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Sent --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-paper-plane" style="color:#1565c0;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Sent</div>
                <div class="stat-value" style="color:#1565c0">{{ $sentThisMonth }}</div>
                <div class="text-muted" style="font-size:.78rem">This Month</div>
                <a href="#notifTable" class="stat-link mt-1">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Unread --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-envelope" style="color:#e65100;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Unread</div>
                <div class="stat-value" style="color:#e65100">{{ $unreadCount }}</div>
                <div class="text-muted" style="font-size:.78rem">&nbsp;</div>
                <a href="#notifTable" class="stat-link mt-1">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Recipients --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-users" style="color:#6a1b9a;font-size:1.4rem"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Recipients</div>
                <div class="stat-value" style="color:#6a1b9a">{{ $recipientsCount }}</div>
                <div class="text-muted" style="font-size:.78rem">This Month</div>
                <a href="#notifTable" class="stat-link mt-1">
                    View Details <i class="fas fa-arrow-right" style="font-size:.65rem"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Table Card ───────────────────────────────────────────── --}}
<div class="dash-card" id="notifTable">

    {{-- Toolbar --}}
    <div class="dash-card-header flex-wrap gap-2">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">

            {{-- Filter by Type --}}
            <label class="small text-muted mb-0 fw-semibold">Filter by Type</label>
            <select name="recipient_group"
                    class="form-select form-select-sm rounded-2"
                    style="width:130px">
                <option value="">All Types</option>
                @foreach(['all'=>'All Users','parents'=>'Parents','staff'=>'Staff','specific'=>'Specific'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('recipient_group')==$v?'selected':'' }}>
                        {{ $l }}
                    </option>
                @endforeach
            </select>

            {{-- Status --}}
            <label class="small text-muted mb-0 fw-semibold">Status</label>
            <select name="read_status"
                    class="form-select form-select-sm rounded-2"
                    style="width:130px">
                <option value="">All Status</option>
                <option value="read"   {{ request('read_status')=='read'  ?'selected':'' }}>Read</option>
                <option value="unread" {{ request('read_status')=='unread'?'selected':'' }}>Unread</option>
            </select>

            {{-- Date Range --}}
            <label class="small text-muted mb-0 fw-semibold">Date Range</label>
            <div class="input-group" style="width:230px">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-calendar-alt text-muted small"></i>
                </span>
                <input type="date" name="date_from"
                       value="{{ request('date_from', now()->startOfMonth()->toDateString()) }}"
                       class="form-control form-control-sm border-start-0 border-end-0"
                       style="max-width:105px">
                <span class="input-group-text bg-white border-start-0 border-end-0 px-1 small text-muted">-</span>
                <input type="date" name="date_to"
                       value="{{ request('date_to', now()->toDateString()) }}"
                       class="form-control form-control-sm border-start-0"
                       style="max-width:105px">
            </div>

            {{-- Search --}}
            <div class="d-flex align-items-center gap-2 ms-auto">
                <div class="input-group" style="max-width:220px">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search notifications...">
                    <button class="btn btn-success btn-sm"
                            style="background:#2e7d32;border-color:#2e7d32">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                {{-- Filters button --}}
                <button type="submit"
                        class="btn btn-sm btn-outline-secondary rounded-2">
                    <i class="fas fa-filter me-1"></i>Filters
                </button>
            </div>
        </form>
    </div>

    {{-- ── Table ──────────────────────────────────────────────── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr style="background:#f8f9fa">
                    <th class="px-3 py-2 small fw-bold" style="color:#2e7d32;width:200px">Title</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Message</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Recipients</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Sent By</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Date Sent</th>
                    <th class="py-2 small fw-bold" style="color:#2e7d32">Status</th>
                    <th class="py-2 small fw-bold text-end pe-3" style="color:#2e7d32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $n)
                @php
                    // Check if notification has been read by anyone
                    $readCount = \DB::table('notification_reads')
                        ->where('notification_id', $n->id)
                        ->whereNotNull('read_at')->count();
                    $isRead = $readCount > 0;

                    // Icon based on title keywords
                    $title = strtolower($n->title ?? '');
                    if (str_contains($title,'enrollment') || str_contains($title,'enroll')) {
                        $icon = 'fa-user-plus'; $ibg = '#e3f2fd'; $ic = '#1565c0';
                    } elseif (str_contains($title,'attendance') || str_contains($title,'absent')) {
                        $icon = 'fa-calendar-check'; $ibg = '#fff3e0'; $ic = '#e65100';
                    } elseif (str_contains($title,'meeting') || str_contains($title,'pta')) {
                        $icon = 'fa-users'; $ibg = '#f3e5f5'; $ic = '#6a1b9a';
                    } elseif (str_contains($title,'payment') || str_contains($title,'fee')) {
                        $icon = 'fa-money-bill'; $ibg = '#fff8e1'; $ic = '#f57f17';
                    } elseif (str_contains($title,'event') || str_contains($title,'sports')) {
                        $icon = 'fa-star'; $ibg = '#e8f5e9'; $ic = '#2e7d32';
                    } else {
                        $icon = 'fa-bell'; $ibg = '#e8f5e9'; $ic = '#2e7d32';
                    }

                    // Recipients label
                    $recipientLabels = [
                        'all'      => 'All Users',
                        'parents'  => 'All Parents',
                        'staff'    => 'All Staff',
                        'specific' => 'Specific User',
                    ];
                    $recipLabel = $recipientLabels[$n->recipient_group] ?? ucfirst($n->recipient_group);
                @endphp
                <tr>
                    {{-- Title with icon --}}
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:10px;
                                        background:{{ $ibg }};color:{{ $ic }};
                                        display:flex;align-items:center;justify-content:center;
                                        flex-shrink:0">
                                <i class="fas {{ $icon }}" style="font-size:.9rem"></i>
                            </div>
                            <div class="fw-semibold small">{{ $n->title }}</div>
                        </div>
                    </td>

                    {{-- Message --}}
                    <td style="max-width:260px">
                        <div class="small text-muted"
                             style="overflow:hidden;display:-webkit-box;
                                    -webkit-line-clamp:2;-webkit-box-orient:vertical">
                            {{ $n->message }}
                        </div>
                    </td>

                    {{-- Recipients --}}
                    <td>
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:#e8f5e9;color:#2e7d32;font-size:.75rem">
                            {{ $recipLabel }}
                        </span>
                    </td>

                    {{-- Sent By --}}
                    <td class="small">
                        {{ $n->sender->name ?? 'Admin' }}
                    </td>

                    {{-- Date Sent --}}
                    <td class="small text-muted">
                        @if($n->sent_at)
                            {{ $n->sent_at->format('M d, Y') }}<br>
                            <span style="font-size:.72rem">{{ $n->sent_at->format('h:i A') }}</span>
                        @else
                            {{ $n->created_at->format('M d, Y') }}<br>
                            <span style="font-size:.72rem">{{ $n->created_at->format('h:i A') }}</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($isRead)
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:#e8f5e9;color:#2e7d32;font-size:.78rem">
                            Read
                        </span>
                        @else
                        <span class="badge rounded-2 px-3 py-2 fw-semibold"
                              style="background:#fff3e0;color:#e65100;font-size:.78rem">
                            Unread
                        </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            {{-- View (show full message in modal) --}}
                            <button class="btn btn-sm rounded-2 p-1 px-2"
                                    style="background:#e3f2fd;color:#1565c0;border:none"
                                    title="View"
                                    onclick="viewNotif('{{ addslashes($n->title) }}','{{ addslashes($n->message) }}','{{ $recipLabel }}','{{ $n->sender->name ?? 'Admin' }}')">
                                <i class="fas fa-eye" style="font-size:.8rem"></i>
                            </button>
                            {{-- More options dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-sm rounded-2 p-1 px-2 dropdown-toggle"
                                        style="background:#f5f5f5;color:#757575;border:none"
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v" style="font-size:.8rem"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-1">
                                    <li>
                                        <form method="POST"
                                              action="{{ route('admin.notifications.destroy',$n) }}"
                                              onsubmit="return confirm('Delete this notification?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="dropdown-item rounded-2 small text-danger">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="color:#bdbdbd">
                            <i class="fas fa-bell-slash fa-3x mb-3 d-block"></i>
                            <div class="fw-semibold">No notifications yet.</div>
                            <div class="small text-muted mt-2">
                                <a href="{{ route('admin.notifications.create') }}"
                                   style="color:#2e7d32">Send the first notification</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────── --}}
    @if($notifications->hasPages())
    <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="small text-muted">
            Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }}
            of {{ $notifications->total() }} entries
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">
                @if($notifications->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#bdbdbd">«</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $notifications->previousPageUrl() }}"
                       style="background:#f5f5f5;color:#2e7d32">«</a>
                </li>
                @endif

                @foreach($notifications->getUrlRange(1, min($notifications->lastPage(), 8)) as $page => $url)
                <li class="page-item">
                    <a class="page-link rounded-2 border-0" href="{{ $url }}"
                       style="{{ $page==$notifications->currentPage()
                           ? 'background:#2e7d32;color:#fff'
                           : 'background:#f5f5f5;color:#212121' }}">
                        {{ $page }}
                    </a>
                </li>
                @endforeach

                @if($notifications->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-2 border-0"
                       href="{{ $notifications->nextPageUrl() }}"
                       style="background:#f5f5f5;color:#2e7d32">»</a>
                </li>
                @else
                <li class="page-item disabled">
                    <span class="page-link rounded-2 border-0"
                          style="background:#f5f5f5;color:#bdbdbd">»</span>
                </li>
                @endif
            </ul>
        </nav>
    </div>
    @else
    <div class="px-3 py-3 border-top small text-muted">
        Showing {{ $notifications->count() }} entries
    </div>
    @endif
</div>

{{-- ── View Notification Modal ─────────────────────────────── --}}
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="modalTitle">Notification</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="small text-muted fw-semibold mb-1">Message</div>
                    <p id="modalMessage" class="mb-0"></p>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="small text-muted fw-semibold mb-1">Recipients</div>
                        <span id="modalRecipients"
                              class="badge rounded-2 px-3 py-2"
                              style="background:#e8f5e9;color:#2e7d32;font-size:.82rem"></span>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted fw-semibold mb-1">Sent By</div>
                        <span id="modalSentBy" class="small fw-semibold"></span>
                    </div>
                </div>
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
function viewNotif(title, message, recipients, sentBy) {
    document.getElementById('modalTitle').textContent     = title;
    document.getElementById('modalMessage').textContent   = message;
    document.getElementById('modalRecipients').textContent= recipients;
    document.getElementById('modalSentBy').textContent    = sentBy;
    new bootstrap.Modal(document.getElementById('viewModal')).show();
}
</script>
@endpush
