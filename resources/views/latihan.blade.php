@extends('layouts.app')

@section('title', 'Latihan Soal Braille - TACTIPAD')

@section('content')
<div id="latihanMainPage">
    <!-- MAIN PRACTICE CONTENT -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        <!-- SINGLE UNIFIED QUESTION CARD -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">

            <!-- Card Header: Title Only -->
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Latihan Soal</h1>
            </div>

            <!-- Row: Custom Filter Dropdown (Kiri) dan Soal X/Y (Kanan) -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100 relative">
                
                <!-- Custom Filter Dropdown -->
                <div class="relative" id="filterDropdownContainer">
                    <button type="button" id="filterDropdownBtn" class="inline-flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:border-blue-500 text-xs sm:text-sm font-bold text-slate-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition cursor-pointer min-w-[170px] touch-manipulation">
                        <span id="currentFilterLabel">Semua Soal</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" id="filterChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Main Dropdown Menu Panel -->
                    <div id="filterDropdownMenu" class="hidden absolute left-0 mt-1.5 w-52 rounded-2xl bg-white border border-slate-200 shadow-xl py-2 z-50">
                        
                        <!-- Option 1: Semua Soal -->
                        <button type="button" data-val="all" data-label="Semua Soal" class="filter-item w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 text-xs sm:text-sm font-bold text-slate-700 transition cursor-pointer flex items-center justify-between touch-manipulation">
                            <span>Semua Soal</span>
                        </button>

                        <div class="my-1 border-t border-slate-100"></div>

                        <!-- Option 2: Soal Huruf with Cascading Submenu on Hover / Accordion on Mobile -->
                        <div class="relative group/huruf" id="groupHuruf">
                            <button type="button" id="toggleHurufSubmenuBtn" class="submenu-toggle-btn w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 text-xs sm:text-sm font-bold text-slate-700 transition cursor-pointer flex items-center justify-between touch-manipulation">
                                <span>Soal Huruf</span>
                                <svg id="chevronHuruf" class="w-3.5 h-3.5 text-slate-400 group-hover/huruf:text-blue-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            
                            <!-- Submenu Huruf -->
                            <div id="submenuHuruf" class="submenu-panel submenu-flyout hidden sm:group-hover/huruf:block sm:absolute sm:left-full sm:top-0 sm:ml-1.5 w-full sm:w-44 sm:rounded-2xl sm:bg-white sm:border sm:border-slate-200 sm:shadow-xl py-0.5 sm:py-2 z-50">
                                <button type="button" data-val="huruf_single" data-label="Huruf A-Z" class="filter-item w-full text-left pl-7 pr-4 sm:px-4 py-2 hover:bg-blue-50 hover:text-blue-700 text-xs sm:text-sm font-bold text-slate-700 transition cursor-pointer block touch-manipulation">
                                    Huruf A-Z
                                </button>
                                <button type="button" data-val="kata_5" data-label="Kata 5 Huruf" class="filter-item w-full text-left pl-7 pr-4 sm:px-4 py-2 hover:bg-blue-50 hover:text-blue-700 text-xs sm:text-sm font-bold text-slate-700 transition cursor-pointer block touch-manipulation">
                                    Kata 5 Huruf
                                </button>
                            </div>
                        </div>

                        <div class="my-1 border-t border-slate-100"></div>

                        <!-- Option 3: Soal Angka with Cascading Submenu on Hover / Accordion on Mobile -->
                        <div class="relative group/angka" id="groupAngka">
                            <button type="button" id="toggleAngkaSubmenuBtn" class="submenu-toggle-btn w-full text-left px-4 py-2.5 hover:bg-blue-50 hover:text-blue-700 text-xs sm:text-sm font-bold text-slate-700 transition cursor-pointer flex items-center justify-between touch-manipulation">
                                <span>Soal Angka</span>
                                <svg id="chevronAngka" class="w-3.5 h-3.5 text-slate-400 group-hover/angka:text-blue-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            <!-- Submenu Angka -->
                            <div id="submenuAngka" class="submenu-panel submenu-flyout hidden sm:group-hover/angka:block sm:absolute sm:left-full sm:top-0 sm:ml-1.5 w-full sm:w-44 sm:rounded-2xl sm:bg-white sm:border sm:border-slate-200 sm:shadow-xl py-0.5 sm:py-2 z-50">
                                <button type="button" data-val="angka_single" data-label="Angka 0-9" class="filter-item w-full text-left pl-7 pr-4 sm:px-4 py-2 hover:bg-blue-50 hover:text-blue-700 text-xs sm:text-sm font-bold text-slate-700 transition cursor-pointer block touch-manipulation">
                                    Angka 0-9
                                </button>
                                <button type="button" data-val="angka_5" data-label="Angka 5 Digit" class="filter-item w-full text-left pl-7 pr-4 sm:px-4 py-2 hover:bg-blue-50 hover:text-blue-700 text-xs sm:text-sm font-bold text-slate-700 transition cursor-pointer block touch-manipulation">
                                    Angka 5 Digit
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Soal X/Y (Kanan, sejajar dengan filter) -->
                <div class="px-4 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs sm:text-sm font-bold font-mono">
                    <span id="questionCounterText">Soal 1/76</span>
                </div>

            </div>

            <!-- QUESTION TARGET DISPLAY -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 sm:p-7 text-center space-y-3 relative">
                
                <!-- Top Question Controls & Badge -->
                <div class="flex items-center justify-between">
                    <span id="targetBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 tracking-wide">
                        Huruf A-Z
                    </span>

                    <button id="speakQuestionBtn" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-blue-700 bg-white hover:bg-blue-100 border border-blue-200 shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        <span>Dengarkan Soal</span>
                    </button>
                </div>

                <!-- Prompt description -->
                <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wider">
                    Bentuklah pola titik Braille untuk:
                </p>

                <!-- Big Target Character / Word Display -->
                <div id="targetTextDisplay" class="text-4xl sm:text-6xl font-black text-blue-700 font-mono tracking-widest py-1 select-all">
                    A
                </div>
            </div>

            <!-- INTERACTIVE BOARD CONTAINER -->
            <div class="space-y-4">
                
                <!-- 1-CELL BOARD -->
                <div id="singleCellBoard" class="flex justify-center py-2">
                    <div class="w-full max-w-[200px] p-5 rounded-2xl bg-white border-2 border-slate-200 shadow-xs flex flex-col items-center">
                        <div class="w-full text-center pb-2 mb-2 border-b border-slate-100 text-xs font-light text-blue-600 tracking-wider">
                            <span>Pola 1</span>
                        </div>
                        <div class="grid grid-cols-2 cell-dot-grid my-3">
                            <!-- Dot 1 & Dot 4 -->
                            <button data-cell="1" data-dot="1" class="quiz-dot dot-clean-empty w-12 h-12 rounded-full cursor-pointer focus:outline-none flex items-center justify-center" aria-label="Titik 1">
                                <span class="sr-only">1</span>
                            </button>
                            <button data-cell="1" data-dot="4" class="quiz-dot dot-clean-empty w-12 h-12 rounded-full cursor-pointer focus:outline-none flex items-center justify-center" aria-label="Titik 4">
                                <span class="sr-only">4</span>
                            </button>
                            
                            <!-- Dot 2 & Dot 5 -->
                            <button data-cell="1" data-dot="2" class="quiz-dot dot-clean-empty w-12 h-12 rounded-full cursor-pointer focus:outline-none flex items-center justify-center" aria-label="Titik 2">
                                <span class="sr-only">2</span>
                            </button>
                            <button data-cell="1" data-dot="5" class="quiz-dot dot-clean-empty w-12 h-12 rounded-full cursor-pointer focus:outline-none flex items-center justify-center" aria-label="Titik 5">
                                <span class="sr-only">5</span>
                            </button>
                            
                            <!-- Dot 3 & Dot 6 -->
                            <button data-cell="1" data-dot="3" class="quiz-dot dot-clean-empty w-12 h-12 rounded-full cursor-pointer focus:outline-none flex items-center justify-center" aria-label="Titik 3">
                                <span class="sr-only">3</span>
                            </button>
                            <button data-cell="1" data-dot="6" class="quiz-dot dot-clean-empty w-12 h-12 rounded-full cursor-pointer focus:outline-none flex items-center justify-center" aria-label="Titik 6">
                                <span class="sr-only">6</span>
                            </button>
                        </div>
                        <div class="w-full pt-2 mt-1 border-t border-slate-100 text-center">
                            <div class="text-2xl font-black text-blue-700 font-mono tracking-wider single-char-val">-</div>
                            <div class="text-xs font-mono text-slate-400 single-char-uni">⠀</div>
                        </div>
                    </div>
                </div>

                <!-- 5-CELL BOARD (Responsive: 3+2 on mobile, 5 in row on desktop) -->
                <div id="multiCellBoard" class="hidden w-full py-2">
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 sm:p-8 flex flex-wrap sm:flex-nowrap items-center justify-center gap-2.5 sm:gap-6">
                        @for($cell = 1; $cell <= 5; $cell++)
                        <div class="braille-cell-card w-[98px] sm:w-auto sm:flex-1 sm:max-w-[130px] p-2.5 sm:p-4 rounded-2xl bg-white border-2 border-slate-200 shadow-xs flex flex-col items-center transition hover:border-blue-400 select-none touch-manipulation" data-cell="{{ $cell }}">
                            
                            <!-- Cell Header -->
                            <div class="w-full text-center pb-1.5 sm:pb-2 mb-1.5 sm:mb-2 border-b border-slate-100 text-[11px] sm:text-xs font-light text-blue-600 tracking-wider">
                                <span>Pola {{ $cell }}</span>
                            </div>

                            <!-- 2-Column x 3-Row Matrix -->
                            <div class="grid grid-cols-2 gap-2 sm:gap-3 cell-dot-grid my-1.5 sm:my-3">
                                <!-- Dot 1 & Dot 4 -->
                                <button data-cell="{{ $cell }}" data-dot="1" class="quiz-dot dot-clean-empty w-9 h-9 sm:w-11 sm:h-11 rounded-full cursor-pointer focus:outline-none flex items-center justify-center touch-manipulation active:scale-90" aria-label="Pola {{ $cell }} Titik 1">
                                    <span class="sr-only">1</span>
                                </button>
                                <button data-cell="{{ $cell }}" data-dot="4" class="quiz-dot dot-clean-empty w-9 h-9 sm:w-11 sm:h-11 rounded-full cursor-pointer focus:outline-none flex items-center justify-center touch-manipulation active:scale-90" aria-label="Pola {{ $cell }} Titik 4">
                                    <span class="sr-only">4</span>
                                </button>

                                <!-- Dot 2 & Dot 5 -->
                                <button data-cell="{{ $cell }}" data-dot="2" class="quiz-dot dot-clean-empty w-9 h-9 sm:w-11 sm:h-11 rounded-full cursor-pointer focus:outline-none flex items-center justify-center touch-manipulation active:scale-90" aria-label="Pola {{ $cell }} Titik 2">
                                    <span class="sr-only">2</span>
                                </button>
                                <button data-cell="{{ $cell }}" data-dot="5" class="quiz-dot dot-clean-empty w-9 h-9 sm:w-11 sm:h-11 rounded-full cursor-pointer focus:outline-none flex items-center justify-center touch-manipulation active:scale-90" aria-label="Pola {{ $cell }} Titik 5">
                                    <span class="sr-only">5</span>
                                </button>

                                <!-- Dot 3 & Dot 6 -->
                                <button data-cell="{{ $cell }}" data-dot="3" class="quiz-dot dot-clean-empty w-9 h-9 sm:w-11 sm:h-11 rounded-full cursor-pointer focus:outline-none flex items-center justify-center touch-manipulation active:scale-90" aria-label="Pola {{ $cell }} Titik 3">
                                    <span class="sr-only">3</span>
                                </button>
                                <button data-cell="{{ $cell }}" data-dot="6" class="quiz-dot dot-clean-empty w-9 h-9 sm:w-11 sm:h-11 rounded-full cursor-pointer focus:outline-none flex items-center justify-center touch-manipulation active:scale-90" aria-label="Pola {{ $cell }} Titik 6">
                                    <span class="sr-only">6</span>
                                </button>
                            </div>

                            <!-- Detected Letter on This Cell -->
                            <div class="w-full pt-1.5 sm:pt-2 mt-1 border-t border-slate-100 text-center">
                                <div class="text-xl sm:text-2xl font-black text-blue-700 font-mono tracking-wider multi-char-val" id="multiCharVal_{{ $cell }}">-</div>
                                <div class="text-[11px] sm:text-xs font-mono text-slate-400 select-all multi-char-uni" id="multiCharUni_{{ $cell }}">⠀</div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

            </div>

            <!-- ACTION CONTROLS -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
                <div class="flex items-center gap-2.5">
                    <!-- Button Reset -->
                    <button id="clearDotsBtn" class="btn-reset-danger lever-press px-4 py-2.5 rounded-xl bg-white hover:bg-red-600 hover:text-white hover:border-red-600 text-slate-700 border border-slate-300 text-xs sm:text-sm font-bold flex items-center gap-1.5 transition cursor-pointer shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Reset</span>
                    </button>

                    <!-- Button Periksa Jawaban -->
                    <button id="submitAnswerBtn" class="lever-press px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-emerald-600/20 flex items-center gap-2 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Periksa Jawaban</span>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Button Sebelumnya -->
                    <button id="prevQuestionBtn" class="lever-press px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold transition cursor-pointer flex items-center gap-1">
                        ← Sebelumnya
                    </button>

                    <!-- Button Berikutnya -->
                    <button id="nextQuestionBtn" class="lever-press px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center gap-1">
                        <span>Berikutnya</span> →
                    </button>
                </div>
            </div>

            <!-- RESULT FEEDBACK BANNER -->
            <div id="feedbackBanner" class="hidden rounded-2xl p-4 sm:p-5 text-center text-sm font-bold transition shadow-xs"></div>

            <!-- COMPLETION CELEBRATION MODAL -->
            <div id="completionBanner" class="hidden rounded-3xl bg-linear-to-br from-emerald-500 to-teal-700 text-white p-8 sm:p-10 text-center space-y-4 shadow-lg">
                <div class="w-16 h-16 mx-auto rounded-full bg-white/20 flex items-center justify-center text-3xl">
                    🎉
                </div>
                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat! Latihan Selesai</h3>
                <p id="completionMessage" class="text-sm sm:text-base text-emerald-50 max-w-xl mx-auto">
                    Seluruh soal pada kategori ini telah berhasil Anda bentuk dengan benar! Kemampuan Braille Anda luar biasa.
                </p>
                <div class="pt-2">
                    <button id="restartPracticeBtn" class="lever-press px-8 py-3.5 rounded-2xl bg-white text-emerald-800 hover:bg-emerald-50 font-black text-sm sm:text-base shadow-md transition cursor-pointer inline-flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Ulangi Latihan / Reset Soal</span>
                    </button>
                </div>
            </div>

        </div>

    </main>
</div>
@endsection
