@extends('layouts.app')

@section('title', $file->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">File Details: {{ $file->title }}</h5>
            </div>
            <div class="card-body">
                {{-- Tab Navigation --}}
                <ul class="nav nav-tabs mb-3" id="fileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                            <i class="fas fa-info-circle"></i> Details
                        </button>
                    </li>
                    
                    @if($file->file_type === 'image' || $file->mime_type === 'application/pdf' || in_array($file->mime_type, ['text/plain', 'text/html', 'text/css', 'text/javascript', 'application/json', 'application/xml', 'text/xml', 'application/rtf']))
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview" type="button">
                            @if($file->file_type === 'image')
                                <i class="fas fa-image"></i> Image Preview
                            @elseif($file->mime_type === 'application/pdf')
                                <i class="fas fa-file-pdf"></i> PDF Preview
                            @else
                                <i class="fas fa-file-alt"></i> Text Preview
                            @endif
                        </button>
                    </li>
                    @endif
                    
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="discussion-tab" data-bs-toggle="tab" data-bs-target="#discussion" type="button">
                            <i class="fas fa-comments"></i> Discussion
                            @if($file->discussions->count() > 0)
                            <span class="badge bg-secondary">{{ $file->discussions->count() }}</span>
                            @endif
                        </button>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content" id="fileTabContent">
                    {{-- Details Tab --}}
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Title:</div>
                            <div class="col-md-8">{{ $file->title }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Description:</div>
                            <div class="col-md-8">{{ $file->description ?? 'No description' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">File Type:</div>
                            <div class="col-md-8">
                                <span class="badge bg-{{ $file->file_type === 'image' ? 'info' : 'secondary' }}">
                                    {{ ucfirst($file->file_type) }}
                                </span>
                                <small class="text-muted ms-2">({{ $file->mime_type }})</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">File Size:</div>
                            <div class="col-md-8">{{ $file->getFormattedSize() }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Uploaded By:</div>
                            <div class="col-md-8">{{ $file->user->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Upload Date:</div>
                            <div class="col-md-8">{{ $file->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Last Updated:</div>
                            <div class="col-md-8">{{ $file->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Tags:</div>
                            <div class="col-md-8">
                                @if($file->tags)
                                    @foreach($file->tags as $tag)
                                        <span class="badge bg-light text-dark tag-badge">#{{ $tag }}</span>
                                    @endforeach
                                @else
                                    No tags
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Status:</div>
                            <div class="col-md-8">
                                @if($file->approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending Approval</span>
                                @endif
                                
                                @if(auth()->user()->isUser())
                                    <br>
                                    <small class="text-muted">
                                        Edit Approved: 
                                        @if($file->edit_approved)
                                            <span class="text-success">Yes</span>
                                        @else
                                            <span class="text-warning">Pending</span>
                                        @endif
                                        | 
                                        Delete Approved: 
                                        @if($file->delete_approved)
                                            <span class="text-success">Yes</span>
                                        @else
                                            <span class="text-warning">Pending</span>
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mb-4">
                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-success">
                                <i class="fas fa-download"></i> Download File
                            </a>
                            
                            @if(auth()->user()->isUser())
                                <a href="{{ route('files.edit', $file->id) }}" class="btn btn-warning {{ $file->edit_approved ? '' : 'disabled' }}">
                                    <i class="fas fa-edit"></i> Edit File
                                </a>
                                <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger {{ $file->delete_approved ? '' : 'disabled' }}" 
                                            onclick="return confirm('Are you sure you want to delete this file?')">
                                        <i class="fas fa-trash"></i> Delete File
                                    </button>
                                </form>
                            @endif
                            
                            @if((auth()->user()->isAdmin() || auth()->user()->isCoadmin()) && !$file->approved)
                                <a href="{{ route('files.approve', $file->id) }}" class="btn btn-outline-success">
                                    <i class="fas fa-check"></i> Approve File
                                </a>
                            @endif
                        </div>

                        {{-- Print Options --}}
                        <div class="mt-4 pt-3 border-top">
                            <h6><i class="fas fa-print"></i> Print & Export Options</h6>
                            <div class="btn-group" role="group">
                                <a href="{{ route('files.print', $file->id) }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-print"></i> Print Details
                                </a>
                                <a href="{{ route('files.pdf', $file->id) }}" class="btn btn-outline-danger">
                                    <i class="fas fa-file-pdf"></i> Export as PDF
                                </a>
                                <a href="{{ route('files.print', ['id' => $file->id, 'autoprint' => true]) }}" target="_blank" class="btn btn-outline-success">
                                    <i class="fas fa-print"></i> Auto Print
                                </a>
                            </div>
                            <p class="text-muted mt-2 small">
                                <i class="fas fa-info-circle"></i> Print options include file details and all discussions.
                            </p>
                        </div>
                    </div>

                    {{-- Preview Tab --}}
                    @if($file->file_type === 'image' || $file->mime_type === 'application/pdf' || in_array($file->mime_type, ['text/plain', 'text/html', 'text/css', 'text/javascript', 'application/json', 'application/xml', 'text/xml', 'application/rtf']))
                    <div class="tab-pane fade" id="preview" role="tabpanel">
                        @if($file->file_type === 'image')
                            {{-- Image Preview --}}
                            <div class="mt-2">
                                <h6>Image Preview:</h6>
                                <div class="image-preview-container">
                                    <div class="preview-wrapper">
                                        <img id="preview-image" 
                                             src="{{ route('files.preview', $file->id) }}" 
                                             data-original-src="{{ $file->getFileUrl() }}"
                                             onerror="this.onerror=null; this.src='{{ $file->getFileUrl() }}';"
                                             alt="{{ $file->title }}" 
                                             class="preview-image">
                                    </div>
                                    <div class="preview-controls mt-2">
                                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomIn()">
                                            <i class="fas fa-search-plus"></i> Zoom In
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomOut()">
                                            <i class="fas fa-search-minus"></i> Zoom Out
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="resetZoom()">
                                            <i class="fas fa-sync"></i> Reset
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="openFullScreenImage()">
                                            <i class="fas fa-expand"></i> Full Screen
                                        </button>
                                        <span class="ms-2 text-muted" id="zoom-level">100%</span>
                                    </div>
                                </div>
                                <div class="mt-3 text-muted small">
                                    <i class="fas fa-info-circle"></i> 
                                    Preview of {{ $file->original_name }} ({{ $file->getFormattedSize() }})
                                </div>
                            </div>
                        @elseif($file->mime_type === 'application/pdf')
                            {{-- PDF Preview --}}
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">PDF Preview:</h6>
                                    <div>
                                        <a href="{{ route('files.preview.pdf', $file->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-expand"></i> Open in New Tab
                                        </a>
                                        <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                                <div class="pdf-preview-container border rounded">
                                    <div style="height: 600px;">
                                        <iframe src="{{ route('files.preview.pdf', $file->id) }}" 
                                                width="100%" 
                                                height="100%" 
                                                style="border: none;">
                                            Your browser does not support PDF preview. 
                                            <a href="{{ route('files.download', $file->id) }}">Download the PDF</a> instead.
                                        </iframe>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small">
                                    <i class="fas fa-info-circle"></i> 
                                    Preview of {{ $file->original_name }} ({{ $file->getFormattedSize() }})
                                </div>
                            </div>
                        @elseif(in_array($file->mime_type, ['text/plain', 'text/html', 'text/css', 'text/javascript', 'application/json', 'application/xml', 'text/xml', 'application/rtf']))
                            {{-- Text Preview --}}
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Text Preview:</h6>
                                    <div>
                                        <a href="{{ route('files.preview.text', $file->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-expand"></i> Full Screen
                                        </a>
                                        <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                                <div class="text-preview-container border rounded" style="height: 500px; overflow: auto;">
                                    <iframe src="{{ route('files.preview.text', $file->id) }}" 
                                            width="100%" 
                                            height="100%" 
                                            style="border: none;">
                                        Your browser does not support iframes. 
                                        <a href="{{ route('files.preview.text', $file->id) }}" target="_blank">Open text preview</a>.
                                    </iframe>
                                </div>
                                <div class="mt-2 text-muted small">
                                    <i class="fas fa-info-circle"></i> 
                                    Preview of {{ $file->original_name }} ({{ $file->getFormattedSize() }})
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- Discussion Tab --}}
                    <div class="tab-pane fade" id="discussion" role="tabpanel">
                        @if($file->discussions->count() > 0)
                            <div class="discussion-list">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Discussion ({{ $file->discussions->count() }} comments)</h6>
                                    <div class="btn-group btn-group-sm">
                                        <button onclick="printDiscussionOnly()" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-print"></i> Print Discussion
                                        </button>
                                        <a href="{{ route('files.print', ['id' => $file->id, '#discussion']) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-external-link-alt"></i> Open for Print
                                        </a>
                                    </div>
                                </div>
                                @foreach($file->discussions as $discussion)
                                    <div class="discussion-item mb-3 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $discussion->user->name }}</strong>
                                            <small class="text-muted">{{ $discussion->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ $discussion->comment }}</p>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin() || auth()->user()->id === $discussion->user_id)
                                            <form action="{{ route('discussions.destroy', $discussion->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this comment?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No discussions yet. Start the conversation!</p>
                        @endif
                        
                        <!-- Add Comment Form -->
                        <form method="POST" action="{{ route('discussions.store', $file->id) }}" class="mt-3">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="form-label">Add Comment</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror" 
                                          id="comment" name="comment" rows="3" required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Approval Status for Admin/Coadmin -->
        @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Approval Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(!$file->approved)
                        <a href="{{ route('files.approve', $file->id) }}" class="btn btn-success">
                            <i class="fas fa-check"></i> Approve File
                        </a>
                    @endif
                    
                    @if(!$file->edit_approved)
                        <a href="{{ route('files.approve-edit', $file->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Approve Edit Request
                        </a>
                    @endif
                    
                    @if(!$file->delete_approved)
                        <a href="{{ route('files.approve-delete', $file->id) }}" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Approve Delete Request
                        </a>
                    @endif
                    
                    {{-- Force Delete Button --}}
                    <button type="button" class="btn btn-outline-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#forceDeleteModal">
                        <i class="fas fa-skull-crossbones"></i> Force Delete (No Approval)
                    </button>

                    @if($file->approved && $file->edit_approved && $file->delete_approved)
                        <p class="text-muted text-center">All actions approved</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Force Delete Modal --}}
        <div class="modal fade" id="forceDeleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle"></i> Force Delete File
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-radiation"></i> Administrative Action Required</h6>
                            <p class="mb-0">This action bypasses all approval requirements and deletes the file immediately.</p>
                        </div>
                        
                        <p><strong>File Details:</strong></p>
                        <ul>
                            <li><strong>Title:</strong> {{ $file->title }}</li>
                            <li><strong>Uploader:</strong> {{ $file->user->name }}</li>
                            <li><strong>Type:</strong> {{ ucfirst($file->file_type) }}</li>
                            <li><strong>Size:</strong> {{ $file->getFormattedSize() }}</li>
                            <li><strong>Upload Date:</strong> {{ $file->created_at->format('M d, Y H:i') }}</li>
                            <li><strong>Status:</strong> 
                                @if($file->approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </li>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            This will also delete all associated discussions ({{ $file->discussions->count() }} comments).
                        </div>
                        
                        <p class="text-danger fw-bold">
                            <i class="fas fa-exclamation-circle"></i>
                            This action is irreversible. The file will be permanently deleted from the system.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('files.force-delete', $file->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-skull-crossbones"></i> Confirm Force Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- File Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">File Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Original Name:</strong><br>
                    <span class="text-muted">{{ $file->original_name }}</span>
                </div>
                <div class="mb-2">
                    <strong>MIME Type:</strong><br>
                    <span class="text-muted">{{ $file->mime_type }}</span>
                </div>
                <div class="mb-2">
                    <strong>Storage Path:</strong><br>
                    <span class="text-muted">{{ $file->file_path }}</span>
                </div>
                <div class="mb-2">
                    <strong>Preview Available:</strong><br>
                    <span class="text-muted">
                        @if($file->file_type === 'image' || $file->mime_type === 'application/pdf' || in_array($file->mime_type, ['text/plain', 'text/html', 'text/css', 'text/javascript', 'application/json', 'application/xml', 'text/xml', 'application/rtf']))
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span> (download only)
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk preview gambar full --}}
@if($file->file_type === 'image')
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $file->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-preview-container">
                    <img id="modal-image" 
                         src="{{ route('files.preview', $file->id) }}"
                         data-original-src="{{ $file->getFileUrl() }}"
                         onerror="this.onerror=null; this.src='{{ $file->getFileUrl() }}';"
                         alt="{{ $file->title }}" 
                         class="modal-preview-image">
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <button class="btn btn-sm btn-outline-secondary" onclick="modalZoomIn()">
                        <i class="fas fa-search-plus"></i> Zoom In
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="modalZoomOut()">
                        <i class="fas fa-search-minus"></i> Zoom Out
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="modalResetZoom()">
                        <i class="fas fa-sync"></i> Reset
                    </button>
                    <span class="ms-2 text-muted" id="modal-zoom-level">100%</span>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
// Konfigurasi zoom
const MIN_ZOOM = 0.5;
const MAX_ZOOM = 3;
const ZOOM_STEP = 0.1;

let scale = 1;
let modalScale = 1;

// Preview image zoom functions
function zoomIn() {
    if (scale < MAX_ZOOM) {
        scale += ZOOM_STEP;
        scale = Math.min(scale, MAX_ZOOM);
        updateZoom();
    }
}

function zoomOut() {
    if (scale > MIN_ZOOM) {
        scale -= ZOOM_STEP;
        scale = Math.max(scale, MIN_ZOOM);
        updateZoom();
    }
}

function resetZoom() {
    scale = 1;
    updateZoom();
}

function updateZoom() {
    const image = document.getElementById('preview-image');
    if (image) {
        image.style.transform = `scale(${scale})`;
        document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
        updateZoomButtons();
    }
}

// Modal image zoom functions
function modalZoomIn() {
    if (modalScale < MAX_ZOOM) {
        modalScale += ZOOM_STEP;
        modalScale = Math.min(modalScale, MAX_ZOOM);
        updateModalZoom();
    }
}

function modalZoomOut() {
    if (modalScale > MIN_ZOOM) {
        modalScale -= ZOOM_STEP;
        modalScale = Math.max(modalScale, MIN_ZOOM);
        updateModalZoom();
    }
}

function modalResetZoom() {
    modalScale = 1;
    updateModalZoom();
}

function updateModalZoom() {
    const image = document.getElementById('modal-image');
    if (image) {
        image.style.transform = `scale(${modalScale})`;
        document.getElementById('modal-zoom-level').textContent = Math.round(modalScale * 100) + '%';
        updateModalZoomButtons();
    }
}

// Update status tombol zoom
function updateZoomButtons() {
    const zoomInBtn = document.querySelector('.preview-controls button:nth-child(1)');
    const zoomOutBtn = document.querySelector('.preview-controls button:nth-child(2)');
    
    if (zoomInBtn) {
        if (scale >= MAX_ZOOM) {
            zoomInBtn.classList.add('disabled');
            zoomInBtn.disabled = true;
        } else {
            zoomInBtn.classList.remove('disabled');
            zoomInBtn.disabled = false;
        }
    }
    
    if (zoomOutBtn) {
        if (scale <= MIN_ZOOM) {
            zoomOutBtn.classList.add('disabled');
            zoomOutBtn.disabled = true;
        } else {
            zoomOutBtn.classList.remove('disabled');
            zoomOutBtn.disabled = false;
        }
    }
}

// Update status tombol zoom modal
function updateModalZoomButtons() {
    const modalZoomInBtn = document.querySelector('.modal-footer button:nth-child(1)');
    const modalZoomOutBtn = document.querySelector('.modal-footer button:nth-child(2)');
    
    if (modalZoomInBtn) {
        if (modalScale >= MAX_ZOOM) {
            modalZoomInBtn.classList.add('disabled');
            modalZoomInBtn.disabled = true;
        } else {
            modalZoomInBtn.classList.remove('disabled');
            modalZoomInBtn.disabled = false;
        }
    }
    
    if (modalZoomOutBtn) {
        if (modalScale <= MIN_ZOOM) {
            modalZoomOutBtn.classList.add('disabled');
            modalZoomOutBtn.disabled = true;
        } else {
            modalZoomOutBtn.classList.remove('disabled');
            modalZoomOutBtn.disabled = false;
        }
    }
}

// Open image in modal on click
function openFullScreenImage() {
    const previewImage = document.getElementById('preview-image');
    if (previewImage) {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
        modalResetZoom();
    }
}

// Function to print discussion only
function printDiscussionOnly() {
    const discussionContent = document.getElementById('discussion').innerHTML;
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Discussion - {{ $file->title }}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    padding: 20px; 
                    max-width: 800px;
                    margin: 0 auto;
                }
                h1 { 
                    color: #333; 
                    border-bottom: 2px solid #007bff; 
                    padding-bottom: 10px;
                }
                .file-info {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .discussion-item { 
                    margin-bottom: 20px; 
                    padding-bottom: 15px; 
                    border-bottom: 1px solid #eee; 
                }
                .discussion-header { 
                    display: flex; 
                    justify-content: space-between; 
                    margin-bottom: 8px; 
                }
                .discussion-user { 
                    font-weight: bold;
                    color: #333;
                }
                .discussion-date { 
                    color: #666; 
                    font-size: 0.9em;
                }
                .discussion-content { 
                    margin-left: 20px; 
                    white-space: pre-wrap;
                    line-height: 1.6;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    color: #666;
                    font-size: 0.9em;
                    border-top: 1px solid #ddd;
                    padding-top: 15px;
                }
                @media print {
                    .no-print { display: none; }
                    body { font-size: 12pt; }
                    .discussion-item { page-break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            <h1>Discussion: {{ $file->title }}</h1>
            
            <div class="file-info">
                <p><strong>File:</strong> {{ $file->title }}</p>
                <p><strong>Uploaded by:</strong> {{ $file->user->name }}</p>
                <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
            </div>
            
            <hr>
            
            <h3>Comments ({{ $file->discussions->count() }})</h3>
            
            ${discussionContent}
            
            <div class="footer">
                <p>Generated from Cloudex File Management System</p>
                <p>File ID: {{ $file->id }}</p>
            </div>
            
            <div class="no-print" style="margin-top: 20px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Print This Page
                </button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                    Close Window
                </button>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
}

// Initialize tab functionality
document.addEventListener('DOMContentLoaded', function() {
    // Activate tab from URL hash
    const hash = window.location.hash;
    if (hash) {
        const tabTrigger = document.querySelector(`[data-bs-target="${hash}"]`);
        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        }
    }
    
    // Initialize zoom buttons
    updateZoomButtons();
    updateModalZoomButtons();
    
    // Keyboard shortcuts untuk modal
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('imageModal');
        if (modal && modal.classList.contains('show')) {
            switch(e.key) {
                case '+':
                case '=':
                    e.preventDefault();
                    modalZoomIn();
                    break;
                case '-':
                    e.preventDefault();
                    modalZoomOut();
                    break;
                case '0':
                    e.preventDefault();
                    modalResetZoom();
                    break;
                case 'Escape':
                    bootstrap.Modal.getInstance(modal).hide();
                    break;
            }
        }
    });
});

// Wheel zoom untuk modal
const imageModal = document.getElementById('imageModal');
if (imageModal) {
    imageModal.addEventListener('wheel', function(e) {
        if (e.ctrlKey) {
            e.preventDefault();
            if (e.deltaY < 0) {
                modalZoomIn();
            } else {
                modalZoomOut();
            }
        }
    }, { passive: false });
}
</script>

<style>
/* Image Preview Styles */
.preview-image {
    transition: transform 0.3s ease;
    transform-origin: center center;
    max-width: 100%;
    max-height: 500px;
    display: block;
    margin: 0 auto;
    cursor: pointer;
}

.preview-image:hover {
    opacity: 0.9;
}

.modal-preview-image {
    transition: transform 0.3s ease;
    transform-origin: center center;
    max-width: 100%;
    max-height: 70vh;
    display: block;
    margin: 0 auto;
}

.image-preview-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    background: #f8f9fa;
    overflow: hidden;
}

.preview-wrapper {
    width: 100%;
    height: 500px;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

.modal-preview-container {
    width: 100%;
    height: 70vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background: #f8f9fa;
}

.preview-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 10px;
}

.btn.disabled {
    pointer-events: none;
    opacity: 0.6;
}

/* PDF Preview Styles */
.pdf-preview-container {
    background: #f8f9fa;
    border-radius: 0.375rem;
    overflow: hidden;
}

/* Text Preview Styles */
.text-preview-container {
    background: #f8f9fa;
    border-radius: 0.375rem;
    overflow: hidden;
}

/* Tab Styles */
.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    font-weight: 600;
}

/* Responsive design */
@media (max-width: 768px) {
    .preview-wrapper {
        height: 300px;
    }
    
    .preview-controls {
        justify-content: center;
    }
    
    .pdf-preview-container iframe,
    .text-preview-container iframe {
        height: 400px;
    }
}

/* Discussion Styles */
.discussion-item {
    transition: background-color 0.2s;
}

.discussion-item:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.tag-badge {
    margin-right: 5px;
    margin-bottom: 3px;
}
</style>
@endpush
@endsection