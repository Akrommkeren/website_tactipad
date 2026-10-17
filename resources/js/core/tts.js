/**
 * TACTIPAD: Core Text-to-Speech (TTS) & Audio Feedback Engine
 * Default: Suara Bahasa Indonesia Versi Google (Desktop & Mobile)
 */

let idVoice = null;
let audioCtx = null;
let isAudioUnlocked = false;
let activeUtterance = null;
let currentAudio = null;

export function loadVoices() {
    if (typeof window === 'undefined' || !('speechSynthesis' in window)) return;
    const voices = window.speechSynthesis.getVoices();
    if (!voices || voices.length === 0) return;

    const savedLang = localStorage.getItem('tactipad_lang') || 'id-ID';
    const langPrefix = savedLang.split('-')[0].toLowerCase();

    // Prioritas 1: Suara Google Bahasa Indonesia (Google Indonesian Voice)
    idVoice = voices.find(v => {
        const name = (v.name || '').toLowerCase();
        const lang = (v.lang || '').toLowerCase().replace('_', '-');
        const isIndo = lang.startsWith('id') || lang.startsWith('in') || name.includes('indonesia') || name.includes('bahasa');
        return isIndo && name.includes('google');
    })
    // Prioritas 2: Suara Google untuk bahasa terpilih
    || voices.find(v => (v.name || '').toLowerCase().includes('google') && v.lang && v.lang.toLowerCase().startsWith(langPrefix))
    // Prioritas 3: Suara Google lainnya
    || voices.find(v => (v.name || '').toLowerCase().includes('google'))
    // Prioritas 4: Bahasa Indonesia lainnya (id-ID atau in-ID)
    || voices.find(v => v.lang === savedLang || v.lang.replace('_', '-') === savedLang)
    || voices.find(v => v.lang && (v.lang.toLowerCase().startsWith(langPrefix) || (langPrefix === 'id' && v.lang.toLowerCase().startsWith('in'))))
    || voices.find(v => v.name && (v.name.toLowerCase().includes('indonesia') || v.name.toLowerCase().includes('bahasa')))
    // Prioritas 5: Default browser voice
    || voices.find(v => v.default)
    || voices[0];
}

/**
 * Mobile Audio & Speech Unlocker:
 * Mobile browsers require user interaction to unblock audio & speech synthesis.
 */
export function unlockMobileAudio() {
    try {
        const ctx = getAudioContext();
        if (ctx && ctx.state === 'suspended') {
            ctx.resume();
        }
        if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
            window.speechSynthesis.resume();
        }
        loadVoices();
        isAudioUnlocked = true;
    } catch (e) {}
}

