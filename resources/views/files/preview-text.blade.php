@extends('layouts.app')

@section('title', 'Preview Text: ' . $file->title)

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Text Preview: {{ $file->title }}</h5>
            <a href="{{ route('files.show', $file->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to File
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <strong>File Name:</strong> {{ $file->original_name }}<br>
                <strong>File Type:</strong> {{ $file->mime_type }}<br>
                <strong>File Size:</strong> {{ $file->getFormattedSize() }}
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('files.download', $file->id) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
        
        <div class="border rounded p-3 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">File Content:</h6>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="copyContent()" title="Copy all text">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleWrap()" title="Toggle word wrap">
                        <i class="fas fa-text-width"></i> Wrap
                    </button>
                </div>
            </div>
            
            <div id="text-content" class="p-3 bg-white border rounded" 
                 style="height: 500px; overflow: auto; font-family: 'Courier New', monospace; white-space: pre-wrap; word-wrap: break-word;">
                {!! $formattedContent !!}
            </div>
            
            <div class="mt-3 text-muted small">
                <i class="fas fa-info-circle"></i> 
                Showing text content of {{ $file->original_name }} ({{ $file->getFormattedSize() }})
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyContent() {
    const textContent = document.getElementById('text-content');
    const text = textContent.innerText || textContent.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('Content copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}

function toggleWrap() {
    const textContent = document.getElementById('text-content');
    if (textContent.style.whiteSpace === 'pre-wrap') {
        textContent.style.whiteSpace = 'pre';
        textContent.style.wordWrap = 'normal';
    } else {
        textContent.style.whiteSpace = 'pre-wrap';
        textContent.style.wordWrap = 'break-word';
    }
}

// Add line numbers
document.addEventListener('DOMContentLoaded', function() {
    const contentElement = document.getElementById('text-content');
    const content = contentElement.innerHTML;
    const lines = content.split('<br>');
    
    if (lines.length > 1) {
        let numberedContent = '';
        lines.forEach((line, index) => {
            numberedContent += `<div class="line"><span class="line-number">${index + 1}</span> ${line}</div>`;
        });
        contentElement.innerHTML = numberedContent;
    }
});
</script>

<style>
.line {
    display: flex;
    margin-bottom: 2px;
}

.line-number {
    min-width: 40px;
    padding-right: 10px;
    text-align: right;
    color: #999;
    user-select: none;
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    margin-right: 10px;
}

#text-content {
    font-size: 14px;
    line-height: 1.5;
}
</style>
@endpush
@endsection