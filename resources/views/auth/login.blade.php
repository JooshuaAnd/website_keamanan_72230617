@extends('layouts.auth')

@section('title', 'Login - FastAPI Auth')

@section('content')
<div class="max-w-md mx-auto mt-20">
    <div class="glass p-10 rounded-3xl">
        <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
        <p class="text-gray-400 mb-8 text-sm">Please enter your credentials to login.</p>
        
        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition" value="{{ old('email') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition">
            </div>
            
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-red-900/40">
                Sign In
            </button>
        </form>
        
        <p class="mt-8 text-center text-sm text-gray-500">
            Don't have an account? <a href="{{ route('register') }}" class="text-red-500 hover:underline">Register</a><br/>
            <a href="{{ route('password.request') }}" class="text-gray-400 hover:text-white transition mt-2 inline-block">Forgot Password?</a>
        </p>
    </div>
</div>
@endsection
