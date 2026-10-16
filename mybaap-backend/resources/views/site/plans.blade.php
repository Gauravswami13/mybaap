@extends('site.layout')
@section('title', 'Plans · MyBaap')
@section('content')
    <h1>Plans</h1>
    @if($current)
        <p class="muted">Active plan: {{ $current }}</p>
    @else
        <p class="muted">No active plan. One plan unlocks every paid video.</p>
    @endif
    <div class="plans">
        @forelse($plans as $plan)
            <article class="plan">
                <h2 style="margin-top:0">{{ $plan->name }}</h2>
                <strong>₹{{ number_format((float) $plan->price, 0) }}</strong>
                <p class="muted">/ {{ $plan->duration_days }} days</p>
                <p class="muted">{{ $plan->description }}</p>
                <p>All paid videos unlock</p>
                <p>{{ $plan->duration_days }} days of access</p>
                @auth
                    <form method="post" action="{{ route('plans.pay', $plan) }}">
                        @csrf
                        <button class="btn" type="submit">Get Subscription</button>
                    </form>
                @else
                    <a class="btn" href="{{ route('login') }}">Get Subscription</a>
                @endauth
            </article>
        @empty
            <p class="muted">No plans yet.</p>
        @endforelse
    </div>
@endsection
