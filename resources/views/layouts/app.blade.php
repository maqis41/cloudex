<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Cloudex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
        }
        .file-card {
            transition: transform 0.2s;
        }
        .file-card:hover {
            transform: translateY(-2px);
        }
        .tag-badge {
            margin-right: 0.25rem;
        }
    </style>
    <style>
        .pagination {
            margin-bottom: 0;
        }
        
        .page-link {
            color: #6c757d;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }
        
        .page-link:hover {
            color: #0d6efd;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        /* Ensure pagination container has proper spacing */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        @media (max-width: 768px) {
            .pagination-container {
                flex-direction: column;
                text-align: center;
            }
            
            .pagination {
                margin-top: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-cloud-upload-alt"></i> Cloudex
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('files.index') }}">My Files</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('files.all') }}">All Files</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-search"></i> Search
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="searchDropdown">
                                <li>
                                    <form class="px-3 py-2" action="{{ route('files.search') }}" method="GET">
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="q" class="form-control" placeholder="Quick search..." value="{{ request('q') }}">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('files.advanced-search') }}"><i class="fas fa-search-plus"></i> Advanced Search</a></li>
                                <li><a class="dropdown-item" href="{{ route('tags.index') }}"><i class="fas fa-tags"></i> Browse Tags</a></li>
                            </ul>
                        </li>

                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}">User Management</a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> 
                                @auth
                                    {{ auth()->user()->name }}
                                    <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'danger' : (auth()->user()->role === 'coadmin' ? 'warning' : 'secondary') }} role-badge role-{{ auth()->user()->role }}">
                                        @if(auth()->user()->isAdmin())
                                            <i class="fas fa-crown"></i>
                                        @elseif(auth()->user()->isCoadmin())
                                            <i class="fas fa-shield-alt"></i>
                                        @else
                                            <i class="fas fa-user"></i>
                                        @endif
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                @endauth
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="dropdown-header">
                                        <strong>{{ auth()->user()->name }}</strong>
                                        <div class="small text-muted">
                                            @if(auth()->user()->isAdmin())
                                                Main Administrator
                                            @elseif(auth()->user()->isCoadmin())
                                                Co-Administrator
                                            @else
                                                Registered User
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Quick Stats</h6>
                            <small class="text-muted">
                                @if(auth()->user()->isUser())
                                    Files: {{ auth()->user()->files->count() }}<br>
                                    Approved: {{ auth()->user()->files->where('approved', true)->count() }}
                                @else
                                    Total Files: {{ \App\Models\File::count() }}<br>
                                    Total Users: {{ \App\Models\User::count() }}
                                @endif
                            </small>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('files.search') }}" method="GET">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="q" class="form-control" placeholder="Search files..." value="{{ request('q') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>

                                {{-- [advanced-search] --}}
                                <div class="d-grid">
                                    <a href="{{ route('files.advanced-search') }}" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-search-plus"></i> Advanced Search
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @php
                        // Ambil tag populer (5 tag paling banyak digunakan)
                        $popularTags = \App\Models\File::whereNotNull('tags')
                            ->get()
                            ->pluck('tags')
                            ->flatten()
                            ->countBy()
                            ->sortDesc()
                            ->take(5)
                            ->keys();
                    @endphp

                    @if($popularTags->count() > 0)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Popular Tags</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($popularTags as $tag)
                                    <a href="{{ route('files.search', ['q' => $tag]) }}" class="badge bg-primary text-decoration-none">#{{ $tag }}</a>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('tags.index') }}" class="small text-muted">View all tags →</a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->isUser())
                    <div class="d-grid gap-2">
                        <a href="{{ route('files.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload"></i> Upload New File
                        </a>
                    </div>
                    @endif
                </div>
            </nav>
            @endauth

            <main class="@auth col-md-9 ms-sm-auto col-lg-10 px-md-4 @else col-12 @endauth">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title')</h1>
                    @yield('header-buttons')
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        // Force delete confirmation dengan double check
        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap semua form force delete
            const forceDeleteForms = document.querySelectorAll('form[action*="force-delete"]');
            
            forceDeleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Check user role from badge class
                    const roleBadge = document.querySelector('.role-badge');
                    let userRole = '';
                    
                    if (roleBadge) {
                        if (roleBadge.classList.contains('role-admin')) {
                            userRole = 'admin';
                        } else if (roleBadge.classList.contains('role-coadmin')) {
                            userRole = 'coadmin';
                        }
                    }
                    
                    // Only allow admin or coadmin
                    if (userRole !== 'admin' && userRole !== 'coadmin') {
                        alert('⚠️ Access Denied: Only administrators can perform force delete.');
                        return false;
                    }
                    
                    // Double confirmation untuk force delete
                    if (confirm('⚠️ WARNING: This is a FORCE DELETE action!\n\nThis will:\n1. Permanently delete the file\n2. Remove all associated comments\n3. Cannot be undone\n\nAre you ABSOLUTELY sure?')) {
                        // Second confirmation
                        if (confirm('⚠️ FINAL WARNING!\n\nThis action bypasses all approval systems.\nClick OK to proceed with permanent deletion.')) {
                            this.submit();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>