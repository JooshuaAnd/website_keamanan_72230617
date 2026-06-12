@extends('layouts.auth')

@section('title', 'Dosen Dashboard - LMS')

@section('content')
<div class="mt-10">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Dosen Dashboard</h1>
            <p class="text-gray-400">Welcome, <span class="text-red-500">{{ $user['full_name'] ?? $user['email'] }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="glass p-6 rounded-2xl border-l-4 border-blue-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Total Participants</div>
            <div class="text-4xl font-black text-white">{{ count($participants) }}</div>
        </div>
        <div class="glass p-6 rounded-2xl border-l-4 border-green-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Total Materials</div>
            <div class="text-4xl font-black text-white">{{ count($materials) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('dosen.materials') }}" class="glass p-8 rounded-3xl group hover:border-blue-500/50 transition block">
            <h3 class="text-xl font-bold text-white mb-2">Materi Saya</h3>
            <p class="text-gray-400 text-sm">View and manage your materials</p>
        </a>
        <a href="{{ route('dosen.upload') }}" class="glass p-8 rounded-3xl group hover:border-green-500/50 transition block">
            <h3 class="text-xl font-bold text-white mb-2">Upload Materi</h3>
            <p class="text-gray-400 text-sm">Upload new learning materials</p>
        </a>
    </div>
</div>
@endsection
