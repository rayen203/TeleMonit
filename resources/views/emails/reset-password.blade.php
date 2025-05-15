@extends('layouts.email-layout')

@section('title', 'Reset Your Password')

@section('content')
    <h2>Reset Your Password</h2>
    <p>Hello!</p>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <a href="{{ $url }}" class="button">Reset Password</a>
    <p>This password reset link will expire in 60 minutes.</p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Regards,<br>TELEMONIT</p>
    <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: {{ $url }}</p>
@endsection
