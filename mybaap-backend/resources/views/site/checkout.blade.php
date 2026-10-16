@extends('site.layout')
@section('title', 'Pay · MyBaap')
@section('content')
    <article class="page">
        <h1>{{ $plan->name }}</h1>
        <p class="muted">₹{{ number_format((float) $plan->price, 0) }} for {{ $plan->duration_days }} days.</p>
        <button class="btn" id="pay" type="button">Pay now</button>
        <form id="verify" method="post" action="{{ route('plans.verify', $plan) }}">
            @csrf
            <input type="hidden" name="razorpay_order_id" id="order_id">
            <input type="hidden" name="razorpay_payment_id" id="payment_id">
            <input type="hidden" name="razorpay_signature" id="signature">
        </form>
    </article>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.getElementById('pay').addEventListener('click', function () {
            var checkout = new Razorpay({
                key: @json($order['key_id']),
                amount: @json($order['amount']),
                currency: 'INR',
                name: 'MyBaap',
                order_id: @json($order['order_id']),
                handler: function (response) {
                    document.getElementById('order_id').value = response.razorpay_order_id;
                    document.getElementById('payment_id').value = response.razorpay_payment_id;
                    document.getElementById('signature').value = response.razorpay_signature;
                    document.getElementById('verify').submit();
                }
            });
            checkout.open();
        });
    </script>
@endsection