@extends('layouts.auth')

@section('title', 'Forgot Password - FastAPI Auth')

@section('content')
<div class="max-w-md mx-auto mt-20">
    <div class="glass p-10 rounded-3xl">
        <h2 class="text-3xl font-bold text-white mb-2">Reset Password</h2>
        <p class="text-gray-400 mb-8 text-sm">Enter your email and we'll send you a link to reset your password.</p>
        
        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition">
            </div>
            
            <button type="submit" class="w-full bg-white hover:bg-gray-200 text-black font-bold py-4 rounded-xl transition shadow-lg">
                Send Reset Link
            </button>
        </form>
        
        <p class="mt-8 text-center text-sm text-gray-500">
            Remembered your password? <a href="{{ route('login') }}" class="text-red-500 hover:underline">Login</a>
        </p>
    </div>
</div>
@endsection
