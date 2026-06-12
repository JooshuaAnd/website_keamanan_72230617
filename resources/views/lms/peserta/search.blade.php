@extends('layouts.auth')

@section('title', 'Cari Materi - Peserta LMS')

@section('content')
<div class="mt-10">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Cari Materi</h1>
            <p class="text-gray-400">Search for learning materials</p>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl mb-8">
        <form action="{{ route('peserta.search.results') }}" method="GET" class="flex space-x-4">
            <input type="text" name="q" placeholder="Search materials by title or description..."
                   class="flex-1 bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition"
                   maxlength="100" value="{{ request('q') }}" required>
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition">
                Search
            </button>
        </form>
    </div>

    @if(isset($results))
    <div class="glass rounded-3xl overflow-hidden">
        @if(count($results) > 0)
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Title</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Description</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Author</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $m)
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="p-6 text-white font-medium">{{ $m['title'] }}</td>
                    <td class="p-6 text-gray-400 text-sm">{{ Str::limit($m['description'] ?? '-', 100) }}</td>
                    <td class="p-6 text-gray-400">{{ $m['creator']['full_name'] ?? 'Unknown' }}</td>
                    <td class="p-6 text-gray-400 text-sm">{{ substr($m['created_at'], 0, 10) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-12 text-center text-gray-500">Data tidak ditemukan</div>
        @endif
    </div>
    @endif
</div>
@endsection
