@extends('emails.layout')

@section('title', 'Bank Transfer Declined')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>Unfortunately, we were unable to verify your bank transfer payment.</p>

<div class="error-box">
    <p><strong>Payment Details:</strong></p>
    <p><strong>Amount:</strong> {{ $transaction->currency ?? 'USD' }} {{ number_format($transaction->amount, 2) }}</p>
    <p><strong>Transaction ID:</strong> {{ $transaction->transaction_id }}</p>
    @if(isset($reason))
    <p><strong>Reason:</strong> {{ $reason }}</p>
    @endif
</div>

<p>This could be due to:</p>
<ul>
    <li>Payment not received or not matching the expected amount</li>
    <li>Incorrect reference number used</li>
    <li>Transfer to wrong account details</li>
</ul>

<p>Please verify your transfer details and try again, or contact our support team for assistance.</p>

<p class="text-center">
    <a href="{{ route('user.billing') }}" class="btn">View Billing</a>
</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
