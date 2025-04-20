<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\ProgettoModel;
use App\Models\UtentiModel;
use App\Models\AnagraficaModel;
use CodeIgniter\I18n\Time;

class ProgettoSeeder extends Seeder
{
    public function run()
    {
        $progettoModel = new ProgettoModel();
        $utentiModel = new UtentiModel();
        $anagraficaModel = new AnagraficaModel();
        
        // Ottieni utenti esistenti
        $utenti = $utentiModel->findAll();
        
        if (empty($utenti)) {
            echo "Nessun utente trovato. Impossibile creare progetti di esempio.\n";
            return;
        }
        
        // Ottieni anagrafiche esistenti
        $anagrafiche = $anagraficaModel->findAll();
        
        if (empty($anagrafiche)) {
            echo "Nessuna anagrafica trovata. Impossibile creare progetti di esempio.\n";
            return;
        }
        
        // Crea progetti di esempio
        $stati = ['attivo', 'completato', 'sospeso', 'annullato'];
        
        $numProgetti = 5; // Numero di progetti da creare
        
        $progettiCreati = 0;
        
        for ($i = 0; $i < $numProgetti; $i++) {
            $anagrafica = $anagrafiche[array_rand($anagrafiche)];
            $utente = $utenti[array_rand($utenti)];
            $responsabile = $utenti[array_rand($utenti)];
            $stato = $stati[array_rand($stati)];
            
            // Genera date
            $dataInizio = Time::now()->subDays(rand(10, 60))->toDateTimeString();
            $dataFine = null;
            if ($stato === 'completato') {
                $dataFine = Time::now()->subDays(rand(1, 9))->toDateTimeString();
            } else if (rand(0, 10) > 7) { // 30% di probabilità per progetti non completati
                $dataFine = Time::now()->addDays(rand(10, 60))->toDateTimeString();
            }
            
            $data = [
                'id_anagrafica' => $anagrafica['id'],
                'id_creato_da' => $utente['id'],
                'id_responsabile' => $responsabile['id'],
                'nome' => 'Progetto di esempio ' . ($i + 1),
                'descrizione' => 'Questa è una descrizione di esempio per il progetto ' . ($i + 1),
                'data_inizio' => $dataInizio,
                'data_fine' => $dataFine,
                'budget' => rand(1000, 10000) * 10,
                'stato' => $stato,
                'note' => 'Note di esempio per il progetto ' . ($i + 1),
                'created_at' => $dataInizio,
                'updated_at' => Time::now()->subDays(rand(1, 9))->toDateTimeString(),
            ];
            
            if ($progettoModel->insert($data)) {
                $progettiCreati++;
            }
        }
        
        echo "Sono stati creati {$progettiCreati} progetti di esempio.\n";
    }
} 