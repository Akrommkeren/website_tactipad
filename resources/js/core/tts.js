/**
 * TACTIPAD: Core Text-to-Speech (TTS) & Audio Feedback Engine
 */

let idVoice = null;
let audioCtx = null;

export function loadVoices() {
    if (!('speechSynthesis' in window)) return;
    const voices = window.speechSynthesis.getVoices();
    idVoice = voices.find(v => v.lang.includes('id') || v.name.includes('Indonesian')) || voices[0];
}

export function initTTS() {
    loadVoices();
    if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
        if (window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
        }
    }
}

export function speak(text) {
    if (localStorage.getItem('tactipad_audio') === 'false') return;
    if (!('speechSynthesis' in window) || !text) return;

    window.speechSynthesis.cancel();
    const utter = new SpeechSynthesisUtterance(text);
    if (idVoice) utter.voice = idVoice;
    utter.lang = 'id-ID';

    const savedRate = parseFloat(localStorage.getItem('tactipad_rate') || '1.0');
    const savedPitch = parseFloat(localStorage.getItem('tactipad_pitch') || '1.0');

    utter.rate = isNaN(savedRate) ? 1.0 : Math.max(0.5, Math.min(2.0, savedRate));
    utter.pitch = isNaN(savedPitch) ? 1.0 : Math.max(0.5, Math.min(2.0, savedPitch));

    window.speechSynthesis.speak(utter);
}

export function getAudioContext() {
    if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

export function playClickSound(freq = 950) {
    if (localStorage.getItem('tactipad_audio') === 'false') return;
    try {
        const ctx = getAudioContext();
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
