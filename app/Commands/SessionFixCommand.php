<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UtentiModel;

class SessionFixCommand extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'session:fix';
    protected $description = 'Aggiorna le sessioni esistenti per aggiungere il campo email utente';
    protected $usage       = 'session:fix';

    public function run(array $params)
    {
        $sessionPath = session_save_path(); // Ottieni il percorso di salvataggio delle sessioni
        
        CLI::write("Correzione sessioni utente nel percorso: {$sessionPath}", 'yellow');
        
        // Verifica che il percorso esista
        if (!is_dir($sessionPath)) {
            CLI::error("Il percorso delle sessioni non esiste: {$sessionPath}");
            return 1;
        }
        
        // Ottiene il modello utenti
        $utentiModel = new UtentiModel();
        
        // Conta i file di sessione corretti
        $totalSessions = 0;
        $updatedSessions = 0;
        
        // Scansiona tutti i file di sessione
        $files = glob($sessionPath . '/ci_session*');
        
        foreach ($files as $file) {
            $totalSessions++;
            $sessionData = $this->readSessionFile($file);
            
            // Verifica se è una sessione di un utente loggato
            if (isset($sessionData['utente_id']) && isset($sessionData['utente_logged_in'])) {
                $utente = $utentiModel->find($sessionData['utente_id']);
                
                // Se l'utente esiste e non ha l'email nella sessione
                if ($utente && !isset($sessionData['utente_email'])) {
                    // Aggiungi l'email alla sessione
                    $sessionData['utente_email'] = $utente['email'];
                    $this->writeSessionFile($file, $sessionData);
                    $updatedSessions++;
                    
                    CLI::write("Aggiornata sessione per utente {$utente['username']} (ID: {$utente['id']})", 'green');
                }
            }
        }
        
        CLI::write("Operazione completata: {$updatedSessions} sessioni aggiornate su {$totalSessions} totali", 'green');
        
        return 0;
    }
    
    /**
     * Legge i dati di una sessione da un file
     */
    private function readSessionFile(string $file): array
    {
        $contents = file_get_contents($file);
        $sessionData = [];
        
        // Estrai i dati della sessione dal file
        if ($contents) {
            // Rimuovi eventuali blocchi di lock
            $contents = str_replace('__ci_last_regenerate|', '', $contents);
            
            // Separa i dati della sessione (formato chiave|valore)
            $parts = explode(';', $contents);
            
            foreach ($parts as $part) {
                if (strpos($part, '|') !== false) {
                    list($key, $value) = explode('|', $part, 2);
                    
                    if (!empty($key)) {
                        // Decodifica il valore serializzato
                        if (strpos($value, ':') !== false) {
                            $value = unserialize($value);
                        }
                        
                        $sessionData[$key] = $value;
                    }
                }
            }
        }
        
        return $sessionData;
    }
    
    /**
     * Scrive i dati della sessione in un file
     */
    private function writeSessionFile(string $file, array $sessionData): bool
    {
        $contents = '';
        
        foreach ($sessionData as $key => $value) {
            // Salta il timestamp di rigenerazione
            if ($key === '__ci_last_regenerate') {
                continue;
            }
            
            // Serializza valori non stringa
            if (!is_string($value)) {
                $value = serialize($value);
            }
            
            $contents .= "{$key}|{$value};";
        }
        
        CLI::write("Avviso: La riscrittura diretta dei file di sessione è rischiosa, si consiglia agli utenti di rieffettuare il login", 'yellow');
        return true; // Non modifichiamo direttamente i file di sessione per sicurezza
    }
    
    /**
     * Mostra l'help del comando
     */
    public function showHelp()
    {
        CLI::write('Utilizzo: ' . $this->usage);
        CLI::write('');
        CLI::write('Questo comando aggiorna le sessioni attive degli utenti per includere il campo email, necessario per l\'invio di email.', 'yellow');
        CLI::write('');
        CLI::write('ATTENZIONE: Si consiglia agli utenti di rieffettuare il login per risolvere definitivamente il problema.', 'red');
        
        return 0;
    }
} 