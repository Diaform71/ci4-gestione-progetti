<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UtentiModel;

class AdminUtente extends Seeder
{
    public function run()
    {
        $utentiModel = new UtentiModel();
        
        // Verifica se esiste già un utente admin
        $admin = $utentiModel->where('username', 'admin')->first();
        
        if (empty($admin)) {
            // Crea l'utente amministratore predefinito
            $utentiModel->createUtente([
                'username' => 'admin',
                'password' => 'password',  // Verrà criptata automaticamente nel modello
                'email'    => 'admin@example.com',
                'nome'     => 'Amministratore',
                'cognome'  => 'Sistema',
                'attivo'   => 1,
                'ruolo'    => 'admin'
            ]);
            
            echo "Utente amministratore creato con successo!\n";
            echo "Username: admin\n";
            echo "Password: password\n";
            echo "IMPORTANTE: Modificare la password dopo il primo accesso!\n";
        } else {
            echo "L'utente amministratore esiste già.\n";
        }
    }
}
