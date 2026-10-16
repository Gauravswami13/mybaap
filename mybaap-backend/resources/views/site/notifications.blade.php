@extends('site.layout')
@section('title', 'Notifications · MyBaap')
@section('content')
    <article class="page">
        <h1>Notifications</h1>
        @forelse($items as $item)
            <div class="plan">
                <h2 style="margin-top:0">{{ $item->title }}</h2>
                <p>{{ $item->body }}</p>
                <p class="muted">{{ $item->created_at?->diffForHumans() }}</p>
            </div>
        @empty
            <p class="muted">No notifications.</p>
        @endforelse
    </article>
@endsection
