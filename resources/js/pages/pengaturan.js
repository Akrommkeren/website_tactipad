/**
 * TACTIPAD: Halaman Pengaturan (Web Bluetooth API, Audio Preferences, & System Tools)
 */

import { playChimeSound, speak } from '../core/tts';

export function initPengaturanPage() {
    // LocalStorage State
    let isAudioOn = localStorage.getItem('tactipad_audio') !== 'false';
    let speechRate = parseFloat(localStorage.getItem('tactipad_rate') || '1.0');
    let rawVol = localStorage.getItem('tactipad_volume');
    let speechVolume = 1.0;
    if (rawVol !== null) {
        let parsed = parseFloat(rawVol);
        if (!isNaN(parsed)) {
            speechVolume = parsed > 1 ? parsed / 100 : parsed;
        }
    }
    let selectedLang = localStorage.getItem('tactipad_lang') || 'id-ID';

    // DOM Elements - Audio
    const toggleAudioBtn = document.getElementById('toggleAudioBtn');
    const audioStatusDesc = document.getElementById('audioStatusDesc');

    // Custom Language Dropdown Elements
    const langDropdownContainer = document.getElementById('langDropdownContainer');
    const langDropdownBtn = document.getElementById('langDropdownBtn');
    const langDropdownMenu = document.getElementById('langDropdownMenu');
    const currentLangLabel = document.getElementById('currentLangLabel');
    const langChevron = document.getElementById('langChevron');

    const speechVolumeSlider = document.getElementById('speechVolumeSlider');
    const volumeValueText = document.getElementById('volumeValueText');
    const speechRateSlider = document.getElementById('speechRateSlider');
    const rateValueText = document.getElementById('rateValueText');
    const testVoiceBtn = document.getElementById('testVoiceBtn');
    const toggleContrastBtn = document.getElementById('toggleContrastBtn');

    // DOM Elements - Bluetooth
    const connectBluetoothBtn = document.getElementById('connectBluetoothBtn');
    const bluetoothStatusDesc = document.getElementById('bluetoothStatusDesc');
    const bluetoothBadge = document.getElementById('bluetoothBadge');
    const bluetoothBtnText = document.getElementById('bluetoothBtnText');
    const serialLogBox = document.getElementById('serialLogBox');

    let bluetoothDevice = null;
    let bluetoothServer = null;

    // Language Label Map
    const langLabelMap = {
        'id-ID': 'Bahasa Indonesia',
        'en-US': 'English (US)',
        'en-GB': 'English (UK)',
        'ms-MY': 'Bahasa Melayu',
        'ar-SA': 'Bahasa Arab',
        'ja-JP': 'Bahasa Jepang'
    };

    if (currentLangLabel) {
        currentLangLabel.textContent = langLabelMap[selectedLang] || selectedLang;
    }

    if (speechVolumeSlider && volumeValueText) {
        speechVolumeSlider.value = Math.round(speechVolume * 100);
        volumeValueText.textContent = Math.round(speechVolume * 100) + '%';
        speechVolumeSlider.addEventListener('input', (e) => {
            const val = parseInt(e.target.value, 10);
            speechVolume = val / 100;
            volumeValueText.textContent = val + '%';
            localStorage.setItem('tactipad_volume', speechVolume);
        });
    }

    if (speechRateSlider && rateValueText) {
        speechRateSlider.value = speechRate;
        rateValueText.textContent = speechRate.toFixed(1) + 'x';
        speechRateSlider.addEventListener('input', (e) => {
            speechRate = parseFloat(e.target.value);
            rateValueText.textContent = speechRate.toFixed(1) + 'x';
            localStorage.setItem('tactipad_rate', speechRate);
        });
    }

    // Audio Toggle Button
    function updateAudioUI() {
        if (!toggleAudioBtn) return;
        if (isAudioOn) {
            toggleAudioBtn.textContent = 'Suara Aktif';
            toggleAudioBtn.className = 'lever-press px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer shadow-xs';
            if (audioStatusDesc) audioStatusDesc.textContent = 'Suara aktif saat titik dan kata terbentuk';
        } else {
            toggleAudioBtn.textContent = 'Suara Mati';
            toggleAudioBtn.className = 'lever-press px-4 py-2 rounded-xl text-xs font-bold bg-slate-200 text-slate-700 hover:bg-slate-300 transition cursor-pointer shadow-xs';
            if (audioStatusDesc) audioStatusDesc.textContent = 'Suara dinonaktifkan';
        }
    }
    updateAudioUI();

    toggleAudioBtn?.addEventListener('click', () => {
        isAudioOn = !isAudioOn;
        localStorage.setItem('tactipad_audio', isAudioOn ? 'true' : 'false');
        updateAudioUI();
        if (isAudioOn) speak('Suara pengucapan diaktifkan.');
    });

    // Language Dropdown
    if (langDropdownBtn && langDropdownMenu) {
        langDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = langDropdownMenu.classList.contains('hidden');
            if (isHidden) {
                langDropdownMenu.classList.remove('hidden');
                if (langChevron) langChevron.classList.add('rotate-180');
            } else {
                langDropdownMenu.classList.add('hidden');
                if (langChevron) langChevron.classList.remove('rotate-180');
            }
        });

        document.addEventListener('click', (e) => {
            if (langDropdownContainer && !langDropdownContainer.contains(e.target)) {
                langDropdownMenu.classList.add('hidden');
                if (langChevron) langChevron.classList.remove('rotate-180');
            }
        });

        document.querySelectorAll('.lang-option').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                selectedLang = item.getAttribute('data-lang');
                const label = item.getAttribute('data-label');
                if (currentLangLabel) currentLangLabel.textContent = label;
                localStorage.setItem('tactipad_lang', selectedLang);

                langDropdownMenu.classList.add('hidden');
                if (langChevron) langChevron.classList.remove('rotate-180');
                speak('Bahasa berhasil diubah ke ' + label);
            });
        });
    }

    testVoiceBtn?.addEventListener('click', () => {
        playChimeSound();
        speak('Halo! Ini adalah uji coba suara sistem pembelajaran TACTIPAD.');
    });

    // High Contrast Toggle
    if (toggleContrastBtn) {
        const isDark = localStorage.getItem('tactipad_contrast') === 'true';
        toggleContrastBtn.textContent = isDark ? 'Nonaktifkan Kontras' : 'Aktifkan Kontras';

        toggleContrastBtn.addEventListener('click', () => {
            const currentlyDark = document.body.classList.contains('dark-theme');
            const newDark = !currentlyDark;
            localStorage.setItem('tactipad_contrast', newDark ? 'true' : 'false');
            if (newDark) {
                document.body.classList.add('dark-theme');
                toggleContrastBtn.textContent = 'Nonaktifkan Kontras';
            } else {
                document.body.classList.remove('dark-theme');
                toggleContrastBtn.textContent = 'Aktifkan Kontras';
            }
            const sunIcon = document.getElementById('themeIconSun');
            const moonIcon = document.getElementById('themeIconMoon');
            if (sunIcon && moonIcon) {
                sunIcon.classList.toggle('hidden', newDark);
                moonIcon.classList.toggle('hidden', !newDark);
            }
        });
    }

    function logSerial(msg) {
        if (!serialLogBox) return;
        const time = new Date().toLocaleTimeString('id-ID');
        const entry = document.createElement('div');
        entry.textContent = `[${time}] ${msg}`;
        serialLogBox.appendChild(entry);
        serialLogBox.scrollTop = serialLogBox.scrollHeight;
    }

    // Web Bluetooth API
    if (connectBluetoothBtn) {
        connectBluetoothBtn.addEventListener('click', async () => {
            if (bluetoothDevice && bluetoothDevice.gatt && bluetoothDevice.gatt.connected) {
                disconnectBluetooth();
                return;
            }

            if (!('bluetooth' in navigator)) {
                logSerial('Perhatian: Browser ini belum mendukung Web Bluetooth API.');
                alert('Web Bluetooth API hanya didukung di Google Chrome, Microsoft Edge, atau Opera pada perangkat dengan modul Bluetooth aktif.');
                return;
            }

            try {
                logSerial('Membuka jendela pemindaian Bluetooth...');
                if (bluetoothStatusDesc) {
                    bluetoothStatusDesc.textContent = 'Mencari sinyal Bluetooth perangkat Tactipad di sekitar Anda...';
                }
                if (bluetoothBtnText) {
                    bluetoothBtnText.textContent = 'Memindai Bluetooth...';
                }

                const device = await navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: [
                        'generic_access',
                        'battery_service',
                        '0000ffe0-0000-1000-8000-00805f9b34fb',
                        '6e400001-b5a3-f393-e0a9-e50e24dcca9e',
                        '4fafc201-1fb5-459e-8fcc-c5c9c331914b'
                    ]
                });

                bluetoothDevice = device;
                logSerial(`Perangkat Bluetooth dipilih: "${device.name || 'Tactipad'}" (ID: ${device.id})`);
                device.addEventListener('gattserverdisconnected', onBluetoothDisconnected);

                logSerial('Menyambungkan ke GATT Server Bluetooth...');
                const server = await device.gatt.connect();
                bluetoothServer = server;

                logSerial('Koneksi Bluetooth Berhasil Terjalin!');
                if (bluetoothStatusDesc) {
                    bluetoothStatusDesc.textContent = `Terhubung ke ${device.name || 'Tactipad'} via Bluetooth. Siap menerima input titik.`;
                }

                if (bluetoothBadge) {
                    bluetoothBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
                    bluetoothBadge.textContent = 'Terhubung';
                }

                connectBluetoothBtn.className = 'lever-press px-6 py-3 rounded-xl text-xs sm:text-sm font-bold bg-emerald-600 hover:bg-red-600 text-white shadow-xs transition cursor-pointer flex items-center justify-center gap-2 shrink-0';
                if (bluetoothBtnText) bluetoothBtnText.textContent = 'Putuskan Sambungan';
                speak('Alat Tactipad berhasil terhubung melalui sinyal Bluetooth.');

                try {
                    const services = await server.getPrimaryServices();
                    for (const service of services) {
                        logSerial(`Layanan Bluetooth aktif: ${service.uuid}`);
                        const characteristics = await service.getCharacteristics();
                        for (const char of characteristics) {
                            if (char.properties.notify || char.properties.indicate) {
                                await char.startNotifications();
                                char.addEventListener('characteristicvaluechanged', handleBluetoothData);
                                logSerial(`Mendengarkan data nirkabel dari UUID: ${char.uuid}`);
                            }
                        }
                    }
                } catch (charErr) {
                    logSerial('Koneksi dasar GATT aktif. Siap menerima sinyal.');
                }
            } catch (err) {
                logSerial(`Pemindaian Bluetooth dibatalkan atau gagal: ${err.message}`);
                onBluetoothDisconnected();
            }
        });
    }

    function onBluetoothDisconnected() {
        logSerial('Koneksi Bluetooth terputus.');
        if (bluetoothStatusDesc) {
            bluetoothStatusDesc.innerHTML = '<strong>Aktifkan Bloetooth perangkat anda</strong> dan Klik tombol di samping untuk menghubungkan alat fisik Tactipad ke website!';
        }

        if (bluetoothBadge) {
            bluetoothBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200';
            bluetoothBadge.textContent = 'Belum Terhubung';
        }

        if (connectBluetoothBtn) {
            connectBluetoothBtn.className = 'lever-press px-6 py-3 rounded-xl text-xs sm:text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center justify-center gap-2 shrink-0';
        }
        if (bluetoothBtnText) bluetoothBtnText.textContent = 'Hubungkan Perangkat';
        bluetoothDevice = null;
        bluetoothServer = null;
    }

    function disconnectBluetooth() {
        if (bluetoothDevice && bluetoothDevice.gatt && bluetoothDevice.gatt.connected) {
            bluetoothDevice.gatt.disconnect();
        }
        onBluetoothDisconnected();
        speak('Koneksi Bluetooth diputuskan.');
    }

    function handleBluetoothData(event) {
        const value = event.target.value;
        const decoder = new TextDecoder('utf-8');
        const text = decoder.decode(value).trim();
        if (text) {
            logSerial(`[BLE Input]: ${text}`);
            try {
                const parsed = JSON.parse(text);
                sendEventToServer(parsed);
            } catch (e) {
                if (text === 'RESET') {
                    sendEventToServer({ action: 'reset', source: 'bluetooth' });
                }
            }
        }
    }

    async function sendEventToServer(payload) {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            await fetch('/event', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload)
            });
        } catch (err) {}
    }
}
