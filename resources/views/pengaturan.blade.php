@extends('layouts.app')

@section('title', 'Pengaturan - TACTIPAD')

@section('content')
<div id="pengaturanMainPage">
    <!-- MAIN SETTINGS CONTENT (max-w-6xl matching other pages) -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        <!-- 1. AUDIO & TEXT-TO-SPEECH (Suara & Pengucapan) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="pb-3 border-b border-slate-100">
                <h2 class="text-lg font-extrabold text-slate-900">Suara & Pengucapan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pengaturan lafal kata, pilihan bahasa, volume suara, dan kecepatan bicara</p>
            </div>

            <!-- Toggle Status Suara -->
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <div>
                    <div class="text-sm font-bold text-slate-800">Status Suara Pengucapan</div>
                    <div id="audioStatusDesc" class="text-xs text-slate-500">Suara aktif saat titik dan kata terbentuk</div>
                </div>
                <button id="toggleAudioBtn" class="lever-press px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer shadow-xs">
                    Suara Aktif
                </button>
            </div>

            <!-- Pilihan Bahasa dengan Custom Dropdown UI (seperti pada Latihan Soal) -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <label class="text-xs font-bold text-slate-700 block">Pilihan Bahasa:</label>
                
                <div class="relative" id="langDropdownContainer">
                    <button type="button" id="langDropdownBtn" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:border-blue-500 text-xs sm:text-sm font-bold text-slate-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition cursor-pointer">
                        <span id="currentLangLabel">Bahasa Indonesia</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" id="langChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Custom Dropdown Menu Panel -->
                    <div id="langDropdownMenu" class="hidden absolute left-0 mt-1.5 w-full rounded-2xl bg-white border border-slate-200 shadow-xl py-2 z-50 text-xs sm:text-sm font-bold text-slate-700">
                        <button type="button" data-lang="id-ID" data-label="Bahasa Indonesia" class="lang-option w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>Bahasa Indonesia</span>
                        </button>
                        <button type="button" data-lang="en-US" data-label="English (US)" class="lang-option w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>English (US)</span>
                        </button>
                        <button type="button" data-lang="en-GB" data-label="English (UK)" class="lang-option w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>English (UK)</span>
                        </button>
                        <button type="button" data-lang="ms-MY" data-label="Bahasa Melayu" class="lang-option w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>Bahasa Melayu</span>
                        </button>
                        <button type="button" data-lang="ar-SA" data-label="Bahasa Arab" class="lang-option w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>Bahasa Arab</span>
                        </button>
                        <button type="button" data-lang="ja-JP" data-label="Bahasa Jepang" class="lang-option w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>Bahasa Jepang</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sliders: Volume Suara & Kecepatan Bicara -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Volume Suara Slider -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">Volume Suara:</span>
                        <span id="volumeValueText" class="font-mono font-bold text-blue-700">100%</span>
                    </div>
                    <input type="range" id="speechVolumeSlider" min="0" max="100" step="5" value="100" class="w-full accent-blue-600 cursor-pointer">
                </div>

                <!-- Kecepatan Bicara Slider (tanpa teks Lambat Normal Cepat) -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">Kecepatan Bicara:</span>
                        <span id="rateValueText" class="font-mono font-bold text-blue-700">1.0x (Normal)</span>
                    </div>
                    <input type="range" id="speechRateSlider" min="0.5" max="1.5" step="0.1" value="1.0" class="w-full accent-blue-600 cursor-pointer">
                </div>

            </div>

            <!-- Test Voice Button -->
            <div class="pt-2 flex justify-end">
                <button id="testVoiceBtn" class="lever-press px-5 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-xs font-bold text-blue-700 border border-blue-200 flex items-center gap-2 transition cursor-pointer shadow-xs">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                    <span>Uji Coba Suara</span>
                </button>
            </div>
        </div>

        <!-- 2. KONEKSI PERANGKAT (Tanpa Icon Bluetooth, Badge Hijau saat terhubung) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="pb-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Koneksi Perangkat</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Hubungkan alat Tactipad dengan website secara nirkabel menggunakan sinyal Bluetooth</p>
                </div>
                <!-- Status Badge: Belum Terhubung (Slate) -> Hijau saat terhubung -->
                <span id="bluetoothBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 transition-colors">
                    Belum Terhubung
                </span>
            </div>

            <!-- Bluetooth Connection Box (Tanpa Box 1,2,3 panduan) -->
            <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-black text-slate-900">
                            Web Bluetooth API
                        </div>
                        <p id="bluetoothStatusDesc" class="text-xs text-slate-500 mt-1">
                            <strong>Aktifkan Bloetooth perangkat anda</strong> dan Klik tombol di samping untuk menghubungkan alat fisik Tactipad ke website!
                        </p>
                    </div>

                    <!-- Button Hubungkan Perangkat -->
                    <button id="connectBluetoothBtn" class="lever-press px-6 py-3 rounded-xl text-xs sm:text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center justify-center gap-2 shrink-0">
                        <span id="bluetoothBtnText">Hubungkan Perangkat</span>
                    </button>
                </div>

                <!-- Console Monitor Log -->
                <div>
                    <label class="text-[11px] font-bold text-slate-500 block mb-1">Monitor Terminal Log Bluetooth:</label>
                    <div id="serialLogBox" class="bg-white border border-slate-300 rounded-xl p-3 h-28 font-mono text-xs text-slate-700 overflow-y-auto leading-relaxed">
                        [System] Siap memindai sinyal Bluetooth alat fisik Tactipad...
                    </div>
                </div>
            </div>

            <!-- Alternative / Firmware Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <div class="text-xs font-bold text-slate-800">Firmware Arduino / ESP32 (BLE & USB)</div>
                    <p class="text-[11px] text-slate-500">Source code sketch C++ siap flash dengan transmisi sinyal Bluetooth BLE, debounce pin, dan reset.</p>
                    <a href="{{ route('tactipad.sketch') }}" class="inline-flex items-center gap-1.5 mt-1 px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh File .ino</span>
                    </a>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                    <div class="text-xs font-bold text-slate-800">Endpoint WiFi & Sinkronisasi</div>
                    <p class="text-[11px] text-slate-500 font-mono">POST /api/tactipad/event</p>
                    <p class="text-[11px] text-slate-500 font-mono">POST /api/tactipad/reset</p>
                    <p class="text-[11px] text-slate-500 font-mono">GET /api/tactipad/stream</p>
                </div>
            </div>
        </div>

        <!-- 3. AKSESIBILITAS & TAMPILAN -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h2 class="text-lg font-extrabold text-slate-900">Aksesibilitas & Tampilan</h2>
                <p class="text-xs text-slate-500">Membantu kenyamanan pengguna dengan tema gelap bernuansa biru</p>
            </div>

            <!-- Mode Kontras / Tema Gelap (Tanpa teks kuning pekat latar hitam) -->
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <div>
                    <div class="text-sm font-bold text-slate-800">Mode Kontras Tinggi</div>
                </div>
                <button id="toggleContrastBtn" class="lever-press px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                    Aktifkan Kontras
                </button>
            </div>
        </div>

    </main>
</div>
@endsection
