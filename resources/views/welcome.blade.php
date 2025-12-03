@extends('layouts.app')

@section('title', 'Welcome to Cloudex')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        <div class="py-5">
            <h1 class="display-4 mb-4">
                <i class="fas fa-cloud-upload-alt text-primary"></i><br>
                Welcome to Cloudex
            </h1>
            <p class="lead mb-4">
                A simple and secure file management system for your documents and images.
                Share, discuss, and manage your files with ease.
            </p>
            
            @guest
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 gap-3">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
            @else
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4 gap-3">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            </div>
            @endguest
        </div>

        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Digital Library</h5>
                        <p class="card-text">Organize your documents and images in a centralized digital library.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Secure Storage</h5>
                        <p class="card-text">Your files are securely stored with proper access controls and permissions.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-comments fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Collaborative Discussion</h5>
                        <p class="card-text">Discuss and share ideas about files with your community.</p>
                    </div>
                </div>
            </div>
        </div>

        @guest
        <div class="mt-5 p-4 bg-light rounded">
            <h5><i class="fas fa-key"></i> Demo Accounts</h5>
            <div class="row text-start">
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title text-primary">
                                <i class="fas fa-crown"></i> Administrator
                            </h6>
                            <small>
                                <strong>Email:</strong> admin@cloudex.com<br>
                                <strong>Password:</strong> Yang@Berkuasa_123<br>
                                <em class="text-muted">Full system access</em>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title text-warning">
                                <i class="fas fa-shield-alt"></i> Co-Administrator
                            </h6>
                            <small>
                                <strong>Email:</strong> coadmin@cloudex.com<br>
                                <strong>Password:</strong> Dibawah@Raja_123<br>
                                <em class="text-muted">File moderation access</em>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title text-success">
                                <i class="fas fa-user"></i> User
                            </h6>
                            <small>
                                <strong>Email:</strong> mahdi@user.com<br>
                                <strong>Password:</strong> Mahdi@Saja_123<br>
                                <em class="text-muted">File upload access</em>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endguest
    </div>
</div>
@endsection