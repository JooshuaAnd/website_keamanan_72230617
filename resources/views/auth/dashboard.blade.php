@extends('layouts.auth')

@section('title', 'Dashboard - FastAPI Auth')

@section('content')
<div class="mt-10">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Welcome Back, <span class="text-red-500">{{ $user['full_name'] ?? $user['email'] }}</span></h1>
            <p class="text-gray-400">Manage your account and profile settings.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="glass p-8 rounded-3xl col-span-2">
            <h3 class="text-xl font-bold text-white mb-6">Profile Information</h3>
            <div class="space-y-4">
                <div class="flex justify-between border-b border-white/5 pb-4">
                    <span class="text-gray-500">Full Name</span>
                    <span class="text-white font-medium">{{ $user['full_name'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between border-b border-white/5 pb-4">
                    <span class="text-gray-500">Email Address</span>
                    <span class="text-white font-medium">{{ $user['email'] }}</span>
                </div>
                <div class="flex justify-between border-b border-white/5 pb-4">
                    <span class="text-gray-500">Status</span>
                    <span class="bg-green-500/10 text-green-500 px-3 py-1 rounded-full text-xs font-bold uppercase">
                        {{ $user['is_verified'] ? 'Verified' : 'Unverified' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Role</span>
                    <span class="bg-{{ ($user['is_superuser'] ?? false) ? 'purple' : 'blue' }}-500/10 text-{{ ($user['is_superuser'] ?? false) ? 'purple' : 'blue' }}-500 px-3 py-1 rounded-full text-xs font-bold uppercase">
                        {{ ($user['is_superuser'] ?? false) ? 'Administrator' : 'Standard User' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="glass p-8 rounded-3xl">
            <h3 class="text-xl font-bold text-white mb-6">Account Summary</h3>
            <div class="bg-red-600/10 border border-red-500/20 p-4 rounded-2xl mb-6">
                <p class="text-sm text-red-500 font-medium mb-1">Security Level</p>
                <p class="text-2xl font-bold text-white">High</p>
            </div>
            
            @if($user['is_superuser'] ?? false)
            <div class="mb-4">
                <a href="{{ route('admin') }}" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center font-bold py-3 rounded-xl transition shadow-lg shadow-red-900/20">
                    VIEW SYSTEM DATABASE
                </a>
            </div>
            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-white/5 hover:bg-white/10 text-white font-bold py-3 rounded-xl transition">
                    Sign Out
                </button>
            </form>
        </div>
    </div>

    <!-- Raw Data Section -->
    <div class="mt-8 glass p-8 rounded-3xl">
        <h3 class="text-xl font-bold text-white mb-6">User Data (Raw JSON)</h3>
        <div class="bg-black/50 p-6 rounded-2xl border border-white/10 font-mono text-xs text-green-400 overflow-x-auto whitespace-pre">
            <pre>{{ json_encode($user, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endsection
