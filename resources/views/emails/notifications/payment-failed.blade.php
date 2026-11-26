@extends('emails.layout')

@section('title', 'Payment Failed')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>We were unable to process your payment.</p>

<div class="error-box">
    <p><strong>Payment Details:</strong></p>
    <p><strong>Plan:</strong> {{ $subscription->plan->name ?? 'N/A' }}</p>
    <p><strong>Amount:</strong> {{ $subscription->plan->currency ?? 'USD' }} {{ number_format($subscription->plan->price ?? 0, 2) }}</p>
</div>

<p>This could be due to:</p>
<ul>
    <li>Insufficient funds</li>
    <li>Expired or invalid payment method</li>
    <li>Bank declining the transaction</li>
</ul>

<p>Please update your payment method to avoid service interruption.</p>

<p class="text-center">
    <a href="{{ route('user.billing') }}" class="btn btn-danger">Update Payment Method</a>
</p>

<p>If you continue to experience issues, please contact your bank or our support team.</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
