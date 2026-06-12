@extends('layouts.auth')

@section('title', 'Materi Saya - Dosen LMS')

@section('content')
<div class="mt-10">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Materi Saya</h1>
            <p class="text-gray-400">Your uploaded learning materials</p>
        </div>
        <a href="{{ route('dosen.upload') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition">
            + Upload New
        </a>
    </div>

    <div class="glass rounded-3xl overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">ID</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Title</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Description</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $m)
                @if($m['created_by'] == $user['id'])
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="p-6 font-mono text-xs text-gray-500">{{ $m['id'] }}</td>
                    <td class="p-6 text-white font-medium">{{ $m['title'] }}</td>
                    <td class="p-6 text-gray-400 text-sm max-w-xs truncate">{{ $m['description'] ?? '-' }}</td>
                    <td class="p-6 text-gray-400 text-sm">{{ substr($m['created_at'], 0, 10) }}</td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="4" class="p-12 text-center text-gray-500">No materials yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
