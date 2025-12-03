<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Discussion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\PDF;

class FileController extends Controller
{
    // Menampilkan file milik user yang login dengan segmentasi
    public function index()
    {
        $files = Auth::user()->files()->orderBy('created_at', 'desc')->paginate(10);
        
        // Separate files by type
        $documents = $files->where('file_type', 'document');
        $images = $files->where('file_type', 'image');

        return view('files.index', compact('files', 'documents', 'images'));
    }

    // Menampilkan semua file (untuk coadmin dan admin) dengan segmentasi
    public function allFiles()
    {
        $user = Auth::user();
        
        if ($user->isUser()) {
            // Untuk user biasa: tampilkan semua file yang approved ATAU milik user itu sendiri
            $files = File::with('user')
                ->where(function($query) use ($user) {
                    $query->where('approved', true)
                          ->orWhere('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Untuk admin/coadmin: tampilkan semua file
            $files = File::with('user')->orderBy('created_at', 'desc')->paginate(10);
        }
        
        // Separate files by type
        $documents = $files->where('file_type', 'document');
        $images = $files->where('file_type', 'image');

        return view('files.all', compact('files', 'documents', 'images'));
    }

    // Menampilkan form upload
    public function create()
    {
        return view('files.create');
    }

    // Menyimpan file yang diupload
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:255'
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $fileSize = $uploadedFile->getSize();
        $mimeType = $uploadedFile->getMimeType();

        // Tentukan tipe file: document atau image
        $fileType = Str::startsWith($mimeType, 'image/') ? 'image' : 'document';

        // Generate unique filename
        $filename = time() . '_' . Str::random(10) . '.' . $uploadedFile->getClientOriginalExtension();

        // Simpan file ke storage
        $storagePath = $fileType . 's';
        $filePath = $uploadedFile->storeAs($storagePath, $filename, 'public');

        // Parse tags (comma separated)
        $tags = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

        // Simpan data file ke database
        $file = File::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'filename' => $filename,
            'original_name' => $originalName,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'description' => $request->description,
            'tags' => $tags,
            'approved' => false, // Default butuh persetujuan
            'edit_approved' => false,
            'delete_approved' => false
        ]);

        return redirect()->route('files.index')->with('success', 'File uploaded successfully. Waiting for approval.');
    }

    // Menampilkan detail file dan diskusi
    public function show($id)
    {
        $file = File::with(['user', 'discussions.user'])->findOrFail($id);
        $user = Auth::user();
        
        if ($user->isUser()) {
            // User biasa hanya bisa melihat file yang approved ATAU miliknya sendiri
            if (!$file->approved && $file->user_id !== $user->id) {
                abort(403, 'You can only view approved files or your own files.');
            }
        }

        return view('files.show', compact('file'));
    }

    public function preview($id)
    {
        $file = File::findOrFail($id);
        $user = Auth::user();
        
        // Pengecekan hak akses
        if ($user->isUser()) {
            if (!$file->approved && $file->user_id !== $user->id) {
                abort(403, 'You can only view approved files or your own files.');
            }
        }
        
        // Hanya untuk file gambar
        if ($file->file_type !== 'image') {
            abort(404, 'Preview only available for images');
        }
        
        $path = storage_path('app/public/' . $file->file_path);
        
        // Cek jika file ada
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        
        // Dapatkan mime type
        $mime = mime_content_type($path);
        
        // Set header untuk response
        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            'Cache-Control' => 'public, max-age=31536000',
        ];
        
