<?php

namespace App\Models;

use CodeIgniter\Model;

class RichiestaMaterialeModel extends Model
{
    protected $table            = 'richieste_materiali';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_richiesta', 'id_materiale', 'quantita', 'id_progetto', 'unita_misura', 'note'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Restituisce i materiali associati a una richiesta d'offerta
     *
     * @param int $id_richiesta ID della richiesta d'offerta
     * @return array
     */
    public function getMaterialiByRichiesta(int $id_richiesta): array
    {
        return $this->select('richieste_materiali.*, 
                               materiali.codice, 
                               materiali.descrizione, 
                               progetti.nome as nome_progetto')
                    ->join('materiali', 'materiali.id = richieste_materiali.id_materiale')
                    ->join('progetti', 'progetti.id = richieste_materiali.id_progetto', 'left')
                    ->where('richieste_materiali.id_richiesta', $id_richiesta)
                    ->where('richieste_materiali.deleted_at IS NULL')
                    ->findAll();
    }

    /**
     * Restituisce le richieste d'offerta associate a un materiale
     *
     * @param int $id_materiale ID del materiale
     * @return array
     */
    public function getRichiesteByMateriale(int $id_materiale): array
    {
        return $this->select('richieste_materiali.*,
                              richieste_offerta.numero,
                              richieste_offerta.data,
                              richieste_offerta.oggetto,
                              richieste_offerta.stato,
                              richieste_offerta.id_anagrafica,
                              anagrafiche.ragione_sociale,
                              progetti.nome as nome_progetto')
                    ->join('richieste_offerta', 'richieste_offerta.id = richieste_materiali.id_richiesta')
                    ->join('anagrafiche', 'anagrafiche.id = richieste_offerta.id_anagrafica', 'left')
                    ->join('progetti', 'progetti.id = richieste_materiali.id_progetto', 'left')
                    ->where('richieste_materiali.id_materiale', $id_materiale)
                    ->where('richieste_materiali.deleted_at IS NULL')
                    ->where('richieste_offerta.deleted_at IS NULL')
                    ->orderBy('richieste_offerta.data', 'DESC')
                    ->findAll();
    }

    /**
     * Verifica se un materiale è già presente nella richiesta
     *
     * @param int $id_richiesta ID della richiesta d'offerta
     * @param int $id_materiale ID del materiale
     * @return bool
     */
    public function esisteMateriale(int $id_richiesta, int $id_materiale): bool
    {
        $count = $this->where('id_richiesta', $id_richiesta)
                      ->where('id_materiale', $id_materiale)
                      ->where('deleted_at IS NULL')
                      ->countAllResults();
        
        return $count > 0;
    }
} 