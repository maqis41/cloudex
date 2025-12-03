@extends('layouts.app')

@section('title', 'All Files')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">All Files</h5>
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
                                    <th>Uploader</th>
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
                                    <td>{{ $file->user->name }}</td>
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
                                            @if($file->approved || $file->user_id == auth()->id())
                                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @else
                                            <button class="btn btn-success disabled" title="Download unavailable - file not approved">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            @endif
                                            
                                            {{-- [TOMBOL APPROVAL HANYA UNTUK ADMIN/COADMIN] --}}
                                            @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
                                                @if(!$file->approved)
                                                    <a href="{{ route('files.approve', $file->id) }}" class="btn btn-outline-success" title="Approve File">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @endif
                                                @if(!$file->edit_approved)
                                                    <a href="{{ route('files.approve-edit', $file->id) }}" class="btn btn-outline-warning" title="Approve Edit">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @endif
                                                @if(!$file->delete_approved)
                                                    <a href="{{ route('files.approve-delete', $file->id) }}" class="btn btn-outline-danger" title="Approve Delete">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            
                                            {{-- TOMBOL FORCE DELETE HANYA UNTUK ADMIN/CO-ADMIN --}}
                                            @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
                                            <button type="button" class="btn btn-danger" 
                                                    title="Force Delete (No Approval Needed)" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#forceDeleteModal{{ $file->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @endif
                                        </div>

                                        {{-- MODAL FORCE DELETE HANYA UNTUK ADMIN/CO-ADMIN --}}
                                        @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
                                        <div class="modal fade" id="forceDeleteModal{{ $file->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-danger">
                                                            <i class="fas fa-exclamation-triangle"></i> Confirm Force Delete
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <strong>Warning!</strong> This action cannot be undone.
                                                        </div>
                                                        <p>Are you sure you want to permanently delete this file?</p>
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                <strong>File:</strong> {{ $file->title }}<br>
                                                                <strong>Uploader:</strong> {{ $file->user->name }}<br>
                                                                <strong>Type:</strong> {{ ucfirst($file->file_type) }}<br>
                                                                <strong>Size:</strong> {{ $file->getFormattedSize() }}<br>
                                                                <strong>Uploaded:</strong> {{ $file->created_at->format('M d, Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('files.force-delete', $file->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash-alt"></i> Yes, Delete Permanently
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
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
                                    <th>Uploader</th>
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
                                    <td>{{ $file->user->name }}</td>
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
                                            @if($file->approved || $file->user_id == auth()->id())
                                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @else
                                            <button class="btn btn-success disabled" title="Download unavailable - file not approved">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            @endif

                                            {{-- [TOMBOL APPROVAL HANYA UNTUK ADMIN/COADMIN] --}}
                                            @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
                                                @if(!$file->approved)
                                                    <a href="{{ route('files.approve', $file->id) }}" class="btn btn-outline-success" title="Approve File">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @endif
                                                @if(!$file->edit_approved)
                                                    <a href="{{ route('files.approve-edit', $file->id) }}" class="btn btn-outline-warning" title="Approve Edit">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @endif
                                                @if(!$file->delete_approved)
                                                    <a href="{{ route('files.approve-delete', $file->id) }}" class="btn btn-outline-danger" title="Approve Delete">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            
                                            {{-- TOMBOL FORCE DELETE HANYA UNTUK ADMIN/CO-ADMIN --}}
                                            @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
                                            <button type="button" class="btn btn-danger" 
                                                    title="Force Delete (No Approval Needed)" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#forceDeleteImageModal{{ $file->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @endif
                                        </div>

                                        {{-- MODAL FORCE DELETE HANYA UNTUK ADMIN/CO-ADMIN --}}
                                        @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
                                        <div class="modal fade" id="forceDeleteImageModal{{ $file->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-danger">
                                                            <i class="fas fa-exclamation-triangle"></i> Confirm Force Delete
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <strong>Warning!</strong> This action cannot be undone.
                                                        </div>
                                                        <p>Are you sure you want to permanently delete this image?</p>
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                <strong>Image:</strong> {{ $file->title }}<br>
                                                                <strong>Uploader:</strong> {{ $file->user->name }}<br>
                                                                <strong>Type:</strong> {{ ucfirst($file->file_type) }}<br>
                                                                <strong>Size:</strong> {{ $file->getFormattedSize() }}<br>
                                                                <strong>Uploaded:</strong> {{ $file->created_at->format('M d, Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('files.force-delete', $file->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash-alt"></i> Yes, Delete Permanently
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
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
                    @if(auth()->user()->isUser())
                        <br><small class="text-info"><i class="fas fa-info-circle"></i> Only approved files and your own files are visible</small>
                    @endif
                </div>
                <div>
                    {{ $files->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No files uploaded yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection