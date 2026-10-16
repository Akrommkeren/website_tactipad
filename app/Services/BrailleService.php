<?php

namespace App\Services;

class BrailleService
{
    /**
     * Standard 6-dot Braille alphabet mappings.
     * Dots are sorted in ascending order for canonical matching.
     */
    protected static array $alphabetMap = [
        'A' => ['dots' => [1], 'name' => 'Huruf A', 'pronounce' => 'A'],
        'B' => ['dots' => [1, 2], 'name' => 'Huruf B', 'pronounce' => 'B'],
        'C' => ['dots' => [1, 4], 'name' => 'Huruf C', 'pronounce' => 'C'],
        'D' => ['dots' => [1, 4, 5], 'name' => 'Huruf D', 'pronounce' => 'D'],
        'E' => ['dots' => [1, 5], 'name' => 'Huruf E', 'pronounce' => 'E'],
        'F' => ['dots' => [1, 2, 4], 'name' => 'Huruf F', 'pronounce' => 'F'],
        'G' => ['dots' => [1, 2, 4, 5], 'name' => 'Huruf G', 'pronounce' => 'G'],
        'H' => ['dots' => [1, 2, 5], 'name' => 'Huruf H', 'pronounce' => 'H'],
        'I' => ['dots' => [2, 4], 'name' => 'Huruf I', 'pronounce' => 'I'],
        'J' => ['dots' => [2, 4, 5], 'name' => 'Huruf J', 'pronounce' => 'J'],
        'K' => ['dots' => [1, 3], 'name' => 'Huruf K', 'pronounce' => 'K'],
        'L' => ['dots' => [1, 2, 3], 'name' => 'Huruf L', 'pronounce' => 'L'],
        'M' => ['dots' => [1, 3, 4], 'name' => 'Huruf M', 'pronounce' => 'M'],
        'N' => ['dots' => [1, 3, 4, 5], 'name' => 'Huruf N', 'pronounce' => 'N'],
        'O' => ['dots' => [1, 3, 5], 'name' => 'Huruf O', 'pronounce' => 'O'],
        'P' => ['dots' => [1, 2, 3, 4], 'name' => 'Huruf P', 'pronounce' => 'P'],
        'Q' => ['dots' => [1, 2, 3, 4, 5], 'name' => 'Huruf Q', 'pronounce' => 'Q'],
        'R' => ['dots' => [1, 2, 3, 5], 'name' => 'Huruf R', 'pronounce' => 'R'],
        'S' => ['dots' => [2, 3, 4], 'name' => 'Huruf S', 'pronounce' => 'S'],
        'T' => ['dots' => [2, 3, 4, 5], 'name' => 'Huruf T', 'pronounce' => 'T'],
        'U' => ['dots' => [1, 3, 6], 'name' => 'Huruf U', 'pronounce' => 'U'],
        'V' => ['dots' => [1, 2, 3, 6], 'name' => 'Huruf V', 'pronounce' => 'V'],
        'W' => ['dots' => [2, 4, 5, 6], 'name' => 'Huruf W', 'pronounce' => 'W'],
        'X' => ['dots' => [1, 3, 4, 6], 'name' => 'Huruf X', 'pronounce' => 'X'],
        'Y' => ['dots' => [1, 3, 4, 5, 6], 'name' => 'Huruf Y', 'pronounce' => 'Y'],
        'Z' => ['dots' => [1, 3, 5, 6], 'name' => 'Huruf Z', 'pronounce' => 'Z'],
    ];

    /**
     * Standard Braille punctuation / symbols.
     */
    protected static array $punctuationMap = [
        '.' => ['dots' => [2, 5, 6], 'name' => 'Tanda Titik', 'pronounce' => 'Titik'],
        ',' => ['dots' => [2], 'name' => 'Tanda Koma', 'pronounce' => 'Koma'],
        ':' => ['dots' => [2, 5], 'name' => 'Titik Dua', 'pronounce' => 'Titik Dua'],
        ';' => ['dots' => [2, 3], 'name' => 'Titik Koma', 'pronounce' => 'Titik Koma'],
        '?' => ['dots' => [2, 3, 6], 'name' => 'Tanda Tanya', 'pronounce' => 'Tanda Tanya'],
        '!' => ['dots' => [2, 3, 5], 'name' => 'Tanda Seru', 'pronounce' => 'Tanda Seru'],
        '-' => ['dots' => [3, 6], 'name' => 'Tanda Hubung', 'pronounce' => 'Tanda Hubung'],
        '#' => ['dots' => [3, 4, 5, 6], 'name' => 'Tanda Angka (Prefix)', 'pronounce' => 'Tanda Angka'],
    ];

