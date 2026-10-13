<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f9f9ff]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Portal Perusahaan - {{ config('app.name', 'TravelSys') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased font-['Inter'] text-[#111c2d] bg-[#f9f9ff] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Brand Logo -->
        <div class="flex flex-col items-center justify-center gap-1">
            <div class="flex items-center justify-center gap-2.5">
                <img src="{{ asset('images/Artwork.png') }}" alt="Logo TravelSys" class="h-10 w-auto object-contain">
                <img src="{{ asset('images/Wordmark.png') }}" alt="Sistem Manajemen TravelSys" class="h-7 w-auto object-contain">
            </div>
            <span class="text-[10px] uppercase tracking-widest text-[#737780] font-semibold mt-1">SMK PLUS PELITA NUSANTARA &bull; KELOMPOK 6 RPL</span>
        </div>

        <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-[#00254e] font-['Plus_Jakarta_Sans']">
            Masuk ke Akun Perusahaan
        </h2>
        <p class="mt-2 text-center text-xs text-[#737780]">
            Sistem Otorisasi Perjalanan Dinas, Validasi Kebijakan &amp; Rekonsiliasi Keuangan
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-sm border border-[#c3c6d1]/40 rounded-2xl sm:rounded-3xl">
            <!-- Session Status / Errors -->
            @if (session('status'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
                    <div class="font-bold flex items-center gap-1.5 mb-1">
                        <span class="material-symbols-outlined text-[16px]">error</span>
                        <span>Autentikasi Gagal</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Work Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-[#111c2d] mb-1">
                        Alamat Email Kantor
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-[#737780] text-[18px]">mail</span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            value="{{ old('email', 'andi.pratama@travelsys.internal') }}"
                            placeholder="nama@perusahaan.com"
                            class="w-full h-10 pl-9 pr-3 text-xs bg-[#f0f3ff] border border-transparent focus:border-[#00677e] focus:bg-white rounded-xl outline-none transition-all"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-bold text-[#111c2d]">
                            Kata Sandi
                        </label>
                        <a href="#" class="text-[11px] font-semibold text-[#00677e] hover:underline">
                            Lupa kata sandi?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-[#737780] text-[18px]">lock</span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            value="password"
                            placeholder="••••••••••••"
                            class="w-full h-10 pl-9 pr-3 text-xs bg-[#f0f3ff] border border-transparent focus:border-[#00677e] focus:bg-white rounded-xl outline-none transition-all"
                        >
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            checked
                            class="w-4 h-4 rounded text-[#00677e] focus:ring-[#00677e] border-gray-300"
                        >
                        <span class="text-xs text-[#43474f]">Ingat perangkat ini (30 hari)</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full h-11 bg-[#00677e] hover:bg-[#005366] text-white font-bold text-xs rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[18px]">login</span>
                        <span>Masuk ke Dasbor</span>
                    </button>
                </div>
            </form>

            <!-- Demo Quick Login Presets -->
            <div class="mt-6 pt-5 border-t border-[#c3c6d1]/30">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-bold text-[#737780] uppercase tracking-wider">
                        Pilih Cepat Akun Demo (Batas Waterfall)
                    </p>
                    <span class="text-[10px] text-[#00677e] font-semibold">Matriks Batas</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        onclick="document.getElementById('email').value='andi.pratama@travelsys.internal'; document.getElementById('password').value='password';"
                        class="p-2 text-left rounded-lg bg-[#f0f3ff] hover:bg-[#dfe8ff] text-[11px] transition-colors border border-transparent hover:border-[#c3c6d1]/50 cursor-pointer"
                    >
                        <p class="font-bold text-[#00254e]">Andi Pratama</p>
                        <p class="text-[#737780] text-[10px]">Karyawan (Band 2)</p>
                        <span class="inline-block mt-1 text-[9px] px-1.5 py-0.2 rounded bg-gray-200 text-gray-700 font-semibold">Pemohon</span>
                    </button>
                    <button
                        type="button"
                        onclick="document.getElementById('email').value='siti.rahma@travelsys.internal'; document.getElementById('password').value='password';"
                        class="p-2 text-left rounded-lg bg-[#e8f5e9] hover:bg-[#c8e6c9] text-[11px] transition-colors border border-emerald-200 cursor-pointer"
                    >
                        <p class="font-bold text-[#1b5e20]">Siti Rahma</p>
                        <p class="text-[#2e7d32] text-[10px]">Line Manager (Band 4)</p>
                        <span class="inline-block mt-1 text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold">Tier 1: &le; Rp 5 Jt</span>
                    </button>
                    <button
                        type="button"
                        onclick="document.getElementById('email').value='budi.santoso@travelsys.internal'; document.getElementById('password').value='password';"
                        class="p-2 text-left rounded-lg bg-[#e3f2fd] hover:bg-[#bbdefb] text-[11px] transition-colors border border-blue-200 cursor-pointer"
                    >
                        <p class="font-bold text-[#0d47a1]">Budi Santoso</p>
                        <p class="text-[#1565c0] text-[10px]">Finance Director (Band 5)</p>
                        <span class="inline-block mt-1 text-[9px] px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-bold">Tier 2: &le; Rp 15 Jt</span>
                    </button>
                    <button
                        type="button"
                        onclick="document.getElementById('email').value='hendra.gunawan@travelsys.internal'; document.getElementById('password').value='password';"
                        class="p-2 text-left rounded-lg bg-[#f3e5f5] hover:bg-[#e1bee7] text-[11px] transition-colors border border-purple-200 cursor-pointer"
                    >
                        <p class="font-bold text-[#4a148c]">Hendra Gunawan</p>
                        <p class="text-[#6a1b9a] text-[10px]">Managing Director / CEO</p>
                        <span class="inline-block mt-1 text-[9px] px-1.5 py-0.2 rounded bg-purple-100 text-purple-800 font-bold">Tier 3: &gt; Rp 15 Jt</span>
                    </button>
                </div>
            </div>

            <!-- Footer: Register Link -->
            <p class="mt-6 text-center text-xs text-[#737780]">
                Pegawai baru di perusahaan?
                <a href="{{ route('register') }}" class="font-bold text-[#00677e] hover:underline ml-1">
                    Daftar profil karyawan
                </a>
            </p>
        </div>

        <div class="mt-6 text-center text-[11px] text-[#737780] flex items-center justify-center gap-3">
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">lock</span> Enkripsi TLS 256-bit</span>
            <span>&bull;</span>
            <span>Mesin Kebijakan v2026.4</span>
            <span>&bull;</span>
            <span>Tersertifikasi Audit</span>
        </div>
    </div>
</body>
</html>
