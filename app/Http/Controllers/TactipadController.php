<?php

namespace App\Http\Controllers;

use App\Models\TactipadEvent;
use App\Services\BrailleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TactipadController extends Controller
{
    protected const CACHE_KEY = 'tactipad_current_state_v2';

    /**
     * Get or initialize current device state from cache.
     * Manages 5 cells (each 6 dots, total 30 dots).
     */
    protected function getCurrentState(): array
    {
        $defaultCells = [
            1 => [],
            2 => [],
            3 => [],
            4 => [],
            5 => [],
        ];

        $state = Cache::get(self::CACHE_KEY, [
            'device_id' => 'TACTIPAD-01',
            'cells' => $defaultCells,
            'global_dots' => [],
            'is_reset' => true,
            'source' => 'system',
            'updated_at' => now()->toISOString(),
            'version' => 1,
        ]);

        if (!isset($state['cells']) || count($state['cells']) !== 5) {
            $state['cells'] = $defaultCells;
        }

        // Compute global active dots and resolved characters
        $globalDots = [];
        for ($c = 1; $c <= 5; $c++) {
            $dots = $state['cells'][$c] ?? [];
            foreach ($dots as $d) {
                $globalDots[] = BrailleService::cellAndDotToGlobalDot($c, $d);
            }
        }
        sort($globalDots);
        $state['global_dots'] = $globalDots;

        // Also keep active_dots (cell 1) for backward compatibility
        $state['active_dots'] = $state['cells'][1] ?? [];

        $resolved = BrailleService::resolveAllCells($state['cells']);
        $state['resolved_cells'] = $resolved['cells'];
        $state['word'] = $resolved['word'];
        $state['braille_word'] = $resolved['braille_word'];
        $state['recognized'] = $resolved['cells'][1] ?? null;

        return $state;
    }

    /**
     * Save current device state to cache.
     */
    protected function saveCurrentState(array $state): array
    {
        $state['version'] = ($state['version'] ?? 0) + 1;
        $state['updated_at'] = now()->toISOString();
        Cache::put(self::CACHE_KEY, $state, now()->addHours(24));
        return $state;
    }

    /**
     * Display the main Tactipad web interface.
     */
    public function index()
    {
        $state = $this->getCurrentState();
        $recentEvents = TactipadEvent::latest()->take(10)->get();

        return view('tactipad', compact('state', 'recentEvents'));
    }

    /**
     * Halaman Kamus Baca Braille
     */
    public function kamus()
    {
        $dictionary = BrailleService::getDictionary();
        $manualBookDriveUrl = env('MANUAL_BOOK_DRIVE_URL', 'https://drive.google.com/file/d/1ZkO3Y0pwead-vSw-gKUJjwnXuMM_Y6Ff/view?usp=drive_link');
        return view('kamus', compact('dictionary', 'manualBookDriveUrl'));
    }

    /**
     * Halaman Latihan Soal Braille
     */
    public function latihan()
    {
        $dictionary = BrailleService::getDictionary();
        return view('latihan', compact('dictionary'));
    }

    /**
     * Halaman Pengaturan Tactipad
     */
    public function pengaturan()
    {
        $state = $this->getCurrentState();
        return view('pengaturan', compact('state'));
    }

    /**
     * API: Get current device status.
     */
    public function getStatus(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->getCurrentState(),
        ]);
    }

    /**
     * API: Push hardware or simulated event.
     */
    public function pushEvent(Request $request): JsonResponse
    {
        $action = $request->input('action', 'toggle_dot'); // toggle_dot, dot_press, dot_release, set_dots, set_cell, reset
        $cell = $request->input('cell'); // 1 - 5
        $dot = $request->input('dot'); // 1 - 6
        $globalDot = $request->input('global_dot'); // 1 - 30
        $dotsInput = $request->input('dots'); // array
        $cellsInput = $request->input('cells'); // map of 5 cells
        $source = $request->input('source', 'hardware');
        $deviceId = $request->input('device_id', 'TACTIPAD-01');

        $state = $this->getCurrentState();
        $state['device_id'] = $deviceId;
        $state['source'] = $source;

        // 1. MASS RESET
        if ($action === 'reset' || $action === 'mass_reset') {
            $state['cells'] = [1 => [], 2 => [], 3 => [], 4 => [], 5 => []];
            $state['global_dots'] = [];
            $state['active_dots'] = [];
            $state['is_reset'] = true;
            $state['word'] = '';
            $state['braille_word'] = '';

            $resolved = BrailleService::resolveAllCells($state['cells']);
            $state['resolved_cells'] = $resolved['cells'];
            $state['recognized'] = null;

            $event = TactipadEvent::create([
                'device_id' => $deviceId,
                'action' => 'reset',
                'dot_index' => null,
                'active_dots' => [],
                'recognized_char' => null,
                'source' => $source,
                'raw_payload' => $request->all(),
            ]);

            try {
                \App\Models\Device::updateOrCreate(
                    ['device_code' => $deviceId],
                    [
                        'status' => 'connected',
                        'last_connected_at' => now(),
                        'connection_type' => in_array($source, ['bluetooth', 'usb_serial', 'wifi', 'simulator']) ? $source : 'simulator',
                    ]
                );
            } catch (\Throwable $e) {}

            $savedState = $this->saveCurrentState($state);

            return response()->json([
                'success' => true,
                'message' => 'Reset masal 30 titik berhasil. Seluruh 5 huruf kembali ke lingkaran kosong.',
                'event' => $event,
                'state' => $savedState,
            ]);
        }

        // 2. Set all 5 cells or all dots at once
        if ($action === 'set_all_cells' && is_array($cellsInput)) {
            for ($c = 1; $c <= 5; $c++) {
                $rawDots = $cellsInput[$c] ?? [];
                $state['cells'][$c] = array_values(array_unique(array_filter(array_map('intval', (array)$rawDots), fn($d) => $d >= 1 && $d <= 6)));
                sort($state['cells'][$c]);
            }
        } elseif ($action === 'set_cell' && $cell && is_array($dotsInput)) {
            $c = max(1, min(5, intval($cell)));
            $state['cells'][$c] = array_values(array_unique(array_filter(array_map('intval', $dotsInput), fn($d) => $d >= 1 && $d <= 6)));
            sort($state['cells'][$c]);
        } elseif ($action === 'set_dots' && is_array($dotsInput)) {
            // If cell specified, set that cell; otherwise default to cell 1
            $c = $cell ? max(1, min(5, intval($cell))) : 1;
            $state['cells'][$c] = array_values(array_unique(array_filter(array_map('intval', $dotsInput), fn($d) => $d >= 1 && $d <= 6)));
            sort($state['cells'][$c]);
        } else {
            // Resolve cell and dot from global_dot (1..30) or cell & dot
            $targetCell = 1;
            $targetDot = 1;

            if ($globalDot !== null) {
                $coord = BrailleService::globalDotToCellAndDot(intval($globalDot));
                $targetCell = $coord['cell'];
                $targetDot = $coord['dot'];
            } elseif ($dot !== null) {
                $targetDot = max(1, min(6, intval($dot)));
                $targetCell = $cell ? max(1, min(5, intval($cell))) : 1;
            }

            $currentCellDots = $state['cells'][$targetCell] ?? [];

            if ($action === 'dot_release') {
                $currentCellDots = array_values(array_diff($currentCellDots, [$targetDot]));
            } elseif ($action === 'dot_press') {
                if (!in_array($targetDot, $currentCellDots)) {
                    $currentCellDots[] = $targetDot;
                }
            } else {
                // toggle_dot
                if (in_array($targetDot, $currentCellDots)) {
                    $currentCellDots = array_values(array_diff($currentCellDots, [$targetDot]));
                } else {
                    $currentCellDots[] = $targetDot;
                }
            }

            sort($currentCellDots);
            $state['cells'][$targetCell] = $currentCellDots;
        }

        // Recalculate global dots
        $globalDots = [];
        $hasAnyDot = false;
        for ($c = 1; $c <= 5; $c++) {
            $dots = $state['cells'][$c] ?? [];
            if (!empty($dots)) $hasAnyDot = true;
            foreach ($dots as $d) {
                $globalDots[] = BrailleService::cellAndDotToGlobalDot($c, $d);
            }
        }
        sort($globalDots);

        $state['global_dots'] = $globalDots;
        $state['active_dots'] = $state['cells'][1] ?? [];
        $state['is_reset'] = !$hasAnyDot;

        $resolved = BrailleService::resolveAllCells($state['cells']);
        $state['resolved_cells'] = $resolved['cells'];
        $state['word'] = $resolved['word'];
        $state['braille_word'] = $resolved['braille_word'];
        $state['recognized'] = $resolved['cells'][$cell ?? 1] ?? $resolved['cells'][1];

        $event = TactipadEvent::create([
            'device_id' => $deviceId,
            'action' => $action,
            'dot_index' => $dot ? intval($dot) : ($globalDot ? intval($globalDot) : null),
            'active_dots' => $state['active_dots'],
            'recognized_char' => $state['word'] ?: null,
            'source' => $source,
            'raw_payload' => $request->all(),
        ]);

        try {
            \App\Models\Device::updateOrCreate(
                ['device_code' => $deviceId],
                [
                    'status' => 'connected',
                    'last_connected_at' => now(),
                    'connection_type' => in_array($source, ['bluetooth', 'usb_serial', 'wifi', 'simulator']) ? $source : 'simulator',
                ]
            );
        } catch (\Throwable $e) {}

        $savedState = $this->saveCurrentState($state);

        return response()->json([
            'success' => true,
            'action' => $action,
            'cell' => $cell ?? 1,
            'dot' => $dot,
            'global_dot' => $globalDot,
            'state' => $savedState,
            'word' => $savedState['word'],
            'resolved_cells' => $savedState['resolved_cells'],
            'recognized' => $savedState['recognized'],
            'active_dots' => $savedState['active_dots'],
            'event' => $event,
        ]);
    }

    /**
     * API: Trigger mass reset.
     */
    public function resetDevice(Request $request): JsonResponse
    {
        $request->merge(['action' => 'reset']);
        return $this->pushEvent($request);
    }

    /**
     * API: Directly set active dots array.
     */
    public function setDots(Request $request): JsonResponse
    {
        $request->validate([
            'dots' => 'required|array',
            'dots.*' => 'integer|between:1,6',
        ]);

        $request->merge(['action' => 'set_dots']);
        return $this->pushEvent($request);
    }

    /**
     * API: Server-Sent Events (SSE) stream for live updates.
     */
    public function streamEvents(Request $request): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($request) {
            $lastVersion = intval($request->query('last_version', 0));
            $startTime = time();

            // Run stream loop for up to 25 seconds
            while (time() - $startTime < 25) {
                if (connection_aborted()) {
                    break;
                }

                $state = $this->getCurrentState();
                $currentVersion = $state['version'] ?? 0;

                if ($currentVersion > $lastVersion) {
                    echo "data: " . json_encode($state) . "\n\n";
                    ob_flush();
                    flush();
                    $lastVersion = $currentVersion;
                }

                echo ": heartbeat\n\n";
                ob_flush();
                flush();

                usleep(400000);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    /**
     * API: Get recent event logs.
     */
    public function getHistory(): JsonResponse
    {
        $history = TactipadEvent::latest()->take(30)->get();
        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    /**
     * API: Clear logs.
     */
    public function clearHistory(): JsonResponse
    {
        TactipadEvent::truncate();
        return response()->json([
            'success' => true,
            'message' => 'Riwayat event berhasil dibersihkan.',
        ]);
    }

    /**
     * Download or view ESP32 / Arduino sample code for Tactipad device (30 dots / 5 cells).
     */
    public function downloadArduinoSketch()
    {
        $code = <<<'INOCONTENT'
/*
  =============================================================================
  WEB-TACTIPAD: Firmware Interkoneksi Alat Fisik & Website
  Media Pembelajaran Braille Interaktif 30 Titik Taktil (5 Sel x 6 Titik)
  =============================================================================
  
  Fitur Utama:
  1. Koneksi Nirkabel Web Bluetooth API (BLE Nordic UART Service)
  2. Koneksi Kabel USB Serial (Baud Rate: 115200)
  3. Matriks 30 Titik Taktil (5 Sel Huruf Braille, 6 Titik per Sel)
  4. Pengendalian Sistem Reset Elektromagnetik Masal (Sakelar Fisik & Remote Web)
  5. Audio Buzzer Feedback & Pemantau Baterai Rechargeable
  
  Kompatibilitas Board:
  - ESP32 (ESP32 Dev Module, NodeMCU-32S, ESP32-WROOM-32) - Rekomendasi BLE & USB
  - Arduino Uno / Nano / Mega - Mode USB Serial (atau BLE dengan Modul HM-10)

  Petunjuk Penggunaan:
  1. Buka Arduino IDE, pilih board "ESP32 Dev Module" (atau board Arduino Anda).
  2. Jika menggunakan ESP32, library BLE sudah terpasang secara default di ESP32 Core.
  3. Upload firmware ke perangkat TACTIPAD.
  4. Buka website TACTIPAD pada menu "Pengaturan" -> Klik "Hubungkan Perangkat"
     untuk menyambungkan via Bluetooth tanpa perlu driver tambahan!
  =============================================================================
*/

#include <Arduino.h>

// =============================================================================
// KONFIGURASI BLUETOOTH LOW ENERGY (ESP32 BLE)
// =============================================================================
#if defined(ESP32)
#include <BLEDevice.h>
#include <BLEServer.h>
#include <BLEUtils.h>
#include <BLE2902.h>

// UUID Standar Nordic UART Service (NUS) - Sesuai dengan Web Bluetooth API Website
#define SERVICE_UUID           "6e400001-b5a3-f393-e0a9-e50e24dcca9e"
#define CHARACTERISTIC_UUID_RX "6e400002-b5a3-f393-e0a9-e50e24dcca9e"
#define CHARACTERISTIC_UUID_TX "6e400003-b5a3-f393-e0a9-e50e24dcca9e"

BLEServer* pServer = NULL;
BLECharacteristic* pTxCharacteristic = NULL;
bool deviceConnected = false;
bool oldDeviceConnected = false;
#endif

// =============================================================================
// KONFIGURASI PIN HARDWARE TACTIPAD
// =============================================================================
#if defined(ESP32)
  const int PIN_RESET_SWITCH = 33;     // Sakelar Reset Elektromagnetik Fisik (Input Pull-up)
  const int PIN_ELECTROMAGNET = 25;   // Triger Relay/MOSFET Solenoid Reset Elektromagnetik
  const int PIN_BUZZER = 4;           // Audio Buzzer Feedback
  const int PIN_BATTERY_ADC = 34;     // ADC Pemantau Tegangan Baterai Rechargeable
#else
  const int PIN_RESET_SWITCH = 2;      // Arduino Uno/Nano: Interrupt Pin D2
  const int PIN_ELECTROMAGNET = 8;    // Arduino Uno/Nano: Pin D8
  const int PIN_BUZZER = 9;           // Arduino Uno/Nano: Pin D9
  const int PIN_BATTERY_ADC = A0;     // Arduino Uno/Nano: Pin A0
#endif

// Kompatibilitas variabel pengujian
const int PIN_RESET = PIN_RESET_SWITCH;

// =============================================================================
// MATRIKS STATUS 30 TITIK TAKTIL (5 SEL x 6 TITIK)
// =============================================================================
// Sel 1 - Sel 5 (Indeks 0 - 4), Titik 1 - 6 (Indeks 0 - 5)
bool dotMatrix[5][6] = {0};

// Variabel Debounce Sakelar Reset
unsigned long lastResetTrigger = 0;
const unsigned long RESET_DEBOUNCE_MS = 500;

// =============================================================================
// PROTOTYPE FUNGSI
// =============================================================================
void broadcastPayload(String jsonPayload);
void sendDotEvent(int cell, int dot, bool state);
void executeElectromagneticReset(bool triggeredByWeb = false);
void playToneBeep(int freq, int durationMs);

// =============================================================================
// CALLBACK BLE (KONEKSI & PENERIMAAN PERINTAH DARI WEBSITE)
// =============================================================================
#if defined(ESP32)
class ServerCallbacks : public BLEServerCallbacks {
  void onConnect(BLEServer* pServer) {
    deviceConnected = true;
    playToneBeep(1200, 100);
    Serial.println("[BLE]: Terhubung dengan Website TACTIPAD!");
  }

  void onDisconnect(BLEServer* pServer) {
    deviceConnected = false;
    playToneBeep(400, 150);
    Serial.println("[BLE]: Koneksi terputus. Memulai ulang iklan BLE...");
    pServer->getAdvertising()->start();
  }
};

class CharacteristicCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue().c_str();
    rxValue.trim();
    if (rxValue.length() > 0) {
      Serial.print("[BLE Terima]: ");
      Serial.println(rxValue);

      // Tangani perintah RESET dari Website
      if (rxValue == "RESET" || rxValue.indexOf("\"action\":\"reset\"") >= 0) {
        executeElectromagneticReset(true);
      }
    }
  }
};
#endif

// =============================================================================
// FUNGSI BROADCAST DATA (USB SERIAL + BLUETOOTH BLE)
// =============================================================================
void broadcastPayload(String jsonPayload) {
  // 1. Kirim via USB Serial
  Serial.println(jsonPayload);

  // 2. Kirim via Web Bluetooth API jika terkoneksi
#if defined(ESP32)
  if (deviceConnected && pTxCharacteristic != NULL) {
    pTxCharacteristic->setValue((uint8_t*)jsonPayload.c_str(), jsonPayload.length());
    pTxCharacteristic->notify();
    delay(10); // Buffer delay BLE stack
  }
#endif
}

// =============================================================================
// EVENT TITIK TAKTIL
// =============================================================================
void sendDotEvent(int cell, int dot, bool state) {
  int globalDot = ((cell - 1) * 6) + dot;
  dotMatrix[cell - 1][dot - 1] = state;

  // Format JSON sesuai protokol TACTIPAD
  String json = "{\"action\":\"toggle_dot\",\"cell\":";
  json += cell;
  json += ",\"dot\":";
  json += dot;
  json += ",\"global_dot\":";
  json += globalDot;
  json += ",\"state\":";
  json += (state ? "true" : "false");
  json += "}";

  playToneBeep(800 + (dot * 100), 40);
  broadcastPayload(json);
}

// =============================================================================
// SISTEM RESET ELEKTROMAGNETIK MASAL
// =============================================================================
void executeElectromagneticReset(bool triggeredByWeb) {
  Serial.println("[RESET]: Mengaktifkan solenoid reset elektromagnetik masal...");

  // Bunyikan nada indikasi reset
  playToneBeep(650, 100);

  // Aktifkan pulsa daya ke kumparan elektromagnet / solenoid selama 250 milidetik
  digitalWrite(PIN_ELECTROMAGNET, HIGH);
  delay(250);
  digitalWrite(PIN_ELECTROMAGNET, LOW);

  // Bersihkan memori matriks 30 titik
  for (int c = 0; c < 5; c++) {
    for (int d = 0; d < 6; d++) {
      dotMatrix[c][d] = false;
    }
  }

  // Kirim event reset ke Website TACTIPAD
  String resetJson = "{\"action\":\"reset\",\"source\":\"";
  resetJson += (triggeredByWeb ? "web_remote" : "hardware_switch");
  resetJson += "\"}";
  broadcastPayload(resetJson);

  playToneBeep(900, 150);
}

// =============================================================================
// AUDIO BUZZER FEEDBACK
// =============================================================================
void playToneBeep(int freq, int durationMs) {
#if defined(ESP32)
  // Simple square wave on ESP32
  tone(PIN_BUZZER, freq, durationMs);
#else
  tone(PIN_BUZZER, freq, durationMs);
#endif
}

// =============================================================================
// SETUP
// =============================================================================
void setup() {
  Serial.begin(115200);
  delay(300);

  // Inisialisasi Pin I/O
  pinMode(PIN_RESET_SWITCH, INPUT_PULLUP);
  pinMode(PIN_ELECTROMAGNET, OUTPUT);
  digitalWrite(PIN_ELECTROMAGNET, LOW);
  pinMode(PIN_BUZZER, OUTPUT);

  // Inisialisasi Bluetooth Low Energy (ESP32)
#if defined(ESP32)
  Serial.println("[BLE]: Menginisialisasi Bluetooth Low Energy...");
  BLEDevice::init("TACTIPAD-01");

  pServer = BLEDevice::createServer();
  pServer->setCallbacks(new ServerCallbacks());

  BLEService* pService = pServer->createService(SERVICE_UUID);

  // Karakteristik TX (Notify ke Web Browser)
  pTxCharacteristic = pService->createCharacteristic(
    CHARACTERISTIC_UUID_TX,
    BLECharacteristic::PROPERTY_NOTIFY | BLECharacteristic::PROPERTY_READ
  );
  pTxCharacteristic->addDescriptor(new BLE2902());

  // Karakteristik RX (Menerima perintah dari Web Browser)
  BLECharacteristic* pRxCharacteristic = pService->createCharacteristic(
    CHARACTERISTIC_UUID_RX,
    BLECharacteristic::PROPERTY_WRITE | BLECharacteristic::PROPERTY_WRITE_NR
  );
  pRxCharacteristic->setCallbacks(new CharacteristicCallbacks());

  pService->start();

  // Memulai Iklan BLE (Advertising)
  BLEAdvertising* pAdvertising = BLEDevice::getAdvertising();
  pAdvertising->addServiceUUID(SERVICE_UUID);
  pAdvertising->setScanResponse(true);
  pAdvertising->setMinPreferred(0x06);
  pAdvertising->setMinPreferred(0x12);
  BLEDevice::startAdvertising();

  Serial.println("[BLE]: Siap! Nama Perangkat: TACTIPAD-01");
#endif

  // Notifikasi Siap
  playToneBeep(1000, 150);
  Serial.println("{\"status\":\"ready\",\"device\":\"TACTIPAD-01\",\"dots\":30,\"cells\":5}");
}

// =============================================================================
// LOOP UTAMA
// =============================================================================
void loop() {
  unsigned long now = millis();

  // 1. Cek Sakelar Reset Elektromagnetik Fisik
  if (digitalRead(PIN_RESET_SWITCH) == LOW) {
    if (now - lastResetTrigger > RESET_DEBOUNCE_MS) {
      lastResetTrigger = now;
      executeElectromagneticReset(false);
    }
  }

  // 2. Tangani Perintah Serial dari USB (Komputer / Laptop)
  if (Serial.available()) {
    String cmd = Serial.readStringUntil('\n');
    cmd.trim();
    if (cmd.length() > 0) {
      if (cmd == "RESET" || cmd.indexOf("\"action\":\"reset\"") >= 0) {
        executeElectromagneticReset(true);
      } else if (cmd.startsWith("DOT,")) {
        // Format simulasi serial: "DOT,cell,dot" (contoh: "DOT,1,1")
        int firstComma = cmd.indexOf(',');
        int secondComma = cmd.indexOf(',', firstComma + 1);
        if (firstComma > 0 && secondComma > 0) {
          int c = cmd.substring(firstComma + 1, secondComma).toInt();
          int d = cmd.substring(secondComma + 1).toInt();
          if (c >= 1 && c <= 5 && d >= 1 && d <= 6) {
            sendDotEvent(c, d, !dotMatrix[c - 1][d - 1]);
          }
        }
      }
    }
  }

  // 3. Tangani Rekoneksi BLE (ESP32)
#if defined(ESP32)
  if (!deviceConnected && oldDeviceConnected) {
    delay(500);
    pServer->startAdvertising();
    oldDeviceConnected = deviceConnected;
  }
  if (deviceConnected && !oldDeviceConnected) {
    oldDeviceConnected = deviceConnected;
  }
#endif

  delay(20);
}
INOCONTENT;

        return response($code, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="tactipad_30dots_firmware.ino"',
        ]);
    }

    /**
     * Google TTS Proxy Stream: Ensures authentic Google Bahasa Indonesia voice across desktop and mobile.
     */
    public function getGoogleTTS(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $text = trim((string) $request->query('text', ''));
        $lang = trim((string) $request->query('lang', 'id'));

        if ($text === '') {
            return response('', 204);
        }

        $url = 'https://translate.google.com/translate_tts?ie=UTF-8&tl=' . urlencode($lang) . '&client=tw-ob&q=' . urlencode(mb_substr($text, 0, 200));

        try {
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
                    'timeout' => 4,
                ],
            ]);
            $audio = @file_get_contents($url, false, $context);
            if ($audio !== false && strlen($audio) > 0) {
                return response($audio, 200, [
                    'Content-Type' => 'audio/mpeg',
                    'Content-Length' => strlen($audio),
                    'Accept-Ranges' => 'bytes',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        } catch (\Throwable $e) {}

        return response('', 502);
    }
}