    /**
     * Number mappings (corresponding letter patterns).
     */
    protected static array $numberMap = [
        '1' => ['dots' => [1], 'name' => 'Angka Satu', 'pronounce' => 'Satu'],
        '2' => ['dots' => [1, 2], 'name' => 'Angka Dua', 'pronounce' => 'Dua'],
        '3' => ['dots' => [1, 4], 'name' => 'Angka Tiga', 'pronounce' => 'Tiga'],
        '4' => ['dots' => [1, 4, 5], 'name' => 'Angka Empat', 'pronounce' => 'Empat'],
        '5' => ['dots' => [1, 5], 'name' => 'Angka Lima', 'pronounce' => 'Lima'],
        '6' => ['dots' => [1, 2, 4], 'name' => 'Angka Enam', 'pronounce' => 'Enam'],
        '7' => ['dots' => [1, 2, 4, 5], 'name' => 'Angka Tujuh', 'pronounce' => 'Tujuh'],
        '8' => ['dots' => [1, 2, 5], 'name' => 'Angka Delapan', 'pronounce' => 'Delapan'],
        '9' => ['dots' => [2, 4], 'name' => 'Angka Sembilan', 'pronounce' => 'Sembilan'],
        '0' => ['dots' => [2, 4, 5], 'name' => 'Angka Nol', 'pronounce' => 'Nol'],
    ];

    /**
     * Convert global dot 1..30 to cell index (1..5) and local dot (1..6).
     */
    public static function globalDotToCellAndDot(int $globalDot): array
    {
        $globalDot = max(1, min(30, $globalDot));
        $cell = intdiv($globalDot - 1, 6) + 1;
        $dot = (($globalDot - 1) % 6) + 1;

        return ['cell' => $cell, 'dot' => $dot];
    }

    /**
     * Convert cell index (1..5) and local dot (1..6) to global dot 1..30.
     */
    public static function cellAndDotToGlobalDot(int $cell, int $dot): int
    {
        $cell = max(1, min(5, $cell));
        $dot = max(1, min(6, $dot));
        return (($cell - 1) * 6) + $dot;
    }

    /**
     * Canonical key generator for dots array.
     */
    public static function dotsToKey(array $dots): string
    {
        $filtered = array_filter(array_map('intval', $dots), fn ($d) => $d >= 1 && $d <= 6);
        $unique = array_unique($filtered);
        sort($unique);
        return implode(',', $unique);
    }

    /**
     * Identify a character based on active dots array (1..6).
     */
    public static function identify(array $dots, bool $numberMode = false): ?array
    {
        $key = self::dotsToKey($dots);
        if ($key === '') {
            return null;
        }

        if ($numberMode) {
            foreach (self::$numberMap as $char => $info) {
                if (self::dotsToKey($info['dots']) === $key) {
                    return array_merge($info, [
                        'char' => $char,
                        'unicode' => self::dotsToUnicode($info['dots']),
                        'type' => 'number',
                    ]);
                }
            }
        }

        // Check alphabet
        foreach (self::$alphabetMap as $char => $info) {
            if (self::dotsToKey($info['dots']) === $key) {
                return array_merge($info, [
                    'char' => $char,
                    'unicode' => self::dotsToUnicode($info['dots']),
                    'type' => 'letter',
                ]);
            }
        }

        // Check punctuation / special
        foreach (self::$punctuationMap as $char => $info) {
            if (self::dotsToKey($info['dots']) === $key) {
                return array_merge($info, [
                    'char' => $char,
                    'unicode' => self::dotsToUnicode($info['dots']),
                    'type' => 'punctuation',
                ]);
            }
        }

        // Unknown combination
        return [
            'char' => '?',
            'dots' => array_values(array_filter(array_map('intval', $dots))),
            'name' => 'Pola Belum Terdaftar (' . implode('-', $dots) . ')',
            'pronounce' => 'Kombinasi titik ' . implode(', ', $dots),
            'unicode' => self::dotsToUnicode($dots),
            'type' => 'unknown',
        ];
    }

