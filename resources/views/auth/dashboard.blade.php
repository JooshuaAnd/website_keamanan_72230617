@extends('layouts.auth')

@section('title', 'Dashboard - FastAPI Auth')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
    <div class="md:col-span-1">
        <div class="glass p-8 rounded-3xl sticky top-10">
            <div class="w-24 h-24 bg-gradient-to-tr from-red-600 to-orange-400 rounded-2xl mb-6 flex items-center justify-center text-4xl font-bold text-white shadow-xl shadow-red-900/20 mx-auto">
                {{ substr($user['full_name'] ?? 'U', 0, 1) }}
            </div>
            <h2 class="text-2xl font-bold text-white text-center">{{ $user['full_name'] ?? 'User' }}</h2>
            <p class="text-gray-500 text-center text-sm mb-8">{{ $user['email'] }}</p>
            
            <div class="space-y-2">
                <div class="flex justify-between p-4 bg-white/5 rounded-2xl border border-white/10">
                    <span class="text-gray-400">Status</span>
                    <span class="text-green-500 font-bold">Active</span>
                </div>
                <div class="flex justify-between p-4 bg-white/5 rounded-2xl border border-white/10">
                    <span class="text-gray-400">Role</span>
                    <span class="text-{{ ($user['is_superuser'] ?? false) ? 'purple' : 'blue' }}-500 font-bold">
                        {{ ($user['is_superuser'] ?? false) ? 'Administrator' : 'Standard User' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="md:col-span-2 space-y-8">
        <div class="glass p-10 rounded-3xl">
            <h3 class="text-2xl font-bold text-white mb-6">Welcome back to your workspace</h3>
            <p class="text-gray-400 leading-relaxed">
                You have successfully authenticated through our FastAPI backend and are now being managed by this Laravel application.
                This hybrid architecture gives you the speed of FastAPI with the robust ecosystem of Laravel.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-blue-600/20 to-blue-900/40 p-8 rounded-3xl border border-blue-500/20">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h4 class="text-xl font-bold text-white mb-2">Performance</h4>
                <p class="text-blue-200/60 text-sm">Lightning fast responses from our optimized API endpoints.</p>
            </div>
            <div class="bg-gradient-to-br from-red-600/20 to-red-900/40 p-8 rounded-3xl border border-red-500/20">
                <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h4 class="text-xl font-bold text-white mb-2">Security</h4>
                <p class="text-red-200/60 text-sm">Protected by industry-standard JWT and Argon2id hashing.</p>
            </div>
        </div>
    </div>
</div>
@endsection
