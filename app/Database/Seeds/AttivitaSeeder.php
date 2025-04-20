<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\AttivitaModel;
use App\Models\ProgettoModel;
use App\Models\UtentiModel;
use CodeIgniter\I18n\Time;

class AttivitaSeeder extends Seeder
{
    public function run()
    {
        $attivitaModel = new AttivitaModel();
        $progettoModel = new ProgettoModel();
        $utentiModel = new UtentiModel();
        
        // Ottieni un progetto esistente
        $progetti = $progettoModel->findAll();
        
        if (empty($progetti)) {
            echo "Nessun progetto trovato. Impossibile creare attività di esempio.\n";
            return;
        }
        
        // Ottieni utenti esistenti
        $utenti = $utentiModel->findAll();
        
        if (count($utenti) < 2) {
            echo "Non ci sono abbastanza utenti nel sistema. Impossibile creare attività di esempio.\n";
            return;
        }
        
        // Crea attività di esempio
        $stati = ['da_iniziare', 'in_corso', 'in_pausa', 'completata', 'annullata'];
        $priorita = ['bassa', 'media', 'alta', 'urgente'];
        
        $numAttivita = 10; // Numero di attività da creare
        
        $attivitaCreate = 0;
        
        for ($i = 0; $i < $numAttivita; $i++) {
            $progetto = $progetti[array_rand($progetti)];
            $creatore = $utenti[array_rand($utenti)];
            $assegnato = $utenti[array_rand($utenti)];
            $stato = $stati[array_rand($stati)];
            $prioritaAttivita = $priorita[array_rand($priorita)];
            
            $completata = ($stato === 'completata');
            $completataIl = $completata ? Time::now()->subDays(rand(1, 10))->toDateTimeString() : null;
            
            // Genera data di scadenza
            $dataScadenza = null;
            if (rand(0, 10) > 3) { // 70% di probabilità di avere una scadenza
                $dataScadenza = Time::now()->addDays(rand(-5, 30))->toDateTimeString();
            }
            
            $data = [
                'id_progetto' => $progetto['id'],
                'id_utente_assegnato' => $assegnato['id'],
                'id_utente_creatore' => $creatore['id'],
                'titolo' => 'Attività di esempio ' . ($i + 1),
                'descrizione' => 'Questa è una descrizione di esempio per l\'attività ' . ($i + 1),
                'priorita' => $prioritaAttivita,
                'stato' => $stato,
                'data_scadenza' => $dataScadenza,
                'data_creazione' => Time::now()->subDays(rand(5, 20))->toDateTimeString(),
                'data_aggiornamento' => Time::now()->subDays(rand(1, 4))->toDateTimeString(),
                'completata' => $completata,
                'completata_il' => $completataIl
            ];
            
            if ($attivitaModel->insert($data)) {
                $attivitaCreate++;
            }
        }
        
        echo "Sono state create {$attivitaCreate} attività di esempio.\n";
    }
} 