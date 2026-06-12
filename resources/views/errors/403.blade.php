<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-dots { background-image: radial-gradient(#333 1px, transparent 1px); background-size: 20px 20px; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#0a0a0a] text-gray-200 min-h-screen bg-dots flex items-center justify-center">
    <div class="glass max-w-md mx-auto p-12 rounded-3xl text-center">
        <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9.364-9.364a9 9 0 110 12.728 9 9 0 010-12.728z"></path>
            </svg>
        </div>
        <h1 class="text-6xl font-black text-white mb-4">403</h1>
        <h2 class="text-2xl font-bold text-white mb-4">Forbidden</h2>
        <p class="text-gray-400 mb-8">You do not have permission to access this page.</p>
        <a href="{{ route('dashboard') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-xl font-bold transition shadow-lg shadow-red-900/40">
            Back to Dashboard
        </a>
    </div>
</body>
</html>
