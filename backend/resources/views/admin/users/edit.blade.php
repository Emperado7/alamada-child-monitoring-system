@extends('layouts.app')
@section('title','Edit User')
@section('page-title','Edit User')

@section('content')
<div class="d-flex mb-3"><a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a></div>

<div class="card" style="max-width:600px">
    <div class="card-header">Edit: {{ $user->name }}</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update',$user) }}">
            @csrf @method('PUT')
            @include('admin.users._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
