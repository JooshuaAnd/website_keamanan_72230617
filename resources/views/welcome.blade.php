@extends('layouts.auth')

@section('title', 'LMS - Learning Management System')

@section('content')
<div class="mt-20 text-center">
    <div class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-wide text-red-500 uppercase bg-red-500/10 rounded-full">
        LMS v1.0 - Secure Coding Practice
    </div>
    <h1 class="text-6xl font-extrabold text-white tracking-tight mb-6">
        Learning <br/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-orange-400">Management System</span>
    </h1>
    <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-10">
        Platform pembelajaran berbasis web dengan sistem autentikasi aman, RBAC, dan proteksi terhadap XSS, SQL Injection, serta Brute Force.
    </p>

    <div class="flex justify-center space-x-4">
        <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl shadow-red-900/40 transition transform hover:-translate-y-1">
            Get Started
        </a>
        <a href="{{ route('login') }}" class="glass px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/10 transition">
            Sign In
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24 text-left">
        <div class="glass p-8 rounded-2xl group hover:border-red-500/50 transition">
            <div class="w-12 h-12 bg-red-500/10 rounded-lg flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Secure Authentication</h3>
            <p class="text-gray-400 leading-relaxed">
                Password hashing dengan bcrypt, brute force protection, session regeneration, dan OWASP ASVS Level 1 compliance.
            </p>
        </div>

        <div class="glass p-8 rounded-2xl group hover:border-red-500/50 transition">
            <div class="w-12 h-12 bg-red-500/10 rounded-lg flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Role Based Access Control</h3>
            <p class="text-gray-400 leading-relaxed">
                Tiga level akses: Admin, Dosen, dan Peserta. Setiap role memiliki hak akses berbeda dengan proteksi 403 Forbidden.
            </p>
        </div>

        <div class="glass p-8 rounded-2xl group hover:border-red-500/50 transition">
            <div class="w-12 h-12 bg-red-500/10 rounded-lg flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">XSS & SQLi Protection</h3>
            <p class="text-gray-400 leading-relaxed">
                Input validation, output sanitization, prepared statements, dan Content Security Policy untuk mencegah serangan XSS dan SQL Injection.
            </p>
        </div>
    </div>
</div>
@endsection
