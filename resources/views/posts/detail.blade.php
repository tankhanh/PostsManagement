@extends('master')
@section('detail')
@section('module', 'Detail Post')
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
                    <h2>{{ $post->title }}</h2>
                </div>
                <div class="card-body">
                    @if($post->image == null)
                    N/A
                    @else
                    <img src="{{ asset('uploads/image/'.$post->image)}}" alt="Image Current" width="200px"
                        height="100px">
                    @endif
                    <p class="text-muted">{!! $post->excerpt !!}</p>
                    <p>{!! $post->content !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Post Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> {{ $post->status == 1 ? 'Public' : 'Private' }}</p>
                    <p><strong>Featured:</strong> {{ $post->is_featured == 1 ? 'Yes' : 'No' }}</p>
                    <p><strong>Posted At:</strong> {{ date('d/m/y', strtotime($post->posted_at))}}</p>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <!-- Edit button -->
                    <a href="{{ route('posts.edit', ['id' => $post->id]) }}" class="btn btn-primary">Edit</a>
                    <!-- Back button -->
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection