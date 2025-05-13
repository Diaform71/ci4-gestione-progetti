<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgettoMaterialeModel extends Model
{
    protected $table            = 'progetti_materiali';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_progetto', 'id_materiale', 'quantita', 'unita_misura', 'note'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Restituisce i materiali associati a un progetto
     *
     * @param int $id_progetto ID del progetto
     * @return array
     */
    public function getMaterialiByProgetto(int $id_progetto): array
    {
        return $this->select('progetti_materiali.*, 
                               materiali.*')
                    ->join('materiali', 'materiali.id = progetti_materiali.id_materiale')
                    ->where('progetti_materiali.id_progetto', $id_progetto)
                    ->where('progetti_materiali.deleted_at IS NULL')
                    ->findAll();
    }

    /**
     * Verifica se un materiale è già presente nel progetto
     *
     * @param int $id_progetto ID del progetto
     * @param int $id_materiale ID del materiale
     * @return bool
     */
    public function esisteMateriale(int $id_progetto, int $id_materiale): bool
    {
        $count = $this->where('id_progetto', $id_progetto)
                      ->where('id_materiale', $id_materiale)
                      ->where('deleted_at IS NULL')
                      ->countAllResults();
        
        return $count > 0;
    }
    
    /**
     * Ottiene i dettagli dei materiali di un progetto a partire dai loro ID
     *
     * @param array $ids Array di ID delle associazioni progetto-materiale
     * @return array
     */
    public function getDettagliMateriali(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        
        return $this->select('progetti_materiali.id, progetti_materiali.id_materiale, progetti_materiali.quantita, 
                             progetti_materiali.unita_misura, progetti_materiali.note, 
                             materiali.codice, materiali.descrizione, materiali.produttore')
                    ->join('materiali', 'materiali.id = progetti_materiali.id_materiale')
                    ->whereIn('progetti_materiali.id', $ids)
                    ->where('progetti_materiali.deleted_at IS NULL')
                    ->findAll();
    }
} 