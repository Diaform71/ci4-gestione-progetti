<?php

if (!function_exists('getStatoBadgeClass')) {
    /**
     * Restituisce la classe del badge in base allo stato del progetto
     *
     * @param string $stato
     * @return string
     */
    function getStatoBadgeClass(string $stato): string
    {
        return match ($stato) {
            'in_corso' => 'primary',
            'completato' => 'success',
            'sospeso' => 'warning',
            'annullato' => 'danger',
            default => 'secondary',
        };
    }
}

if (!function_exists('formatFileSize')) {
    /**
     * Formatta le dimensioni di un file in formato leggibile
     *
     * @param int $bytes Dimensione del file in bytes
     * @param int $decimals Numero di decimali
     * @return string
     */
    function formatFileSize(int $bytes, int $decimals = 2): string
    {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        
        return sprintf("%.{$decimals}f", $bytes / (1024 ** $factor)) . ' ' . $size[$factor];
    }
} 