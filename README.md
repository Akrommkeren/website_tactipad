# WEBISTE TACTIPAD: Media Tulis & Baca Braille Tuna Netra

Aplikasi web pendidikan & assistive technology berbasis **Laravel 13** dan **Tailwind CSS** yang terintegrasi langsung dengan alat fisik **TACTIPAD** (papan tulis & baca Braille 30 titik dengan mekanisme reset masal).

---

## Halaman & Fitur Utama

### 1. [Papan Utama Tactipad](http://localhost:8000/) (`/`)
- Tampilan ramah mata dengan palet **soft white-slate & calming blue**, tanpa efek berlebihan.
- **Papan 30 Titik Braille (5 Huruf)**:
  - 5 sel Braille berderet, masing-masing 2 kolom x 3 baris.
  - Jarak titik rapat (~0.5 mm) dan jarak pemisah antar-huruf (~1.0 mm).
  - Titik belum ditekan berupa **lingkaran kosong (outline)** (`○`), dan saat ditekan berubah menjadi **warna hitam pekat** (`●`).
- **Deteksi Kata 5 Huruf Real-time**: Menampilkan huruf pada masing-masing sel dan kata yang terbentuk (contoh: `B U K U`).
- **Tombol Reset Masal 30 Titik**: Mengembalikan seluruh pin ke posisi awal secara serentak.
- **Penyusun Kalimat**: Simpan kata ke rangkaian kalimat dengan tombol Spasi atau tombol di layar.
- **Dukungan Audio**: Text-to-Speech bahasa Indonesia & efek suara mekanik clack.

### 2. [Kamus Huruf & Simbol Braille](http://localhost:8000/kamus) (`/kamus`)
- Halaman referensi lengkap yang terpisah agar halaman utama tetap bersih dan fokus:
  - **Alfabet (A - Z)**: Dilengkapi diagram posisi titik dan tombol dengarkan suara.
  - **Angka (0 - 9)**: Menjelaskan tanda angka (prefix) dan polanya.
  - **Tanda Baca & Simbol**: Titik, koma, tanda tanya, tanda seru, dll.
  - Fitur pencarian cepat dan filter kategori.

### 3. [Latihan Soal Interaktif](http://localhost:8000/latihan) (`/latihan`)
- Halaman belajar dan asah kemampuan untuk siswa:
  - **Mode 1 (Tebak Huruf)**: Disajikan pola titik Braille, siswa memilih huruf Latin yang sesuai (dilengkapi pembaca audio soal).
  - **Mode 2 (Praktik Bentuk Pola)**: Diberikan instruksi sebuah huruf, siswa mengklik titik-titik pada Tactipad mini untuk membentuk pola yang benar.
  - Skor benar & salah otomatis, streak, serta evaluasi suara langsung.
