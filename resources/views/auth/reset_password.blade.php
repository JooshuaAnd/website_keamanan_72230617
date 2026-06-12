@extends('layouts.auth')

@section('title', 'Reset Password - FastAPI Auth')

@section('content')
<div class="max-w-md mx-auto mt-20">
    <div class="glass p-10 rounded-3xl">
        <h2 class="text-3xl font-bold text-white mb-2 text-center">New Password</h2>
        <p class="text-gray-400 mb-8 text-sm text-center">Please enter your new password below.</p>
        
        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">New Password</label>
                <input type="password" name="password" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition">
            </div>
            
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-red-900/40">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
