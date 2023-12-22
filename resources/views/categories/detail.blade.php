@extends('layouts.app')
@section('categories.detail')
@section('module', 'Detail Category')
<style>
.btn-list {
    visibility: hidden;
}
</style>
<div class="container-xl">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>{{ $category->name }}</h2>
                </div>
                <div class="card-body">
                    @if($category->status == 0)
                    N/A
                    @elseif($category->status == 1)
                    <span class="right badge bg-success">Public</span>
                    @else
                    <span class="right badge badge-danger">Private</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Category Details</h3>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <!-- Edit button -->
                    <a href="{{ route('categories.edit', ['id' => $category->id]) }}" class="btn btn-primary">Edit</a>
                    <!-- Back button -->
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection