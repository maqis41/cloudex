@extends('layouts.app')

@section('title', 'Advanced Search')

@section('header-buttons')
    <a href="{{ route('files.search') }}" class="btn btn-outline-secondary me-2">
        <i class="fas fa-search"></i> Simple Search
    </a>
    <a href="{{ route('files.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-list"></i> My Files
    </a>
@endsection

@section('content')
<div class="row">
    <!-- Search Filters Panel -->
    <div class="col-md-4 col-lg-3 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter"></i> Search Filters
                </h5>
            </div>
            <div class="card-body">
                <form id="advanced-search-form" method="GET" action="{{ route('files.advanced-search') }}">
                    
                    <!-- Keyword Search -->
                    <div class="mb-3">
                        <label for="keyword" class="form-label">Keyword</label>
                        <input type="text" class="form-control" id="keyword" name="keyword" 
                               value="{{ $filters['keyword'] ?? '' }}" 
                               placeholder="Search in title, description, tags...">
                    </div>
                    
                    <!-- File Type Filter -->
                    <div class="mb-3">
                        <label for="file_type" class="form-label">File Type</label>
                        <select class="form-select" id="file_type" name="file_type">
                            <option value="">All Types</option>
                            <option value="document" {{ ($filters['file_type'] ?? '') == 'document' ? 'selected' : '' }}>
                                Document
                            </option>
                            <option value="image" {{ ($filters['file_type'] ?? '') == 'image' ? 'selected' : '' }}>
                                Image
                            </option>
                        </select>
                    </div>
                    
                    <!-- Approval Status Filter -->
                    <div class="mb-3">
                        <label for="approval_status" class="form-label">Approval Status</label>
                        <select class="form-select" id="approval_status" name="approval_status">
                            <option value="">All Status</option>
                            <option value="approved" {{ ($filters['approval_status'] ?? '') == 'approved' ? 'selected' : '' }}>
                                Approved
                            </option>
                            <option value="pending" {{ ($filters['approval_status'] ?? '') == 'pending' ? 'selected' : '' }}>
                                Pending Approval
                            </option>
                            @if(auth()->user()->isUser())
                            <option value="edit_pending" {{ ($filters['approval_status'] ?? '') == 'edit_pending' ? 'selected' : '' }}>
                                Edit Pending
                            </option>
                            <option value="delete_pending" {{ ($filters['approval_status'] ?? '') == 'delete_pending' ? 'selected' : '' }}>
                                Delete Pending
                            </option>
                            @endif
                        </select>
                    </div>
                    
                    <!-- Uploader Filter (Admin/Coadmin only) -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
                    <div class="mb-3">
                        <label for="uploader" class="form-label">Uploader</label>
                        <input type="text" class="form-control" id="uploader" name="uploader" 
                               value="{{ $filters['uploader'] ?? '' }}" 
                               placeholder="Uploader name or email...">
                    </div>
                    @endif
                    
                    <!-- Date Range Filter -->
                    <div class="mb-3">
                        <label class="form-label">Upload Date Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ $filters['date_from'] ?? '' }}" 
                                       max="{{ date('Y-m-d') }}">
                                <small class="form-text text-muted">From</small>
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ $filters['date_to'] ?? '' }}" 
                                       max="{{ date('Y-m-d') }}">
                                <small class="form-text text-muted">To</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Size Filter -->
                    <div class="mb-3">
                        <label class="form-label">File Size (KB)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" class="form-control" id="size_min" name="size_min" 
                                       value="{{ $filters['size_min'] ?? '' }}" 
                                       min="0" placeholder="Min">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" id="size_max" name="size_max" 
                                       value="{{ $filters['size_max'] ?? '' }}" 
                                       min="0" placeholder="Max">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tags Filter -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags (comma separated)</label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               value="{{ $filters['tags'] ?? '' }}" 
                               placeholder="tag1, tag2, tag3">
                        @if($allTags->count() > 0)
                        <div class="mt-2">
                            <small class="text-muted">Popular tags:</small>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @foreach($allTags as $tag)
                                <span class="badge bg-light text-dark tag-suggestion" 
                                      onclick="addTagToFilter('{{ $tag }}')"
                                      style="cursor: pointer;">
                                    #{{ $tag }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- MIME Type Filter -->
                    <div class="mb-3">
                        <label for="mime_type" class="form-label">File Format</label>
                        <input type="text" class="form-control" id="mime_type" name="mime_type" 
                               value="{{ $filters['mime_type'] ?? '' }}" 
                               placeholder="e.g., image/jpeg, application/pdf">
                        @if($allMimeTypes->count() > 0)
                        <div class="mt-2">
                            <small class="text-muted">Available formats:</small>
                            <select class="form-select form-select-sm mt-1" id="mime_type_suggestions">
                                <option value="">Select a format...</option>
                                @foreach($allMimeTypes as $mime)
                                <option value="{{ $mime }}">{{ $mime }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Sort Options -->
                    <div class="mb-3">
                        <label class="form-label">Sort By</label>
                        <div class="row g-2">
                            <div class="col-8">
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ ($filters['sort_by'] ?? 'created_at') == 'created_at' ? 'selected' : '' }}>
                                        Upload Date
                                    </option>
                                    <option value="title" {{ ($filters['sort_by'] ?? '') == 'title' ? 'selected' : '' }}>
                                        Title
                                    </option>
                                    <option value="file_size" {{ ($filters['sort_by'] ?? '') == 'file_size' ? 'selected' : '' }}>
                                        File Size
                                    </option>
                                    <option value="updated_at" {{ ($filters['sort_by'] ?? '') == 'updated_at' ? 'selected' : '' }}>
                                        Last Modified
                                    </option>
                                </select>
                            </div>
                            <div class="col-4">
                                <select class="form-select" id="sort_order" name="sort_order">
                                    <option value="desc" {{ ($filters['sort_order'] ?? 'desc') == 'desc' ? 'selected' : '' }}>
                                        Desc
                                    </option>
                                    <option value="asc" {{ ($filters['sort_order'] ?? '') == 'asc' ? 'selected' : '' }}>
                                        Asc
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results Per Page -->
                    <div class="mb-4">
                        <label for="per_page" class="form-label">Results Per Page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="10" {{ ($filters['per_page'] ?? 15) == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ ($filters['per_page'] ?? 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ ($filters['per_page'] ?? 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($filters['per_page'] ?? 15) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                            <i class="fas fa-undo"></i> Reset Filters
                        </button>
                    </div>
                    
                    <!-- Active Filters Summary -->
                    @if(count(array_filter($filters)) > 0)
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">Active Filters:</small>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($filters as $key => $value)
                                @if(!empty($value) && !in_array($key, ['page', 'per_page', 'sort_by', 'sort_order']))
                                <span class="badge bg-info">
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                                    <button type="button" class="btn-close btn-close-white ms-1" 
                                            style="font-size: 0.5rem;"
                                            onclick="removeFilter('{{ $key }}')"></button>
                                </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
    
    <!-- Results Panel -->
    <div class="col-md-8 col-lg-9">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-search"></i> Advanced Search Results
                        @if($files->total() > 0)
                        <span class="badge bg-primary ms-2">{{ $files->total() }} files found</span>
                        @endif
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportResults('csv')">CSV</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportResults('json')">JSON</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($files->count() > 0)
                    <!-- Results Summary -->
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle"></i>
                            Found <strong>{{ $files->total() }}</strong> file(s) matching your criteria.
                            @if(isset($filters['keyword']))
                                Showing results for: <strong>"{{ $filters['keyword'] }}"</strong>
                            @endif
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-info" onclick="copySearchUrl()">
                                <i class="fas fa-link"></i> Copy Search Link
                            </button>
                        </div>
                    </div>
                    
                    <!-- Results Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    @if(!auth()->user()->isUser())
                                        <th>Uploader</th>
                                    @endif
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Tags</th>
                                    <th>Status</th>
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
                                        <br>
                                        <small class="text-muted">{{ $file->mime_type }}</small>
                                    </td>
                                    <td>{{ $file->getFormattedSize() }}</td>
                                    <td>
                                        @if($file->tags)
                                            @foreach(array_slice($file->tags, 0, 2) as $tag)
                                                <a href="{{ route('files.advanced-search', ['tags' => $tag]) }}" 
                                                   class="badge bg-light text-dark tag-badge text-decoration-none">
                                                    #{{ $tag }}
                                                </a>
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
                                        @if(auth()->user()->isUser())
                                            <br>
                                            <small>
                                                Edit: 
                                                @if($file->edit_approved)
                                                    <span class="text-success">✓</span>
                                                @else
                                                    <span class="text-warning">⏳</span>
                                                @endif
                                                Delete: 
                                                @if($file->delete_approved)
                                                    <span class="text-success">✓</span>
                                                @else
                                                    <span class="text-warning">⏳</span>
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $file->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $file->created_at->format('H:i') }}</small>
                                    </td>
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
                            Showing {{ $files->firstItem() }} to {{ $files->lastItem() }} of {{ $files->total() }} files
                        </div>
                        <div>
                            {{ $files->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                    
                    <!-- Search Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Search Statistics</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li>Documents: {{ $files->where('file_type', 'document')->count() }}</li>
                                        <li>Images: {{ $files->where('file_type', 'image')->count() }}</li>
                                        <li>Approved: {{ $files->where('approved', true)->count() }}</li>
                                        <li>Pending: {{ $files->where('approved', false)->count() }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Quick Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('files.create') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-upload"></i> Upload New File
                                        </a>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="saveSearch()">
                                            <i class="fas fa-save"></i> Save This Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Results -->
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5>No files found</h5>
                        <p class="text-muted">Try adjusting your search criteria or filters.</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-outline-primary" onclick="resetFilters()">
                                <i class="fas fa-undo"></i> Reset Filters
                            </button>
                            <a href="{{ route('files.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> View All Files
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Add tag to filter input
function addTagToFilter(tag) {
    const tagsInput = document.getElementById('tags');
    const currentTags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t);
    
    if (!currentTags.includes(tag)) {
        if (currentTags.length > 0) {
            tagsInput.value = currentTags.join(', ') + ', ' + tag;
        } else {
            tagsInput.value = tag;
        }
    }
}

// Remove specific filter
function removeFilter(filterName) {
    const form = document.getElementById('advanced-search-form');
    const input = document.querySelector(`[name="${filterName}"]`);
    
    if (input) {
        if (input.type === 'checkbox') {
            input.checked = false;
        } else {
            input.value = '';
        }
    }
    
    form.submit();
}

// Reset all filters
function resetFilters() {
    const form = document.getElementById('advanced-search-form');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (input.name && !['_token', 'page'].includes(input.name)) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else if (input.type === 'select-multiple') {
                Array.from(input.options).forEach(option => option.selected = false);
            } else {
                input.value = '';
            }
        }
    });
    
    form.submit();
}

// Copy search URL to clipboard
function copySearchUrl() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Search URL copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy URL: ', err);
    });
}

