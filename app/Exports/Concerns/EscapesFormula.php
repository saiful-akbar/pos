<?php

declare(strict_types=1);

namespace App\Exports\Concerns;

trait EscapesFormula
{
    /**
     * Mencegah formula injection: teks yang diawali karakter formula diberi awalan tanda petik.
     */
    protected function safeText(?string $value): string
    {
        $text = (string) $value;

        // Tanda strip tunggal ("-") adalah placeholder kosong, bukan formula.
        return $text !== '' && $text !== '-' && in_array($text[0], ['=', '+', '-', '@', "\t", "\r"], true)
            ? "'".$text
            : $text;
    }
}
