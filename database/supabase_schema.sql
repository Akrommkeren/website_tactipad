-- =============================================================================
-- TACTIPAD SUPABASE POSTGRESQL SCHEMA & INITIAL DATA
-- Project: https://bqnxprmxqojvpzyoyhuh.supabase.co
-- Generated for TACTIPAD Interactive Braille Learning System
-- =============================================================================

-- 1. TABEL DEVICES (Perangkat Fisik Tactipad)
CREATE TABLE IF NOT EXISTS devices (
    id BIGSERIAL PRIMARY KEY,
    device_code VARCHAR(50) UNIQUE DEFAULT 'TACTIPAD-01',
    name VARCHAR(255) DEFAULT 'TACTIPAD Braille Board',
    connection_type VARCHAR(50) DEFAULT 'bluetooth',
    bluetooth_mac VARCHAR(100),
    ip_address VARCHAR(45),
    battery_level SMALLINT DEFAULT 100,
    firmware_version VARCHAR(50) DEFAULT 'v2.1.0-BLE',
    status VARCHAR(50) DEFAULT 'disconnected',
    last_connected_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Seed perangkat default
INSERT INTO devices (device_code, name, connection_type, battery_level, firmware_version, status, last_connected_at)
VALUES ('TACTIPAD-01', 'TACTIPAD 5-Cell Braille Display', 'bluetooth', 100, 'v2.1.0-BLE', 'standby', CURRENT_TIMESTAMP)
ON CONFLICT (device_code) DO NOTHING;

-- 2. TABEL TACTIPAD_EVENTS (Log Telemetri & Input Titik/Tombol)
CREATE TABLE IF NOT EXISTS tactipad_events (
    id BIGSERIAL PRIMARY KEY,
    device_id VARCHAR(50) DEFAULT 'TACTIPAD-01',
    action VARCHAR(50) NOT NULL,
    dot_index SMALLINT,
    active_dots JSONB,
    recognized_char VARCHAR(10),
    source VARCHAR(50) DEFAULT 'simulator',
    raw_payload JSONB,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_tactipad_events_device ON tactipad_events(device_id);
CREATE INDEX IF NOT EXISTS idx_tactipad_events_action ON tactipad_events(action);

-- 3. TABEL BRAILLE_DICTIONARIES (Kamus Braille: Huruf, Angka, & Simbol)
CREATE TABLE IF NOT EXISTS braille_dictionaries (
    id BIGSERIAL PRIMARY KEY,
    character VARCHAR(10) NOT NULL,
    category VARCHAR(30) NOT NULL,
    dots JSONB NOT NULL,
    unicode VARCHAR(10),
    name VARCHAR(100) NOT NULL,
    pronounce VARCHAR(100) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_braille_dict_char ON braille_dictionaries(character);
CREATE INDEX IF NOT EXISTS idx_braille_dict_cat ON braille_dictionaries(category);

-- Seed data kamus lengkap
INSERT INTO braille_dictionaries (character, category, dots, unicode, name, pronounce, description, sort_order) VALUES
-- ALFABET A - Z
('A', 'alphabet', '[1]', '⠁', 'Huruf A', 'A', 'Titik Braille: 1', 1),
('B', 'alphabet', '[1, 2]', '⠃', 'Huruf B', 'B', 'Titik Braille: 1, 2', 2),
('C', 'alphabet', '[1, 4]', '⠉', 'Huruf C', 'C', 'Titik Braille: 1, 4', 3),
('D', 'alphabet', '[1, 4, 5]', '⠙', 'Huruf D', 'D', 'Titik Braille: 1, 4, 5', 4),
('E', 'alphabet', '[1, 5]', '⠑', 'Huruf E', 'E', 'Titik Braille: 1, 5', 5),
('F', 'alphabet', '[1, 2, 4]', '⠋', 'Huruf F', 'F', 'Titik Braille: 1, 2, 4', 6),
('G', 'alphabet', '[1, 2, 4, 5]', '⠛', 'Huruf G', 'G', 'Titik Braille: 1, 2, 4, 5', 7),
('H', 'alphabet', '[1, 2, 5]', '⠓', 'Huruf H', 'H', 'Titik Braille: 1, 2, 5', 8),
('I', 'alphabet', '[2, 4]', '⠊', 'Huruf I', 'I', 'Titik Braille: 2, 4', 9),
('J', 'alphabet', '[2, 4, 5]', '⠚', 'Huruf J', 'J', 'Titik Braille: 2, 4, 5', 10),
('K', 'alphabet', '[1, 3]', '⠅', 'Huruf K', 'K', 'Titik Braille: 1, 3', 11),
('L', 'alphabet', '[1, 2, 3]', '⠇', 'Huruf L', 'L', 'Titik Braille: 1, 2, 3', 12),
('M', 'alphabet', '[1, 3, 4]', '⠍', 'Huruf M', 'M', 'Titik Braille: 1, 3, 4', 13),
('N', 'alphabet', '[1, 3, 4, 5]', '⠝', 'Huruf N', 'N', 'Titik Braille: 1, 3, 4, 5', 14),
('O', 'alphabet', '[1, 3, 5]', '⠕', 'Huruf O', 'O', 'Titik Braille: 1, 3, 5', 15),
('P', 'alphabet', '[1, 2, 3, 4]', '⠏', 'Huruf P', 'P', 'Titik Braille: 1, 2, 3, 4', 16),
('Q', 'alphabet', '[1, 2, 3, 4, 5]', '⠟', 'Huruf Q', 'Q', 'Titik Braille: 1, 2, 3, 4, 5', 17),
('R', 'alphabet', '[1, 2, 3, 5]', '⠗', 'Huruf R', 'R', 'Titik Braille: 1, 2, 3, 5', 18),
('S', 'alphabet', '[2, 3, 4]', '⠎', 'Huruf S', 'S', 'Titik Braille: 2, 3, 4', 19),
('T', 'alphabet', '[2, 3, 4, 5]', '⠞', 'Huruf T', 'T', 'Titik Braille: 2, 3, 4, 5', 20),
('U', 'alphabet', '[1, 3, 6]', '⠥', 'Huruf U', 'U', 'Titik Braille: 1, 3, 6', 21),
('V', 'alphabet', '[1, 2, 3, 6]', '⠧', 'Huruf V', 'V', 'Titik Braille: 1, 2, 3, 6', 22),
('W', 'alphabet', '[2, 4, 5, 6]', '⠺', 'Huruf W', 'W', 'Titik Braille: 2, 4, 5, 6', 23),
('X', 'alphabet', '[1, 3, 4, 6]', '⠭', 'Huruf X', 'X', 'Titik Braille: 1, 3, 4, 6', 24),
('Y', 'alphabet', '[1, 3, 4, 5, 6]', '⠽', 'Huruf Y', 'Y', 'Titik Braille: 1, 3, 4, 5, 6', 25),
('Z', 'alphabet', '[1, 3, 5, 6]', '⠵', 'Huruf Z', 'Z', 'Titik Braille: 1, 3, 5, 6', 26),
-- ANGKA 0 - 9
('1', 'number', '[1]', '⠁', 'Angka Satu', 'Satu', 'Titik Braille: 1', 27),
('2', 'number', '[1, 2]', '⠃', 'Angka Dua', 'Dua', 'Titik Braille: 1, 2', 28),
('3', 'number', '[1, 4]', '⠉', 'Angka Tiga', 'Tiga', 'Titik Braille: 1, 4', 29),
('4', 'number', '[1, 4, 5]', '⠙', 'Angka Empat', 'Empat', 'Titik Braille: 1, 4, 5', 30),
('5', 'number', '[1, 5]', '⠑', 'Angka Lima', 'Lima', 'Titik Braille: 1, 5', 31),
('6', 'number', '[1, 2, 4]', '⠋', 'Angka Enam', 'Enam', 'Titik Braille: 1, 2, 4', 32),
('7', 'number', '[1, 2, 4, 5]', '⠛', 'Angka Tujuh', 'Tujuh', 'Titik Braille: 1, 2, 4, 5', 33),
('8', 'number', '[1, 2, 5]', '⠓', 'Angka Delapan', 'Delapan', 'Titik Braille: 1, 2, 5', 34),
('9', 'number', '[2, 4]', '⠊', 'Angka Sembilan', 'Sembilan', 'Titik Braille: 2, 4', 35),
('0', 'number', '[2, 4, 5]', '⠚', 'Angka Nol', 'Nol', 'Titik Braille: 2, 4, 5', 36),
-- TANDA BACA & SIMBOL
('.', 'punctuation', '[2, 5, 6]', '⠲', 'Tanda Titik', 'Titik', 'Titik Braille: 2, 5, 6', 37),
(',', 'punctuation', '[2]', '⠂', 'Tanda Koma', 'Koma', 'Titik Braille: 2', 38),
(':', 'punctuation', '[2, 5]', '⠒', 'Titik Dua', 'Titik Dua', 'Titik Braille: 2, 5', 39),
(';', 'punctuation', '[2, 3]', '⠆', 'Titik Koma', 'Titik Koma', 'Titik Braille: 2, 3', 40),
('?', 'punctuation', '[2, 3, 6]', '⠦', 'Tanda Tanya', 'Tanda Tanya', 'Titik Braille: 2, 3, 6', 41),
('!', 'punctuation', '[2, 3, 5]', '⠖', 'Tanda Seru', 'Tanda Seru', 'Titik Braille: 2, 3, 5', 42),
('-', 'punctuation', '[3, 6]', '⠤', 'Tanda Hubung', 'Tanda Hubung', 'Titik Braille: 3, 6', 43),
('#', 'punctuation', '[3, 4, 5, 6]', '⠼', 'Tanda Angka (Prefix)', 'Tanda Angka', 'Titik Braille: 3, 4, 5, 6', 44);

-- 4. TABEL PRACTICE_QUESTIONS & PRACTICE_LOGS (Kuis Latihan & Riwayat Skor Siswa)
CREATE TABLE IF NOT EXISTS practice_questions (
    id BIGSERIAL PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    prompt_text TEXT NOT NULL,
    target_text VARCHAR(50) NOT NULL,
    expected_cells JSONB NOT NULL,
    difficulty VARCHAR(20) DEFAULT 'easy',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS practice_logs (
    id BIGSERIAL PRIMARY KEY,
    question_id BIGINT REFERENCES practice_questions(id) ON DELETE SET NULL,
    device_code VARCHAR(50) DEFAULT 'TACTIPAD-01',
    user_answer VARCHAR(100),
    is_correct BOOLEAN DEFAULT FALSE,
    duration_seconds INT DEFAULT 0,
    cell_states JSONB,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_practice_logs_device ON practice_logs(device_code);
