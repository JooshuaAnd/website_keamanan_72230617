@extends('layouts.auth')

@section('title', 'Upload Materi - Dosen LMS')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Upload Materi</h1>
            <p class="text-gray-400">Share new learning materials</p>
        </div>
    </div>

    <div class="glass p-10 rounded-3xl">
        <form action="{{ route('dosen.upload.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Judul Materi</label>
                <input type="text" name="title" required maxlength="255"
                       class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" maxlength="1000"
                          class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">File (optional)</label>
                <input type="file" name="file"
                       class="w-full bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition">
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-red-900/40">
                Upload Materi
            </button>
        </form>
    </div>
</div>
@endsection
