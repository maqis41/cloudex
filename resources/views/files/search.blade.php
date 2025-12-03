@extends('layouts.app')

@section('title', 'Search Results')

@section('header-buttons')
    <a href="{{ route('files.advanced-search') }}" class="btn btn-primary me-2">
        <i class="fas fa-search-plus"></i> Advanced Search
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Search Results for "{{ $query }}"</h5>
    </div>
    <div class="card-body">
        @if($files->count() > 0)
            <p>Found {{ $files->count() }} file(s) matching your search.</p>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            @if(!auth()->user()->isUser())
                                <th>Uploader</th>
                            @endif
                            <th>Type</th>
                            <th>Size</th>
                            <th>Tags</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                        <tr>
                            <td>
                                <strong>{{ $file->title }}</strong>
                                @if($file->description)
                                    <br><small class="text-muted">{{ Str::limit($file->description, 50) }}</small>
                                @endif
                            </td>
                            @if(!auth()->user()->isUser())
                                <td>{{ $file->user->name }}</td>
                            @endif
                            <td>
                                <span class="badge bg-{{ $file->file_type === 'image' ? 'info' : 'secondary' }}">
                                    {{ ucfirst($file->file_type) }}
                                </span>
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
                            <td>{{ $file->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('files.show', $file->id) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-success" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Found {{ $files->total() }} file(s) matching "{{ $query }}"
                    (showing {{ $files->firstItem() }} to {{ $files->lastItem() }})
                </div>
                <div>
                    {{ $files->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>No files found for "{{ $query }}"</h5>
                <p class="text-muted">Try a different search term or use advanced search.</p>
                
                <div class="mt-3">
                    <a href="{{ route('files.advanced-search') }}" class="btn btn-primary me-2">
                        <i class="fas fa-search-plus"></i> Try Advanced Search
                    </a>
                    <a href="{{ route('files.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> View All Files
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection