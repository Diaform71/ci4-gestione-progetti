<?php

/**
 * CIFunctions Helper 
 * Funzioni di utilità generale per l'applicazione
 */

if (!function_exists('get_settings')) {
    /**
     * Ottiene le impostazioni del sistema
     *
     * @return object Le impostazioni del sistema
     */
    function get_settings()
    {
        $db = \Config\Database::connect();
        
        // Valori predefiniti
        $default_settings = [
            'document_logo' => 'default_logo.png'
            // Aggiungi altri valori predefiniti qui se necessario
        ];
        
        $settings = (object) $default_settings;
        
        // Verifica se esiste la tabella settings
        $tables = $db->listTables();
        if (in_array('settings', $tables)) {
            $builder = $db->table('settings');
            $db_settings = $builder->get()->getFirstRow();
            
            if (!empty($db_settings)) {
                // Merge dei valori dal database con i valori predefiniti
                foreach ($default_settings as $key => $value) {
                    if (!isset($db_settings->$key)) {
                        $db_settings->$key = $value;
                    }
                }
                $settings = $db_settings;
            }
        } else {
            // Se non esiste la tabella, controlla il file di impostazioni predefinite
            $defaultSettingsPath = WRITEPATH . 'default_settings.php';
            if (file_exists($defaultSettingsPath)) {
                $file_settings = include $defaultSettingsPath;
                // Verifica che $file_settings sia un array
                if (is_array($file_settings)) {
                    // Merge dei valori dal file con i valori predefiniti
                    $settings = (object) array_merge($default_settings, $file_settings);
                } else {
                    // Se non è un array, usa solo i valori predefiniti
                    $settings = (object) $default_settings;
                }
            }
        }
        
        // Verifica finale: se document_logo non è impostato, usa il valore predefinito
        if (!isset($settings->document_logo) || empty($settings->document_logo)) {
            $settings->document_logo = 'default_logo.png';
        }
        
        // Verifica l'esistenza fisica del file
        $logo_path = './public/images/system/' . $settings->document_logo;
        if (!file_exists($logo_path) || !is_readable($logo_path)) {
            // Se il file non esiste o non è leggibile, non usare alcun logo
            $settings->document_logo = null;
        }
        
        return $settings;
    }
}

if (!function_exists('calculate_row_total')) {
    /**
     * Calcola il totale di una riga dell'ordine
     *
     * @param float $quantita La quantità
     * @param float $importo L'importo unitario
     * @param float $sconto Lo sconto percentuale
     * @return float Il totale calcolato
     */
    function calculate_row_total($quantita, $importo, $sconto)
    {
        $importo_totale = $quantita * $importo;
        if ($sconto > 0) {
            $importo_totale = $importo_totale - ($importo_totale * ($sconto / 100));
        }
        return $importo_totale;
    }
}

if (!function_exists('get_sconto_nome')) {
    /**
     * Formatta lo sconto per la visualizzazione
     *
     * @param float $sconto Lo sconto percentuale
     * @return string La rappresentazione dello sconto
     */
    function get_sconto_nome($sconto)
    {
        if ($sconto <= 0) {
            return '-';
        }
        return $sconto . '%';
    }
} 