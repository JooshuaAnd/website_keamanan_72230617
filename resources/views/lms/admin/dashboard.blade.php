@extends('layouts.auth')

@section('title', 'Admin Dashboard - LMS')

@section('content')
<div class="mt-10">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Admin Dashboard</h1>
            <p class="text-gray-400">Welcome, <span class="text-red-500">{{ $user['full_name'] ?? $user['email'] }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="glass p-6 rounded-2xl border-l-4 border-blue-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Total Participants</div>
            <div class="text-4xl font-black text-white">{{ count($participants) }}</div>
        </div>
        <div class="glass p-6 rounded-2xl border-l-4 border-green-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Total Lecturers</div>
            <div class="text-4xl font-black text-white">{{ count($lecturers) }}</div>
        </div>
        <div class="glass p-6 rounded-2xl border-l-4 border-purple-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Total Materials</div>
            <div class="text-4xl font-black text-white">{{ count($materials) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.participants') }}" class="glass p-8 rounded-3xl group hover:border-blue-500/50 transition block">
            <h3 class="text-xl font-bold text-white mb-2">Data Peserta</h3>
            <p class="text-gray-400 text-sm">Manage participant data</p>
        </a>
        <a href="{{ route('admin.lecturers') }}" class="glass p-8 rounded-3xl group hover:border-green-500/50 transition block">
            <h3 class="text-xl font-bold text-white mb-2">Data Dosen</h3>
            <p class="text-gray-400 text-sm">Manage lecturer data</p>
        </a>
        <a href="{{ route('admin.materials') }}" class="glass p-8 rounded-3xl group hover:border-purple-500/50 transition block">
            <h3 class="text-xl font-bold text-white mb-2">Data Materi</h3>
            <p class="text-gray-400 text-sm">Manage learning materials</p>
        </a>
    </div>
</div>
@endsection
