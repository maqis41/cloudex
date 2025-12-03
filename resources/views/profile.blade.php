@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" disabled>
                            <small class="form-text text-muted">Email cannot be changed</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                  id="bio" name="bio" rows="3">{{ old('bio', auth()->user()->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Account Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Role:</strong>
                    <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'danger' : (auth()->user()->role === 'coadmin' ? 'warning' : 'secondary') }} float-end">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Member since:</strong>
                    <span class="float-end">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                </div>
                <div class="mb-3">
                    <strong>Total Files:</strong>
                    <span class="float-end">{{ auth()->user()->files->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection