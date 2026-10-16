@extends('site.layout')
@section('title', 'Subscription history · MyBaap')
@section('content')
    <article class="page">
        <h1>Subscription history</h1>
        @forelse($items as $item)
            <div class="plan">
                <h2 style="margin-top:0">{{ $item->plan?->name ?? 'Plan' }}</h2>
                <p>₹{{ number_format((float) ($item->plan?->price ?? 0), 0) }}</p>
                <p class="muted">{{ $item->start_date?->toDateString() }} to {{ $item->end_date?->toDateString() }}</p>
                <p>{{ ucfirst($item->status) }}</p>
            </div>
        @empty
            <p class="muted">No purchases yet.</p>
        @endforelse
        {{ $items->links() }}
    </article>
@endsection