    /**
     * Process all 5 cells and identify characters for each.
     */
    public static function resolveAllCells(array $cellsDots, bool $numberMode = false): array
    {
        $resolvedCells = [];
        $word = '';
        $brailleWord = '';

        for ($cell = 1; $cell <= 5; $cell++) {
            $dots = $cellsDots[$cell] ?? [];
            $identified = self::identify($dots, $numberMode);

            $resolvedCells[$cell] = [
                'cell' => $cell,
                'dots' => $dots,
                'char' => $identified['char'] ?? null,
                'unicode' => $identified['unicode'] ?? self::dotsToUnicode($dots),
                'name' => $identified['name'] ?? null,
                'pronounce' => $identified['pronounce'] ?? null,
            ];

            if ($identified && $identified['char'] !== '?') {
                $word .= $identified['char'];
                $brailleWord .= $identified['unicode'];
            }
        }

        return [
            'cells' => $resolvedCells,
            'word' => $word,
            'braille_word' => $brailleWord,
        ];
    }

    /**
     * Convert dots array to Unicode Braille Symbol (U+2800 .. U+283F).
     */
    public static function dotsToUnicode(array $dots): string
    {
        $offset = 0;
        $clean = array_map('intval', $dots);
        if (in_array(1, $clean)) $offset |= 0x01;
        if (in_array(2, $clean)) $offset |= 0x02;
        if (in_array(3, $clean)) $offset |= 0x04;
        if (in_array(4, $clean)) $offset |= 0x08;
        if (in_array(5, $clean)) $offset |= 0x10;
        if (in_array(6, $clean)) $offset |= 0x20;

        return mb_chr(0x2800 + $offset, 'UTF-8');
    }

    /**
     * Get complete dictionary of all letters, numbers, and symbols.
     */
    public static function getDictionary(): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('braille_dictionaries')) {
                $dbEntries = \App\Models\BrailleDictionary::where('is_active', true)->orderBy('sort_order')->get();
                if ($dbEntries->isNotEmpty()) {
                    $alphabet = [];
                    $numbers = [];
                    $punctuation = [];

                    foreach ($dbEntries as $row) {
                        $entry = [
                            'dots' => $row->dots,
                            'name' => $row->name,
                            'pronounce' => $row->pronounce,
                            'char' => $row->character,
                            'unicode' => $row->unicode ?? self::dotsToUnicode($row->dots),
                            'type' => $row->category === 'alphabet' ? 'letter' : $row->category,
                        ];

                        if ($row->category === 'alphabet') {
                            $alphabet[$row->character] = $entry;
                        } elseif ($row->category === 'number') {
                            $numbers[$row->character] = $entry;
                        } else {
                            $punctuation[$row->character] = $entry;
                        }
                    }

                    return [
                        'alphabet' => $alphabet,
                        'numbers' => $numbers,
                        'punctuation' => $punctuation,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Fallback to static mapping if database is unmigrated or offline
        }

        $alphabet = [];
        foreach (self::$alphabetMap as $char => $item) {
            $alphabet[$char] = array_merge($item, [
                'char' => $char,
                'unicode' => self::dotsToUnicode($item['dots']),
                'type' => 'letter',
            ]);
        }

        $numbers = [];
        foreach (self::$numberMap as $char => $item) {
            $numbers[$char] = array_merge($item, [
                'char' => $char,
                'unicode' => self::dotsToUnicode($item['dots']),
                'type' => 'number',
            ]);
        }

        $punctuation = [];
        foreach (self::$punctuationMap as $char => $item) {
            $punctuation[$char] = array_merge($item, [
                'char' => $char,
                'unicode' => self::dotsToUnicode($item['dots']),
                'type' => 'punctuation',
            ]);
        }

        return [
            'alphabet' => $alphabet,
            'numbers' => $numbers,
            'punctuation' => $punctuation,
        ];
    }
}
