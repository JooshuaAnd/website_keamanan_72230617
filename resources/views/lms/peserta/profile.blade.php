@extends('layouts.auth')

@section('title', 'Profil Saya - Peserta LMS')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Profil Saya</h1>
            <p class="text-gray-400">Your account information</p>
        </div>
    </div>

    <div class="glass p-10 rounded-3xl">
        <div class="space-y-6">
            <div class="flex justify-between border-b border-white/10 pb-4">
                <span class="text-gray-500">Nama Lengkap</span>
                <span class="text-white font-medium">{{ $user['full_name'] ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-b border-white/10 pb-4">
                <span class="text-gray-500">Email</span>
                <span class="text-white font-medium">{{ $user['email'] }}</span>
            </div>
            <div class="flex justify-between border-b border-white/10 pb-4">
                <span class="text-gray-500">Role</span>
                <span class="bg-blue-500/10 text-blue-500 px-3 py-1 rounded-full text-xs font-bold uppercase">
                    {{ $user['role'] ?? 'peserta' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                <span class="bg-green-500/10 text-green-500 px-3 py-1 rounded-full text-xs font-bold uppercase">
                    {{ isset($user['is_verified']) && $user['is_verified'] ? 'Verified' : 'Active' }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
