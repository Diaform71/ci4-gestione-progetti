<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class ContattoModel extends Model
{
    protected $table            = 'contatti';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'cognome',
        'email',
        'telefono',
        'interno',
        'cellulare',
        'immagine',
        'note',
        'attivo'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'attivo' => 'boolean',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nome' => 'required|min_length[2]|max_length[100]',
        'cognome' => 'required|min_length[2]|max_length[100]',
        'email' => 'permit_empty|valid_email|max_length[255]',
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'Il campo Nome è obbligatorio',
            'min_length' => 'Il Nome deve contenere almeno {param} caratteri',
            'max_length' => 'Il Nome non può superare {param} caratteri',
        ],
        'cognome' => [
            'required' => 'Il campo Cognome è obbligatorio',
            'min_length' => 'Il Cognome deve contenere almeno {param} caratteri',
            'max_length' => 'Il Cognome non può superare {param} caratteri',
        ],
        'email' => [
            'valid_email' => 'Inserire un indirizzo email valido',
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
     * Ottiene il nome completo del contatto
     *
     * @return string
     */
    public function getNomeCompleto(array $contatto): string
    {
        return $contatto['nome'] . ' ' . $contatto['cognome'];
    }
    
    /**
     * Ottiene tutti i contatti attivi
     *
     * @return array
     */
    public function getActiveContatti(): array
    {
        return $this->where('attivo', 1)
                    ->findAll();
    }
}
