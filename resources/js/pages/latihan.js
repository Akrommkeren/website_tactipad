/**
 * TACTIPAD: Halaman Latihan Soal Braille (Quiz Engine & Interactive Practice)
 */

import { playChimeSound, speak } from '../core/tts';

export function initLatihanPage() {
    // 26 Standard Indonesian Braille Letters
    const LETTER_MAP = {
        'A': [1], 'B': [1, 2], 'C': [1, 4], 'D': [1, 4, 5], 'E': [1, 5],
        'F': [1, 2, 4], 'G': [1, 2, 4, 5], 'H': [1, 2, 5], 'I': [2, 4], 'J': [2, 4, 5],
        'K': [1, 3], 'L': [1, 2, 3], 'M': [1, 3, 4], 'N': [1, 3, 4, 5], 'O': [1, 3, 5],
        'P': [1, 2, 3, 4], 'Q': [1, 2, 3, 4, 5], 'R': [1, 2, 3, 5], 'S': [2, 3, 4], 'T': [2, 3, 4, 5],
        'U': [1, 3, 6], 'V': [1, 2, 3, 6], 'W': [2, 4, 5, 6], 'X': [1, 3, 4, 6], 'Y': [1, 3, 4, 5, 6],
        'Z': [1, 3, 5, 6]
    };

    // 10 Standard Indonesian Braille Digits (0 - 9)
    const NUMBER_MAP = {
        '1': [1], '2': [1, 2], '3': [1, 4], '4': [1, 4, 5], '5': [1, 5],
        '6': [1, 2, 4], '7': [1, 2, 4, 5], '8': [1, 2, 5], '9': [2, 4], '0': [2, 4, 5]
    };

    // 20 Indonesian 5-letter Words
    const WORDS_5 = [
        'KAPAL', 'RUMAH', 'BUNGA', 'LAMPU', 'POHON',
        'SURAT', 'PINTU', 'TANAH', 'HUJAN', 'ANGIN',
        'BULAN', 'PASIR', 'DANAU', 'HUTAN', 'KOTAK',
        'MEKAR', 'BADAN', 'SUARA', 'JARUM', 'BALON'
    ];

    // 20 Indonesian 5-digit Numbers
    const NUMS_5 = [
        '12345', '67890', '54321', '24680', '13579',
        '98765', '20261', '11223', '45678', '90123',
        '31415', '27182', '88990', '55443', '78901',
        '65432', '10203', '40506', '70809', '99887'
    ];

    const KEY_TO_LETTER = {};
    Object.entries(LETTER_MAP).forEach(([k, dots]) => {
        KEY_TO_LETTER[[...dots].sort((a, b) => a - b).join(',')] = k;
    });

    const KEY_TO_NUMBER = {};
    Object.entries(NUMBER_MAP).forEach(([k, dots]) => {
        KEY_TO_NUMBER[[...dots].sort((a, b) => a - b).join(',')] = k;
    });

    function dotsToUnicode(dots) {
        if (!dots || dots.length === 0) return '⠀';
        let code = 0x2800;
        dots.forEach(d => {
            code |= (1 << (d - 1));
        });
        return String.fromCharCode(code);
    }

    // Build all 76 questions in order
    const ALL_QUESTIONS = [];

    // 1. Soal Huruf: 26 Single Letters A-Z
    Object.keys(LETTER_MAP).forEach(letter => {
        ALL_QUESTIONS.push({
            id: `letter_${letter}`,
            category: 'huruf',
            subCategory: 'huruf_single',
            badge: 'Huruf A-Z',
            target: letter,
            type: 'single',
            pronounce: `Huruf ${letter}`,
            dotsTarget: { 1: LETTER_MAP[letter] }
        });
    });

    // 2. Soal Kata: 20 Words with 5 Letters
    WORDS_5.forEach((word) => {
        const dotsTarget = {};
        for (let i = 0; i < 5; i++) {
            const char = word[i];
            dotsTarget[i + 1] = LETTER_MAP[char] || [];
        }
        ALL_QUESTIONS.push({
            id: `word_${word}`,
            category: 'huruf',
            subCategory: 'kata_5',
            badge: 'Kata 5 Huruf',
            target: word,
            type: 'multi',
            pronounce: `Kata ${word.split('').join(' ')}, ${word}`,
            dotsTarget: dotsTarget
        });
    });

    // 3. Soal Angka: 10 Single Digits 0-9
    Object.keys(NUMBER_MAP).forEach(digit => {
        ALL_QUESTIONS.push({
            id: `digit_${digit}`,
            category: 'angka',
            subCategory: 'angka_single',
            badge: 'Angka 0-9',
            target: digit,
            type: 'single',
            pronounce: `Angka ${digit}`,
            dotsTarget: { 1: NUMBER_MAP[digit] }
        });
    });

    // 4. Soal Angka 5 Digit: 20 Numbers with 5 Digits
    NUMS_5.forEach((numStr) => {
        const dotsTarget = {};
        for (let i = 0; i < 5; i++) {
            const digit = numStr[i];
            dotsTarget[i + 1] = NUMBER_MAP[digit] || [];
        }
        ALL_QUESTIONS.push({
            id: `num5_${numStr}`,
            category: 'angka',
            subCategory: 'angka_5',
            badge: 'Angka 5 Digit',
            target: numStr,
            type: 'multi',
            pronounce: `Angka ${numStr.split('').join(' ')}`,
            dotsTarget: dotsTarget
        });
    });

    // State
    let activeCategory = 'all';
    let activeSubCategory = 'all';
    let currentFilteredQuestions = [];
    let currentQuestionIndex = 0;
    let completedQuestionIds = new Set();
    let autoAdvanceTimer = null;
    let userCells = { 1: [], 2: [], 3: [], 4: [], 5: [] };

    // DOM Elements
    const filterDropdownContainer = document.getElementById('filterDropdownContainer');
    const filterDropdownBtn = document.getElementById('filterDropdownBtn');
    const filterDropdownMenu = document.getElementById('filterDropdownMenu');
    const currentFilterLabel = document.getElementById('currentFilterLabel');
    const filterChevron = document.getElementById('filterChevron');

    const questionCounterText = document.getElementById('questionCounterText');
    const targetBadge = document.getElementById('targetBadge');
    const targetTextDisplay = document.getElementById('targetTextDisplay');
    const singleCellBoard = document.getElementById('singleCellBoard');
    const multiCellBoard = document.getElementById('multiCellBoard');
    const feedbackBanner = document.getElementById('feedbackBanner');
    const completionBanner = document.getElementById('completionBanner');
    const completionMessage = document.getElementById('completionMessage');
    const prevQuestionBtn = document.getElementById('prevQuestionBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const clearDotsBtn = document.getElementById('clearDotsBtn');
    const submitAnswerBtn = document.getElementById('submitAnswerBtn');
    const speakQuestionBtn = document.getElementById('speakQuestionBtn');
    const restartPracticeBtn = document.getElementById('restartPracticeBtn');

    // Submenu elements
    const toggleHurufSubmenuBtn = document.getElementById('toggleHurufSubmenuBtn');
    const submenuHuruf = document.getElementById('submenuHuruf');
    const chevronHuruf = document.getElementById('chevronHuruf');

    const toggleAngkaSubmenuBtn = document.getElementById('toggleAngkaSubmenuBtn');
    const submenuAngka = document.getElementById('submenuAngka');
    const chevronAngka = document.getElementById('chevronAngka');

    if (!filterDropdownBtn || !singleCellBoard) return;

    function closeSubmenus() {
        if (submenuHuruf) submenuHuruf.classList.add('hidden');
        if (chevronHuruf) chevronHuruf.classList.remove('rotate-90');
        if (submenuAngka) submenuAngka.classList.add('hidden');
        if (chevronAngka) chevronAngka.classList.remove('rotate-90');
    }

    // Toggle Submenu Huruf (Mobile & Desktop Click)
    if (toggleHurufSubmenuBtn && submenuHuruf) {
        toggleHurufSubmenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = submenuHuruf.classList.contains('hidden');
            if (submenuAngka) submenuAngka.classList.add('hidden');
            if (chevronAngka) chevronAngka.classList.remove('rotate-90');

            if (isHidden) {
                submenuHuruf.classList.remove('hidden');
                if (chevronHuruf) chevronHuruf.classList.add('rotate-90');
            } else {
                submenuHuruf.classList.add('hidden');
                if (chevronHuruf) chevronHuruf.classList.remove('rotate-90');
            }
        });
    }

    // Toggle Submenu Angka (Mobile & Desktop Click)
    if (toggleAngkaSubmenuBtn && submenuAngka) {
        toggleAngkaSubmenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = submenuAngka.classList.contains('hidden');
            if (submenuHuruf) submenuHuruf.classList.add('hidden');
            if (chevronHuruf) chevronHuruf.classList.remove('rotate-90');

            if (isHidden) {
                submenuAngka.classList.remove('hidden');
                if (chevronAngka) chevronAngka.classList.add('rotate-90');
            } else {
                submenuAngka.classList.add('hidden');
                if (chevronAngka) chevronAngka.classList.remove('rotate-90');
            }
        });
    }

    // Dropdown Toggle
    filterDropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = filterDropdownMenu.classList.contains('hidden');
        if (isHidden) {
            filterDropdownMenu.classList.remove('hidden');
            filterChevron.classList.add('rotate-180');
        } else {
            filterDropdownMenu.classList.add('hidden');
            filterChevron.classList.remove('rotate-180');
            closeSubmenus();
        }
    });

    // Close Dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (filterDropdownContainer && !filterDropdownContainer.contains(e.target)) {
            filterDropdownMenu.classList.add('hidden');
            filterChevron.classList.remove('rotate-180');
            closeSubmenus();
        }
    });

    // Filter item click handler
    document.querySelectorAll('.filter-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = item.getAttribute('data-val');
            const label = item.getAttribute('data-label');

            if (val === 'all') {
                activeCategory = 'all';
                activeSubCategory = 'all';
            } else if (val === 'huruf') {
                activeCategory = 'huruf';
                activeSubCategory = 'all';
            } else if (val === 'huruf_single') {
                activeCategory = 'huruf';
                activeSubCategory = 'huruf_single';
            } else if (val === 'kata_5') {
                activeCategory = 'huruf';
                activeSubCategory = 'kata_5';
            } else if (val === 'angka') {
                activeCategory = 'angka';
                activeSubCategory = 'all';
            } else if (val === 'angka_single') {
                activeCategory = 'angka';
                activeSubCategory = 'angka_single';
            } else if (val === 'angka_5') {
                activeCategory = 'angka';
                activeSubCategory = 'angka_5';
            }

            if (currentFilterLabel) currentFilterLabel.textContent = label;
            filterDropdownMenu.classList.add('hidden');
            if (filterChevron) filterChevron.classList.remove('rotate-180');
            closeSubmenus();
            applyFilter(true);
        });
    });

    function applyFilter(shouldSpeak = false) {
        if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);

        currentFilteredQuestions = ALL_QUESTIONS.filter(q => {
            if (activeCategory !== 'all' && q.category !== activeCategory) return false;
            if (activeSubCategory !== 'all' && q.subCategory !== activeSubCategory) return false;
            return true;
        });

        currentQuestionIndex = 0;
        if (feedbackBanner) feedbackBanner.classList.add('hidden');
        checkCompletionStatus();
        loadCurrentQuestion(shouldSpeak);
    }

    function loadCurrentQuestion(shouldSpeak = false) {
        if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);
        if (currentFilteredQuestions.length === 0) return;

        const q = currentFilteredQuestions[currentQuestionIndex];
        if (feedbackBanner) feedbackBanner.classList.add('hidden');

        userCells = { 1: [], 2: [], 3: [], 4: [], 5: [] };

        if (targetBadge) targetBadge.textContent = q.badge;
        if (targetTextDisplay) targetTextDisplay.textContent = q.target.split('').join(' ');
        if (questionCounterText) questionCounterText.textContent = `Soal ${currentQuestionIndex + 1}/${currentFilteredQuestions.length}`;

        if (q.type === 'single') {
            singleCellBoard.classList.remove('hidden');
            multiCellBoard.classList.add('hidden');
        } else {
            singleCellBoard.classList.add('hidden');
            multiCellBoard.classList.remove('hidden');
        }

        renderAllDots();
        if (shouldSpeak) {
            speak(`Bentuklah pola untuk ${q.pronounce}`);
        }
    }

    // Dot interaction
    document.querySelectorAll('.quiz-dot').forEach(btn => {
        btn.addEventListener('click', () => {
            const cell = Number(btn.getAttribute('data-cell'));
            const dot = Number(btn.getAttribute('data-dot'));

            if (!userCells[cell]) userCells[cell] = [];
            const idx = userCells[cell].indexOf(dot);
            if (idx > -1) {
                userCells[cell].splice(idx, 1);
            } else {
                userCells[cell].push(dot);
            }
            userCells[cell].sort((a, b) => a - b);

            renderAllDots();
        });
    });

    function renderAllDots() {
        document.querySelectorAll('.quiz-dot').forEach(btn => {
            const cell = Number(btn.getAttribute('data-cell'));
            const dot = Number(btn.getAttribute('data-dot'));
            const isActive = (userCells[cell] || []).includes(dot);

            btn.classList.toggle('dot-clean-filled', isActive);
            btn.classList.toggle('dot-clean-empty', !isActive);
        });

        const currentQ = currentFilteredQuestions[currentQuestionIndex];
        const isNumType = currentQ && (currentQ.category === 'angka');

        const key1 = (userCells[1] || []).join(',');
        const char1 = isNumType ? (KEY_TO_NUMBER[key1] || KEY_TO_LETTER[key1] || '-') : (KEY_TO_LETTER[key1] || KEY_TO_NUMBER[key1] || '-');
        const uni1 = dotsToUnicode(userCells[1] || []);

        const singleVal = document.querySelector('.single-char-val');
        const singleUni = document.querySelector('.single-char-uni');
        if (singleVal) singleVal.textContent = char1;
        if (singleUni) singleUni.textContent = uni1;

        for (let c = 1; c <= 5; c++) {
            const k = (userCells[c] || []).join(',');
            const ch = isNumType ? (KEY_TO_NUMBER[k] || KEY_TO_LETTER[k] || '-') : (KEY_TO_LETTER[k] || KEY_TO_NUMBER[k] || '-');
            const u = dotsToUnicode(userCells[c] || []);

            const elVal = document.getElementById(`multiCharVal_${c}`);
            const elUni = document.getElementById(`multiCharUni_${c}`);
            if (elVal) elVal.textContent = ch;
            if (elUni) elUni.textContent = u;
        }
    }

    clearDotsBtn?.addEventListener('click', () => {
        if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);
        userCells = { 1: [], 2: [], 3: [], 4: [], 5: [] };
        renderAllDots();
        if (feedbackBanner) feedbackBanner.classList.add('hidden');
        speak('Pola titik direset.');
    });

    submitAnswerBtn?.addEventListener('click', () => {
        const q = currentFilteredQuestions[currentQuestionIndex];
        if (!q) return;

        let isAllCorrect = true;
        const targetDots = q.dotsTarget;
        const requiredCellsCount = (q.type === 'single') ? 1 : 5;

        for (let c = 1; c <= requiredCellsCount; c++) {
            const expected = [...(targetDots[c] || [])].sort((a, b) => a - b);
            const actual = [...(userCells[c] || [])].sort((a, b) => a - b);
            if (JSON.stringify(expected) !== JSON.stringify(actual)) {
                isAllCorrect = false;
                break;
            }
        }

        if (feedbackBanner) feedbackBanner.classList.remove('hidden');

        if (isAllCorrect) {
            completedQuestionIds.add(q.id);

            if (feedbackBanner) {
                feedbackBanner.className = 'rounded-2xl p-4 sm:p-5 text-center text-sm sm:text-base font-bold bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-xs';
                feedbackBanner.innerHTML = `
                    <div class="flex items-center justify-center">
                        <span>jawaban benar!</span>
                    </div>
                `;
            }
            speak('jawaban benar!');

            if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);
            autoAdvanceTimer = setTimeout(() => {
                if (feedbackBanner) feedbackBanner.classList.add('hidden');
                if (currentQuestionIndex < currentFilteredQuestions.length - 1) {
                    currentQuestionIndex++;
                    loadCurrentQuestion();
                } else {
                    const setFinished = currentFilteredQuestions.every(item => completedQuestionIds.has(item.id));
                    if (setFinished) {
                        showCompletionScreen();
                    } else {
                        currentQuestionIndex = 0;
                        loadCurrentQuestion();
                    }
                }
            }, 1000);
        } else {
            if (feedbackBanner) {
                feedbackBanner.className = 'rounded-2xl p-4 sm:p-5 text-center text-sm sm:text-base font-bold bg-rose-50 border border-rose-200 text-rose-800 shadow-xs';
                feedbackBanner.innerHTML = `
                    <div class="flex items-center justify-center">
                        <span>Jawaban Belum Tepat!</span>
                    </div>
                `;
            }
            speak('Jawaban Belum Tepat!');
        }
    });

    function showCompletionScreen() {
        if (!completionBanner) return;
        completionBanner.classList.remove('hidden');
        let catLabel = currentFilterLabel ? currentFilterLabel.textContent : 'Semua Soal';
        if (completionMessage) {
            completionMessage.textContent = `Seluruh ${currentFilteredQuestions.length} soal pada kategori ${catLabel} telah berhasil Anda selesaikan dengan benar!`;
        }
        completionBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        speak(`Selamat! Anda telah menyelesaikan seluruh soal ${catLabel} dengan benar!`);
    }

    function checkCompletionStatus() {
        if (!completionBanner) return;
        completionBanner.classList.add('hidden');
        if (currentFilteredQuestions.length > 0 && currentFilteredQuestions.every(item => completedQuestionIds.has(item.id))) {
            showCompletionScreen();
        }
    }

    restartPracticeBtn?.addEventListener('click', () => {
        if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);
        currentFilteredQuestions.forEach(q => completedQuestionIds.delete(q.id));
        if (completionBanner) completionBanner.classList.add('hidden');
        currentQuestionIndex = 0;
        loadCurrentQuestion();
        speak('Latihan direset. Silakan mulai kembali dari soal nomor 1.');
    });

    prevQuestionBtn?.addEventListener('click', () => {
        if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
        } else {
            currentQuestionIndex = currentFilteredQuestions.length - 1;
        }
        loadCurrentQuestion(true);
    });

    nextQuestionBtn?.addEventListener('click', () => {
        if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);
        if (currentQuestionIndex < currentFilteredQuestions.length - 1) {
            currentQuestionIndex++;
        } else {
            currentQuestionIndex = 0;
        }
        loadCurrentQuestion(true);
    });

    speakQuestionBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        playChimeSound();
        const q = currentFilteredQuestions[currentQuestionIndex];
        if (q) speak(`Bentuklah pola titik Braille untuk ${q.pronounce}`);
    });

    applyFilter();
}
