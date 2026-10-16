@extends('site.layout')
@section('title', 'MyBaap')
@section('content')
    @php
        $hero = $banners->first();
        $needsPlan = $featured && $featured['is_paid'] && ! $subscribed;
        $playUrl = $featured ? (auth()->check() ? url('/watch/'.$featured['id']) : route('login')) : route('login');
    @endphp
    <section class="hero" @if($hero && $hero['image']) style="background-image:url('{{ $hero['image'] }}')" @endif>
        <div class="hero-copy">
            @if($featured && $featured['category'])
                <div class="muted">{{ $featured['category'] }}</div>
            @endif
            <h1>{{ $hero['title'] ?? ($featured['title'] ?? 'MyBaap') }}</h1>
            @if($featured)
                <p class="muted">
                    {{ collect([$featured['duration'], $featured['is_paid'] ? 'Paid' : 'Free'])->filter()->implode(' · ') }}
                </p>
                <p class="muted">{{ \Illuminate\Support\Str::limit($featured['description'], 160) }}</p>
                @if($needsPlan)
                    <a class="btn" href="{{ route('plans') }}">Get Subscription</a>
                @else
                    <a class="btn" href="{{ $playUrl }}">Play Now</a>
                @endif
            @endif
        </div>
    </section>

    <h2>Categories</h2>
    <div class="chips">
        @forelse($categories as $category)
            <a class="chip" href="{{ url('/category/'.$category['id']) }}">{{ $category['name'] }}</a>
        @empty
            <span class="muted">No categories yet.</span>
        @endforelse
    </div>

    @foreach(['New Videos' => $videos, 'Paid' => $paid, 'Free' => $free] as $label => $rail)
        @if($rail->isNotEmpty())
            <h2>{{ $label }}</h2>
            <div class="rail">
                @foreach($rail as $video)
                    @include('site.poster', ['video' => $video])
                @endforeach
            </div>
        @endif
    @endforeach

    @if($bottomBanner)
        <a class="banner-card" href="{{ $featured ? ($needsPlan ? route('plans') : $playUrl) : route('plans') }}">
            <div class="thumb" @if($bottomBanner['image']) style="background-image:url('{{ $bottomBanner['image'] }}')" @endif></div>
            <div class="copy">
                <h2 style="margin:0 0 12px">{{ $bottomBanner['title'] }}</h2>
                <span class="btn">{{ $needsPlan ? 'Get Subscription' : 'Play Now' }}</span>
            </div>
        </a>
    @endif
@endsection
