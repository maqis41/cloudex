<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discussion;
use App\Models\File;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function store(Request $request, $fileId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        Discussion::create([
            'file_id' => $fileId,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Comment added successfully.');
    }

    public function destroy($id)
    {
        $discussion = Discussion::findOrFail($id);
        
        // Hanya pemilik komentar atau admin/coadmin yang bisa hapus
        if (Auth::user()->isUser() && $discussion->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $discussion->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }
}