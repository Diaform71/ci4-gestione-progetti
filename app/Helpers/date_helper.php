<?php

if (!function_exists('formatDateToItalian')) {
    /**
     * Converte una data dal formato ISO (yyyy-mm-dd) al formato italiano (dd/mm/yyyy)
     *
     * @param string|null $date Data in formato ISO
     * @return string Data formattata o stringa vuota se la data è null o non valida
     */
    function formatDateToItalian(?string $date): string
    {
        if (empty($date)) {
            return '';
        }
        
        try {
            return date('d/m/Y', strtotime($date));
        } catch (\Exception $e) {
            return '';
        }
    }
}

if (!function_exists('formatDateToISO')) {
    /**
     * Converte una data dal formato italiano (dd/mm/yyyy) al formato ISO (yyyy-mm-dd)
     *
     * @param string|null $date Data in formato italiano
     * @return string|null Data formattata o null se la data è null o non valida
     */
    function formatDateToISO(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        // Verifica se la data è già in formato ISO
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        
        // Splitta la data e verifica che sia in formato italiano
        $parts = explode('/', $date);
        if (count($parts) !== 3) {
            return null;
        }
        
        // Verifica che i componenti siano numeri validi
        if (!is_numeric($parts[0]) || !is_numeric($parts[1]) || !is_numeric($parts[2])) {
            return null;
        }
        
        // Crea la data in formato ISO
        $isoDate = sprintf('%04d-%02d-%02d', (int)$parts[2], (int)$parts[1], (int)$parts[0]);
        
        // Valida la data
        $dateTime = \DateTime::createFromFormat('Y-m-d', $isoDate);
        if ($dateTime && $dateTime->format('Y-m-d') === $isoDate) {
            return $isoDate;
        }
        
        return null;
    }
}

if (!function_exists('isValidItalianDate')) {
    /**
     * Verifica se una data in formato italiano è valida
     *
     * @param string|null $date Data in formato italiano (dd/mm/yyyy)
     * @return bool True se la data è valida, altrimenti false
     */
    function isValidItalianDate(?string $date): bool
    {
        if (empty($date)) {
            return false;
        }
        
        $parts = explode('/', $date);
        if (count($parts) !== 3) {
            return false;
        }
        
        // Verifica che i componenti siano numeri validi
        if (!is_numeric($parts[0]) || !is_numeric($parts[1]) || !is_numeric($parts[2])) {
            return false;
        }
        
        // Verifica che la data sia valida
        return checkdate((int)$parts[1], (int)$parts[0], (int)$parts[2]);
    }
} 