        // Return response file
        return response()->file($path, $headers);
    }

    public function previewPdf($id)
    {
        $file = File::findOrFail($id);
        $user = Auth::user();
        
        // Pengecekan hak akses
        if ($user->isUser()) {
            if (!$file->approved && $file->user_id !== $user->id) {
                abort(403, 'You can only view approved files or your own files.');
            }
        }
        
        // Hanya untuk file PDF
        if ($file->mime_type !== 'application/pdf') {
            abort(404, 'Preview only available for PDF files');
        }
        
        $path = storage_path('app/public/' . $file->file_path);
        
        // Cek jika file ada
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        
        // Return response file PDF untuk inline view
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    // Preview untuk file text
    public function previewText($id)
    {
        $file = File::findOrFail($id);
        $user = Auth::user();
        
        // Pengecekan hak akses
        if ($user->isUser()) {
            if (!$file->approved && $file->user_id !== $user->id) {
                abort(403, 'You can only view approved files or your own files.');
            }
        }
        
        // Tentukan tipe file yang didukung untuk text preview
        $textMimeTypes = [
            'text/plain',
            'text/html',
            'text/css',
            'text/javascript',
            'application/json',
            'application/xml',
            'text/xml',
            'application/rtf',
        ];
        
        if (!in_array($file->mime_type, $textMimeTypes)) {
            abort(404, 'Preview only available for text files');
        }
        
        $path = storage_path('app/public/' . $file->file_path);
        
        // Cek jika file ada
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        
        // Baca isi file
        $content = file_get_contents($path);
        
        // Encode content untuk keamanan
        $content = htmlspecialchars($content);
        
        // Format content berdasarkan tipe file
        if ($file->mime_type === 'text/html') {
            $formattedContent = $content; // HTML tidak perlu nl2br
        } else {
            $formattedContent = nl2br($content);
        }
        
        return view('files.preview-text', compact('file', 'formattedContent'));
    }

    // Print file details and discussions
    public function print($id)
    {
        $file = File::with(['user', 'discussions.user'])->findOrFail($id);
        $user = Auth::user();
        
        // Pengecekan hak akses
        if ($user->isUser()) {
            if (!$file->approved && $file->user_id !== $user->id) {
                abort(403, 'You can only view approved files or your own files.');
            }
        }
        
        return view('files.print', compact('file'));
    }

    // Generate PDF untuk file details and discussions
    public function pdf($id, PDF $pdfGenerator)
    {
        $file = File::with(['user', 'discussions.user'])->findOrFail($id);
        $user = Auth::user();
        
        // Pengecekan hak akses
        if ($user->isUser()) {
            if (!$file->approved && $file->user_id !== $user->id) {
                abort(403, 'You can only view approved files or your own files.');
            }
        }
        
        // Generate PDF menggunakan dependency injection
        $pdf = $pdfGenerator->loadView('files.print-pdf', compact('file'));
        
        return $pdf->download($file->title . '_details.pdf');
    }

    // Menampilkan form edit file
    public function edit($id)
    {
        $file = File::findOrFail($id);
        
        // Jika user biasa, hanya boleh edit file miliknya sendiri dan harus sudah disetujui editnya
        if (Auth::user()->isUser()) {
            if ($file->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
            if (!$file->edit_approved) {
                return redirect()->route('files.show', $file->id)->with('error', 'Edit request not approved yet.');
            }
        }

        return view('files.edit', compact('file'));
    }

    // Update file
    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);
        
        // Jika user biasa, hanya boleh update file miliknya sendiri dan harus sudah disetujui editnya
        if (Auth::user()->isUser()) {
            if ($file->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
            if (!$file->edit_approved) {
                return redirect()->route('files.show', $file->id)->with('error', 'Edit request not approved yet.');
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:255'
        ]);

        $tags = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

        $file->title = $request->title;
        $file->description = $request->description;
        $file->tags = $tags;
        
        // Reset edit_approved untuk user biasa setelah edit
        if (Auth::user()->isUser()) {
            $file->edit_approved = false;
        }
        
        $file->save();

        return redirect()->route('files.show', $file->id)->with('success', 'File updated successfully.');
    }

    // Menghapus file
    public function destroy($id)
    {
        $file = File::findOrFail($id);
        
        // Jika user biasa, hanya boleh hapus file miliknya sendiri dan harus sudah disetujui hapusnya
        if (Auth::user()->isUser()) {
            if ($file->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
            if (!$file->delete_approved) {
                return redirect()->route('files.show', $file->id)->with('error', 'Delete request not approved yet.');
            }
        }

        // Hapus file dari storage
        Storage::disk('public')->delete($file->file_path);

        // Hapus data file dari database
        $file->delete();

        return redirect()->route('files.index')->with('success', 'File deleted successfully.');
    }

    // Approve file (untuk coadmin dan admin)
    public function approve($id)
    {
        $file = File::findOrFail($id);
        $file->approved = true;
        $file->save();
        
        return back()->with('success', 'File approved successfully.');
    }

    // Approve edit (untuk coadmin dan admin)
    public function approveEdit($id)
    {
        $file = File::findOrFail($id);
        $file->edit_approved = true;
        $file->save();
        
        return back()->with('success', 'Edit request approved.');
    }

    // Approve delete (untuk coadmin dan admin)
    public function approveDelete($id)
    {
        $file = File::findOrFail($id);
        $file->delete_approved = true;
        $file->save();
        
        return back()->with('success', 'Delete request approved.');
    }

    // Download file
    public function download($id)
    {
        $file = File::findOrFail($id);
        $user = Auth::user();
        
        if ($user->isUser()) {
            // User biasa hanya bisa download file yang approved ATAU miliknya sendiri
            if (!$file->approved && $file->user_id !== $user->id) {
                abort(403, 'You can only download approved files or your own files.');
            }
        }

        $filePath = $file->file_path;
        $originalName = $file->original_name;

        return Storage::disk('public')->download($filePath, $originalName);
    }

    // Pencarian file
    public function search(Request $request)
    {
        $query = $request->get('q');
        $user = Auth::user();

        if ($user->isUser()) {
            $files = File::where(function($q) use ($user) {
                    $q->where('approved', true)
                      ->orWhere('user_id', $user->id);
                })
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%$query%")
                      ->orWhere('description', 'like', "%$query%")
                      ->orWhereJsonContains('tags', $query)
                      ->orWhere('tags', 'like', '%"' . $query . '"%')
                      ->orWhere('tags', 'like', '%' . $query . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        } else {
            $files = File::with('user')
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%$query%")
                      ->orWhere('description', 'like', "%$query%")
                      ->orWhereJsonContains('tags', $query)
                      ->orWhere('tags', 'like', '%"' . $query . '"%')
                      ->orWhere('tags', 'like', '%' . $query . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }

        return view('files.search', compact('files', 'query'));
    }

    /**
     * Advanced Search dengan multiple filters
     */
    public function advancedSearch(Request $request)
    {
        $user = Auth::user();
        
        // Build query berdasarkan role user
        if ($user->isUser()) {
            $query = File::where(function($q) use ($user) {
                $q->where('approved', true)
                  ->orWhere('user_id', $user->id);
            });
        } else {
            $query = File::with('user');
            
            // Filter berdasarkan uploader (hanya untuk admin/coadmin)
            if ($request->filled('uploader')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->uploader . '%')
                      ->orWhere('email', 'like', '%' . $request->uploader . '%');
                });
            }
        }
        
        // Filter: Keyword (title, description, tags)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%$keyword%")
                  ->orWhere('description', 'like', "%$keyword%")
                  ->orWhereJsonContains('tags', $keyword)
                  ->orWhere('tags', 'like', '%"' . $keyword . '"%')
                  ->orWhere('tags', 'like', '%' . $keyword . '%');
            });
        }
        
        // Filter: File Type
        if ($request->filled('file_type') && in_array($request->file_type, ['document', 'image'])) {
            $query->where('file_type', $request->file_type);
        }
        
        // Filter: Approval Status
        if ($request->filled('approval_status')) {
            switch ($request->approval_status) {
                case 'approved':
                    $query->where('approved', true);
                    break;
                case 'pending':
                    $query->where('approved', false);
                    break;
                case 'edit_pending':
                    $query->where('edit_approved', false);
                    break;
                case 'delete_pending':
                    $query->where('delete_approved', false);
                    break;
            }
        }
        
        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter: File Size
        if ($request->filled('size_min')) {
            $query->where('file_size', '>=', $request->size_min * 1024); // Convert KB to bytes
        }
        if ($request->filled('size_max')) {
            $query->where('file_size', '<=', $request->size_max * 1024); // Convert KB to bytes
        }
        
        // Filter: MIME Type
        if ($request->filled('mime_type')) {
            $query->where('mime_type', 'like', '%' . $request->mime_type . '%');
        }
        
        // Filter: Specific Tags
        if ($request->filled('tags')) {
            $tags = array_map('trim', explode(',', $request->tags));
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }
        
        // Sort Options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $validSortColumns = ['created_at', 'title', 'file_size', 'updated_at'];
        $validSortOrders = ['asc', 'desc'];
        
        if (in_array($sortBy, $validSortColumns) && in_array($sortOrder, $validSortOrders)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $files = $query->paginate($perPage)->appends($request->except('page'));
        
        // Get all unique tags for filter suggestions
        $allTags = File::whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values()
            ->take(20);
        
        // Get all unique mime types for filter suggestions
        $allMimeTypes = File::select('mime_type')
            ->distinct()
            ->orderBy('mime_type')
            ->pluck('mime_type')
            ->take(20);
        
        // Pass all request parameters to view for form persistence
        $filters = $request->all();
        
        return view('files.advanced-search', compact('files', 'filters', 'allTags', 'allMimeTypes'));
    }

    // Force delete file (untuk admin dan coadmin tanpa persetujuan)
    public function forceDelete($id)
    {
        // Hanya admin dan coadmin yang bisa force delete
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoadmin()) {
            abort(403, 'Unauthorized action. Only administrators can force delete files.');
        }

        $file = File::findOrFail($id);

        // Log the action
        \Log::info('Force delete performed by ' . Auth::user()->name . ' on file ID: ' . $id . ' - ' . $file->title);

        // Hapus file dari storage
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Hapus diskusi terkait file
        $file->discussions()->delete();

        // Hapus data file dari database
        $file->delete();

        return redirect()->route('files.all')->with('success', 'File has been permanently deleted by administrator.');
    }
}