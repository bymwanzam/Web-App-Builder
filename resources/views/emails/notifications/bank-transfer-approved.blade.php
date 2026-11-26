@extends('emails.layout')

@section('title', 'Bank Transfer Approved')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>Great news! Your bank transfer payment has been approved and your subscription is now active.</p>

<div class="success-box">
    <p><strong>Payment Details:</strong></p>
    <p><strong>Amount:</strong> {{ $transaction->currency ?? 'USD' }} {{ number_format($transaction->amount, 2) }}</p>
    <p><strong>Transaction ID:</strong> {{ $transaction->transaction_id }}</p>
    <p><strong>Plan:</strong> {{ $subscription->plan->name ?? 'N/A' }}</p>
    <p><strong>Next Billing:</strong> {{ $subscription->renewal_at ? $subscription->renewal_at->format('M j, Y') : 'N/A' }}</p>
</div>

<p>Your build credits have been added to your account. You can now start creating and building your mobile apps!</p>

<p class="text-center">
    <a href="{{ route('user.apps') }}" class="btn btn-success">Go to Dashboard</a>
</p>

<p>Thank you for your payment!</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
