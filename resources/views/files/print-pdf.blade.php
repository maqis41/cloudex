<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $file->title }} - Cloudex</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24pt;
            color: #333;
            margin-bottom: 5px;
        }
        
        .header .date {
            color: #666;
            font-size: 10pt;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #f5f5f5;
            padding: 8px 12px;
            font-weight: bold;
            border-left: 4px solid #007bff;
            margin: 15px 0;
            font-size: 14pt;
        }
        
        .file-info {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .file-info th, .file-info td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .file-info th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
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
            font-size: 10pt;
        }
        
        .discussion-content {
            white-space: pre-wrap;
            margin-left: 20px;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 10pt;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10pt;
            margin-right: 5px;
        }
        
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Cloudex File Document</h1>
        <div class="date">Generated on {{ now()->format('F d, Y \a\t H:i:s') }}</div>
    </div>
    
    <!-- File Information -->
    <div class="section">
        <div class="section-title">File Information</div>
        <table class="file-info">
            <tr>
                <th>Title</th>
                <td>{{ $file->title }}</td>
            </tr>
            <tr>
                <th>Original Name</th>
                <td>{{ $file->original_name }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $file->description ?: 'No description provided' }}</td>
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
                <td>{{ $file->user->name }} &lt;{{ $file->user->email }}&gt;</td>
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
                        <span class="badge badge-success">Approved</span>
                    @else
                        <span class="badge badge-warning">Pending Approval</span>
                    @endif
                    
                    @if($file->edit_approved)
                        <span class="badge badge-info">Edit Approved</span>
                    @endif
                    
                    @if($file->delete_approved)
                        <span class="badge badge-danger">Delete Approved</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Discussions -->
    @if($file->discussions->count() > 0)
        <div class="section page-break">
            <div class="section-title">Discussions ({{ $file->discussions->count() }} comments)</div>
            <div class="discussions-list">
                @foreach($file->discussions as $discussion)
                    <div class="discussion-item">
                        <div class="discussion-header">
                            <span class="discussion-user">{{ $discussion->user->name }}</span>
                            <span class="discussion-date">{{ $discussion->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="discussion-content">{{ $discussion->comment }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="section">
            <div class="section-title">Discussions</div>
            <p>No discussions yet.</p>
        </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>Generated from Cloudex File Management System</p>
        <p>File ID: {{ $file->id }} | Document generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>© {{ date('Y') }} Cloudex. All rights reserved.</p>
    </div>
</body>
</html>