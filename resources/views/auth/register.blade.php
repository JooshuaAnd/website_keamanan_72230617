@extends('layouts.auth')

@section('title', 'Create Account - FastAPI Auth')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="glass p-10 rounded-3xl">
        <h2 class="text-3xl font-bold text-white mb-2">Create Account</h2>
        <p class="text-gray-400 mb-8 text-sm">Join us and start your journey today.</p>
        
        <form action="{{ route('register') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Full Name</label>
                <input type="text" name="full_name" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition" placeholder="John Doe">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition" placeholder="name@company.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition" placeholder="••••••••">
            </div>
            
            <button type="submit" class="w-full bg-white hover:bg-gray-200 text-black font-bold py-4 rounded-xl transition shadow-lg">
                Create Account
            </button>
        </form>
        
        <p class="mt-8 text-center text-sm text-gray-500">
            Already have an account? <a href="{{ route('login') }}" class="text-red-500 hover:underline">Sign In</a>
        </p>
    </div>
</div>
@endsection
