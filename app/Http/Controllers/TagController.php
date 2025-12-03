<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua tag dan hitung frekuensinya
        $allTags = File::whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->countBy()
            ->sortByDesc(function ($count, $tag) {
                return $count;
            });

        // Konfigurasi pagination
        $perPage = 32; // 4 baris x 8 tag
        $currentPage = $request->get('page', 1);
        
        // Kelompokkan tag berdasarkan huruf pertama
        $groupedTags = [];
        foreach ($allTags as $tag => $count) {
            $firstChar = strtoupper(mb_substr($tag, 0, 1));
            if (!isset($groupedTags[$firstChar])) {
                $groupedTags[$firstChar] = [];
            }
            $groupedTags[$firstChar][$tag] = $count;
        }
        
        // Urutkan berdasarkan huruf
        ksort($groupedTags);
        
        // Hitung total tag
        $totalTags = $allTags->count();
        
        // Untuk pagination manual, kita perlu membagi data
        $currentGroup = [];
        $currentCount = 0;
        $startIndex = ($currentPage - 1) * $perPage;
        $endIndex = $startIndex + $perPage;
        $currentIndex = 0;
        
        foreach ($groupedTags as $letter => $tags) {
            foreach ($tags as $tag => $count) {
                if ($currentIndex >= $startIndex && $currentIndex < $endIndex) {
                    if (!isset($currentGroup[$letter])) {
                        $currentGroup[$letter] = [];
                    }
                    $currentGroup[$letter][$tag] = $count;
                }
                $currentIndex++;
            }
        }
        
        // Buat paginator manual
        $tags = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentGroup,
            $totalTags,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('tags.index', compact('tags', 'totalTags'));
    }
}