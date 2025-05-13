<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CondizioniPagamentoSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nome' => 'Bonifico 30gg data fattura',
                'descrizione' => 'Pagamento a 30 giorni dalla data di emissione della fattura',
                'giorni' => 30,
                'fine_mese' => 0,
                'attivo' => 1
            ],
            [
                'nome' => 'Bonifico 60gg data fattura',
                'descrizione' => 'Pagamento a 60 giorni dalla data di emissione della fattura',
                'giorni' => 60,
                'fine_mese' => 0,
                'attivo' => 1
            ],
            [
                'nome' => 'Bonifico 30gg fine mese',
                'descrizione' => 'Pagamento a 30 giorni fine mese dalla data di emissione della fattura',
                'giorni' => 30,
                'fine_mese' => 1,
                'attivo' => 1
            ],
            [
                'nome' => 'Bonifico 60gg fine mese',
                'descrizione' => 'Pagamento a 60 giorni fine mese dalla data di emissione della fattura',
                'giorni' => 60,
                'fine_mese' => 1,
                'attivo' => 1
            ],
            [
                'nome' => 'Rimessa diretta',
                'descrizione' => 'Pagamento immediato alla consegna',
                'giorni' => 0,
                'fine_mese' => 0,
                'attivo' => 1
            ],
            [
                'nome' => 'Pagamento anticipato',
                'descrizione' => 'Pagamento anticipato prima della consegna',
                'giorni' => 0,
                'fine_mese' => 0,
                'attivo' => 1
            ],
        ];

        $this->db->table('condizioni_pagamento')->insertBatch($data);
    }
} 