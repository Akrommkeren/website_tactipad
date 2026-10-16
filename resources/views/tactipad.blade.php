@extends('layouts.app')

@section('title', 'TACTIPAD - Papan Taktil Braille')

@section('content')
<div id="brailleMainPage">
    <!-- MAIN DASHBOARD -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        <!-- 30-DOT BRAILLE BOARD (5 CELLS) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">

            <!-- Title & Board Info -->
            <div class="pb-4 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900">Papan Tactipad</h2>
            </div>

            <!-- HASIL TERBENTUK & ACTION BUTTONS -->
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5 sm:p-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase font-bold text-blue-700 tracking-wider block -translate-y-1.5">Hasil Terbentuk</span>
                    <div id="wordDisplay" class="text-3xl sm:text-4xl font-black font-mono tracking-widest text-slate-900 min-w-[10rem]">
                        _ _ _ _ _
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2.5">
                    <button id="speakWordBtn" class="lever-press px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-xs flex items-center gap-2 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        <span>Dengarkan Hasil</span>
                    </button>
                    <button id="massResetBtn" class="lever-press px-4 py-3 rounded-xl bg-white hover:bg-red-600 hover:text-white hover:border-red-600 text-slate-700 border border-slate-200 font-bold text-xs sm:text-sm flex items-center gap-1.5 transition cursor-pointer shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Reset</span>
                    </button>
                </div>
            </div>

            <!-- THE 5-CELL TACTILE BOARD SURFACE -->
            <div class="w-full overflow-x-auto py-2">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 sm:p-8 min-w-[780px] flex items-center justify-center inter-cell-gap">

                    <!-- 5 Cells -->
                    @for($cell = 1; $cell <= 5; $cell++)
                    <div class="braille-cell-card flex-1 max-w-[130px] p-4 rounded-2xl bg-white border-2 border-slate-200 shadow-xs flex flex-col items-center transition hover:border-blue-400" data-cell="{{ $cell }}" id="cellCard_{{ $cell }}">
                        
                        <!-- Cell Header -->
                        <div class="cell-header-label w-full text-center pb-2 mb-2 border-b border-slate-100 text-xs font-light text-blue-600 tracking-wider">
                            <span>Pola {{ $cell }}</span>
                        </div>

                        <!-- 2-Column x 3-Row Matrix (~0.5 mm gap) -->
                        <div class="grid grid-cols-2 cell-dot-grid my-3">
                            
                            <!-- Dot 1 & Dot 4 (Row 1) -->
                            <button data-cell="{{ $cell }}" data-dot="1" class="braille-dot-circle dot-empty-clean w-11 h-11 rounded-full flex items-center justify-center cursor-pointer focus:outline-none" aria-label="Huruf {{ $cell }} Titik 1">
                                <span class="sr-only">H{{ $cell }}T1</span>
                            </button>
                            <button data-cell="{{ $cell }}" data-dot="4" class="braille-dot-circle dot-empty-clean w-11 h-11 rounded-full flex items-center justify-center cursor-pointer focus:outline-none" aria-label="Huruf {{ $cell }} Titik 4">
                                <span class="sr-only">H{{ $cell }}T4</span>
                            </button>

                            <!-- Dot 2 & Dot 5 (Row 2) -->
                            <button data-cell="{{ $cell }}" data-dot="2" class="braille-dot-circle dot-empty-clean w-11 h-11 rounded-full flex items-center justify-center cursor-pointer focus:outline-none" aria-label="Huruf {{ $cell }} Titik 2">
                                <span class="sr-only">H{{ $cell }}T2</span>
                            </button>
                            <button data-cell="{{ $cell }}" data-dot="5" class="braille-dot-circle dot-empty-clean w-11 h-11 rounded-full flex items-center justify-center cursor-pointer focus:outline-none" aria-label="Huruf {{ $cell }} Titik 5">
                                <span class="sr-only">H{{ $cell }}T5</span>
                            </button>

                            <!-- Dot 3 & Dot 6 (Row 3) -->
                            <button data-cell="{{ $cell }}" data-dot="3" class="braille-dot-circle dot-empty-clean w-11 h-11 rounded-full flex items-center justify-center cursor-pointer focus:outline-none" aria-label="Huruf {{ $cell }} Titik 3">
                                <span class="sr-only">H{{ $cell }}T3</span>
                            </button>
                            <button data-cell="{{ $cell }}" data-dot="6" class="braille-dot-circle dot-empty-clean w-11 h-11 rounded-full flex items-center justify-center cursor-pointer focus:outline-none" aria-label="Huruf {{ $cell }} Titik 6">
                                <span class="sr-only">H{{ $cell }}T6</span>
                            </button>

                        </div>

                        <!-- Detected Letter on This Cell -->
                        <div class="w-full pt-2 mt-1 border-t border-slate-100 text-center">
                            <div class="text-2xl font-black text-blue-700 font-mono tracking-wider" id="cellChar_{{ $cell }}">-</div>
                            <div class="text-xs font-mono text-slate-400 select-all" id="cellUnicode_{{ $cell }}">⠀</div>
                        </div>

                    </div>
                    @endfor

                </div>
            </div>

        </div>

    </main>
</div>
@endsection

@push('scripts')
<script>
    window.TACTIPAD_INITIAL_STATE = {
        cells: {
            1: (@json($state['cells'][1] ?? [])).map(Number),
            2: (@json($state['cells'][2] ?? [])).map(Number),
            3: (@json($state['cells'][3] ?? [])).map(Number),
            4: (@json($state['cells'][4] ?? [])).map(Number),
            5: (@json($state['cells'][5] ?? [])).map(Number),
        },
        word: '{{ $state["word"] ?? "" }}',
        brailleWord: '{{ $state["braille_word"] ?? "" }}',
        lastSseVersion: {{ $state['version'] ?? 0 }},
        routes: {
            event: '{{ route("tactipad.event") }}',
            stream: '{{ url("/api/tactipad/stream") }}'
        }
    };
</script>
@endpush
