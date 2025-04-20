<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UtentiModel;

class UpdateAdminRuolo extends Seeder
{
    public function run()
    {
        $utentiModel = new UtentiModel();
        
        // Aggiorna l'utente admin esistente
        $admin = $utentiModel->where('username', 'admin')->first();
        
        if ($admin) {
            $utentiModel->update($admin['id'], ['ruolo' => 'admin']);
            echo "Ruolo admin assegnato all'utente amministratore.\n";
        } else {
            echo "Utente admin non trovato.\n";
        }
    }
} 