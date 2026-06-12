@extends('layouts.auth')

@section('title', 'Data Peserta - Admin LMS')

@section('content')
<div class="mt-10">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Data Peserta</h1>
            <p class="text-gray-400">Manage all participant records</p>
        </div>
    </div>

    <!-- Search with XSS Protection -->
    <div class="glass p-6 rounded-2xl mb-8">
        <form id="searchForm" class="flex space-x-4">
            <input type="text" id="searchInput" placeholder="Search by name, NIM, class, or study program..."
                   class="flex-1 bg-[#151515] border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-red-500 outline-none transition"
                   maxlength="100">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition">
                Search
            </button>
        </form>
        <div id="searchError" class="text-red-500 text-sm mt-2 hidden"></div>
    </div>

    <div class="glass rounded-3xl overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">ID</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">NIM</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Nama</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Kelas</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Program Studi</th>
                    <th class="p-6 text-gray-500 uppercase text-xs font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody id="participantsBody">
                @forelse($participants as $p)
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="p-6 font-mono text-xs text-gray-500">{{ $p['id'] }}</td>
                    <td class="p-6 text-white font-mono text-sm">{{ $p['nim'] }}</td>
                    <td class="p-6 text-white font-medium">{{ $p['user']['full_name'] ?? 'N/A' }}</td>
                    <td class="p-6 text-gray-400">{{ $p['kelas'] }}</td>
                    <td class="p-6 text-gray-400">{{ $p['program_studi'] }}</td>
                    <td class="p-6">
                        <form action="{{ route('admin.participants.delete', $p['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-12 text-center text-gray-500">No participants found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const q = document.getElementById('searchInput').value.trim();
    const errorDiv = document.getElementById('searchError');
    errorDiv.classList.add('hidden');

    if (!q || q.length > 100) return;

    try {
        const response = await fetch('{{ route("admin.participants.search") }}?q=' + encodeURIComponent(q));
        const data = await response.json();
        const tbody = document.getElementById('participantsBody');

        if (!response.ok || data.error) {
            errorDiv.textContent = 'Input tidak valid';
            errorDiv.classList.remove('hidden');
            return;
        }

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center text-gray-500">Data tidak ditemukan</td></tr>';
            return;
        }

        tbody.innerHTML = data.map(p => `
            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                <td class="p-6 font-mono text-xs text-gray-500">${p.id}</td>
                <td class="p-6 text-white font-mono text-sm">${escapeHtml(p.nim)}</td>
                <td class="p-6 text-white font-medium">${escapeHtml(p.user?.full_name || 'N/A')}</td>
                <td class="p-6 text-gray-400">${escapeHtml(p.kelas)}</td>
                <td class="p-6 text-gray-400">${escapeHtml(p.program_studi)}</td>
                <td class="p-6">
                    <form action="/admin/participants/${p.id}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta ini?')">
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        errorDiv.textContent = 'Input tidak valid';
        errorDiv.classList.remove('hidden');
    }
});

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
</script>
@endsection
