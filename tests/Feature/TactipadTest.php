<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TactipadTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_with_30_dots_5_cells_interface(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('TACTIPAD');
        $response->assertSee('Icon-Tactipad.png');
        $response->assertSee('Braille');
        $response->assertSee('Latihan Soal');
        $response->assertSee('Tentang');
        $response->assertSee('Pengaturan');
        $response->assertSee('Papan Tactipad');
        $response->assertSee('Hasil Terbentuk');
        $response->assertSee('Pola 1');
        $response->assertSee('Pola 5');
    }

    public function test_kamus_page_loads_successfully(): void
    {
        $response = $this->get('/kamus');
        $response->assertStatus(200);
        $response->assertSee('Tentang');
        $response->assertSee('TACTIPAD');
        $response->assertSee('Cara Penggunaan Alat');
        $response->assertSee('Menggunakan TACTIPAD');
        $response->assertSee('Setelah Selesai Digunakan');
        $response->assertSee('Mengaktifkan Sistem Reset');
        $response->assertSee('Prosedur Pengisian Baterai');
        $response->assertSee('Dokumen Manual Book');
        $response->assertSee('Integrasi Alat dan Website');
        $response->assertSee('Cara Penggunaan Website');
        $response->assertSee('Koneksi Alat');
        $response->assertSee('lalu klik tombol <strong>Hubungkan Perangkat</strong>', false);
        $response->assertSee('sambungkan kabel USB alat ke perangkat atau laptop');
        $response->assertSee('audio pintar dari perangkat');
        $response->assertSee('Soal Huruf');
        $response->assertSee('Huruf A-Z atau Kata 5 Huruf');
        $response->assertSee('Kamus Braille');
        $response->assertSee('Semua Kategori');
        $response->assertSee('1ZkO3Y0pwead-vSw-gKUJjwnXuMM_Y6Ff');
        $response->assertSee('Alfabet Braille');
        $response->assertSee('Angka Braille');
    }

    public function test_latihan_page_loads_successfully(): void
    {
        $response = $this->get('/latihan');
        $response->assertStatus(200);
        $response->assertSee('Latihan Soal');
        $response->assertSee('Semua Soal');
        $response->assertSee('Soal Huruf');
    }

    public function test_pengaturan_page_loads_successfully(): void
    {
        $response = $this->get('/pengaturan');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan');
        $response->assertSee('Suara');
        $response->assertSee('Koneksi Perangkat');
        $response->assertSee('Web Bluetooth API');
        $response->assertSee('Aktifkan Bloetooth perangkat anda');
        $response->assertSee('menghubungkan alat fisik Tactipad ke website');
        $response->assertSee('Hubungkan Perangkat');
    }

    public function test_dot_press_event_and_letter_recognition(): void
    {
        // Dot 1 on cell 1 forms letter A
        $response = $this->postJson('/api/tactipad/event', [
            'action' => 'set_dots',
            'cell' => 1,
            'dots' => [1],
            'source' => 'test'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'active_dots' => [1],
            'recognized' => [
                'char' => 'A',
                'name' => 'Huruf A',
            ]
        ]);
    }

    public function test_dots_1_and_2_form_letter_b(): void
    {
        $response = $this->postJson('/api/tactipad/event', [
            'action' => 'set_dots',
            'cell' => 1,
            'dots' => [1, 2],
            'source' => 'test'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'active_dots' => [1, 2],
            'recognized' => [
                'char' => 'B',
                'name' => 'Huruf B',
            ]
        ]);
    }

    public function test_multi_cell_word_formation(): void
    {
        $response = $this->postJson('/api/tactipad/event', [
            'action' => 'set_all_cells',
            'cells' => [
                1 => [1, 2],
                2 => [1, 3, 6],
                3 => [1, 3],
                4 => [1, 3, 6],
                5 => [],
            ],
            'source' => 'test'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'word' => 'BUKU',
        ]);
    }

    public function test_mass_reset_clears_all_30_dots(): void
    {
        $this->postJson('/api/tactipad/event', [
            'action' => 'set_all_cells',
            'cells' => [
                1 => [1, 2],
                2 => [1, 4],
            ],
            'source' => 'test'
        ]);

        $response = $this->postJson('/api/tactipad/reset', [
            'source' => 'test_reset_button'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'state' => [
                'is_reset' => true,
                'word' => '',
            ]
        ]);

        $this->assertDatabaseHas('tactipad_events', [
            'action' => 'reset',
        ]);
    }

    public function test_arduino_sketch_download_endpoint(): void
    {
        $response = $this->get('/arduino-sketch');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('WEB-TACTIPAD');
        $response->assertSee('PIN_RESET');
        $response->assertSee('6e400001-b5a3-f393-e0a9-e50e24dcca9e');
        $response->assertSee('TACTIPAD-01');
        $response->assertSee('executeElectromagneticReset');
    }
}
