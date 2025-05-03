<?php

namespace App\Models;

use CodeIgniter\Model;

class CondizioniPagamentoModel extends Model
{
    protected $table = 'condizioni_pagamento';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    
    protected $allowedFields = [
        'nome', 'descrizione', 'giorni', 'fine_mese', 'attivo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Ottiene tutte le condizioni di pagamento attive
     *
     * @return array
     */
    public function getCondizioni()
    {
        return $this->where('attivo', 1)
                   ->orderBy('nome', 'ASC')
                   ->findAll();
    }
} 