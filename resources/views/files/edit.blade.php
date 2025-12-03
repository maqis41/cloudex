@extends('layouts.app')

@section('title', 'Edit File: ' . $file->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit File: {{ $file->title }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('files.update', $file->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="title" class="form-label">File Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $file->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $file->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                               id="tags" name="tags" value="{{ old('tags', $file->tags ? implode(', ', $file->tags) : '') }}">
                        @error('tags')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Separate tags with commas (e.g., document, report, 2024).</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        @if(auth()->user()->isUser())
                            After editing, the file will require approval again before further edits can be made.
                        @else
                            As an administrator, your changes will be applied immediately.
                        @endif
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('files.show', $file->id) }}" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update File</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Current File Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Current File Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>File Name:</strong> {{ $file->original_name }}<br>
                        <strong>File Type:</strong> {{ ucfirst($file->file_type) }}<br>
                        <strong>File Size:</strong> {{ $file->getFormattedSize() }}
                    </div>
                    <div class="col-md-6">
                        <strong>Uploaded:</strong> {{ $file->created_at->format('M d, Y') }}<br>
                        <strong>Last Updated:</strong> {{ $file->updated_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection