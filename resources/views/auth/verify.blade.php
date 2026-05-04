@extends('layouts.auth')

@section('title', 'Verify Email - FastAPI Auth')

@section('content')
<div class="max-w-md mx-auto mt-20 text-center">
    <div class="glass p-10 rounded-3xl">
        @if($success)
            <div class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-4">Verified!</h2>
            <p class="text-gray-400 mb-8">Your email has been successfully verified. You can now login.</p>
        @else
            <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-4">Verification Failed</h2>
            <p class="text-gray-400 mb-8">{{ $message }}</p>
        @endif
        
        <a href="{{ route('login') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-3 rounded-xl transition">
            Go to Login
        </a>
    </div>
</div>
@endsection
