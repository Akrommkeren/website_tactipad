/**
 * TACTIPAD: Halaman Kamus & Tentang (Card Click-to-Speech & Filter Dropdown)
 */

import { playChimeSound, speak } from '../core/tts';

export function initKamusPage() {
    // Click card to speak
    document.querySelectorAll('.kamus-card').forEach(card => {
        card.addEventListener('click', () => {
            playChimeSound();
            const pronounce = card.getAttribute('data-pronounce');
            const name = card.getAttribute('data-name');
            const char = card.getAttribute('data-char');
            speak(pronounce || name || char);
        });
    });

    // Custom Category Filter Dropdown
    const filterDropdownContainer = document.getElementById('filterDropdownContainer');
    const filterDropdownBtn = document.getElementById('filterDropdownBtn');
    const filterDropdownMenu = document.getElementById('filterDropdownMenu');
    const currentFilterLabel = document.getElementById('currentFilterLabel');
    const filterChevron = document.getElementById('filterChevron');

    const secAlpha = document.getElementById('sectionAlphabet');
    const secNum = document.getElementById('sectionNumbers');
    const secPunc = document.getElementById('sectionPunctuation');

    function applyFilter(target, label) {
        if (currentFilterLabel && label) {
            currentFilterLabel.textContent = label;
        }
        if (secAlpha) secAlpha.style.display = (target === 'all' || target === 'alphabet') ? 'block' : 'none';
        if (secNum) secNum.style.display = (target === 'all' || target === 'numbers') ? 'block' : 'none';
        if (secPunc) secPunc.style.display = (target === 'all' || target === 'punctuation') ? 'block' : 'none';
    }

    if (filterDropdownBtn && filterDropdownMenu) {
        filterDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = filterDropdownMenu.classList.contains('hidden');
            if (isHidden) {
                filterDropdownMenu.classList.remove('hidden');
                if (filterChevron) filterChevron.classList.add('rotate-180');
            } else {
                filterDropdownMenu.classList.add('hidden');
                if (filterChevron) filterChevron.classList.remove('rotate-180');
            }
        });

        document.addEventListener('click', (e) => {
            if (filterDropdownContainer && !filterDropdownContainer.contains(e.target)) {
                filterDropdownMenu.classList.add('hidden');
                if (filterChevron) filterChevron.classList.remove('rotate-180');
            }
        });

        document.querySelectorAll('#filterDropdownMenu .filter-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = item.getAttribute('data-val');
                const label = item.getAttribute('data-label');
                applyFilter(val, label);
                filterDropdownMenu.classList.add('hidden');
                if (filterChevron) filterChevron.classList.remove('rotate-180');
            });
        });
    }
}
