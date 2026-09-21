@extends('layouts.app')
@section('title','Edit Activity')
@section('page-title','Edit Activity')

@section('content')
<div class="d-flex mb-3">
    <a href="{{ route('staff.activities.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="card" style="max-width:640px">
    <div class="card-header">Edit Activity</div>
    <div class="card-body">
        <form method="POST" action="{{ route('staff.activities.update',$activity) }}">
            @csrf @method('PUT')
            @include('staff.activities._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                <a href="{{ route('staff.activities.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
