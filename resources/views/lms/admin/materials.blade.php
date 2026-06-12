@extends('layouts.auth')

@section('title', 'Data Materi - Admin LMS')

@section('content')
<div class="mt-10">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Data Materi</h1>
            <p class="text-gray-400">All learning materials</p>
        </div>
    </div>

    <div class="glass rounded-3xl overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">ID</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Title</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Created By</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $m)
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="p-6 font-mono text-xs text-gray-500">{{ $m['id'] }}</td>
                    <td class="p-6 text-white font-medium">{{ $m['title'] }}</td>
                    <td class="p-6 text-gray-400">{{ $m['creator']['full_name'] ?? 'N/A' }}</td>
                    <td class="p-6 text-gray-400 text-sm">{{ substr($m['created_at'], 0, 10) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-12 text-center text-gray-500">No materials found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
