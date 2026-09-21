@extends('layouts.app')
@section('title','Notifications')
@section('page-title','Notifications')

@section('content')
<div class="card">
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @forelse($notifications as $n)
            <div class="list-group-item list-group-item-action {{ !$n->is_read ? 'list-group-item-primary' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="fw-{{ $n->is_read ? 'normal' : 'bold' }} small">{{ $n->title }}</div>
                        <div class="text-muted small mt-1">{{ $n->message }}</div>
                        <div class="text-muted mt-1" style="font-size:.72rem">
                            <i class="fas fa-user me-1"></i>{{ $n->sender->name ?? 'System' }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-clock me-1"></i>{{ $n->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    <div class="ms-3 d-flex gap-2 align-items-center">
                        @if(!$n->is_read)
                        <span class="badge bg-primary">New</span>
                        <form method="POST" action="{{ route('staff.notifications.read',$n) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check me-1"></i>Mark Read
                            </button>
                        </form>
                        @else
                        <span class="text-muted small"><i class="fas fa-check-double"></i></span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="list-group-item text-center text-muted py-5">
                <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                No notifications yet.
            </div>
            @endforelse
        </div>
    </div>
    @if($notifications->hasPages())
    <div class="card-footer">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
