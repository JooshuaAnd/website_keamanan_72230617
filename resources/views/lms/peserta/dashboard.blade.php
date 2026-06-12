@extends('layouts.auth')

@section('title', 'Dashboard - Peserta LMS')

@section('content')
<div class="mt-10">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Student Dashboard</h1>
            <p class="text-gray-400">Welcome, <span class="text-red-500">{{ $user['full_name'] ?? $user['email'] }}</span></p>
        </div>
    </div>

    <div class="mb-10">
        <div class="glass p-6 rounded-2xl border-l-4 border-blue-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Available Materials</div>
            <div class="text-4xl font-black text-white">{{ count($materials) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('peserta.search') }}" class="glass p-8 rounded-3xl group hover:border-blue-500/50 transition block">
            <h3 class="text-xl font-bold text-white mb-2">Cari Materi</h3>
            <p class="text-gray-400 text-sm">Search for learning materials</p>
        </a>
        <a href="{{ route('peserta.search') }}" class="glass p-8 rounded-3xl group hover:border-green-500/50 transition block">
            <h3 class="text-xl font-bold text-white mb-2">Daftar Materi</h3>
            <p class="text-gray-400 text-sm">Browse all materials</p>
        </a>
        <a href="{{ route('profile') }}" class="glass p-8 rounded-3xl group hover:border-purple-500/50 transition block">
            <h3 class="text-xl font-bold text-white mb-2">Profil</h3>
            <p class="text-gray-400 text-sm">View your profile</p>
        </a>
    </div>

    <!-- Material List -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-white mb-6">Latest Materials</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($materials as $m)
            <div class="glass p-6 rounded-2xl">
                <h3 class="text-lg font-bold text-white mb-2">{{ $m['title'] }}</h3>
                <p class="text-gray-400 text-sm mb-4">{{ Str::limit($m['description'] ?? 'No description', 100) }}</p>
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>{{ $m['creator']['full_name'] ?? 'Unknown' }}</span>
                    <span>{{ substr($m['created_at'], 0, 10) }}</span>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-12">No materials available yet</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
