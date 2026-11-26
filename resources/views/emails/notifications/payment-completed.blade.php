@extends('emails.layout')

@section('title', 'Payment Received')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>We've received your payment. Thank you!</p>

<div class="success-box">
    <p><strong>Payment Details:</strong></p>
    <p><strong>Amount:</strong> {{ $transaction->currency ?? 'USD' }} {{ number_format($transaction->amount, 2) }}</p>
    <p><strong>Transaction ID:</strong> {{ $transaction->transaction_id }}</p>
    <p><strong>Date:</strong> {{ $transaction->transaction_date->format('M j, Y g:i A') }}</p>
    <p><strong>Payment Method:</strong> {{ $transaction->payment_method }}</p>
</div>

<p>Your build credits have been replenished for this billing cycle.</p>

<p class="text-center">
    <a href="{{ route('user.billing') }}" class="btn">View Billing History</a>
</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
