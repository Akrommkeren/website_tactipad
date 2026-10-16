@extends('layouts.app')

@section('title', 'Tentang & Kamus Braille - TACTIPAD')

@section('content')
<div id="kamusMainPage">

    <!-- MAIN KAMUS CONTENT -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        <!-- SECTION 1: PENJELASAN SINGKAT TENTANG ALAT TACTIPAD -->
        <section class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-4">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-blue-600 tracking-tight">TACTIPAD</h2>
                    <span class="text-xs text-blue-700 font-bold uppercase tracking-wider">Papan Taktil Interaktif untuk Pembelajaran Tunanetra</span>
                </div>
                <div class="shrink-0">
                    <img src="{{ asset('images/Icon-Tactipad.png') }}" alt="Logo Tactipad" class="h-10 w-auto object-contain">
                </div>
            </div>

            <p class="text-sm text-slate-700 leading-relaxed">
                <strong>TACTIPAD</strong> merupakan media pembelajaran taktil interaktif yang dirancang untuk membantu penyandang tunanetra mengenali pola huruf Braille melalui indra peraba. Perangkat ini memiliki titik-titik taktil yang dapat ditekan untuk membentuk pola secara langsung, serta dilengkapi <strong>sistem reset elektromagnetik</strong> yang mengembalikan seluruh titik ke posisi awal secara masal sehingga perangkat dapat digunakan berulang kali secara efisien.
            </p>

            <!-- 4 Fitur Kunci Singkat (Tanpa Subteks) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-center flex items-center justify-center">
                    <span class="font-bold text-slate-900">30 Titik Taktil</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-center flex items-center justify-center">
                    <span class="font-bold text-slate-900">Dua Sisi Kerja</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-center flex items-center justify-center">
                    <span class="font-bold text-slate-900">Reset Elektromagnetik</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-center flex items-center justify-center">
                    <span class="font-bold text-slate-900">Integrasi Website</span>
                </div>
            </div>
        </section>

        <!-- SECTION 2: CARA PENGGUNAAN ALAT -->
        <section class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">
            
            <!-- Header Cara Penggunaan Alat (Ukuran sama seperti Papan Tactipad di menu Braille) -->
            <div class="pb-4 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900">Cara Penggunaan Alat</h2>
            </div>

            <!-- CARA KERJA & PENGOPERASIAN ALAT FISIK TACTIPAD (MENURUN KE BAWAH POIN 1-4) -->
            <div class="space-y-4">
                
                <!-- Poin 1: Menggunakan TACTIPAD -->
                <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50">
                    <div class="flex items-start gap-3.5">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <div class="space-y-2.5 flex-1">
                            <h4 class="text-sm sm:text-base font-bold text-slate-900">Menggunakan TACTIPAD</h4>
                            <ol class="text-xs sm:text-sm text-slate-600 space-y-2 list-decimal list-outside pl-4 leading-relaxed">
                                <li>Pengguna dapat menggunakan area taktil untuk mengenali pola yang terdapat pada permukaan TACTIPAD.</li>
                                <li>Rabalah permukaan secara perlahan menggunakan ujung jari untuk mengenali susunan titik taktil.</li>
                                <li>Gunakan jari atau stylus yang telah disediakan untuk menekan titik pada area taktil hingga membentuk pola braille yang diinginkan.</li>
                                <li>Setelah pola braille selesai disusun, balik atau ubah sisi TACTIPAD, kemudian rabalah permukaannya untuk mengenali dan membaca pola braille yang telah dibuat.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Poin 2: Setelah Selesai Digunakan -->
                <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50">
                    <div class="flex items-start gap-3.5">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <div class="space-y-2 flex-1">
                            <h4 class="text-sm sm:text-base font-bold text-slate-900">Setelah Selesai Digunakan</h4>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                Setelah proses pembelajaran selesai, titik taktil dapat dikembalikan ke posisi awal menggunakan sistem reset elektromagnetik.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Poin 3: Mengaktifkan Sistem Reset -->
                <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50">
                    <div class="flex items-start gap-3.5">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <div class="space-y-2.5 flex-1">
                            <h4 class="text-sm sm:text-base font-bold text-slate-900">Mengaktifkan Sistem Reset</h4>
                            <ol class="text-xs sm:text-sm text-slate-600 space-y-2 list-decimal list-outside pl-4 leading-relaxed">
                                <li>Pastikan tangan tidak berada pada area titik taktil.</li>
                                <li>Geser tombol/sakelar ke posisi ON.</li>
                                <li>Sistem reset elektromagnetik akan bekerja secara otomatis.</li>
                                <li>Tunggu hingga seluruh titik taktil kembali ke posisi awal.</li>
                                <li>Setelah proses reset selesai, geser tombol/sakelar ke posisi OFF.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Poin 4: Prosedur Pengisian Baterai -->
                <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50 space-y-4">
                    <div class="flex items-start gap-3.5">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">4</span>
                        <div class="space-y-2.5 flex-1">
                            <h4 class="text-sm sm:text-base font-bold text-slate-900">Prosedur Pengisian Baterai</h4>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                TACTIPAD menggunakan baterai yang dapat diisi ulang (<em>rechargeable battery</em>) sebagai sumber daya sistem reset elektromagnetik.
                            </p>
                            <ol class="text-xs sm:text-sm text-slate-600 space-y-2 list-decimal list-outside pl-4 leading-relaxed">
                                <li>Pastikan sistem reset dalam posisi OFF.</li>
                                <li>Hubungkan kabel pengisi daya ke port charging TACTIPAD.</li>
                                <li>Hubungkan ujung kabel lainnya ke sumber daya yang sesuai.</li>
                                <li>Tunggu hingga baterai terisi penuh.</li>
                                <li>Setelah pengisian selesai, lepaskan kabel pengisi daya.</li>
                                <li>TACTIPAD dapat digunakan kembali untuk melakukan reset.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Tombol Dokumen Manual Book (Drive) di Bawah Poin 4 -->
                    <div class="pt-3 border-t border-slate-200/80 flex items-center justify-between flex-wrap gap-3">
                        <span class="text-xs text-slate-500 font-medium">Akses dokumen lengkap alat melalui tautan Google Drive:</span>
                        <a href="{{ $manualBookDriveUrl ?: 'https://drive.google.com/file/d/1ZkO3Y0pwead-vSw-gKUJjwnXuMM_Y6Ff/view?usp=drive_link' }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="lever-press inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs cursor-pointer group">
                            <span>Dokumen Manual Book</span>
                            <svg class="w-3.5 h-3.5 text-blue-200 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>

            </div>
        </section>

        <!-- SECTION 3: INTEGRASI ALAT DAN WEBSITE (BOX TERPISAH) -->
        <section class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">
            
            <div class="pb-4 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900">Integrasi Alat dan Website</h2>
            </div>

            <!-- 4 Kartu Fitur Integrasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <div class="font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Area Taktil (30 Titik)
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Papan utama berisi 5 sel huruf Braille. Sisi Tulis digunakan untuk menekan titik, dan Sisi Baca menampilkan tonjolan timbul untuk diraba jari.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <div class="font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Sakelar Reset Elektromagnetik
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Sakelar geser di sisi alat untuk mengembalikan seluruh 30 titik secara serentak ke posisi awal saat kata selesai dipelajari.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <div class="font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Sinkronisasi Visual Web
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Layar website menampilkan lingkaran kosong (<span class="font-bold">○</span>) yang seketika berubah hitam pekat (<span class="font-bold">●</span>) saat titik pada alat ditekan.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <div class="font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Konfirmasi Suara (TTS)
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Sistem audio pintar otomatis melafalkan nama huruf dan mengeja kata Bahasa Indonesia setiap kali pola titik selesai dibentuk.
                    </p>
                </div>
            </div>

            <!-- Cara Penggunaan Website (Menurun ke Bawah Poin 1-5) -->
            <div class="space-y-4 pt-2">
                <div class="pb-3 border-b border-slate-100">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">Cara Penggunaan Website</h3>
                </div>

                <div class="space-y-4">
                    <!-- Poin 1: Koneksi Alat -->
                    <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50">
                        <div class="flex items-start gap-3.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <div class="space-y-2.5 flex-1">
                                <h4 class="text-sm sm:text-base font-bold text-slate-900">Koneksi Alat</h4>
                                <ol class="text-xs sm:text-sm text-slate-600 space-y-2 list-decimal list-outside pl-4 leading-relaxed">
                                    <li>Jika memiliki alat fisik, sambungkan kabel USB alat ke perangkat atau laptop, buka menu <strong>Pengaturan</strong>, lalu klik tombol <strong>Hubungkan Perangkat</strong>.</li>
                                    <li>Jika belum memiliki alat fisik, Anda tetap dapat belajar langsung dengan mengklik lingkaran titik taktil pada halaman menu <strong>Braille</strong> di website.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Poin 2: Sisi Baca & Konfirmasi Suara -->
                    <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50">
                        <div class="flex items-start gap-3.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <div class="space-y-2.5 flex-1">
                                <h4 class="text-sm sm:text-base font-bold text-slate-900">Sisi Baca &amp; Konfirmasi Suara</h4>
                                <ol class="text-xs sm:text-sm text-slate-600 space-y-2 list-decimal list-outside pl-4 leading-relaxed">
                                    <li>Balikkan atau rabalah permukaan pada <strong>Sisi Baca</strong> untuk mengenali tonjolan timbul yang baru saja terbentuk.</li>
                                    <li>Dengarkan konfirmasi audio pintar dari perangkat yang otomatis melafalkan nama huruf dan posisi sel yang sedang disusun.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Poin 3: Rangkai Kata -->
                    <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50">
                        <div class="flex items-start gap-3.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <div class="space-y-2.5 flex-1">
                                <h4 class="text-sm sm:text-base font-bold text-slate-900">Rangkai Kata</h4>
                                <ol class="text-xs sm:text-sm text-slate-600 space-y-2 list-decimal list-outside pl-4 leading-relaxed">
                                    <li>Lanjutkan mengisi sel berikutnya (Sel 1 hingga Sel 5) untuk menyusun kata lengkap (contoh: <strong>B - U - K - U</strong>).</li>
                                    <li>Tekan tombol <kbd class="px-1.5 py-0.5 rounded bg-slate-200 font-mono font-bold text-slate-800">Spasi</kbd> pada keyboard atau klik tombol <strong>Simpan Kata</strong> untuk memasukkan kata ke dalam kalimat cerita.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Poin 4: Reset Masal -->
                    <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50">
                        <div class="flex items-start gap-3.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">4</span>
                            <div class="space-y-2.5 flex-1">
                                <h4 class="text-sm sm:text-base font-bold text-slate-900">Reset Masal</h4>
                                <ol class="text-xs sm:text-sm text-slate-600 space-y-2 list-decimal list-outside pl-4 leading-relaxed">
                                    <li>Setelah kata selesai disusun, pastikan tangan atau jari terangkat dari area taktil.</li>
                                    <li>Geser sakelar reset ke posisi <strong>ON</strong> pada alat atau tekan tombol <kbd class="px-1.5 py-0.5 rounded bg-slate-200 font-mono font-bold text-slate-800">R</kbd> di keyboard agar seluruh 30 titik kembali rata seketika.</li>
                                    <li>Geser sakelar kembali ke posisi <strong>OFF</strong> untuk memulai penyusunan kata berikutnya.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Poin 5: Latihan Kuis -->
                    <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50">
                        <div class="flex items-start gap-3.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">5</span>
                            <div class="space-y-2.5 flex-1">
                                <h4 class="text-sm sm:text-base font-bold text-slate-900">Latihan Kuis</h4>
                                <ol class="text-xs sm:text-sm text-slate-600 space-y-2 list-decimal list-outside pl-4 leading-relaxed">
                                    <li>Setelah memahami dasar penggunaan, buka menu <strong>Latihan Soal</strong> untuk mengasah ingatan lewat kuis interaktif.</li>
                                    <li>Pilih kategori soal yang sesuai kebutuhan: <strong>Semua Soal</strong>, <strong>Soal Huruf</strong> (Huruf A-Z atau Kata 5 Huruf), atau <strong>Soal Angka</strong> (Angka 0-9 atau Angka 5 Digit).</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner Mulai Praktik -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <span class="text-slate-700 font-medium">Sudah siap mencoba? Mulai tulis kata Braille pertama kamu sekarang di papan interaktif!</span>
                <a href="{{ route('tactipad.home') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <span>Mulai Menulis di Braille</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

        </section>

        <!-- SECTION 4: KAMUS BRAILLE -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Kamus Braille</h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">
                        Panduan referensi lengkap pola titik Braille standar 6 titik (2 kolom x 3 baris). Klik kartu untuk mendengarkan lafal suaranya.
                    </p>
                </div>

                <!-- Custom Filter Dropdown (Model disesuaikan dengan dropdown lainnya) -->
                <div class="relative" id="filterDropdownContainer">
                    <button type="button" id="filterDropdownBtn" class="inline-flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:border-blue-500 text-xs sm:text-sm font-bold text-slate-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition cursor-pointer min-w-[180px]">
                        <span id="currentFilterLabel">Semua Kategori</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" id="filterChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu Panel -->
                    <div id="filterDropdownMenu" class="hidden absolute right-0 mt-1.5 w-52 rounded-2xl bg-white border border-slate-200 shadow-xl py-2 z-50 text-xs sm:text-sm font-bold text-slate-700">
                        <button type="button" data-val="all" data-label="Semua Kategori" class="filter-item w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>Semua Kategori</span>
                        </button>
                        <button type="button" data-val="alphabet" data-label="Alfabet (A - Z)" class="filter-item w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>Alfabet (A - Z)</span>
                        </button>
                        <button type="button" data-val="numbers" data-label="Angka (0 - 9)" class="filter-item w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>Angka (0 - 9)</span>
                        </button>
                        <button type="button" data-val="punctuation" data-label="Tanda Baca &amp; Simbol" class="filter-item w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer flex items-center justify-between">
                            <span>Tanda Baca &amp; Simbol</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. ALFABET (A - Z) -->
        <section id="sectionAlphabet" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">
                    Alfabet Braille (A - Z)
                </h3>
                <span class="text-xs text-slate-500 font-medium">26 Huruf Standar</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5" id="gridAlphabet">
                @foreach($dictionary['alphabet'] as $char => $item)
                <div data-char="{{ $char }}" data-name="{{ $item['name'] }}" data-pronounce="{{ $item['pronounce'] }}" class="kamus-card bg-white rounded-2xl border border-slate-200 p-4 shadow-xs hover:border-blue-500 hover:shadow-md transition cursor-pointer flex flex-col justify-between group">
                    <div class="flex items-start justify-between">
                        <span class="text-2xl font-black text-slate-900 group-hover:text-blue-600 transition">{{ $char }}</span>
                        <span class="text-3xl font-mono text-blue-600 select-all">{{ $item['unicode'] }}</span>
                    </div>

                    <!-- Mini 6-Dot Preview Matrix -->
                    <div class="my-3 py-2 px-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-1.5">
                            @for($d = 1; $d <= 3; $d++)
                                <span class="w-3.5 h-3.5 rounded-full {{ in_array($d, $item['dots']) ? 'bg-black border border-black' : 'border border-slate-300 bg-white' }}"></span>
                                <span class="w-3.5 h-3.5 rounded-full {{ in_array($d+3, $item['dots']) ? 'bg-black border border-black' : 'border border-slate-300 bg-white' }}"></span>
                            @endfor
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t border-slate-100">
                        <span class="font-medium text-slate-500">Titik {{ implode(', ', $item['dots']) }}</span>
                        <button class="p-1 text-slate-400 hover:text-blue-600" title="Dengarkan Suara">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- 2. ANGKA (0 - 9) -->
        <section id="sectionNumbers" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">
                    Angka Braille (0 - 9)
                </h3>
                <span class="text-xs text-slate-500 font-medium">Didahului Tanda Angka (Titik 3-4-5-6)</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5" id="gridNumbers">
                @foreach($dictionary['numbers'] as $num => $item)
                <div data-char="{{ $num }}" data-name="{{ $item['name'] }}" data-pronounce="{{ $item['pronounce'] }}" class="kamus-card bg-white rounded-2xl border border-slate-200 p-4 shadow-xs hover:border-blue-500 hover:shadow-md transition cursor-pointer flex flex-col justify-between group">
                    <div class="flex items-start justify-between">
                        <span class="text-2xl font-black text-slate-900 group-hover:text-blue-600 transition">{{ $num }}</span>
                        <span class="text-3xl font-mono text-blue-600 select-all">{{ $item['unicode'] }}</span>
                    </div>

                    <!-- Mini 6-Dot Matrix -->
                    <div class="my-3 py-2 px-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-1.5">
                            @for($d = 1; $d <= 3; $d++)
                                <span class="w-3.5 h-3.5 rounded-full {{ in_array($d, $item['dots']) ? 'bg-black border border-black' : 'border border-slate-300 bg-white' }}"></span>
                                <span class="w-3.5 h-3.5 rounded-full {{ in_array($d+3, $item['dots']) ? 'bg-black border border-black' : 'border border-slate-300 bg-white' }}"></span>
                            @endfor
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t border-slate-100">
                        <span class="font-medium text-slate-500">Titik {{ implode(', ', $item['dots']) }}</span>
                        <button class="p-1 text-slate-400 hover:text-blue-600" title="Dengarkan Suara">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- 3. TANDA BACA & SIMBOL -->
        <section id="sectionPunctuation" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">
                    Tanda Baca & Simbol
                </h3>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5" id="gridPunctuation">
                @foreach($dictionary['punctuation'] as $sym => $item)
                <div data-char="{{ $sym }}" data-name="{{ $item['name'] }}" data-pronounce="{{ $item['pronounce'] }}" class="kamus-card bg-white rounded-2xl border border-slate-200 p-4 shadow-xs hover:border-blue-500 hover:shadow-md transition cursor-pointer flex flex-col justify-between group">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xl font-black text-slate-900 block group-hover:text-blue-600 transition">{{ $sym }}</span>
                            <span class="text-[11px] text-slate-500 font-medium">{{ $item['name'] }}</span>
                        </div>
                        <span class="text-3xl font-mono text-blue-600 select-all">{{ $item['unicode'] }}</span>
                    </div>

                    <div class="my-3 py-2 px-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-1.5">
                            @for($d = 1; $d <= 3; $d++)
                                <span class="w-3.5 h-3.5 rounded-full {{ in_array($d, $item['dots']) ? 'bg-black border border-black' : 'border border-slate-300 bg-white' }}"></span>
                                <span class="w-3.5 h-3.5 rounded-full {{ in_array($d+3, $item['dots']) ? 'bg-black border border-black' : 'border border-slate-300 bg-white' }}"></span>
                            @endfor
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t border-slate-100">
                        <span class="font-medium text-slate-500">Titik {{ implode(', ', $item['dots']) }}</span>
                        <button class="p-1 text-slate-400 hover:text-blue-600" title="Dengarkan Suara">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

    </main>
</div>
@endsection
