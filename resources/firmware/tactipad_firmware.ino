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
