<?php

namespace Database\Seeders;

use App\Models\BrailleDictionary;
use App\Services\BrailleService;
use Illuminate\Database\Seeder;

class BrailleDictionarySeeder extends Seeder
{
    public function run(): void
    {
        $alphabet = [
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

        $numbers = [
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

        $punctuation = [
            '.' => ['dots' => [2, 5, 6], 'name' => 'Tanda Titik', 'pronounce' => 'Titik'],
            ',' => ['dots' => [2], 'name' => 'Tanda Koma', 'pronounce' => 'Koma'],
            ':' => ['dots' => [2, 5], 'name' => 'Titik Dua', 'pronounce' => 'Titik Dua'],
            ';' => ['dots' => [2, 3], 'name' => 'Titik Koma', 'pronounce' => 'Titik Koma'],
            '?' => ['dots' => [2, 3, 6], 'name' => 'Tanda Tanya', 'pronounce' => 'Tanda Tanya'],
            '!' => ['dots' => [2, 3, 5], 'name' => 'Tanda Seru', 'pronounce' => 'Tanda Seru'],
            '-' => ['dots' => [3, 6], 'name' => 'Tanda Hubung', 'pronounce' => 'Tanda Hubung'],
            '#' => ['dots' => [3, 4, 5, 6], 'name' => 'Tanda Angka (Prefix)', 'pronounce' => 'Tanda Angka'],
        ];

        $order = 1;
        foreach ($alphabet as $char => $info) {
            BrailleDictionary::updateOrCreate(
                ['character' => $char, 'category' => 'alphabet'],
                [
                    'dots' => $info['dots'],
                    'unicode' => BrailleService::dotsToUnicode($info['dots']),
                    'name' => $info['name'],
                    'pronounce' => $info['pronounce'],
                    'description' => 'Titik Braille: ' . implode(', ', $info['dots']),
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );
        }

        foreach ($numbers as $char => $info) {
            BrailleDictionary::updateOrCreate(
                ['character' => $char, 'category' => 'number'],
                [
                    'dots' => $info['dots'],
                    'unicode' => BrailleService::dotsToUnicode($info['dots']),
                    'name' => $info['name'],
                    'pronounce' => $info['pronounce'],
                    'description' => 'Titik Braille: ' . implode(', ', $info['dots']),
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );
        }

        foreach ($punctuation as $char => $info) {
            BrailleDictionary::updateOrCreate(
                ['character' => $char, 'category' => 'punctuation'],
                [
                    'dots' => $info['dots'],
                    'unicode' => BrailleService::dotsToUnicode($info['dots']),
                    'name' => $info['name'],
                    'pronounce' => $info['pronounce'],
                    'description' => 'Titik Braille: ' . implode(', ', $info['dots']),
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );
        }
    }
}
