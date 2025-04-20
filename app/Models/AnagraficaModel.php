<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class AnagraficaModel extends Model
{
    protected $table            = 'anagrafiche';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ragione_sociale',
        'indirizzo',
        'citta',
        'nazione',
        'cap',
        'email',
        'url',
        'telefono',
        'fax',
        'partita_iva',
        'codice_fiscale',
        'sdi',
        'id_iva',
        'fornitore',
        'cliente',
        'logo',
        'attivo'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'fornitore' => 'boolean',
        'cliente' => 'boolean',
        'attivo' => 'boolean',
        'id_iva' => 'integer',
    ];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'ragione_sociale' => 'required|min_length[3]|max_length[255]',
        'email' => 'permit_empty|valid_email|max_length[255]',
        'partita_iva' => 'required|max_length[20]',
        'codice_fiscale' => 'permit_empty|max_length[20]',
        'sdi' => 'permit_empty|max_length[7]',
    ];
    
    protected $validationMessages = [
        'ragione_sociale' => [
            'required' => 'Il campo Ragione Sociale è obbligatorio',
            'min_length' => 'La Ragione Sociale deve contenere almeno {param} caratteri',
            'max_length' => 'La Ragione Sociale non può superare {param} caratteri',
        ],
        'email' => [
            'valid_email' => 'Inserire un indirizzo email valido',
        ],
        'partita_iva' => [
            'required' => 'Il campo Partita IVA è obbligatorio',
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Ottiene tutte le anagrafiche attive
     *
     * @return array
     */
    public function getActiveAnagrafiche(): array
    {
        return $this->where('attivo', 1)
                    ->findAll();
    }
    
    /**
     * Ottiene tutti i fornitori attivi
     *
     * @return array
     */
    public function getActiveFornitori(): array
    {
        return $this->where('fornitore', 1)
                    ->where('attivo', 1)
                    ->findAll();
    }
    
    /**
     * Ottiene tutti i clienti attivi
     *
     * @return array
     */
    public function getActiveClienti(): array
    {
        return $this->where('cliente', 1)
                    ->where('attivo', 1)
                    ->findAll();
    }
}
