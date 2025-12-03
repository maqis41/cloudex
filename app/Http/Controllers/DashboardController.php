<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isUser()) {
            $data = [
                'userFiles' => $user->files()->count(),
                'approvedFiles' => $user->files()->where('approved', true)->count(),
                'pendingFiles' => $user->files()->where('approved', false)->count(),
            ];
        } else {
            $data = [
                'totalFiles' => File::count(),
                'totalUsers' => User::count(),
                'pendingFiles' => File::where('approved', false)->count(),
                'pendingEdits' => File::where('edit_approved', false)->count(),
                'pendingDeletes' => File::where('delete_approved', false)->count(),
                'totalSize' => File::sum('file_size'),
                'recentFiles' => File::with('user')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(),
            ];
        }

        return view('dashboard', $data);
    }
}