<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class ProgettiDemoSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        
        // Ottieni un'anagrafica
        $anagrafica = $db->table('anagrafiche')->get()->getFirstRow('array');
        
        if (!$anagrafica) {
            echo "Nessuna anagrafica trovata. Impossibile creare progetti di esempio.\n";
            return;
        }
        
        // Ottieni un utente
        $utente = $db->table('utenti')->get()->getFirstRow('array');
        
        if (!$utente) {
            echo "Nessun utente trovato. Impossibile creare progetti di esempio.\n";
            return;
        }
        
        // Crea progetti di esempio
        $progetti = [
            [
                'id_anagrafica' => $anagrafica['id'],
                'id_creato_da' => $utente['id'],
                'id_responsabile' => $utente['id'],
                'nome' => 'Sviluppo Sito Web E-commerce',
                'descrizione' => 'Creazione di un sito web di e-commerce completo con gestione prodotti, carrello e pagamenti',
                'data_inizio' => Time::now()->subDays(30)->toDateTimeString(),
                'data_fine' => Time::now()->addDays(60)->toDateTimeString(),
                'budget' => 15000.00,
                'stato' => 'in_corso',
                'created_at' => Time::now()->toDateTimeString(),
                'updated_at' => Time::now()->toDateTimeString(),
            ],
            [
                'id_anagrafica' => $anagrafica['id'],
                'id_creato_da' => $utente['id'],
                'id_responsabile' => $utente['id'],
                'nome' => 'Sviluppo App Mobile',
                'descrizione' => 'Sviluppo di un\'applicazione mobile per Android e iOS per la gestione delle prenotazioni',
                'data_inizio' => Time::now()->subDays(60)->toDateTimeString(),
                'data_fine' => Time::now()->addDays(30)->toDateTimeString(),
                'budget' => 20000.00,
                'stato' => 'in_corso',
                'created_at' => Time::now()->toDateTimeString(),
                'updated_at' => Time::now()->toDateTimeString(),
            ],
            [
                'id_anagrafica' => $anagrafica['id'],
                'id_creato_da' => $utente['id'],
                'id_responsabile' => $utente['id'],
                'nome' => 'Redesign Sito Aziendale',
                'descrizione' => 'Aggiornamento del sito web aziendale con un nuovo design responsive e contenuti aggiornati',
                'data_inizio' => Time::now()->subDays(15)->toDateTimeString(),
                'data_fine' => Time::now()->addDays(15)->toDateTimeString(),
                'budget' => 8000.00,
                'stato' => 'in_corso',
                'created_at' => Time::now()->toDateTimeString(),
                'updated_at' => Time::now()->toDateTimeString(),
            ],
        ];
        
        $db->table('progetti')->insertBatch($progetti);
        
        echo "Sono stati creati " . count($progetti) . " progetti di esempio.\n";
    }
} 