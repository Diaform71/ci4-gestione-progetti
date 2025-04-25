<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\AnagraficaModel;

class AnagraficaDemoSeeder extends Seeder
{
    public function run()
    {
        $anagraficaModel = new AnagraficaModel();
        
        // Nomi di aziende di esempio
        $aziende = [
            'Tech Innovations Srl',
            'Digital Solutions Spa',
            'Green Energy Ltd',
            'Smart Logistics Inc',
            'Creative Design Studio',
            'Food Services Group',
            'Healthcare Solutions Srl',
            'Educational Systems Spa',
            'Financial Advisors Ltd',
            'Insurance Partners Srl'
        ];
        
        // Città di esempio
        $citta = [
            'Milano', 'Roma', 'Torino', 'Bologna', 'Napoli', 
            'Bari', 'Firenze', 'Padova', 'Genova', 'Verona'
        ];
        
        $anagraficheCreate = 0;
        
        foreach ($aziende as $index => $azienda) {
            $data = [
                'ragione_sociale' => $azienda,
                'indirizzo' => 'Via Esempio ' . ($index + 1),
                'citta' => $citta[array_rand($citta)],
                'nazione' => 'Italia',
                'cap' => rand(10000, 90000),
                'email' => 'info@' . strtolower(str_replace(' ', '', $azienda)) . '.com',
                'telefono' => '0' . rand(100, 999) . rand(100000, 999999),
                'partita_iva' => '0' . rand(1000000000, 9999999999),
                'codice_fiscale' => 'CF' . rand(1000000000, 9999999999),
                'cliente' => rand(0, 1),
                'fornitore' => rand(0, 1),
                'id_iva' => 1,
                'attivo' => 1
            ];
            
            if ($anagraficaModel->insert($data)) {
                $anagraficheCreate++;
            }
        }
        
        echo "Sono state create {$anagraficheCreate} anagrafiche di esempio.\n";
    }
} 