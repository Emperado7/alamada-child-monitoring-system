@extends('layouts.app')
@section('title','Add Child')
@section('page-title','Add Child Record')

@section('content')
<div class="d-flex mb-3">
    <a href="{{ route('admin.children.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card">
    <div class="card-header">Child Information Form</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.children.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.children._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Record</button>
                <a href="{{ route('admin.children.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
