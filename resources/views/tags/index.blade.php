@extends('layouts.app')

@section('title', 'All Tags')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">All Tags</h5>
            <span class="text-muted">{{ $totalTags }} tags total</span>
        </div>
    </div>
    <div class="card-body">
        @if(count($tags) > 0)
            @foreach($tags as $letter => $letterTags)
                <div class="mb-4">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <strong>{{ $letter }}</strong>
                    </h5>
                    <div class="row">
                        @php
                            $tagCount = 0;
                        @endphp
                        @foreach($letterTags as $tag => $count)
                            @if($tagCount % 8 == 0 && $tagCount > 0)
                                </div><div class="row">
                            @endif
                            <div class="col-md-3 col-lg-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                    <a href="{{ route('files.search', ['q' => $tag]) }}" 
                                       class="text-decoration-none flex-grow-1">
                                        #{{ $tag }}
                                    </a>
                                    <span class="badge bg-secondary ms-2">{{ $count }}</span>
                                </div>
                            </div>
                            @php
                                $tagCount++;
                            @endphp
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-4">
                {{ $tags->links('vendor.pagination.bootstrap-5') }}
            </div>
        @else
            <p class="text-muted">No tags found.</p>
        @endif
    </div>
</div>
@endsection