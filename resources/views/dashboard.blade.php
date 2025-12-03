@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@if(auth()->user()->isUser())
<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Files</h5>
                        <h2>{{ $userFiles }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Approved Files</h5>
                        <h2>{{ $approvedFiles }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Approval</h5>
                        <h2>{{ $pendingFiles }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->isAdmin() || auth()->user()->isCoadmin())
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Files</h5>
                        <h2>{{ $totalFiles }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2>{{ $totalUsers }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Files</h5>
                        <h2>{{ $pendingFiles }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-hourglass-half fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Size</h5>
                        <h2>{{ number_format($totalSize / (1024*1024), 2) }} MB</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-database fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Edits</h5>
                        <h2>{{ $pendingEdits }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-edit fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Deletes</h5>
                        <h2>{{ $pendingDeletes }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-trash fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Admin Actions</h5>
                        <p class="mb-0">
                            <small>Force delete available for immediate removal</small>
                        </p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shield-alt fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($recentFiles) && $recentFiles->count() > 0)
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Uploads</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($recentFiles as $file)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $file->title }}</h6>
                            <small>{{ $file->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $file->description ?: 'No description' }}</p>
                        <small>Uploaded by: {{ $file->user->name }} | Type: {{ $file->file_type }}</small>
                        <div class="mt-1">
                            @if($file->tags)
                                @foreach($file->tags as $tag)
                                    <span class="badge bg-light text-dark tag-badge">#{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endif
@endsection