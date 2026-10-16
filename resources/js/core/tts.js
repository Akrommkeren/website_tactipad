/**
 * TACTIPAD: Core Text-to-Speech (TTS) & Audio Feedback Engine
 * Optimized for Mobile (iOS Safari & Android Chrome) and Desktop
 */

let idVoice = null;
let audioCtx = null;
let isAudioUnlocked = false;

export function loadVoices() {
    if (!('speechSynthesis' in window)) return;
    const voices = window.speechSynthesis.getVoices();
    if (!voices || voices.length === 0) return;

    const savedLang = localStorage.getItem('tactipad_lang') || 'id-ID';
    const langPrefix = savedLang.split('-')[0].toLowerCase();

    // Prioritas: Bahasa terpilih -> Indonesian -> Default
    idVoice = voices.find(v => v.lang === savedLang || v.lang.replace('_', '-') === savedLang)
        || voices.find(v => v.lang.toLowerCase().startsWith(langPrefix))
        || voices.find(v => v.lang.includes('id') || v.name.toLowerCase().includes('indonesian'))
        || voices[0];
}

/**
 * Mobile Audio & Speech Unlocker:
 * Mobile browsers require user interaction to unblock audio & speech synthesis
 */
export function unlockMobileAudio() {
    if (isAudioUnlocked) return;
    try {
        const ctx = getAudioContext();
        if (ctx && ctx.state === 'suspended') {
            ctx.resume();
        }
        if ('speechSynthesis' in window) {
            window.speechSynthesis.resume();
            // Silent priming utterance for iOS Safari & Android Chrome
            const prime = new SpeechSynthesisUtterance('');
            prime.volume = 0;
            window.speechSynthesis.speak(prime);
        }
        isAudioUnlocked = true;
    } catch (e) {}
}

export function initTTS() {
    loadVoices();
    if (typeof window !== 'undefined') {
        if ('speechSynthesis' in window && window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
        }

        // Auto-unlock on first mobile user gesture
        const events = ['touchstart', 'touchend', 'pointerdown', 'click'];
        const unlockHandler = () => {
            unlockMobileAudio();
            events.forEach(ev => window.removeEventListener(ev, unlockHandler));
        };
        events.forEach(ev => window.addEventListener(ev, unlockHandler, { passive: true, once: true }));
    }
}

/**
 * Haptic Vibration Feedback for Mobile Phones
 */
export function triggerHaptic(pattern = 15) {
    if (typeof navigator !== 'undefined' && 'vibrate' in navigator) {
        try {
            navigator.vibrate(pattern);
        } catch (e) {}
    }
}

export function speak(text) {
    if (localStorage.getItem('tactipad_audio') === 'false') return;
    if (!('speechSynthesis' in window) || !text) return;

    try {
        // Mobile browsers: resume speech queue if stuck or paused
        if (window.speechSynthesis.paused) {
            window.speechSynthesis.resume();
        }
        window.speechSynthesis.cancel();

        const utter = new SpeechSynthesisUtterance(text);
        const savedLang = localStorage.getItem('tactipad_lang') || 'id-ID';
        utter.lang = savedLang;

        if (idVoice && (idVoice.lang.startsWith(savedLang.split('-')[0]) || savedLang.includes('id'))) {
            utter.voice = idVoice;
        }

        const savedRate = parseFloat(localStorage.getItem('tactipad_rate') || '1.0');
        const savedPitch = parseFloat(localStorage.getItem('tactipad_pitch') || '1.0');
        const savedVolume = parseFloat(localStorage.getItem('tactipad_volume') || '100');

        utter.rate = isNaN(savedRate) ? 1.0 : Math.max(0.5, Math.min(2.0, savedRate));
        utter.pitch = isNaN(savedPitch) ? 1.0 : Math.max(0.5, Math.min(2.0, savedPitch));
        utter.volume = isNaN(savedVolume) ? 1.0 : Math.max(0, Math.min(1.0, savedVolume / 100));

        window.speechSynthesis.speak(utter);
    } catch (e) {
        // Fallback tone if speech synthesis fails on mobile
        playClickSound(880);
    }
}

export function getAudioContext() {
    if (!audioCtx) {
        const AudioClass = window.AudioContext || window.webkitAudioContext;
        if (AudioClass) {
            audioCtx = new AudioClass();
        }
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

export function playClickSound(freq = 950) {
    if (localStorage.getItem('tactipad_audio') === 'false') return;
    triggerHaptic(12); // Subtle tactile feedback
    try {
        const ctx = getAudioContext();
        if (!ctx) return;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, ctx.currentTime);
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.05);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        osc.stop(ctx.currentTime + 0.05);
    } catch (e) {}
}

export function playResetClackSound() {
    if (localStorage.getItem('tactipad_audio') === 'false') return;
    triggerHaptic([30, 25, 30]); // Double-tap tactile haptic for mass reset
    try {
        const ctx = getAudioContext();
        const now = ctx.currentTime;

        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'square';
        osc1.frequency.setValueAtTime(140, now);
        osc1.frequency.exponentialRampToValueAtTime(40, now + 0.09);
        gain1.gain.setValueAtTime(0.35, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.09);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.09);

        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'triangle';
        osc2.frequency.setValueAtTime(450, now + 0.02);
        osc2.frequency.exponentialRampToValueAtTime(120, now + 0.12);
        gain2.gain.setValueAtTime(0.25, now + 0.02);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.12);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now + 0.02);
        osc2.stop(now + 0.12);
    } catch (e) {}
}
