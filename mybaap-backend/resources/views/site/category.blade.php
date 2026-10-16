@extends('site.layout')
@section('title', $category['name'].' · MyBaap')
@section('content')
    <h1>{{ $category['name'] }}</h1>
    <div class="grid">
        @forelse($videos as $video)
            <a class="card" href="{{ url('/videos/'.$video['id']) }}">
                <div class="thumb-wrap">
                    <div class="thumb" @if($video['thumbnail']) style="background-image:url('{{ $video['thumbnail'] }}')" @endif></div>
                    <span class="tag">{{ $video['is_paid'] ? 'PAID' : 'FREE' }}</span>
                </div>
                <p>{{ $video['title'] }}</p>
            </a>
        @empty
            <p class="muted">No videos in this category.</p>
        @endforelse
    </div>
@endsection
