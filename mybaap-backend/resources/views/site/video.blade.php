@extends('site.layout')
@section('title', $video['title'].' · MyBaap')
@section('content')
    <article class="detail">
        <div class="stage" @if($video['thumbnail']) style="background-image:url('{{ $video['thumbnail'] }}')" @endif></div>
        <div>
            @if($video['category'])
                <a class="chip" href="{{ url('/category/'.$video['category_id']) }}">{{ $video['category'] }}</a>
            @endif
            <h1>{{ $video['title'] }}</h1>
            <p class="muted">{{ collect([$video['duration'], $video['is_paid'] ? 'Paid' : 'Free'])->filter()->implode(' · ') }}</p>
            <p class="muted">{{ $video['description'] }}</p>
            @if($video['is_paid'] && ! $subscribed)
                <a class="btn" href="{{ auth()->check() ? route('plans') : route('login') }}">Get Subscription</a>
                <p class="muted">This video plays after you take a plan.</p>
            @else
                <a class="btn" href="{{ auth()->check() ? route('watch', $video['id']) : route('login') }}">Watch now</a>
            @endif
        </div>
    </article>
    @if($related->isNotEmpty())
        <h2>More like this</h2>
        <div class="rail">
            @foreach($related as $item)
                @include('site.poster', ['video' => $item])
            @endforeach
        </div>
    @endif
@endsection
