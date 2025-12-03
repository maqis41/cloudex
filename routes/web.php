<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\TagController;
 
// Public Routes
Route::get('/', function () {
    return view('welcome');
})->middleware('nocache');

// Authentication Routes
Route::middleware('nocache')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth', 'nocache'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // File Management Routes
    Route::prefix('files')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('files.index');
        Route::get('/create', [FileController::class, 'create'])->name('files.create');
        Route::post('/', [FileController::class, 'store'])->name('files.store');
        Route::get('/search', [FileController::class, 'search'])->name('files.search');
        Route::get('/advanced-search', [FileController::class, 'advancedSearch'])->name('files.advanced-search');
        Route::get('/{id}/preview', [FileController::class, 'preview'])->name('files.preview');
        Route::get('/{id}/preview-pdf', [FileController::class, 'previewPdf'])->name('files.preview.pdf'); // TAMBAH INI
        Route::get('/{id}/preview-text', [FileController::class, 'previewText'])->name('files.preview.text');
        Route::get('/{id}/print', [FileController::class, 'print'])->name('files.print');
        Route::get('/{id}/pdf', [FileController::class, 'pdf'])->name('files.pdf');
        Route::get('/{id}', [FileController::class, 'show'])->name('files.show');
        Route::get('/{id}/edit', [FileController::class, 'edit'])->name('files.edit');
        Route::put('/{id}', [FileController::class, 'update'])->name('files.update');
        Route::delete('/{id}', [FileController::class, 'destroy'])->name('files.destroy');
        Route::get('/{id}/download', [FileController::class, 'download'])->name('files.download');
        Route::get('/all/files', [FileController::class, 'allFiles'])->name('files.all');
        
        // Approval routes untuk coadmin dan admin
        Route::middleware(['coadmin'])->group(function () {
            Route::get('/{id}/approve', [FileController::class, 'approve'])->name('files.approve');
            Route::get('/{id}/approve-edit', [FileController::class, 'approveEdit'])->name('files.approve-edit');
            Route::get('/{id}/approve-delete', [FileController::class, 'approveDelete'])->name('files.approve-delete');
            Route::delete('/{id}/force-delete', [FileController::class, 'forceDelete'])->name('files.force-delete');
        });
    });

    // Tag Routes
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');

    // Discussion Routes
    Route::post('/files/{fileId}/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::delete('/discussions/{id}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');
    
    // User Management - hanya untuk admin
    Route::prefix('users')->middleware('admin')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{id}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});