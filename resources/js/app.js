import { initTheme } from './core/theme';
import { initTTS } from './core/tts';
import { initBraillePage } from './pages/braille';
import { initLatihanPage } from './pages/latihan';
import { initKamusPage } from './pages/kamus';
import { initPengaturanPage } from './pages/pengaturan';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Sistem Tema Global & Audio TTS
    initTheme();
    initTTS();

    // 1.1 Inisialisasi Mobile Navigation Menu
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileNavMenu = document.getElementById('mobileNavMenu');
    const mobileMenuIconOpen = document.getElementById('mobileMenuIconOpen');
    const mobileMenuIconClose = document.getElementById('mobileMenuIconClose');

    if (mobileMenuBtn && mobileNavMenu) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !mobileNavMenu.classList.contains('hidden');
            if (isOpen) {
                mobileNavMenu.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                if (mobileMenuIconOpen) mobileMenuIconOpen.classList.remove('hidden');
                if (mobileMenuIconClose) mobileMenuIconClose.classList.add('hidden');
            } else {
                mobileNavMenu.classList.remove('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'true');
                if (mobileMenuIconOpen) mobileMenuIconOpen.classList.add('hidden');
                if (mobileMenuIconClose) mobileMenuIconClose.classList.remove('hidden');
            }
        });

        // Tutup otomatis saat klik link di dalam menu atau klik area luar
        document.addEventListener('click', (e) => {
            if (!mobileNavMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                mobileNavMenu.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                if (mobileMenuIconOpen) mobileMenuIconOpen.classList.remove('hidden');
                if (mobileMenuIconClose) mobileMenuIconClose.classList.add('hidden');
            }
        });

        mobileNavMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileNavMenu.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                if (mobileMenuIconOpen) mobileMenuIconOpen.classList.remove('hidden');
                if (mobileMenuIconClose) mobileMenuIconClose.classList.add('hidden');
            });
        });
    }

    // 2. Inisialisasi Modul Khusus Halaman
    if (document.getElementById('brailleMainPage')) {
        initBraillePage();
    }
    if (document.getElementById('latihanMainPage')) {
        initLatihanPage();
    }
    if (document.getElementById('kamusMainPage')) {
        initKamusPage();
    }
    if (document.getElementById('pengaturanMainPage')) {
        initPengaturanPage();
    }
});
