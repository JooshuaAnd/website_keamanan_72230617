<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - LMS</title>
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
        <div class="w-20 h-20 bg-yellow-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-6xl font-black text-white mb-4">404</h1>
        <h2 class="text-2xl font-bold text-white mb-4">Page Not Found</h2>
        <p class="text-gray-400 mb-8">The page you are looking for does not exist.</p>
        <a href="{{ route('dashboard') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-xl font-bold transition shadow-lg shadow-red-900/40">
            Back to Dashboard
        </a>
    </div>
</body>
</html>
