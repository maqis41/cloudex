<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print: {{ $file->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            body {
                font-size: 12pt;
            }
            .print-header {
                border-bottom: 2px solid #000;
                margin-bottom: 20px;
                padding-bottom: 10px;
            }
            .section-title {
                background-color: #f8f9fa;
                padding: 5px 10px;
                margin: 15px 0;
                font-weight: bold;
            }
        }
        
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            background: white;
        }
        
        .file-info td, .file-info th {
            padding: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="print-header text-center mb-4">
            <h1>Cloudex File Details</h1>
            <p class="text-muted">Document generated on {{ now()->format('F d, Y H:i:s') }}</p>
        </div>
        
        <!-- File Information -->
        <div class="section-title">File Information</div>
        <table class="table table-bordered file-info">
            <tr>
                <th width="30%">Title</th>
                <td>{{ $file->title }}</td>
            </tr>
            <tr>
                <th>Original Name</th>
                <td>{{ $file->original_name }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $file->description ?: 'No description' }}</td>
            </tr>
            <tr>
                <th>File Type</th>
                <td>{{ ucfirst($file->file_type) }} ({{ $file->mime_type }})</td>
            </tr>
            <tr>
                <th>File Size</th>
                <td>{{ $file->getFormattedSize() }}</td>
            </tr>
            <tr>
                <th>Uploaded By</th>
                <td>{{ $file->user->name }} ({{ $file->user->email }})</td>
            </tr>
            <tr>
                <th>Upload Date</th>
                <td>{{ $file->created_at->format('F d, Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Last Updated</th>
                <td>{{ $file->updated_at->format('F d, Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Tags</th>
                <td>
                    @if($file->tags && count($file->tags) > 0)
                        {{ implode(', ', $file->tags) }}
                    @else
                        No tags
                    @endif
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($file->approved)
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-warning">Pending Approval</span>
                    @endif
                    
                    @if($file->edit_approved)
                        <span class="badge bg-info ms-1">Edit Approved</span>
                    @endif
                    
                    @if($file->delete_approved)
                        <span class="badge bg-danger ms-1">Delete Approved</span>
                    @endif
                </td>
            </tr>
        </table>
        
        <!-- Discussions -->
        @if($file->discussions->count() > 0)
            <div class="section-title page-break">Discussions ({{ $file->discussions->count() }} comments)</div>
            <div class="discussions-list">
                @foreach($file->discussions as $discussion)
                    <div class="discussion-item mb-4 pb-3 border-bottom">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>{{ $discussion->user->name }}</strong>
                            <small class="text-muted">{{ $discussion->created_at->format('M d, Y H:i') }}</small>
                        </div>
                        <p style="white-space: pre-wrap;">{{ $discussion->comment }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="section-title">Discussions</div>
            <p class="text-muted">No discussions yet.</p>
        @endif
        
        <!-- Footer -->
        <div class="mt-5 pt-4 border-top text-center text-muted">
            <p>Generated from Cloudex File Management System</p>
            <p>File ID: {{ $file->id }} | URL: {{ url()->current() }}</p>
        </div>
    </div>
    
    <!-- Print Controls -->
    <div class="no-print text-center mt-4">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="{{ route('files.pdf', $file->id) }}" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Download as PDF
        </a>
        <a href="{{ route('files.show', $file->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to File
        </a>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Auto-print on page load if requested
        @if(request()->has('autoprint'))
        window.onload = function() {
            window.print();
        }
        @endif
    </script>
</body>
</html>