@extends('layouts.auth')

@section('title', 'Email Verification - FastAPI Auth')

@section('content')
<div class="max-w-md mx-auto mt-20">
    <div class="glass p-10 rounded-3xl text-center">
        @if($status === 'pending')
            <div class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 text-red-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Verify Your Email</h2>
            <p class="text-gray-400 mb-8 text-sm">Please click the button below to confirm your email address and activate your account.</p>
            
            <form action="{{ route('verify.confirm') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <button type="submit" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-red-900/40">
                    Confirm Verification
                </button>
            </form>
        @else
            @if($success)
                <div class="w-16 h-16 bg-green-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Email Verified</h2>
                <p class="text-gray-400 mb-8 text-sm">Your account has been successfully verified. You can now access all features.</p>
                <a href="{{ route('login') }}" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-red-900/40">
                    Sign In Now
                </a>
            @else
                <div class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Verification Failed</h2>
                <p class="text-gray-400 mb-8 text-sm">{{ $message ?? 'The verification link is invalid or has expired.' }}</p>
                <a href="{{ route('login') }}" class="block w-full bg-white/5 hover:bg-white/10 text-white font-bold py-4 rounded-xl transition">
                    Back to Login
                </a>
            @endif
        @endif
    </div>
</div>
@endsection
