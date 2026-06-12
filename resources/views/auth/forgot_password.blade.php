@extends('layouts.auth')

@section('title', 'Forgot Password - FastAPI Auth')

@section('content')
<div class="max-w-md mx-auto mt-20">
    <div class="glass p-10 rounded-3xl text-center">
        <div class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        </div>
        <h2 class="text-3xl font-bold text-white mb-2">Forgot Password</h2>
        <p class="text-gray-400 mb-8 text-sm">Enter your email and we'll send you a link to reset your password.</p>
        
        <form action="{{ route('password.email') }}" method="POST" class="space-y-6 text-left">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition">
            </div>
            
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-red-900/40">
                Send Reset Link
            </button>
        </form>
        
        <p class="mt-8 text-center text-sm text-gray-500">
            Remember your password? <a href="{{ route('login') }}" class="text-red-500 hover:underline">Sign In</a>
        </p>
    </div>
</div>
@endsection
