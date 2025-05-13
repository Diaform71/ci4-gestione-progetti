<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ImpostazioniSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Impostazioni generali
            [
                'chiave'       => 'nome_azienda',
                'valore'       => 'La Mia Azienda',
                'tipo'         => 'stringa',
                'descrizione'  => 'Nome dell\'azienda',
                'gruppo'       => 'generale'
            ],
            [
                'chiave'       => 'email_azienda',
                'valore'       => 'info@lamiaazienda.it',
                'tipo'         => 'stringa',
                'descrizione'  => 'Email principale dell\'azienda',
                'gruppo'       => 'generale'
            ],
            [
                'chiave'       => 'telefono_azienda',
                'valore'       => '+39 0123 456789',
                'tipo'         => 'stringa',
                'descrizione'  => 'Numero di telefono dell\'azienda',
                'gruppo'       => 'generale'
            ],
            [
                'chiave'       => 'indirizzo_azienda',
                'valore'       => 'Via Roma 123, 00100 Roma',
                'tipo'         => 'stringa',
                'descrizione'  => 'Indirizzo dell\'azienda',
                'gruppo'       => 'generale'
            ],
            [
                'chiave'       => 'partita_iva',
                'valore'       => '01234567890',
                'tipo'         => 'stringa',
                'descrizione'  => 'Partita IVA dell\'azienda',
                'gruppo'       => 'generale'
            ],
            [
                'chiave'       => 'codice_fiscale',
                'valore'       => '01234567890',
                'tipo'         => 'stringa',
                'descrizione'  => 'Codice fiscale dell\'azienda',
                'gruppo'       => 'generale'
            ],
            
            // Impostazioni email
            [
                'chiave'       => 'smtp_host',
                'valore'       => 'smtp.gmail.com',
                'tipo'         => 'stringa',
                'descrizione'  => 'Host SMTP per l\'invio di email',
                'gruppo'       => 'email'
            ],
            [
                'chiave'       => 'smtp_port',
                'valore'       => '587',
                'tipo'         => 'intero',
                'descrizione'  => 'Porta SMTP per l\'invio di email',
                'gruppo'       => 'email'
            ],
            [
                'chiave'       => 'smtp_user',
                'valore'       => 'user@gmail.com',
                'tipo'         => 'stringa',
                'descrizione'  => 'Username SMTP per l\'invio di email',
                'gruppo'       => 'email'
            ],
            [
                'chiave'       => 'smtp_pass',
                'valore'       => 'password',
                'tipo'         => 'stringa',
                'descrizione'  => 'Password SMTP per l\'invio di email',
                'gruppo'       => 'email'
            ],
            [
                'chiave'       => 'email_from',
                'valore'       => 'noreply@lamiaazienda.it',
                'tipo'         => 'stringa',
                'descrizione'  => 'Indirizzo mittente per l\'invio di email',
                'gruppo'       => 'email'
            ],
            [
                'chiave'       => 'email_from_name',
                'valore'       => 'Sistema Gestione Progetti',
                'tipo'         => 'stringa',
                'descrizione'  => 'Nome mittente per l\'invio di email',
                'gruppo'       => 'email'
            ],
            
            // Impostazioni notifiche
            [
                'chiave'       => 'notifiche_email_attive',
                'valore'       => '1',
                'tipo'         => 'booleano',
                'descrizione'  => 'Attiva/disattiva l\'invio di notifiche via email',
                'gruppo'       => 'notifiche'
            ],
            [
                'chiave'       => 'notifica_progetto_creato',
                'valore'       => '1',
                'tipo'         => 'booleano',
                'descrizione'  => 'Invia notifica quando viene creato un nuovo progetto',
                'gruppo'       => 'notifiche'
            ],
            [
                'chiave'       => 'notifica_scadenza_progetto',
                'valore'       => '1',
                'tipo'         => 'booleano',
                'descrizione'  => 'Invia notifica quando un progetto sta per scadere',
                'gruppo'       => 'notifiche'
            ],
            [
                'chiave'       => 'giorni_anticipo_notifica_scadenza',
                'valore'       => '3',
                'tipo'         => 'intero',
                'descrizione'  => 'Numero di giorni di anticipo per le notifiche di scadenza',
                'gruppo'       => 'notifiche'
            ],
            
            // Impostazioni sistema
            [
                'chiave'       => 'items_per_pagina',
                'valore'       => '10',
                'tipo'         => 'intero',
                'descrizione'  => 'Numero di elementi per pagina nelle tabelle',
                'gruppo'       => 'sistema'
            ],
            [
                'chiave'       => 'formato_data',
                'valore'       => 'd/m/Y',
                'tipo'         => 'stringa',
                'descrizione'  => 'Formato di visualizzazione delle date',
                'gruppo'       => 'sistema'
            ],
            [
                'chiave'       => 'formato_ora',
                'valore'       => 'H:i',
                'tipo'         => 'stringa',
                'descrizione'  => 'Formato di visualizzazione dell\'ora',
                'gruppo'       => 'sistema'
            ],
            [
                'chiave'       => 'formato_datetime',
                'valore'       => 'd/m/Y H:i',
                'tipo'         => 'stringa',
                'descrizione'  => 'Formato di visualizzazione di data e ora',
                'gruppo'       => 'sistema'
            ],
            [
                'chiave'       => 'lingua_predefinita',
                'valore'       => 'it',
                'tipo'         => 'stringa',
                'descrizione'  => 'Lingua predefinita del sistema',
                'gruppo'       => 'sistema'
            ],
            [
                'chiave'       => 'tema_predefinito',
                'valore'       => 'light',
                'tipo'         => 'stringa',
                'descrizione'  => 'Tema predefinito dell\'interfaccia',
                'gruppo'       => 'sistema'
            ],
            [
                'chiave'       => 'debug_mode',
                'valore'       => '0',
                'tipo'         => 'booleano',
                'descrizione'  => 'Attiva/disattiva la modalità debug',
                'gruppo'       => 'sistema'
            ],
        ];
        
        // Inserisci le impostazioni
        $builder = $this->db->table('impostazioni');
        
        foreach ($data as $row) {
            // Controlla se l'impostazione esiste già
            $esistente = $builder->where('chiave', $row['chiave'])
                               ->where('id_utente IS NULL')
                               ->get()
                               ->getRow();
            
            if (!$esistente) {
                $builder->insert($row);
            }
        }
    }
} 