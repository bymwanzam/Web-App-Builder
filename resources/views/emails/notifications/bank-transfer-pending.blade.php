@extends('emails.layout')

@section('title', 'Bank Transfer Pending Approval')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>We have received your bank transfer payment request. Your subscription will be activated once we confirm the payment.</p>

<div class="warning-box">
    <p><strong>Payment Details:</strong></p>
    <p><strong>Amount:</strong> {{ $transaction->currency ?? 'USD' }} {{ number_format($transaction->amount, 2) }}</p>
    <p><strong>Transaction ID:</strong> {{ $transaction->transaction_id }}</p>
    <p><strong>Date:</strong> {{ $transaction->created_at->format('M j, Y g:i A') }}</p>
</div>

@if(isset($transaction->metadata['bank_transfer_instructions']))
<div class="info-box">
    <p><strong>Bank Transfer Instructions:</strong></p>
    <p>{!! nl2br(e($transaction->metadata['bank_transfer_instructions'])) !!}</p>
</div>
@endif

<p>Please ensure you have completed the transfer according to the instructions provided. Once we verify the payment, your subscription will be activated and you'll receive a confirmation email.</p>

<p class="text-center">
    <a href="{{ route('user.billing') }}" class="btn">View Billing</a>
</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
