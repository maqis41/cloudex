@extends('layouts.app')

@section('title', 'My Files')

@section('header-buttons')
    <a href="{{ route('files.create') }}" class="btn btn-primary">
        <i class="fas fa-upload"></i> Upload New File
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">My Files</h5>
    </div>
    <div class="card-body">
        @if($files->count() > 0)
            <!-- Documents Section -->
            @if($documents->count() > 0)
                <div class="mb-4">
                    <h5 class="text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-file-alt"></i> Documents
                        <span class="badge bg-secondary">{{ $documents->count() }}</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Size</th>
                                    <th>Tags</th>
                                    <th>Status</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $file)
                                <tr>
                                    <td>
                                        <strong>{{ $file->title }}</strong>
                                        @if($file->description)
                                            <br><small class="text-muted">{{ Str::limit($file->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                                    <td>
                                        @if($file->tags)
                                            @foreach(array_slice($file->tags, 0, 2) as $tag)
                                                <span class="badge bg-light text-dark tag-badge">#{{ $tag }}</span>
                                            @endforeach
                                            @if(count($file->tags) > 2)
                                                <span class="badge bg-light text-dark">+{{ count($file->tags) - 2 }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($file->approved)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $file->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('files.show', $file->id) }}" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('files.edit', $file->id) }}" class="btn btn-warning {{ $file->edit_approved ? '' : 'disabled' }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger {{ $file->delete_approved ? '' : 'disabled' }}" title="Delete" 
                                                        onclick="return confirm('Are you sure you want to delete this file?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Images Section -->
            @if($images->count() > 0)
                <div class="mb-4">
                    <h5 class="text-info border-bottom pb-2 mb-3">
                        <i class="fas fa-image"></i> Images
                        <span class="badge bg-info">{{ $images->count() }}</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Size</th>
                                    <th>Tags</th>
                                    <th>Status</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($images as $file)
                                <tr>
                                    <td>
                                        <strong>{{ $file->title }}</strong>
                                        @if($file->description)
                                            <br><small class="text-muted">{{ Str::limit($file->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                                    <td>
                                        @if($file->tags)
                                            @foreach(array_slice($file->tags, 0, 2) as $tag)
                                                <span class="badge bg-light text-dark tag-badge">#{{ $tag }}</span>
                                            @endforeach
                                            @if(count($file->tags) > 2)
                                                <span class="badge bg-light text-dark">+{{ count($file->tags) - 2 }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($file->approved)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $file->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('files.show', $file->id) }}" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('files.edit', $file->id) }}" class="btn btn-warning {{ $file->edit_approved ? '' : 'disabled' }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger {{ $file->delete_approved ? '' : 'disabled' }}" title="Delete" 
                                                        onclick="return confirm('Are you sure you want to delete this file?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $files->firstItem() }} to {{ $files->lastItem() }} of {{ $files->total() }} files
                </div>
                <div>
                    {{ $files->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                <p class="text-muted">No files uploaded yet.</p>
                <a href="{{ route('files.create') }}" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Your First File
                </a>
            </div>
        @endif
    </div>
</div>
@endsection