// Export search results
function exportResults(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    window.location.href = `{{ route('files.advanced-search') }}?${params.toString()}`;
}

// Save search (placeholder - implement with backend if needed)
function saveSearch() {
    const searchName = prompt('Enter a name for this search:');
    if (searchName) {
        // Here you would typically send this to your backend
        // For now, we'll just save to localStorage
        const searchParams = window.location.search;
        const searches = JSON.parse(localStorage.getItem('savedSearches') || '[]');
        
        searches.push({
            name: searchName,
            url: searchParams,
            date: new Date().toISOString()
        });
        
        localStorage.setItem('savedSearches', JSON.stringify(searches));
        alert('Search saved!');
    }
}

// MIME type suggestions
document.getElementById('mime_type_suggestions')?.addEventListener('change', function() {
    if (this.value) {
        document.getElementById('mime_type').value = this.value;
    }
});

// Initialize date max values
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_to')?.setAttribute('max', today);
    document.getElementById('date_from')?.setAttribute('max', today);
    
    // Set date_to to today if date_from is set
    document.getElementById('date_from')?.addEventListener('change', function() {
        const dateTo = document.getElementById('date_to');
        if (dateTo && !dateTo.value) {
            dateTo.value = today;
        }
    });
});

// Auto-submit per_page changes
document.getElementById('per_page')?.addEventListener('change', function() {
    document.getElementById('advanced-search-form').submit();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') {
        document.getElementById('advanced-search-form').submit();
    }
    if (e.key === 'Escape') {
        resetFilters();
    }
});
</script>

<style>
.tag-suggestion:hover {
    background-color: #e9ecef !important;
    transform: translateY(-1px);
    transition: all 0.2s;
}

.card-header.bg-primary {
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
}

.badge.bg-info .btn-close {
    opacity: 1;
    filter: invert(1);
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.tag-badge:hover {
    background-color: #dee2e6 !important;
    text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body .row.g-2 {
        margin-bottom: 0.5rem;
    }
    .btn-group-sm {
        flex-wrap: wrap;
    }
}
</style>
@endpush
@endsection