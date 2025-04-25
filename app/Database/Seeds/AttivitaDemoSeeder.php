<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AttivitaDemoSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        
        // Ottieni progetti esistenti
        $progetti = $db->table('progetti')->get()->getResultArray();
        
        if (empty($progetti)) {
            echo "Nessun progetto trovato. Impossibile creare attività di esempio.\n";
            return;
        }
        
        // Ottieni utenti esistenti
        $utenti = $db->table('utenti')->get()->getResultArray();
        
        if (empty($utenti)) {
            echo "Nessun utente trovato. Impossibile creare attività di esempio.\n";
            return;
        }
        
        // Stati e priorità possibili
        $stati = ['da_iniziare', 'in_corso', 'in_pausa', 'completata', 'annullata'];
        $priorita = ['bassa', 'media', 'alta', 'urgente'];
        
        $attivita = [];
        
        // Per ogni progetto, crea alcune attività
        foreach ($progetti as $progetto) {
            $numAttivita = rand(2, 5); // Da 2 a 5 attività per progetto
            
            for ($i = 0; $i < $numAttivita; $i++) {
                $stato = $stati[array_rand($stati)];
                $prioritaAttivita = $priorita[array_rand($priorita)];
                $utente = $utenti[array_rand($utenti)];
                $creatore = $utenti[array_rand($utenti)];
                
                $completata = ($stato === 'completata');
                $completataIl = $completata ? Time::now()->subDays(rand(1, 10))->toDateTimeString() : null;
                
                // Genera data di scadenza
                $dataScadenza = null;
                if (rand(0, 10) > 3) { // 70% di probabilità di avere una scadenza
                    $dataScadenza = Time::now()->addDays(rand(-5, 30))->toDateTimeString();
                }
                
                $attivita[] = [
                    'id_progetto' => $progetto['id'],
                    'id_utente_assegnato' => $utente['id'],
                    'id_utente_creatore' => $creatore['id'],
                    'titolo' => 'Task ' . ($i + 1) . ' del progetto ' . $progetto['nome'],
                    'descrizione' => 'Descrizione dell\'attività ' . ($i + 1) . ' relativa al progetto ' . $progetto['nome'],
                    'priorita' => $prioritaAttivita,
                    'stato' => $stato,
                    'data_scadenza' => $dataScadenza,
                    'data_creazione' => Time::now()->subDays(rand(5, 20))->toDateTimeString(),
                    'data_aggiornamento' => Time::now()->subDays(rand(1, 4))->toDateTimeString(),
                    'completata' => $completata,
                    'completata_il' => $completataIl
                ];
            }
        }
        
        if (!empty($attivita)) {
            $db->table('attivita')->insertBatch($attivita);
            echo "Sono state create " . count($attivita) . " attività di esempio.\n";
        } else {
            echo "Nessuna attività creata.\n";
        }
    }
} 