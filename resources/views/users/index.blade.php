@extends('layouts.app')

@section('title', 'User Management')

@section('header-buttons')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Add New User
    </a>
@endsection

@section('content')
<!-- User Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Admins</h5>
                        <h2>{{ $roleCounts['admin'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-crown fa-2x"></i>
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
                        <h5 class="card-title">Co-Admins</h5>
                        <h2>{{ $roleCounts['coadmin'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Users</h5>
                        <h2>{{ $roleCounts['user'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Manage Users</h5>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
            <!-- Admins Section -->
            @if($admins->count() > 0)
                <div class="mb-4">
                    <h5 class="text-danger border-bottom pb-2 mb-3">
                        <i class="fas fa-crown"></i> Administrators
                        <span class="badge bg-danger">{{ $admins->count() }}</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Files</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $user)
                                <tr>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <form action="{{ route('users.update-role', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" 
                                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                <option value="coadmin" {{ $user->role === 'coadmin' ? 'selected' : '' }}>Co-Admin</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $user->files->count() }}</span>
                                    </td>
                                    <td>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted">Current User</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Co-Admins Section -->
            @if($coadmins->count() > 0)
                <div class="mb-4">
                    <h5 class="text-warning border-bottom pb-2 mb-3">
                        <i class="fas fa-shield-alt"></i> Co-Administrators
                        <span class="badge bg-warning">{{ $coadmins->count() }}</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Files</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coadmins as $user)
                                <tr>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <form action="{{ route('users.update-role', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" 
                                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                <option value="coadmin" {{ $user->role === 'coadmin' ? 'selected' : '' }}>Co-Admin</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $user->files->count() }}</span>
                                    </td>
                                    <td>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted">Current User</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Regular Users Section -->
            @if($regularUsers->count() > 0)
                <div class="mb-4">
                    <h5 class="text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-users"></i> Regular Users
                        <span class="badge bg-secondary">{{ $regularUsers->count() }}</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Files</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regularUsers as $user)
                                <tr>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <form action="{{ route('users.update-role', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" 
                                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                <option value="coadmin" {{ $user->role === 'coadmin' ? 'selected' : '' }}>Co-Admin</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $user->files->count() }}</span>
                                    </td>
                                    <td>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted">Current User</span>
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
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                <div>
                    {{ $users->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">No users found.</p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add New User
                </a>
            </div>
        @endif
    </div>
</div>
@endsection