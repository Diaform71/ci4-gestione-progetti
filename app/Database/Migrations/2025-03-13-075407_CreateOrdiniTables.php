<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdiniTables extends Migration
{
    public function up()
    {
        // Tabella per gli ordini materiale
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'data' => [
                'type'       => 'DATE',
                'null'       => false,
            ],
            'oggetto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'descrizione' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'id_anagrafica' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_referente' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_progetto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'stato' => [
                'type'       => 'ENUM',
                'constraint' => ['bozza', 'in_attesa', 'inviato', 'confermato', 'in_consegna', 'consegnato', 'completato', 'annullato'],
                'default'    => 'bozza',
                'null'       => false,
            ],
            'id_utente_creatore' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'data_invio' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'data_accettazione' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'data_consegna_prevista' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'data_consegna_effettiva' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'data_completamento' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'data_annullamento' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'note' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'importo_totale' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'sconto_totale' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'sconto_fisso' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'costo_trasporto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'condizioni_pagamento' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'condizioni_consegna' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'id_offerta_fornitore' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['id_anagrafica', 'id_progetto', 'stato']);
        $this->forge->createTable('ordini_materiale');
        
        // Tabella per le voci degli ordini
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_ordine' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_materiale' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'codice' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'descrizione' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'quantita' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 1.00,
            ],
            'prezzo_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'importo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'unita_misura' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'pz',
            ],
            'sconto' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'id_progetto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_offerta_voce' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'note' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'data_consegna_prevista' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['id_ordine', 'id_materiale']);
        $this->forge->createTable('ordini_materiale_voci');

        // Aggiungiamo i vincoli FOREIGN KEY
        $this->db->query('ALTER TABLE `ordini_materiale` ADD CONSTRAINT `ordini_materiale_id_anagrafica_fk` FOREIGN KEY (`id_anagrafica`) REFERENCES `anagrafiche` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `ordini_materiale` ADD CONSTRAINT `ordini_materiale_id_progetto_fk` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `ordini_materiale` ADD CONSTRAINT `ordini_materiale_id_referente_fk` FOREIGN KEY (`id_referente`) REFERENCES `contatti` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `ordini_materiale` ADD CONSTRAINT `ordini_materiale_id_utente_creatore_fk` FOREIGN KEY (`id_utente_creatore`) REFERENCES `utenti` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `ordini_materiale` ADD CONSTRAINT `ordini_materiale_id_offerta_fornitore_fk` FOREIGN KEY (`id_offerta_fornitore`) REFERENCES `offerte_fornitore` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ordini_materiale_voci` ADD CONSTRAINT `ordini_materiale_voci_id_ordine_fk` FOREIGN KEY (`id_ordine`) REFERENCES `ordini_materiale` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `ordini_materiale_voci` ADD CONSTRAINT `ordini_materiale_voci_id_materiale_fk` FOREIGN KEY (`id_materiale`) REFERENCES `materiali` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `ordini_materiale_voci` ADD CONSTRAINT `ordini_materiale_voci_id_progetto_fk` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `ordini_materiale_voci` ADD CONSTRAINT `ordini_materiale_voci_id_offerta_voce_fk` FOREIGN KEY (`id_offerta_voce`) REFERENCES `offerte_fornitore_voci` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Rimuoviamo i vincoli FOREIGN KEY
        $this->db->query('ALTER TABLE `ordini_materiale_voci` DROP FOREIGN KEY `ordini_materiale_voci_id_ordine_fk`');
        $this->db->query('ALTER TABLE `ordini_materiale_voci` DROP FOREIGN KEY `ordini_materiale_voci_id_materiale_fk`');
        $this->db->query('ALTER TABLE `ordini_materiale_voci` DROP FOREIGN KEY `ordini_materiale_voci_id_progetto_fk`');
        $this->db->query('ALTER TABLE `ordini_materiale_voci` DROP FOREIGN KEY `ordini_materiale_voci_id_offerta_voce_fk`');

        $this->db->query('ALTER TABLE `ordini_materiale` DROP FOREIGN KEY `ordini_materiale_id_anagrafica_fk`');
        $this->db->query('ALTER TABLE `ordini_materiale` DROP FOREIGN KEY `ordini_materiale_id_progetto_fk`');
        $this->db->query('ALTER TABLE `ordini_materiale` DROP FOREIGN KEY `ordini_materiale_id_referente_fk`');
        $this->db->query('ALTER TABLE `ordini_materiale` DROP FOREIGN KEY `ordini_materiale_id_utente_creatore_fk`');
        $this->db->query('ALTER TABLE `ordini_materiale` DROP FOREIGN KEY `ordini_materiale_id_offerta_fornitore_fk`');

        // Eliminiamo le tabelle
        $this->forge->dropTable('ordini_materiale_voci');
        $this->forge->dropTable('ordini_materiale');
    }
}