export function initTTS() {
    loadVoices();
    if (typeof window !== 'undefined') {
        if ('speechSynthesis' in window && window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = () => {
                loadVoices();
            };
        }

        // Auto-unlock on first user gesture (touch / pointer / click)
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

/**
 * Web Audio Context Singleton
 */
export function getAudioContext() {
    if (!audioCtx && typeof window !== 'undefined') {
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

/**
 * Speaker Button Tap Chime: Instant auditory feedback on mobile & desktop
 */
export function playChimeSound() {
    if (localStorage.getItem('tactipad_audio') === 'false') return;
    triggerHaptic(20);
    try {
        const ctx = getAudioContext();
        if (!ctx) return;
        const now = ctx.currentTime + 0.01;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(587.33, now); // D5
        osc.frequency.exponentialRampToValueAtTime(880, now + 0.12); // A5
        gain.gain.setValueAtTime(0.3, now);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.25);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.25);
    } catch (e) {}
}

/**
 * Fallback Web Speech API (with Google Bahasa Indonesia as primary voice)
 */
function speakWebSpeech(text, targetRate = null, targetVol = null) {
    if (typeof window === 'undefined' || !('speechSynthesis' in window)) {
        playClickSound(880);
        return;
    }

    try {
        if (window.speechSynthesis.paused) {
            window.speechSynthesis.resume();
        }

        if (!idVoice) {
            loadVoices();
        }

        const utter = new SpeechSynthesisUtterance(text);

        // Global reference to prevent garbage collection halting speech in mobile Chrome
        window.__activeTtsUtterance = utter;
        activeUtterance = utter;

        utter.onend = () => {
            if (window.__activeTtsUtterance === utter) {
                window.__activeTtsUtterance = null;
            }
        };

        utter.onerror = (e) => {
            console.warn('Speech synthesis error:', e);
            playClickSound(880);
            if (window.__activeTtsUtterance === utter) {
                window.__activeTtsUtterance = null;
            }
        };

        const savedLang = localStorage.getItem('tactipad_lang') || 'id-ID';

        if (idVoice) {
            utter.voice = idVoice;
            const voiceLang = (idVoice.lang || '').toLowerCase();
            if (voiceLang.startsWith('id') || voiceLang.startsWith('in')) {
                utter.lang = idVoice.lang || 'id-ID';
            } else if (voiceLang.startsWith(savedLang.split('-')[0].toLowerCase())) {
                utter.lang = savedLang;
            } else {
                utter.lang = idVoice.lang || 'id-ID';
            }
        } else {
            utter.lang = savedLang;
        }

        // Safe volume parsing (handles 0-1 and 0-100 formats)
        let vol = targetVol !== null ? targetVol : 1.0;
        if (targetVol === null) {
            const rawVol = localStorage.getItem('tactipad_volume');
            if (rawVol !== null) {
                const p = parseFloat(rawVol);
                if (!isNaN(p)) {
                    vol = p > 1 ? p / 100 : p;
                }
            }
        }
        utter.volume = Math.max(0.1, Math.min(1.0, vol));

        const savedRate = targetRate !== null ? targetRate : parseFloat(localStorage.getItem('tactipad_rate') || '1.0');
        utter.rate = isNaN(savedRate) ? 1.0 : Math.max(0.5, Math.min(2.0, savedRate));

        const savedPitch = parseFloat(localStorage.getItem('tactipad_pitch') || '1.0');
        utter.pitch = isNaN(savedPitch) ? 1.0 : Math.max(0.5, Math.min(2.0, savedPitch));

        window.speechSynthesis.speak(utter);

        if (window.speechSynthesis.paused) {
            window.speechSynthesis.resume();
        }
    } catch (err) {
        console.warn('Speech synthesis speak failed:', err);
        playClickSound(880);
    }
}

export function setAudioRate(newRate) {
    const rate = parseFloat(newRate);
    if (!isNaN(rate) && currentAudio) {
        try {
            currentAudio.defaultPlaybackRate = rate;
            currentAudio.playbackRate = rate;
        } catch (e) {}
    }
}

export function setAudioVolume(newVol) {
    const vol = parseFloat(newVol);
    if (!isNaN(vol) && currentAudio) {
        try {
            currentAudio.volume = Math.max(0, Math.min(1.0, vol));
        } catch (e) {}
    }
}

/**
 * Main Speak Function:
 * Prioritizes authentic Google Bahasa Indonesia voice stream on both desktop & mobile.
 * Accurately honors and applies playbackRate across all lifecycle events.
 * Automatically falls back to Web Speech API (Google voice) if offline/blocked.
 */
export function speak(text, customRate = null) {
    if (localStorage.getItem('tactipad_audio') === 'false') return;
    if (!text || typeof window === 'undefined') return;

    try {
        const ctx = getAudioContext();
        if (ctx && ctx.state === 'suspended') {
            ctx.resume();
        }
    } catch (e) {}

    // Stop previous audio if playing
    if (currentAudio) {
        try {
            currentAudio.pause();
            currentAudio.currentTime = 0;
        } catch (e) {}
        currentAudio = null;
    }

    // Cancel ongoing web speech
    if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
        try {
            if (window.speechSynthesis.speaking || window.speechSynthesis.pending) {
                window.speechSynthesis.cancel();
            }
        } catch (e) {}
    }

    const savedLang = localStorage.getItem('tactipad_lang') || 'id-ID';
    const langCode = savedLang.split('-')[0].toLowerCase();
    const encoded = encodeURIComponent(text.trim());

    // Calculate target rate and volume
    const rawRate = customRate !== null ? customRate : parseFloat(localStorage.getItem('tactipad_rate') || '1.0');
    const targetRate = isNaN(rawRate) ? 1.0 : Math.max(0.5, Math.min(2.0, rawRate));

    const rawVol = localStorage.getItem('tactipad_volume');
    let targetVol = 1.0;
    if (rawVol !== null) {
        const p = parseFloat(rawVol);
        if (!isNaN(p)) targetVol = p > 1 ? p / 100 : p;
    }
    targetVol = Math.max(0.1, Math.min(1.0, targetVol));

    // Stream Authentic Google Bahasa Indonesia Audio
    try {
        const audio = new Audio();
        currentAudio = audio;

        audio.volume = targetVol;
        audio.defaultPlaybackRate = targetRate;

        // Ensure playbackRate is strictly applied and pitch is preserved
        const enforcePlaybackRate = () => {
            try {
                audio.defaultPlaybackRate = targetRate;
                audio.playbackRate = targetRate;
                if ('preservesPitch' in audio) {
                    audio.preservesPitch = true;
                } else if ('mozPreservesPitch' in audio) {
                    audio.mozPreservesPitch = true;
                } else if ('webkitPreservesPitch' in audio) {
                    audio.webkitPreservesPitch = true;
                }
            } catch (e) {}
        };

        // Attach listeners across all lifecycle events
        audio.addEventListener('loadstart', enforcePlaybackRate);
        audio.addEventListener('loadedmetadata', enforcePlaybackRate);
        audio.addEventListener('canplay', enforcePlaybackRate);
        audio.addEventListener('play', enforcePlaybackRate);
        audio.addEventListener('playing', enforcePlaybackRate);
        audio.addEventListener('ended', () => {
            if (currentAudio === audio) {
                currentAudio = null;
            }
        });

        // Set src AFTER setting defaultPlaybackRate & listeners
        audio.src = `/tts?text=${encoded}&lang=${langCode}`;
        enforcePlaybackRate();

        let fallbackDone = false;
        const triggerFallback = () => {
            if (fallbackDone) return;
            fallbackDone = true;
            if (window.speechSynthesis && (window.speechSynthesis.speaking || window.speechSynthesis.pending)) {
                try { window.speechSynthesis.cancel(); } catch (e) {}
                setTimeout(() => speakWebSpeech(text, targetRate, targetVol), 50);
            } else {
                speakWebSpeech(text, targetRate, targetVol);
            }
        };

        audio.onerror = () => {
            // Direct Google fallback if proxy has an issue
            audio.onerror = () => triggerFallback();
            audio.src = `https://translate.google.com/translate_tts?ie=UTF-8&tl=${langCode}&client=tw-ob&q=${encoded}`;
            enforcePlaybackRate();
            const retryPromise = audio.play();
            if (retryPromise !== undefined) {
                retryPromise.then(() => {
                    enforcePlaybackRate();
                }).catch(() => triggerFallback());
            }
        };

        const playPromise = audio.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                enforcePlaybackRate();
            }).catch(() => {
                triggerFallback();
            });
        }
    } catch (e) {
        speakWebSpeech(text, targetRate, targetVol);
    }
}

export function playClickSound(freq = 950) {
    if (localStorage.getItem('tactipad_audio') === 'false') return;
    triggerHaptic(12);
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
    triggerHaptic([30, 25, 30]);
    try {
        const ctx = getAudioContext();
        if (!ctx) return;
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
