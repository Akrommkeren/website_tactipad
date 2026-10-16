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
