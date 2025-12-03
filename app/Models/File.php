<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'filename',
        'original_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'description',
        'tags',
        'approved',
        'edit_approved',
        'delete_approved'
    ];

    protected $casts = [
        'tags' => 'array',
        'approved' => 'boolean',
        'edit_approved' => 'boolean',
        'delete_approved' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    // Method untuk mendapatkan URL file yang benar
    public function getFileUrl()
    {
        try {
            // Cek jika file ada di storage
            if (Storage::disk('public')->exists($this->file_path)) {
                // Gunakan Storage::url() yang lebih reliable
                $url = Storage::url($this->file_path);
                
                // Pastikan URL lengkap dengan domain
                if (!str_starts_with($url, 'http')) {
                    $url = url($url);
                }
                
                return $url;
            }
            
            // Coba dengan path alternatif
            $physicalPath = storage_path('app/public/' . $this->file_path);
            if (file_exists($physicalPath)) {
                // Jika symbolic link bermasalah, gunakan route untuk file preview
                return route('files.preview', $this->id);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error getting file URL: ' . $e->getMessage());
        }
        
        return asset('images/default-file.png');
    }

    // Method untuk mendapatkan path fisik file
    public function getPhysicalPath()
    {
        return storage_path('app/public/' . $this->file_path);
    }

    // Method untuk mengecek apakah file ada secara fisik
    public function fileExists()
    {
        return file_exists($this->getPhysicalPath());
    }

    // Method untuk format file size
    public function getFormattedSize()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    // Scope untuk file yang approved
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    // Scope untuk file yang pending
    public function scopePending($query)
    {
        return $query->where('approved', false);
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhereJsonContains('tags', $search);
    }
}