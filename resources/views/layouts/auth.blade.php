<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LMS - Learning Management System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-dots {
            background-image: radial-gradient(#333 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
    <script>
        // Force reload if page is retrieved from back-forward cache or back-forward navigation
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</head>
<body class="bg-[#0a0a0a] text-gray-200 min-h-screen bg-dots">
    <nav class="flex justify-between items-center p-6 max-w-7xl mx-auto">
        <div class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center font-bold text-white shadow-lg shadow-red-900/20">L</div>
            <span class="text-xl font-bold tracking-tight text-white">LMS <span class="text-red-500 italic">Secure</span></span>
        </div>
        <div class="flex space-x-6 text-sm font-medium items-center">
            <a href="/" class="hover:text-red-500 transition">Home</a>
            @if(Session::has('api_token'))
                @php
                    $navRole = session('user_role', 'peserta');
                @endphp
                @if($navRole === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-red-500 transition">Dashboard</a>
                    <a href="{{ route('admin.participants') }}" class="hover:text-red-500 transition">Peserta</a>
                    <a href="{{ route('admin.lecturers') }}" class="hover:text-red-500 transition">Dosen</a>
                    <a href="{{ route('admin.materials') }}" class="hover:text-red-500 transition">Materi</a>
                    <a href="{{ route('profile') }}" class="hover:text-red-500 transition">Profil</a>
                @elseif($navRole === 'dosen')
                    <a href="{{ route('dosen.dashboard') }}" class="hover:text-red-500 transition">Dashboard</a>
                    <a href="{{ route('dosen.materials') }}" class="hover:text-red-500 transition">Materi</a>
                    <a href="{{ route('dosen.upload') }}" class="hover:text-red-500 transition">Upload</a>
                    <a href="{{ route('profile') }}" class="hover:text-red-500 transition">Profil</a>
                @else
                    <a href="{{ route('peserta.dashboard') }}" class="hover:text-red-500 transition">Dashboard</a>
                    <a href="{{ route('peserta.search') }}" class="hover:text-red-500 transition">Cari Materi</a>
                    <a href="{{ route('profile') }}" class="hover:text-red-500 transition">Profil</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white transition">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:text-red-500 transition">Login</a>
                <a href="{{ route('register') }}" class="bg-white text-black px-4 py-2 rounded-full hover:bg-gray-200 transition">Register</a>
            @endif
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6">
        @if(session('success'))
            <div class="max-w-md mx-auto mb-6 bg-green-500/10 border border-green-500/20 text-green-500 px-4 py-3 rounded-xl text-center text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-md mx-auto mb-6 bg-red-500/10 border border-red-500/20 text-red-500 px-4 py-3 rounded-xl text-center text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
