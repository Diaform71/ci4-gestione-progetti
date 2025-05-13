<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class BaseSeeder extends Seeder
{
    /**
     * Crea o aggiorna le anagrafiche
     */
    protected function createOrUpdateAnagrafiche()
    {
        $db = $this->db;
        
        // Verifica se esistono aliquote IVA
        $aliquoteIvaCount = $db->table('aliquote_iva')->countAllResults();
        
        if ($aliquoteIvaCount === 0) {
            // Crea le aliquote IVA
            $db->table('aliquote_iva')->insertBatch([
                [
                    'codice' => 'STD',
                    'descrizione' => 'Standard',
                    'percentuale' => 22.00,
                    'created_at' => Time::now()->toDateTimeString(),
                    'updated_at' => Time::now()->toDateTimeString(),
                ],
                [
                    'codice' => 'RID',
                    'descrizione' => 'Ridotta',
                    'percentuale' => 10.00,
                    'created_at' => Time::now()->toDateTimeString(),
                    'updated_at' => Time::now()->toDateTimeString(),
                ],
            ]);
            
            echo "Aliquote IVA create\n";
        }
        
        // Ottieni l'ID di un'aliquota IVA
        $aliquotaIva = $db->table('aliquote_iva')->get()->getFirstRow('array');
        
        if (!$aliquotaIva) {
            return;
        }
        
        // Aggiorna le anagrafiche esistenti impostando id_iva a 1 se è NULL
        $db->table('anagrafiche')
           ->where('id_iva IS NULL')
           ->update(['id_iva' => $aliquotaIva['id']]);
        
        echo "Anagrafiche aggiornate con id_iva\n";
    }
} 