/**
 * TACTIPAD: Halaman Braille (Papan Taktil Interaktif 5 Sel × 6 Titik)
 */

import { getAudioContext, playClickSound, playResetClackSound, speak } from '../core/tts';

export function initBraillePage() {
    const initialState = window.TACTIPAD_INITIAL_STATE || {};

    const state = {
        cells: initialState.cells || { 1: [], 2: [], 3: [], 4: [], 5: [] },
        selectedCell: 1,
        word: initialState.word || '',
        brailleWord: initialState.brailleWord || '',
        isAudioEnabled: localStorage.getItem('tactipad_audio') !== 'false',
        speechVoice: null,
        sentence: '',
        sentenceBraille: '',
        lastSseVersion: initialState.lastSseVersion || 0,
        serialPort: null,
        routes: initialState.routes || {
            event: '/event',
            stream: '/api/tactipad/stream'
        }
    };

    // Braille standard lookup
    const brailleMap = {
        '1': 'A', '1,2': 'B', '1,4': 'C', '1,4,5': 'D', '1,5': 'E',
        '1,2,4': 'F', '1,2,4,5': 'G', '1,2,5': 'H', '2,4': 'I', '2,4,5': 'J',
        '1,3': 'K', '1,2,3': 'L', '1,3,4': 'M', '1,3,4,5': 'N', '1,3,5': 'O',
        '1,2,3,4': 'P', '1,2,3,4,5': 'Q', '1,2,3,5': 'R', '2,3,4': 'S', '2,3,4,5': 'T',
        '1,3,6': 'U', '1,2,3,6': 'V', '2,4,5,6': 'W', '1,3,4,6': 'X', '1,3,4,5,6': 'Y',
        '1,3,5,6': 'Z',
        '2,5,6': '.', '2': ',', '2,5': ':', '2,3': ';', '2,3,6': '?', '2,3,5': '!', '3,6': '-'
    };

    // DOM Elements
    const wordDisplay = document.getElementById('wordDisplay');
    const brailleWordDisplay = document.getElementById('brailleWordDisplay');
    const massResetBtn = document.getElementById('massResetBtn');
    const speakWordBtn = document.getElementById('speakWordBtn');
    const audioMuteBtn = document.getElementById('audioMuteBtn');
    const audioStatusText = document.getElementById('audioStatusText');
    const audioIcon = document.getElementById('audioIcon');
    const connectSerialBtn = document.getElementById('connectSerialBtn');
    const serialStatusText = document.getElementById('serialStatusText');
    const srAnnouncement = document.getElementById('srAnnouncement');
    const sentenceInput = document.getElementById('sentenceInput');
    const sentenceBraille = document.getElementById('sentenceBraille');

    function speakText(text) {
        if (!state.isAudioEnabled || !text) return;
        speak(text);
        if (srAnnouncement) srAnnouncement.textContent = text;
    }

    function dotsToUnicode(dots) {
        let offset = 0;
        dots.forEach(d => {
            if (d === 1) offset |= 0x01;
            if (d === 2) offset |= 0x02;
            if (d === 3) offset |= 0x04;
            if (d === 4) offset |= 0x08;
            if (d === 5) offset |= 0x10;
            if (d === 6) offset |= 0x20;
        });
        return String.fromCodePoint(0x2800 + offset);
    }

    function identifyDots(dots) {
        if (!dots || dots.length === 0) return null;
        const sorted = [...dots].map(Number).sort((a, b) => a - b);
        const key = sorted.join(',');
        const char = brailleMap[key] || '?';
        return { char: char, unicode: dotsToUnicode(sorted) };
    }

    function renderAll() {
        let combinedWord = '';
        let combinedBraille = '';

        for (let c = 1; c <= 5; c++) {
            const cellDots = state.cells[c] || [];

            for (let d = 1; d <= 6; d++) {
                const btn = document.querySelector(`button[data-cell="${c}"][data-dot="${d}"]`);
                if (btn) {
                    const isFilled = cellDots.includes(d);
                    btn.classList.toggle('dot-filled-black', isFilled);
                    btn.classList.toggle('dot-empty-clean', !isFilled);
                }
            }

            const identified = identifyDots(cellDots);
            const charElem = document.getElementById(`cellChar_${c}`);
            const unicodeElem = document.getElementById(`cellUnicode_${c}`);
            const cardElem = document.getElementById(`cellCard_${c}`);

            if (identified) {
                if (charElem) charElem.textContent = identified.char;
                if (unicodeElem) unicodeElem.textContent = identified.unicode;
                combinedWord += identified.char;
                combinedBraille += identified.unicode;
            } else {
                if (charElem) charElem.textContent = '-';
                if (unicodeElem) unicodeElem.textContent = '⠀';
                combinedWord += '_ ';
                combinedBraille += '⠀';
            }

            if (cardElem) {
                if (c === state.selectedCell) {
                    cardElem.classList.add('ring-2', 'ring-blue-600', 'border-blue-500');
                } else {
                    cardElem.classList.remove('ring-2', 'ring-blue-600', 'border-blue-500');
                }
            }
        }

        state.word = combinedWord.replace(/_ /g, '').trim();
        state.brailleWord = combinedBraille.trim();
        if (wordDisplay) wordDisplay.textContent = combinedWord || '_ _ _ _ _';
        if (brailleWordDisplay) brailleWordDisplay.textContent = combinedBraille || '⠀⠀⠀';
    }

    function toggleCellDot(cell, dot, source = 'simulator') {
        cell = Number(cell);
        dot = Number(dot);
        if (cell < 1 || cell > 5 || dot < 1 || dot > 6) return;

        state.selectedCell = cell;
        const cellDots = state.cells[cell] || [];
        const idx = cellDots.indexOf(dot);

        if (idx > -1) {
            cellDots.splice(idx, 1);
            playClickSound(750);
        } else {
            cellDots.push(dot);
            cellDots.sort((a, b) => a - b);
            playClickSound(1050 + (dot * 70));
        }
        state.cells[cell] = cellDots;
        renderAll();

        const idf = identifyDots(cellDots);
        if (idf && idf.char !== '?') {
            speakText(`Huruf ${idf.char} pada sel ${cell}`);
        } else {
            speakText(`Titik ${dot} sel ${cell}`);
        }

        sendEventToServer({
            action: 'toggle_dot',
            cell: cell,
            dot: dot,
            global_dot: ((cell - 1) * 6) + dot,
            source: source
        });
    }

    function performMassReset(source = 'simulator') {
        playResetClackSound();
        for (let c = 1; c <= 5; c++) state.cells[c] = [];
        renderAll();
        speakText('Reset berhasil. Seluruh sel kembali kosong.');

        sendEventToServer({ action: 'reset', source: source });
    }

    function speakCurrentWord() {
        const cleanWord = state.word.replace(/\s+/g, '');
        if (!cleanWord) {
            speakText('Papan masih kosong.');
            return;
        }
        speakText(cleanWord);
    }

    function commitWordToSentence() {
        const cleanWord = state.word.replace(/\s+/g, '');
        if (!cleanWord) {
            speakText('Tidak ada kata untuk disimpan.');
            return;
        }

        state.sentence += (state.sentence ? ' ' : '') + cleanWord;
        state.sentenceBraille += (state.sentenceBraille ? '  ' : '') + state.brailleWord;
        if (sentenceInput) sentenceInput.value = state.sentence;
        if (sentenceBraille) sentenceBraille.textContent = state.sentenceBraille;
        speakText(`Kata ${cleanWord} disimpan.`);

        setTimeout(() => performMassReset('auto_next_word'), 300);
    }

    async function sendEventToServer(payload) {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            await fetch(state.routes.event, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload)
            });
        } catch (e) {}
    }

    function initSSEStream() {
        if (!window.EventSource) return;
        try {
            const evtSource = new EventSource(`${state.routes.stream}?last_version=${state.lastSseVersion}`);
            evtSource.onmessage = function(e) {
                try {
                    const data = JSON.parse(e.data);
                    if (data && data.version > state.lastSseVersion) {
                        state.lastSseVersion = data.version;
                        if (data.source !== 'simulator') {
                            if (data.cells) {
                                for (let c = 1; c <= 5; c++) {
                                    state.cells[c] = (data.cells[c] || []).map(Number);
                                }
                                renderAll();
                            }
                            if (data.is_reset) {
                                playResetClackSound();
                                speakText('Reset masal dari alat fisik');
                            } else if (data.word) {
                                playClickSound(1000);
                                speakText(data.word);
                            }
                        }
                    }
                } catch (err) {}
            };
            evtSource.onerror = function() {
                evtSource.close();
                setTimeout(initSSEStream, 5000);
            };
        } catch (e) {}
    }

    // USB Web Serial
    async function connectWebSerial() {
        if (!('serial' in navigator)) {
            alert('Gunakan Google Chrome, Microsoft Edge, atau Opera untuk fitur Web Serial USB.');
            return;
        }
        try {
            const port = await navigator.serial.requestPort();
            await port.open({ baudRate: 115200 });
            state.serialPort = port;
            if (serialStatusText) serialStatusText.textContent = 'USB Terhubung';
            if (connectSerialBtn) {
                connectSerialBtn.classList.remove('bg-blue-600');
                connectSerialBtn.classList.add('bg-emerald-600');
            }
            speakText('Alat Tactipad berhasil terhubung lewat kabel USB.');
            readSerialLoop(port);
        } catch (err) {}
    }

    async function readSerialLoop(port) {
        const textDecoder = new TextDecoderStream();
        port.readable.pipeTo(textDecoder.writable);
        const reader = textDecoder.readable.getReader();
        let buffer = '';
        try {
            while (true) {
                const { value, done } = await reader.read();
                if (done) break;
                if (value) {
                    buffer += value;
                    let lines = buffer.split('\n');
                    buffer = lines.pop();
                    for (const line of lines) {
                        const trimmed = line.trim();
                        if (!trimmed) continue;
                        try {
                            if (trimmed.startsWith('{') && trimmed.endsWith('}')) {
                                const parsed = JSON.parse(trimmed);
                                if (parsed.action === 'reset') performMassReset('hardware_serial');
                                else if (parsed.action === 'toggle_dot') toggleCellDot(parsed.cell || 1, parsed.dot || 1, 'hardware_serial');
                                else if (parsed.global_dot) {
                                    const cell = Math.floor((parsed.global_dot - 1) / 6) + 1;
                                    const dot = ((parsed.global_dot - 1) % 6) + 1;
                                    toggleCellDot(cell, dot, 'hardware_serial');
                                }
                            } else if (trimmed === 'RESET') performMassReset('hardware_serial');
                        } catch (e) {}
                    }
                }
            }
        } catch (err) {}
    }

    // Event Listeners
    document.querySelectorAll('.braille-dot-circle').forEach(btn => {
        btn.addEventListener('click', () => {
            const cell = btn.getAttribute('data-cell');
            const dot = btn.getAttribute('data-dot');
            if (cell && dot) toggleCellDot(cell, dot, 'simulator');
        });
    });

    massResetBtn?.addEventListener('click', () => performMassReset('manual_button'));
    speakWordBtn?.addEventListener('click', speakCurrentWord);

    const saveWordBtn = document.getElementById('saveWordBtn');
    saveWordBtn?.addEventListener('click', commitWordToSentence);

    if (audioMuteBtn) {
        audioMuteBtn.addEventListener('click', () => {
            state.isAudioEnabled = !state.isAudioEnabled;
            if (state.isAudioEnabled) {
                if (audioStatusText) audioStatusText.textContent = 'Suara Aktif';
                if (audioIcon) {
                    audioIcon.classList.remove('text-slate-400');
                    audioIcon.classList.add('text-blue-600');
                }
                speakText('Suara diaktifkan.');
            } else {
                window.speechSynthesis.cancel();
                if (audioStatusText) audioStatusText.textContent = 'Suara Mati';
                if (audioIcon) {
                    audioIcon.classList.remove('text-blue-600');
                    audioIcon.classList.add('text-slate-400');
                }
            }
        });
    }

    if (connectSerialBtn) {
        connectSerialBtn.addEventListener('click', connectWebSerial);
    }

    // Keyboard shortcuts
    window.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'TEXTAREA' || e.target.tagName === 'INPUT') return;
        const key = e.key.toUpperCase();

        if (['1', '2', '3', '4', '5'].includes(key) && !e.ctrlKey && !e.altKey) {
            e.preventDefault();
            state.selectedCell = Number(key);
            renderAll();
            speakText(`Sel ${key} dipilih.`);
        } else if (key === 'Q') {
            e.preventDefault();
            toggleCellDot(state.selectedCell, 1);
        } else if (key === 'W') {
            e.preventDefault();
            toggleCellDot(state.selectedCell, 2);
        } else if (key === 'E') {
            e.preventDefault();
            toggleCellDot(state.selectedCell, 3);
        } else if (key === 'A') {
            e.preventDefault();
            toggleCellDot(state.selectedCell, 4);
        } else if (key === 'S') {
            e.preventDefault();
            toggleCellDot(state.selectedCell, 5);
        } else if (key === 'D') {
            e.preventDefault();
            toggleCellDot(state.selectedCell, 6);
        } else if (key === 'R' || key === 'ESCAPE') {
            e.preventDefault();
            performMassReset('keyboard_shortcut');
        } else if (key === 'ENTER') {
            e.preventDefault();
            speakCurrentWord();
        } else if (key === ' ' || e.code === 'Space') {
            e.preventDefault();
            commitWordToSentence();
        }
    });

    initSSEStream();
    renderAll();
}
